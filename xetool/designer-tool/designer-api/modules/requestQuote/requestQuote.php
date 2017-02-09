<?php
	/* Check Un-authorize Access */
if (!defined('accessUser')) {
    die("Error");
}

class RequestQuote extends UTIL
{    
	/**
	*
	*date of created 19-5-2016(dd-mm-yy)
	*date of Modified (dd-mm-yy)
	*add for a quote
	*
	* @return refid
	* 
	*/ 
	public function addToQuote(){
		$error = false;
		$result = $this->storeApiLogin();
		if($this->storeApiLogin==true){
			$apikey = $this->_request['apikey'];               
			$designData = $this->_request['designData'];
			$refid = $this->_request['refid'];
			$refid = $this->saveDesignStateCart($apikey, $refid, $designData);
			if($refid>0){
				$dbstat = $this->saveProductPreviewSvgImagesOnAddToCart($apikey, $refid, $designData);
			}else{
				$msg=array('status'=>'invalid refid','error'=>$refid);
				$this->response($this->json($msg), 200);
			}
			$msg=array('status'=>'success','refid'=>$refid);
			$this->response($this->json($msg), 200);
		}else{
			$msg=array('status'=>'apiLoginFailed','error'=>json_decode($result));
			$this->response($this->json($msg), 200);
		}
	}
	/**
	*Custom function to add customer info for Request a Quote
	*
	*@param Customer details 
	*
	* @return json string
	* 
	*/ 
	public function addCustomerInfo(){
		$this->_request = json_decode(stripslashes($this->_request['formData']),true);
		/* echo $this->_request->refid;
		echo $this->_request->productData;
		exit; */
		$refId = $this->_request['refid'];
		$productData = $this->_request['productData'];
		//$productData = json_decode(stripslashes($postProductData),true);
		//$cData = $this->_request['customerData'];
		$customerData = $this->_request['customerData'];
		$customerName = $customerData['name'];
		$email = $customerData['email'];
		$address = json_encode($customerData);			
		$sql = "INSERT INTO ".TABLE_PREFIX."customer_order_info (refid, customer_name, email, address, product_info, order_date) VALUES ('".$refId."','".$customerName."','".$email."','".$address."','".$postProductData."',now())";
		$orderrId = $this->executeGenericInsertQuery($sql); 
		if($orderrId){
			$filepath = '';
			if(isset($_FILES) && $_FILES['customerFile']['name']!='')
			{
				$orderPath = $this->getOrdersPath()."/".$orderrId;//copy to
				if(!is_dir($orderPath)){ 
					$mkDir = "";
					$tags = explode('/' ,$orderPath);           
					foreach($tags as $folder){          
						$mkDir .= $folder ."/";   
						if (!file_exists($mkDir)) mkdir($mkDir, 0755, true);         
					}
				}
				if(is_uploaded_file($_FILES["customerFile"]["tmp_name"]))
				{ 
					if(move_uploaded_file($_FILES["customerFile"]["tmp_name"], $orderPath."/".$_FILES['customerFile']['name']))
					{
						$filepath = $_FILES['customerFile']['name'];
					}
				}
			}
			$createHtml = $this->createHtml($orderrId,$refId,$customerData,$productData,$filepath);
			$msg=array("status"=>"success","orderid"=>$orderrId,"mail"=>$createHtml);
		}else{
			$msg=array("status"=>"failed","sql"=>$sql);
		}
		
		$this->response($this->json($msg), 200);  
	}
	
	/**
	*Custom function to create info html for Request a Quote
	*
	*@param order information
	*
	* @return boolean true/false 
	* 
	*/
	public function createHtml($orderrId,$refId,$customerData,$productData,$filepath){
		$orderId = $orderrId;
		$itemId = 1;
		$productname = $productData[0]['product_name'];
		$sku = $productData[0]['product_sku'];
		$productSize = $productData[0]['xe_size'];
		$customerName = $customerData['name'];
		$customerEmail = $customerData['email'];
		$quantity = $customerData['quantity'];
		$base64 = ($sku!='')?$sku:$productname;
		$date = date("d/m/Y");
		$html = '';
		$html = '<html><title>Order APP</title><style>body{background-color:#fff!important;margin:0;padding:0;font-size:18px;color:#333}.page-wrap{width:1024px;margin:auto}.topbar{margin-top:20px}.topbar .left{float:left;width:50%}.topbar .right{float:left}.topbar .right img{max-width:100%;}.topbar h2{margin:0;padding:0;text-transform:uppercase}.topbar p{margin:0 0 10px;padding:0;font-size:20px}.product-box{margin-top:20px;display:inline-block;clear:both;border:1px solid blue;}.product-box .leftbar{float:left;width:50%}.product-box .leftbar .header{background:green;padding:10px 20px;font-size:24px;color:#fff}.product-box .leftbar .product{padding:10px 20px;background:#fff;}.product-box .leftbar .product .product-stage{width:100%;height:auto;clear:both;padding:20px 0}.product-box .leftbar .product .barcode-img{margin-bottom:10px;display:inline-block}.product-box .leftbar .product .barcode-img img{max-width:100%;float:left}.product-box .leftbar .product .product-img{width:100%;height:auto}.product-box .rightbar{float:left;width:50%}.product-box .rightbar .header{padding:10px 20px;font-size:24px;color:#fff}.product-box .rightbar .product{padding:10px 20px;background:#fff;text-align:center}.product-box .rightbar .product .product-stage{width:100%;height:auto;clear:both;padding:20px 0}.product-box .rightbar .product .barcode-img{margin-bottom:10px;display:block}.product-box .rightbar .product .barcode-img img{max-width: 100%;float:left}.product-box .rightbar .product .product-img{width:100%;height:auto}.bold{font-weight:700} .p-r-15 { padding-right:15px; text-align:left;} .repeattd{color: #333;border-collapse: collapse;border-spacing: 0; width: 100%;}.repeattd td, th {border: 1px solid transparent; 
		height: 30px; text-align: left;	border-bottom: 1px solid #ddd;	padding: 3px;} .repeattd th {background: #DFDFDF; font-weight: bold;}
		.repeattd tr:nth-child(even) td { background: #F1F1F1; } .repeattd tr:nth-child(odd) td { background: #FEFEFE; } 
		.leftbar .clear { clear:both; }
		</style><body><div class="page-wrap"><div class="topbar">';
		$generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();
		$html.='<div class="left"><h2>'.$customerName.'</h2><strong>'.$date.'</strong><p>'
		.$orderId.'</p></div><div class="right"><img src="data:image/png;base64,' . base64_encode($generatorPNG->getBarcode($orderId, $generatorPNG::TYPE_CODE_128)) . '" alt="" height="35px" /></div></div><div class="product-box">';  
		$colorincrment = 1;
		$absPath = getcwd();
		$final = str_replace('\\', '/', $absPath);
		$final = $final.'/../../../';
		$refPath = $final."designer-tool/".ASSET_PATH."/previewimg/".$refId."/svg";
		$itemPath = $final."designer-tool/".ORDER_PATH_DIR."/".$orderId."/".$itemId;	
		$currentUrl = $this->getCurrentUrl();//$currentUrl = substr($currentUrl,0,-1);
		$designPath = $currentUrl."/designer-tool/custom-assets/orders/".$orderId."/".$itemId."/";
		$orderpath = $final."designer-tool/".ORDER_PATH_DIR."/".$orderId;
		if($refId != null){
			if(!is_dir($itemPath)){ 
				$mkDir = "";
				$tags = explode('/' ,$itemPath);           
				foreach($tags as $folder){          
					$mkDir .= $folder ."/";   
					if (!file_exists($mkDir)) mkdir($mkDir, 0755, true);         
				}
			} 
			$dir_handle = @opendir($refPath) or die("Unable to open");
			while ($file = readdir($dir_handle)) {
				if($file!="." && $file!=".." && !is_dir("$refPath/$file") )
				copy("$refPath/$file","$itemPath/$file");
			}
			closedir($dir_handle);	
		} 
		$designState_json = file_get_contents($refPath.'/designState.json');
		$json_content = json_decode(stripslashes($designState_json),true);
		$product_barcode = base64_encode($generatorPNG->getBarcode($base64, $generatorPNG::TYPE_CODE_128));
		$printColorNames = "";
		$printColors = "";
		$cmykValue = "";
		$printColorCategories = "";
		$k =1;
		$odd = 1; 
		foreach($json_content['sides'] as $key=>$sides){  
		if($odd>1)$clear = ($odd%2 == 0)? 'clear:none': 'clear:both'; 	
			$printSize = (isset($sides['printSize']) && $sides['printSize'] != '') ? $sides['printSize']:"No PrintSize";
			$printType = (isset($json_content['printType']) && $json_content['printType'] != '')? $json_content['printType']:"No Printtype"; 
			 $printColorNames = (isset($sides['printColorNames']))? count($sides['printColorNames']):0;
			 $html .='<div class="leftbar" style= "'.$clear.';"><div class="header" style="background:blue;">'.$productname.'</div><div class="product" >
			 <div class="barcode-img"><img src="data:image/png;base64,' . $product_barcode . '" height="50px" alt="" /></div>
			 <div style="clear:both;text-align:left">'.$base64.'</div><div class="product-stage">
			 <img class="product-img" src="'.$designPath.'preview_0'.$k.'.svg" alt="" /></div><table><tr><td class=bold>Total Qty :</td>
			 <td>'.$quantity.'</td><tr><td class=bold>Side :</td><td>'.$k.'</td><tr><td class=bold>Size :</td><td>'.$productSize.'</td>
			 <tr><tr><td class=bold>Print Size :<td>'.$printSize.'</td><tr><td class=bold>Print Method :<td>
			 '.$printType.'</td><tr><tr><tr></table>';
			if($printColorNames > 0){	 
			$html .= '<table style="height:90px" class="repeattd"><tbody> <tr>
				<th class="p-r-15" >Color Name</th> <th class="p-r-15">Category</th> 
				<th class="p-r-15">CMYK</th> <th class="p-r-15">Hex Code</th> </tr>';
				foreach($sides['printColorNames'] as $y=>$printcolornames){
					$printcolornames = (!empty($printcolornames))? $printcolornames:'-';
					$printColors[$y] = (!empty($sides['printColors']))? $sides['printColors'][$y]:'-';
					$printColors[$y] = ($printColors[$y][0]=="#")? $printColors[$y]:'<img src="'.$printColors[$y].'" width="20" height="20" />';
					if(!empty($sides['cmykValue'])){
						$content_svg = json_encode($sides['cmykValue'][$y]);
						$cmykValue[$y] = substr($content_svg,0,-1);
						$cmykValue[$y] = ltrim($cmykValue[$y], '{');
						$cmykValue[$y] = str_replace('"','',$cmykValue[$y]);
					}else{
						$cmykValue[$y] = '-';
					}
					$printColorCategories[$y] = (!empty($sides['printColorCategories']))?$sides['printColorCategories'][$y]:'-';
					$html .='<tr> <td class="p-r-15">'.$printcolornames.'</td> <td class="p-r-15">'.$printColorCategories[$y] .'</td> 
					<td class="p-r-15">'.$cmykValue[$y].'</td> 
					<td class="p-r-15">'.$printColors[$y].'</td> </tr>';
				}
			}
			$html .= '</tbody> </table></div></div>'; 
			$k++;
			$odd++; 
		}
		$productData[0]['ref_id'] = $refId;
		$productData[0]['item_id'] = 1;
		$productData[0]['quantity'] = $quantity;
		$decodeResult['order_details']['billing_address'] = $customerData;
		$decodeResult['order_details']['shipping_address'] = $customerData;
		$decodeResult['order_details']['order_id'] = $orderId;
		$decodeResult['order_details']['order_incremental_id'] = $orderId;
		$decodeResult['order_details']['order_status'] = 'pending';
		$decodeResult['order_details']['order_date'] = $date;
		$decodeResult['order_details']['customer_name'] = $customerName;
		$decodeResult['order_details']['customer_email'] = $customerEmail;
		$decodeResult['order_details']['order_items'] = $productData;				
		$orderFileName = $orderpath."/order.json";
		$myfile = fopen($orderFileName, "w") or die("Unable to open file!");
		fwrite($myfile, json_encode($decodeResult));
		fclose($myfile);
		$html.= '</div></div></body></html>';
		$myFile = $orderpath."/info.html"; // or .php  
		$fh = fopen($myFile, 'w'); // or die("error"); 
		fwrite($fh, $html); 
		return $msg = $this->emailSend($orderId,$customerName,$customerEmail,$filepath);
	}
	
	/**
	*Custom function to send mail for Request a Quote
	*
	*@param customer email, order id, customer name
	*
	* @return boolean true/false 
	* 
	*/
	public function emailSend($orderId,$customerName,$customerEmail,$filepath){
		$orderId = $orderId;
		$orderPath = $this->getOrdersPath()."/".$orderId."/";//copy to
		$my_file = "info.html";
		$my_path = $orderPath;
		$my_name = COMPANY_NAME;
		$my_mail = SENDER_EMAIL;
		$my_replyto = SENDER_EMAIL;
		$my_subject = SUBJECT;
		$my_message = "Hi ".$customerName.",<br/>";
		$my_message .= "Request a quote successful. Thank you! <br/><br/>";
		$my_message .= "We deal with your message as quickly as possible . If you have not received a reply to the message within a few days , please check the spam folder and , if necessary, to contact again. <br/><br/>";			
		$my_message .= "Regards,  <br/><br/>";
		$my_message .= "Customer service | <a href='http://inkxe.com/'>inkxe.com.sg</a> <br/><br/>";
		$my_message .= "Tel: (+65) 9000 0000 <br/><br/>";
		$my_message .= "Email: enquiry@inkxe.com <br/><br/>";
		$msg = $this->mail_attachment($my_file, $my_path, $customerEmail, $my_mail, $my_name, $my_subject, $my_message, $my_replyto);
		if($msg == 'send')
		{
			$message = "Hi ,<br/>";
			$message .= $customerName." leave a quote request .<br/><br/>";
			$subject = "Request a Quote inkxe.com designer tool.";
			if($filepath!='')
			{
				$msg = $this->mail_attachment($filepath, $my_path, SENDER_EMAIL, $customerEmail, $customerName, $subject, $message, $customerEmail);
				return $msg;
			}
			else
			{
				$mailHeaders  = "From: ".$customerName."<".$customerEmail."> \r\n";
				$mailHeaders .= "Reply-To: ".$customerEmail."\r\n";
				$mailHeaders .= "Content-type: text/html; charset: utf8\r\n";
				$mailHeaders .= "MIME-Version: 1.0 ";
				$mail = mail(SENDER_EMAIL, $subject, $message, $mailHeaders);
				if($mail){
					return $msg = 'send'; // or use booleans here
				} else {
					return $msg = 'failed'."-3";;
				}
			}
			
		}
		else
		{
			return $msg;
		}
	}
	
	/**
	*Custom attach file in mail content.
	*
	*@param filename, file path, mail to, from mail, from name, subject, body, reply to
	*
	* @return string send/failed 
	* 
	*/
	private function mail_attachment($filename, $path, $mailto, $from_mail, $from_name,$subject, $body, $replyto) {
		$file = $path.$filename;
		$file_size = filesize($file);
		$handle = fopen($file, "r");
		$content = fread($handle, $file_size);
		fclose($handle);
		$content = chunk_split(base64_encode($content));
		$uid = md5(uniqid(time()));
		$eol = PHP_EOL;
		$header = "From: ".$from_name." <".$from_mail.">".$eol;
		$header .= "Reply-To: ".$replyto."\r\n";
		$header .= "MIME-Version: 1.0\r\n";
		$header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n";
		$header .= "This is a multi-part message in MIME format.\r\n";
		$message = "--".$uid.$eol;
		$message .= "Content-type: text/html; charset: utf8\r\n".$eol;
		$message .= $body.$eol;
		$message .= "--".$uid.$eol;
		$message .= "Content-Type: application/pdf; name=\"".$filename."\"".$eol; // use different content types here
		$message .= "Content-Transfer-Encoding: base64".$eol;
		$message .= "Content-Disposition: attachment; filename=\"".$filename."\"".$eol;
		$message .= $content.$eol;
		$message .= "--".$uid."--";
		error_reporting(0);
		if (mail($mailto, $subject, $message, $header)) {
			return $msg = 'send'; // or use booleans here
		} else {
			return $msg = 'failed';
		}
	}
}
