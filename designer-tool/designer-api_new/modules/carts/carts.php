<?php
/* Check Un-authorize Access */
if (!defined('accessUser')) {
    die("Error");
}

class Carts extends CartsStore
{
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Save design state cart data
     *
     *@param (String)apikey
     *@param (Array)jsonData
     *@param (Int)refid
     *@return Integer value
     *
     */
    public function saveDesignStateCart($apiKey, $refid, $jsonData)
    {
        $newArray = array();
        $previewImageIds = (isset($this->_request['previewImageId']) && $this->_request['previewImageId'] != '' && $this->_request['previewImageId'] != 'undefined') ? $this->_request['previewImageId'] : $newArray;
        $previewImageIds = implode(',', $previewImageIds);
        try {
            if ($refid == 0) {
                $sql = "INSERT INTO " . TABLE_PREFIX . "design_state (json_data,  date_created) VALUES ('', now())";
                $refid = $this->executeGenericInsertQuery($sql);
            } else {
                $sql = "update " . TABLE_PREFIX . "design_state set json_data='' where id=" . $refid;
                $status = $this->executeGenericDMLQuery($sql);
            }
            if(!empty($previewImageIds)){
                $sql = "UPDATE " . TABLE_PREFIX . "capture_image SET state_id='" . $refid . "' WHERE id IN (" . $previewImageIds . ")";
                $status = $this->executeGenericDMLQuery($sql);
            }
            $fileName = 'designState.json';
            $baseImagePath = $this->getPreviewImagePath();
            $savePath = $baseImagePath . $refid . '/';
            $stateDesignPath = $savePath . 'svg/';
            if (!file_exists($stateDesignPath)) {
                mkdir($stateDesignPath, 0777, true);
            }
            $stateDesignPath = $stateDesignPath . $fileName;
            $svgFileStatus = file_put_contents($stateDesignPath, $jsonData);
            if ($svgFileStatus) {
                return $refid;
            } else {
                return -1;
            }

        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            return $result;
        }
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Save Product Preview svg image data when add to cart
     *
     *@param (String)apikey
     *@param (Int)refid
     *@param (Array)jsonData
     *@param (Boolean)saveProductImage
     *@return json data
     *
     */
    public function saveProductPreviewSvgImagesOnAddToCart($apiKey, $refid, $jsonData, $saveProductImage = true)
    {
        if ($this->isValidCall($apiKey) && $jsonData) {
            $dbstat = "";
            $fileList = array();
            $dataArray = array();
            $baseImagePath = $this->getPreviewImagePath();
            $savePath = $baseImagePath . $refid . '/';
            $dataArray = (is_array($jsonData)) ? $jsonData : $this->formatJSONToArray($jsonData, false);
            $previewImageData = $dataArray->sides;
            if (!empty($previewImageData)) {
                $printid = $dataArray->printTypeId;
                foreach ($previewImageData as $side => $imageData) {
                    $sideValue = $side + 1;
                    try {
                        $productURL = $imageData->url;
                        $svgData = $imageData->svg;
                        $design_status = '0';
                        if ($svgData) {
                            $xmlDoc = new DOMDocument();
                            $xmlDoc->loadXML($svgData);

                            $searchNode = $xmlDoc->getElementsByTagName('g');
                            foreach ($searchNode as $search) {
                                if ($search->getAttribute('id') == 'layer_stage') {
                                    foreach ($search->childNodes as $item) {
                                        if ($item->nodeName == 'g' && $item->hasAttribute('xe_p')) {
                                            $design_status = 1;
                                        }

                                    }
                                }
                            }
                        }
                        $productImgContent = file_get_contents($productURL);
                        $base64ProductImgData = base64_encode($productImgContent);
                        $svgPreviewDatas = $this->parsePrintSVG($svgData);
						if(isset($svgPreviewDatas['url']) && !empty($svgPreviewDatas['url'])){
							foreach ($svgPreviewDatas['url'] as $key => $value) {
								if (!empty($value)) {
									$info = pathinfo($imgData['xlink:href']);
									$fileNames = basename($value);  
									$userImageData = file_get_contents($value);
									$extension = $info['extension'];
									$userImagepath = $savePath . 'assets/'.$sideValue.'/';
									$userImageFileName = $userImagepath .$fileNames;
									if (!file_exists($userImagepath)) {
										mkdir($userImagepath, 0777, true);
									}
									$svgFileStatus = file_put_contents($userImageFileName, $userImageData);
								}
							}
						}
                       
                        $svgPreviewData = str_ireplace(array('data: png', 'data: jpg', '<svg', '</svg>'), array('data:image/png', 'data:image/jpg', '<g', '</g>'), $svgPreviewDatas['svgStringwithImageURL']);
                        $html = new simple_html_dom();
                        $html->load($svgPreviewData, false);
                        preg_match_all('/(https?:\/\/\S+\.(?:svg))/', $svgPreviewData, $svgMatch);
                        if (!empty($svgMatch) && !empty($svgMatch[0])) {
                            $imageXlink = $this->getUserImageURL();
                            $imageXlink = str_replace('/userimg','',$imageXlink);
                            $imageXlink = str_replace('//','/',$imageXlink);
                            $imageXlink = $imageXlink[0];
                            $main = $html->find('image[xlink:href^=' . $imageXlink . ']');

                            $res = '';
                            $ImgX = '';
                            foreach ($main as $k => $v) {
                                $imgData = $main[$k]->attr;
                                $Imgwidth = $imgData['width'];
                                $Imgheight = $imgData['height'];
                                $ImgX = $imgData['x'];
                                $ImgY = $imgData['y'];
                                $id[$k] = $imgData['id'];
                                if (strpos( $imgData['xlink:href'], "userimg")!==false){
                                    $info = pathinfo($imgData['xlink:href']);
                                    $fileNames = basename($imgData['xlink:href']);  
                                    $userImageData = file_get_contents($imgData['xlink:href']);
                                    $extension = $info['extension'];
                                    $userImagepath = $savePath . 'assets/'.$sideValue.'/';
                                    $userImageFileName = $userImagepath .$fileNames;
                                    if (!file_exists($userImagepath)) {
                                        mkdir($userImagepath, 0777, true);
                                    }
                                    $svgFileStatus = file_put_contents($userImageFileName, $userImageData);
                                }
                                $fileContent = file_get_contents($imgData['xlink:href']);
                                $html1 = new simple_html_dom();
                                $html1->load($fileContent);
                                $viewBox = $html1->find('svg[viewBox]');
                                if (!empty($viewBox)) {
                                    $viewBox = $viewBox[0]->attr;
                                    $viewBox = $viewBox['viewbox'];
                                    $viewBox = explode(' ', $viewBox);
                                    $vBwidth = $viewBox[2];
                                    $vBheight = $viewBox[3];
                                    if (strpos( $imgData['xlink:href'], "userimg")!==false){
										$height = $width = $Imgwidth / $vBwidth;
									}else{
										$width = $Imgwidth / $vBwidth;
										$height = $Imgheight / $vBheight;
									}
                                } else {
                                    $width = $Imgwidth;
                                    $height = $Imgheight;
                                }

                                $rstr = stripos($fileContent, '<svg ');
                                $fileContent = substr($fileContent, $rstr);

                                preg_match_all('/id="([^"]+)"/', $fileContent, $idMatch);
                                if (!empty($idMatch)) {
                                    $idMatchArr[$k] = $idMatch[1];
                                }
                                foreach ($idMatchArr[$k] as $key => $idVal) {
                                    $fileContent = str_replace($idVal, uniqid($k . '_xe_', true), $fileContent);
                                }
								if (strpos($imgData['xlink:href'], "userimg")!==false){
									preg_match_all('/style="([^"]+)"/', $fileContent, $styleMatch);
									if (!empty($styleMatch)) {
										$styleMatch[$k] = $styleMatch[1];
									}
									foreach ($styleMatch[$k] as $k1 => $vStyle) {
										if (strpos($vStyle, 'display: none;') !== false) {
											$fileContent = str_replace('display: none;','display: block;' , $fileContent);
										}
									}
								}
                                $fileContent = str_ireplace(array('<svg', '/svg>'), array('<g', '/g>'), $fileContent);
                                $res = '<g  transform="translate(' . $ImgX . ', ' . $ImgY . ') scale(' . $width . ', ' . $height . ')">' . $fileContent . '</g>';
                                $html2 = new simple_html_dom();
                                $html2->load($res, false);
                                if ($html->getElementById($id[$k])) {
                                    $html->getElementById($id[$k])->outertext = $html2;
                                }

                            }
                            //if(!$saveProductImage){
                            //    if($html->getElementById('svg_1'))$html->getElementById('svg_1')->outertext = '';
                            //}
                            $html->save();
                            $svgPreviewData = $html;
                        }
                        if ($saveProductImage) {
                            $productPreviewData = "<svg xmlns='http://www.w3.org/2000/svg' id='svgroot' xlinkns='http://www.w3.org/1999/xlink' width='500' height='500' x='0' y='0' overflow='visible'><image x='0' y='0' width='500' height='500' id='svg_1' xmlns:xlink='http://www.w3.org/1999/xlink' xlink:href='data:image/png;base64," . $base64ProductImgData . "'></image>" . $svgPreviewData . "</svg>";
                        } else {
                            $productPreviewData = "<svg xmlns='http://www.w3.org/2000/svg' id='svgroot' xlinkns='http://www.w3.org/1999/xlink' width='500' height='500' x='0' y='0' overflow='visible'>" . $svgPreviewData . "</svg>";
                        }

                        $ext = 'svg';
                        
                        $fileName = "preview_0" . $sideValue . '.' . $ext;
                        $savePath = $baseImagePath . $refid . '/';
                        $baseImageURL = $this->getPreviewImageURL();
                        $imageURL = $baseImageURL . $refid . '/';
                        $svgSavePath = $savePath . 'svg/';
                        if (!file_exists($svgSavePath)) {
                            mkdir($svgSavePath, 0777, true);
                        }
                        $svgFilePath = $svgSavePath . $fileName;
                        $svgFileStatus = file_put_contents($svgFilePath, $productPreviewData);

                        if ($svgFileStatus) {
                            $sql = "INSERT INTO " . TABLE_PREFIX . "preview_image_data (refid, side, svg, product_url, type, image_generated, date_created, date_modified,print_id, design_status) VALUES ($refid, '$side', '$fileName', '$productURL', '$ext', 0, now(), now(),'$printid','$design_status')";
                            $status = $this->executeGenericDMLQuery($sql);
                            if ($status) {
                                $dbstat = "insertSuccess";
                            } else {
                                $dbstat = "insertFailed :" . $sql;
                            }
                        } else {
                            $dbstat = "writeFailed";
                        }
                    } catch (Exception $e) {
                        $this->log('Exception:' . $e->getMessage(), true, 'logc.log');
                    }
                }
            }
            return $dbstat;
        } else {
            $this->log('refid=' . $refid . ' INVALID API KEY=' . $apiKey, true, 'logc.log');
            return "invalid";
        }
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Pars Print svg file
     *
     *@param (String)apikey
     *@param (String)svgStringwithImageURL
     *@return json data
     *
     */
    public function parsePrintSVG($svgStringwithImageURL)
    {
        $svgStringWithBase64 = '';
        try {
            $userimage = array();
            preg_match_all('/(https?:\/\/\S+\.(?:jpg|png|gif|jpeg|bmp))/', $svgStringwithImageURL, $match);
            for ($i = 0; $i < count($match[0]); ++$i) {
                $b64image = "";
                $b64image = base64_encode(file_get_contents($match[0][$i]));
                if (strpos( $match[0][$i], "userimg")!==false){
                    $userimage['url'][] = $match[0][$i];
                }
                $info = pathinfo($match[0][$i]);
                $ext = $info['extension'];
                $src = 'data: ' . $ext . ';base64,';
                $svgStringwithImageURL = str_ireplace($match[0][$i], $src . $b64image, $svgStringwithImageURL);
            }
            $userimage['svgStringwithImageURL'] = $svgStringwithImageURL;
            return $userimage;
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Save design data
     *
     *@param (String)apikey
     *@param (Array)designData
     *@param (Int)apisessId
     *@param (Int)refid
     *@return json value
     *
     */
    public function saveDesignData()
    {
        try {
            $error = false;
            $result = $this->storeApiLogin();
            if ($this->storeApiLogin == true) {
                $key = $GLOBALS['params']['apisessId'];
                $apikey = $this->_request['apikey']; //'A610^Gx{!%3D3l%23%23i*905Q';
                $refid = $this->_request['refid']; //0
                $designData = $this->_request['designData']; //$this->designData;
                $refid = $this->saveDesignStateCart($apikey, $refid, $designData);
                if ($refid > 0) {
                    $dbstat = $this->saveProductPreviewSvgImagesOnAddToCart($apikey, $refid, $designData);
                }

                $designDataArr = $this->formatJSONToArray($designData);
                $msg = array('status' => 'success', 'refid' => $refid, 'confId' => $designDataArr['productInfo']['productId']);

            } else {
                $msg = array('status' => 'apiLoginFailed', 'error' => $this->formatJSONToArray($result));
            }
            $this->response($this->json($msg), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Save Preview cart image
     *
     *@param (String)apikey
     *@param (Int)refid
     *@param (Array)jsonData
     *@param (Boolean)saveProductImage
     *@return json data
     *
     */
    public function previewCartImage()
    {
        try {
            $apiKey = $this->_request['apikey'];
            $refid = $this->_request['refid'];
            $jsonData = $this->_request['jsonData'];
            $this->savePreviewImagesOnAddToCart($apiKey, $refid, $jsonData);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Save preview Svg image data when add to cart
     *
     *@param (String)apikey
     *@param (Int)refid
     *@param (Array)jsonData
     *@param (Array)previewImgData
     *@return json data
     *
     */
    public function savePreviewSvgImagesOnAddToCart($apiKey, $refid, $jsonData)
    {
        if ($this->isValidCall($apiKey) && $jsonData) {
            $dbstat = "";
            $fileList = array();
            $dataArray = array();
            if (is_array($jsonData)) {
                $dataArray = $jsonData;
            } else {
                $dataArray = $this->formatJSONToArray($jsonData);
            }

            foreach ($dataArray as $side => $imageData) {
                $this->log($side, true, 'logc.log');
                try {
                    $productURL = $imageData["productURL"];
                    $svgData = $imageData["svgData"];

                    $ext = 'svg';
                    $sideValue = substr($side, 4);
                    $fileName = $sideValue . '.' . $ext;

                    $baseImagePath = $this->getPreviewImagePath();
                    $savePath = $baseImagePath . $refid . '/';
                    $baseImageURL = $this->getPreviewImageURL();
                    $imageURL = $baseImageURL . $refid . '/';

                    $svgSavePath = $savePath . 'svg/';
                    if (!file_exists($svgSavePath)) {
                        mkdir($svgSavePath, 0777, true);
                        chmod($svgSavePath, 0777);
                    }
                    $svgFilePath = $svgSavePath . $fileName;
                    $svgFileStatus = file_put_contents($svgFilePath, $svgData);
                    $this->log($side . ' :: svgFileStatus' . $svgFileStatus, true, 'logc.log');

                    if ($svgFileStatus) {
                        $sql = "INSERT INTO " . TABLE_PREFIX . "preview_image_data (refid, side, svg, product_url, type, image_generated, date_created, date_modified) VALUES ($refid, '$side', '$fileName', '$productURL', '$ext', 0, now(), now())";
                        $status = $this->executeGenericDMLQuery($sql);
                        if ($status) {
                            $dbstat = "insertSuccess";
                        } else {
                            $this->log('insertFailed :: ' . $sql, true, 'logc.log');
                            $dbstat = "insertFailed :" . $sql;
                        }
                    } else {
                        $this->log($side . ' :: write Failed :: svgFilePath:' . $svgFilePath, true, 'logc.log');
                        $dbstat = "writeFailed";
                    }
                } catch (Exception $e) {
                    $this->log('Exception:' . $e->getMessage(), true, 'logc.log');
                }
            }
            return $dbstat;
        } else {
            $this->log('refid=' . $refid . ' INVALID API KEY=' . $apiKey, true, 'logc.log');
            return "invalid";
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Save preview image data when add to cart
     *
     *@param (String)apikey
     *@param (Int)refid
     *@param (Array)jsonData
     *@param (Array)previewImgData
     *@return json data
     *
     */
    public function savePreviewImagesOnAddToCart($apiKey, $refid, $jsonData)
    {
        if ($this->isValidCall($apiKey) && $jsonData) {
            $dbstat = "";
            $fileList = array();
            $dataArray = array();
            if (is_array($jsonData)) {
                $dataArray = $jsonData;
            } else {
                $dataArray = $this->formatJSONToArray($jsonData);
            }

            foreach ($dataArray as $side => $imageData) {
                $this->log($side, true, 'logc.log');
                try {
                    $productURL = $imageData["productURL"];
                    $svgData = $imageData["svgData"];

                    $qpSVG = htmlqp("svg.html");
                    if ($qpSVG) {
                        $qpSVG->find('div#capture_product')->html($svgData)->writeHTML('svg.html');
                    } else {
                        $this->log(" savePreviewImagesOnAddToCart :: Invalid QP-SVG html:");
                    }

                    $qp = htmlqp("image.html");
                    if ($qp) {
                        $content = '<img src="' . $productURL . '"/>' . $svgData;
                        $qp->find('div#capture_product')->html($content)->writeHTML('image.html');
                    } else {
                        $this->log(" savePreviewImagesOnAddToCart :: Invalid QP html:");
                    }

                    $sql0 = "Select refid from  " . TABLE_PREFIX . "preview_image_data where refid=" . $refid . " and side='$side'";
                    $result0 = $this->executeFetchAssocQuery($sql0);

                    $ext = 'png';
                    $sideValue = substr($side, 4);
                    $fileName = $sideValue . '.' . $ext;
                    $baseImagePath = $this->getPreviewImagePath();
                    $savePath = $baseImagePath . $refid . '/';
                    $baseImageURL = $this->getPreviewImageURL();
                    $imageURL = $baseImageURL . $refid . '/';
                    $designSavePath = $savePath . 'design/';
                    if (!file_exists($savePath)) {
                        mkdir($savePath, 0777, true);
                        chmod($savePath, 0777);
                    }
                    if (!file_exists($designSavePath)) {
                        mkdir($designSavePath, 0777, true);
                        chmod($designSavePath, 0777);
                    }
                    $filePath = $savePath . $fileName;
                    $designFilePath = $designSavePath . $fileName;
                    //echo 'filePath:'.$filePath;

                    //echo exec('phantomjs webCapture.js');
                    //echo exec('phantomjs webCapture.js '.$filePath);
                    $phantomPath = dirname(__FILE__) . '/';

                    $this->log($side . ' :: before saveDesignImage :: time=' . time(), true, 'logc.log');
                    $designFileStatus = exec($phantomPath . 'phantomjsdesign webCaptureDesign.js svg.html ' . $designFilePath);
                    $this->log($side . ' :: after SaveDesignImage :: time=' . time(), true, 'logc.log');

                    $this->log($side . ' :: before saveImage :: time=' . time(), true, 'logc.log');
                    $filestatus = exec($phantomPath . 'phantomjs webCapture.js image.html ' . $filePath);
                    $this->log($side . ' :: after saveImage :: time=' . time(), true, 'logc.log');

                    $this->log($side . ' :: filestatus' . $filestatus, true, 'logc.log');
                    $this->log($side . ' :: designFileStatus' . $designFileStatus, true, 'logc.log');

                    if ($filestatus == 'success') {
                        if (!empty($result0)) {
                            $sql = "UPDATE " . TABLE_PREFIX . "preview_image_data set image='$fileName', type='$ext', date_created=now() where refid=" . $refid . " and side='$side'";
                            $id = $this->executeGenericInsertQuery($sql);
                            if ($id) {
                                $dbstat = "updateSuccess";
                            } else {
                                $dbstat = "updateFailed";
                            }
                        } else {
                            $sql = "INSERT INTO " . TABLE_PREFIX . "preview_image_data (refid, side, image, type, date_created) VALUES ($refid, '$side', '$fileName','$ext', now())";
                            $status = $this->executeGenericInsertQuery($sql);
                            if ($id) {
                                $dbstat = "insertSuccess";
                            } else {
                                $dbstat = "insertFailed :" . $sql;
                            }
                        }
                    } else {
                        $dbstat = "writeFailed";
                    }
                    if (isset($this->_request['singleSide']) && $this->_request['singleSide'] == true) //for Social share only single side is required
                    {
                        break;
                    }

                } catch (Exception $e) {
                    //echo 'Caught exception: ', $e->getMessage(), "\n";
                    $this->log('Exception:' . $e->getMessage(), true, 'logc.log');
                }
            } //END: foreach
            return $dbstat;
        } else {
            $this->log('refid=' . $refid . ' INVALID API KEY=' . $apiKey, true, 'logc.log');
            return "invalid";
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Get preview image Array
     *
     *@param (String)apikey
     *@param (Int)refid
     *@return string
     *
     */
    private function getPreviewImageArray($refid)
    {
        $imageList = array();
        $basePath = $this->getPreviewImagePath();
        $baseImagePath = $basePath . $refid . '/';
        try {
            $sql = "Select image,side,product_url from  " . TABLE_PREFIX . "preview_image_data where refid=" . $refid . " order by side";
            //$this->log($sql);
            $result = $this->executeFetchAssocQuery($sql);
            if (!empty($result)) {
                foreach ($result as $rows) {
                    $image = $rows['image'];
                    $side = $rows['side'];
                    $productUrl = $rows['product_url'];
                    $imagePath = $baseImagePath . $image;
                    $designImagePath = $baseImagePath . 'design/' . $image;
                    $imageList[] = array("side" => $side, "image" => $imagePath, "design" => $designImagePath, "productUrl" => $productUrl);
                }
            }
            return $imageList;
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }
	/**
     *
     *date created 9-2-2017(dd-mm-yy)
     *date modified (dd-mm-yy)
     *Get name and number list by refid
     *
     *@param (Int)pid
     *@param (Int)refId
     *@return json array
     *
     */
    public function getNameAndNumberByRefId(){
        $refId = $this->_request['refId'];
        $pid = $this->_request['pid'];
		$attValue = $this->_request['attValue'];
        $fileName ='designState.json';
        if(isset($refId) && (isset($pid) || isset($attValue))){
            $baseImagePath = $this->getPreviewImagePath();
            $savePath = $baseImagePath.$refId.'/';
            $stateDesignPath = $savePath.'svg/';
            $stateDesignPath = $stateDesignPath.$fileName;
            if (file_exists($stateDesignPath)){
				$jsonData = $this->formatJSONToArray(file_get_contents($stateDesignPath),true);
            }
            $result= array();
            $i = 0;
            foreach ($jsonData['nameNumberData']['list'] as $key => $value) {
                if($pid == $value['pid'] || $attValue == $value['size']){
					$result[$i]['size'] = $value['size'];
					if($jsonData['nameNumberData']['front']){
						if ($jsonData['nameNumberData']['frontView'] == 'name_num') {
							$result[$i]['front']['name'] = $value['name'];
							$result[$i]['front']['number'] = $value['number'];
						}
						if ($jsonData['nameNumberData']['frontView'] == 'name') {
							 $result[$i]['front']['name'] = $value['name'];
							 $result[$i]['front']['number'] = '_';
						}
						if ($jsonData['nameNumberData']['frontView'] == 'num') {
							$result[$i]['front']['number'] = $value['number'];
							$result[$i]['front']['name'] = '_';
						}
					}else{
						$result[$i]['front']['number'] ='_';
						$result[$i]['front']['name'] = '_';
					}
					if($jsonData['nameNumberData']['back']){
						if ($jsonData['nameNumberData']['backView'] == 'name_num') {
							$result[$i]['back']['name'] = $value['name'];
							$result[$i]['back']['number'] = $value['number'];
						}
						if ($jsonData['nameNumberData']['backView'] == 'name') {
							$result[$i]['back']['name'] = $value['name'];
							$result[$i]['back']['number'] = '_';
						}
						if ($jsonData['nameNumberData']['backView'] == 'num') {
							$result[$i]['back']['number'] = $value['number'];
							$result[$i]['back']['name'] = '_';
						}
					}else{
						$result[$i]['back']['number'] ='_';
						$result[$i]['back']['name'] = '_';
					}
                    $i++;
                }
            }
            $finalResult['nameNumberData'] =array_values($result);
        }else{
            $finalResult['nameNumberData'] ='no refId';
        }
        $this->response($this->json($finalResult), 200);
    }

}
