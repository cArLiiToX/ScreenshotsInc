<?php
/* Check Un-authorize Access */
if(!defined('accessUser')) die("Error");

class Predecorate extends UTIL {

	/**
	*
    *date of created 16-6-2016(dd-mm-yy)
	*date of Modified 17-6-2016(dd-mm-yy)
    *save predecorated product images
    *
    * @param (String)apikey 
	* @param (Array)designData 
	* @param (String)name 
	* @param (Int)ref_id 
	* @param (Int)product_id 
	* @param (Float)product_price 
	* @param (Float)custom_price 
	* @param (Int)print_method_id
	* @param (Array)template_image_json 		
    * @return JSON  data
    * 
    */ 
	public function saveDecoratedImage(){
		if(isset($this->_request['cartEncData'])){
			$cartData = $this->formatJSONToArray(stripslashes($this->rcEncDec5(self::POST_KEY,$this->_request['cartEncData'])));
			$msg = array('status'=>'checking','cartData'=>$this->_request['cartEncData']);
			$this->response($this->json($msg), 200);
			$apikey = $cartData['apikey'];               
			$designData = $cartData['designData'];
			$name = $this->_request['name'];
			$refid = $this->_request['ref_id'];
			$product_id = $this->_request['product_id'];
			$product_price = $this->_request['product_price'];
			$custom_price = $this->_request['custom_price'];
			$print_method_id = $this->_request['print_method_id'];
			$mini_qty = $this->_request['mini_qty'];
			$template_image_json = $this->_request['template_image_json'];
		}else{
			$apikey = $this->_request['apikey'];               
			$designData = $this->_request['designData'];
			$name = $this->_request['name'];
			$refid = $this->_request['ref_id'];
			$product_id = $this->_request['product_id'];
			$product_price = $this->_request['product_price'];
			$custom_price = $this->_request['custom_price'];
			$print_method_id = $this->_request['print_method_id'];
			$mini_qty = $this->_request['mini_qty'];
			$template_image_json = $this->_request['template_image_json'];
		} 
		$status = 0;
		$checkExistSql = "SELECT count(*) AS nos FROM ".TABLE_PREFIX."decorated_product WHERE name = '".$name."'";
		$exist = $this->executeFetchAssocQuery($checkExistSql);
		if(!empty($exist) && $exist[0]['nos']){
			$msg['status'] = 'Duplicate pre-decorated product name.';
		}else{
			if($refid==0){
				$cartObj = Flight::carts();
				$refid = $cartObj->saveDesignStateCart($apikey, $refid, $designData);// private
				if($refid>0){
					$dbstat = $cartObj->saveProductPreviewSvgImagesOnAddToCart($apikey, $refid, $designData);
				}
			}
			$sql="INSERT INTO ".TABLE_PREFIX."decorated_product (name,refid,product_id,product_price,custom_price,print_method_id,mini_qty,template_image_json,date_created,date_madified)
			values('".$name."',".$refid.",".$product_id.",".$product_price.",".$custom_price.",".$print_method_id.",".$mini_qty.",'".$template_image_json."',NOW(),NOW())";
			$status = $this->executeGenericDMLQuery($sql);
			$msg['status'] = ($status)?'success':'failed';
		}
		$this->response($this->json($msg), 200);
	}

	/**
	*
    *date of created 2-2-2016(dd-mm-yy)
	*date of Modified 13-4-2016(dd-mm-yy)
    *get predecorated product images data
    *
    * @param (String)apikey 
    * @return JSON  data
    * 
    */
	public function getDecoratedImage(){
		$apikey = $this->_request['apikey'];    
		$start = $this->_request['srtIndex'];    
		$range = $this->_request['range'];
		if(isset($apikey) && ($this->isValidCall($apikey)) && isset($start) && isset($range)){
			$sql = "SELECT * FROM ".TABLE_PREFIX."decorated_product order by pk_id DESC LIMIT ".$start.",".$range."";
			$rows = $this->executeFetchAssocQuery($sql);
			$result = array();
			foreach($rows as $k=>$v){
				$result[$k]['id'] = $v['pk_id'];
				$result[$k]['name'] = $v['name'];
				$result[$k]['ref_id'] = $v['refid'];
				$result[$k]['print_method_id'] = $v['print_method_id'];
				$result[$k]['mini_qty'] = $v['mini_qty'];
				$result[$k]['product_id'] = $v['product_id'];
				$result[$k]['product_price'] = $v['product_price'];
				$result[$k]['custom_price'] = $v['custom_price'];
				$result[$k]['template_image_json'] = $this->formatJSONToArray($v['template_image_json']);
			}
			$resultArr = array();
			$resultArr['pre_decorated_product'] = $result;
		}else{
			$resultArr['status'] = 'Invalid key';
		}
		$this->response($this->json($resultArr), 200);
	}

	/**
	*
    *date created 22-08-2016(dd-mm-yy)
	*date modified 16-09-2016(dd-mm-yy)
    *Remove Predecorated Template
    *	    
    *@param (Int)ids
    *@return json data
    * 
    */
	public function removePreDecoTemplate(){
		$apiKey = $this->_request['apikey'];
		if($this->isValidCall($apiKey)){
			if(!empty($this->_request['ids'])){
				try{
					$templateIdsArray = $this->_request['ids'];
					$ids = implode(',',$templateIdsArray); 
					
					$sql = "select refid from decorated_product where pk_id IN(".$ids.")";
					$rows = $this->executeFetchAssocQuery($sql);
					if(!empty($rows)){
						$refIdDecoArr = array();
						foreach($rows as $row){
							$refIdDecoArr[] = $row['refid'];
						}

						$path = $this->getPreviewImagePath();
						if (is_dir($path) === true && !empty($refIdDecoArr)){
							$dirs = array_diff(scandir($path), array('.', '..'));
							foreach ($dirs as $dir){
								if(in_array($dir, $refIdDecoArr)){
									$this->rrmdir($path.$dir);
								}
							}
							$sql = "DELETE FROM ".TABLE_PREFIX."decorated_product WHERE pk_id IN(".$ids.")";
							$status = $this->executeGenericDMLQuery($sql);
						}
						$response['status'] = true;
						$response['message'] = "Predecorated product template is deleted successfully !!";
					}
					$this->closeConnection();
					$this->response($this->json($response), 200);					
				}catch(Exception $e) {
					$result = array('Caught exception:'=>$e->getMessage());
					$this->response($this->json($result), 200);
				}
			}
		}else{
			$msg=array("status"=>"invalid");
			$this->response($this->json($msg), 200);
		}
	}

	/**
	*
    *date created 07-06-2016(dd-mm-yy)
	*date modified (dd-mm-yy)
    *Add template to cart
    *
    * 
    */
	public function addTemplateToCartById(){
		$confProductId = $this->_request['pid'];
		$xe_size = $this->_request['xe_size'];
		$xe_color = $this->_request['xe_color'];
		$qty = $this->_request['orderQty'];
		try{
			$sql="SELECT ref_id FROM ".TABLE_PREFIX."template_state_rel WHERE 
				temp_id=".$confProductId;
			$rows = $this->executeFetchAssocQuery($sql);
		}catch(Exception $e){
			$msg = array('Caught exception:'=>$e->getMessage());
			return $msg;
		}
		$refId = $rows[0]['ref_id'];
		$cartStoreObj = Flight::carts();
		return $result = $cartStoreObj->addTemplateToCart($confProductId,$xe_size,$xe_color,$qty,$refId);
	}

	/**
	*
    *date created 21-06-2016(dd-mm-yy)
	*date modified (dd-mm-yy)
    *Get ref id by template id
    *
    * 
    */
	public function getRefId(){
		$templateId = $this->_request['pid'];
		try{
			$sql="SELECT ref_id FROM ".TABLE_PREFIX."template_state_rel WHERE 
			temp_id=".$templateId;
			$rows = $this->executeFetchAssocQuery($sql);
			$result = $rows[0]['ref_id'];
		}catch(Exception $e){
			$msg = array('Caught exception:'=>$e->getMessage());
			$this->response($msg, 200);
		}
		if(empty($result)){
			$result = 0;
		}
		$this->response($result, 200);
	}
}