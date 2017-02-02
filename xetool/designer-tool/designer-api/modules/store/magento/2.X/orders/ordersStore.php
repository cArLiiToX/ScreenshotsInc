<?php
/* Check Un-authorize Access */
if (!defined('accessUser')) {
    die("Error");
}

class OrdersStore extends UTIL
{

    ####################################################
    ############## syncOrdersZip ###################
    ####################################################
    public function getOrders()
    {
        $error = false;

        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];

            $lastOrderId = (isset($this->_request['lastOrderId']) && trim($this->_request['lastOrderId']) != '') ? trim($this->_request['lastOrderId']) : 0;
            $range = (isset($this->_request['range']) && trim($this->_request['range']) != '') ? trim($this->_request['range']) : 0;
            $fromDate = (isset($this->_request['fromDate']) && trim($this->_request['fromDate']) != '') ? trim($this->_request['fromDate']) : '';
            $toDate = (isset($this->_request['toDate']) && trim($this->_request['toDate']) != '') ? trim($this->_request['toDate']) : '';

            try {
                $filters = array('lastOrderId' => $lastOrderId, 'store' => $this->getDefaultStoreId(), 'range' => $range, 'fromDate' => $fromDate, 'toDate' => $toDate);
                //$result = $this->proxy->call($key, 'cedapi_cart.getOrders',$filters);
                $result = $this->apiCall('Cart', 'getOrders', $filters);
                $result = $result->result;

                if ($result) {
                    $result = json_decode($result, true);
                    foreach ($result['order_list'] as $k => $order) {
                        $select_sql = 'SELECT order_status FROM ' . TABLE_PREFIX . 'sync_order  WHERE orderId="' . $order['order_incremental_id'] . '"';
                        $rows = $this->executeGenericDQLQuery($select_sql);
                        $order['print_status'] = $rows[0]['order_status'];
                        $result['order_list'][$k] = $order;
                    }
                }
            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $error = true;
            }

            if (!$error) {
                print_r(json_encode($result));
                // $this->response($this->json($result), 200);
            } else {
                print_r(json_decode($result));
                // $this->response($this->json($result), 200);
            }exit();
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
            $this->response($this->json($msg), 200);
        }
    }

    ####################################################
    ############## syncOrdersZip ###################
    ####################################################
    public function getOrdersGraph()
    {
        $error = false;
        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];

            $from = (isset($this->_request['from']) && trim($this->_request['from']) != '') ? trim($this->_request['from']) : '';
            $to = (isset($this->_request['to']) && trim($this->_request['to']) != '') ? trim($this->_request['to']) : '';

            try {
                $filters = array('from' => $from, 'to' => $to, 'store' => $this->getDefaultStoreId());
                //$result = $this->proxy->call($key, 'cedapi_cart.getOrdersGraph',$filters);
                $result = $this->apiCall('Cart', 'getOrdersGraph', $filters);
                $result = $result->result;

            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $error = true;
            }
            if (!$error) {
                //print_r($result);
                $this->response($result, 200);
            } else {
                //print_r(json_decode($result));
                $this->response($result, 200);
            }
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
            $this->response($this->json($msg), 200);
        }
    }

    ####################################################
    ############## syncOrdersZip ###################
    ####################################################
    public function getOrderDetails($return = 0)
    {
        $error = false;
        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];

            $orderId = 0;
            if (isset($this->_request['orderId']) && trim($this->_request['orderId']) != '') {
                $orderId = trim($this->_request['orderId']);
            }

            try {
                $filters = array('orderId' => $orderId, 'store' => $this->getDefaultStoreId());
                $result = $this->apiCall('Cart', 'getOrderDetails', $filters);
                $result = $result->result;
                $result = json_decode($result);
            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $error = true;
            }
            if (!$error) {
                if ($return) {
                    return json_encode($result);
                } else {
                    $this->response(json_encode($result), 200);
                }

            } else {
                //print_r(json_decode($result));
                $this->response($result, 200);
            }
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
            $this->response($this->json($msg), 200);
        }
    }

    /**
     *
     *date 15th_Apr-2016
     *download zip containing order files for Order App
     *
     * @param (int)order_id
     * @param (int)range
     * @param (int)fd
     * @param (int)download
     * @return zip file link or zip download
     *
     */
    public function downloadOrdersZipApp()
    {
	    ini_set('memory_limit','18000M');
        $downloadUrl = '';
        $msg = "Success";
        $last_order_id = (isset($this->_request['last_order_id'])) ? $this->_request['last_order_id'] : 0;
        $range = (isset($this->_request['range'])) ? $this->_request['range'] : 20;
        $fd = (isset($this->_request['fd'])) ? $this->_request['fd'] : 0; // force Download flag//
        $download = (isset($this->_request['download'])) ? $this->_request['download'] : '';
        $is_create = (isset($this->_request['is_create'])) ? $this->_request['is_create'] : 0;

        // get Orders id from Store (returns an array with all selected orders) //
        //$orders = $this->orderIdFromStore($last_order_id,$range);

        $error = false;
        $orders = array();
        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];
            $lastOrderId = $last_order_id;
            try {
                $filters = array('lastOrderId' => $lastOrderId, 'range' => $range, 'store' => $this->getDefaultStoreId());
                $orderList = $this->proxy->call($key, 'cedapi_cart.orderIdFromStore', $filters);

            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
                $this->response($this->json($msg), 200);
            }
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
            $this->response($this->json($msg), 200);
        }
		
		$orders = json_decode($orderList, true);
        $orders = isset($orders['order_list']) ? $orders['order_list'] : array();
        $orders2 = $orders;
        $orderPath = $this->getOrdersPath();
        if (is_array($orders2) && count($orders2) > 0) {
            if ($fd == 1 || $is_create == 1) {
                // if $is_create is 1 or $fd (force download) is 1 //
                foreach ($orders2 as $order_id_arry) {
					$order_id = $order_id_arry['order_id'];
					$increment_id = $order_id_arry['order_incremental_id'];
					
					// fetch order_id or incremental_id //
					if (file_exists($orderPath . "/" . $increment_id) && is_dir($orderPath . "/" . $increment_id)) { 
						$orderFolderPath = $orderPath . "/" . $increment_id; // increment_id //
						$orderTypeFlag = 1;
						$order_id = $increment_id;
					}else{
						$orderFolderPath = $orderPath . "/" . $order_id;
						$orderTypeFlag = 0;
					}
                    
                    if (file_exists($orderFolderPath) && is_dir($orderFolderPath)) {
                        $scanProductDir = scandir($orderFolderPath); // scan directory to fetch all items folder //
						if (file_exists($orderFolderPath . '/order.json')) {
                                $order_json = file_get_contents($orderFolderPath . '/order.json');
                                $json_content = $this->formatJSONToArray($order_json);
                                foreach ($json_content['order_details']['order_items'] as $item_details) {
                                    $item_id = $item_details['item_id'];
                                    $sizeArr[$item_id] = $item_details['xe_size'];
                                }
						}
						
                        if (is_array($scanProductDir)) {
                            foreach ($scanProductDir as $itemDir) {
                                if ($itemDir != '.' && $itemDir != '..' && is_dir($orderFolderPath . "/" . $itemDir)){ // check the item folders under the product folder //
									//Fetch the design state json details //
									$designState = file_get_contents(XEPATH . "designer-tool" . ORDER_PATH_DIR . "/" . $order_id . "/" . $itemDir . "/designState.json");
									$resultDesign = $this->formatJSONToArray($designState);
									
									// check if side_index folder exists or not //
									$sidePath = $orderFolderPath . "/" . $itemDir;
									$scanSidePath = scandir($sidePath);
									$scanSideDir = $scanSidePath;
									$orderTypeFlag = 0;
									if(is_array($scanSideDir)){
										foreach($scanSideDir as $sidecheckPath){
											if(strpos($sidecheckPath, "side_") !== false){
												$orderTypeFlag = 1;
												continue;
											}
										}
									}
									
									// for new file structure //
									if($orderTypeFlag == 1){
								        //check and find the sides of each item //
										$sidePath = $orderFolderPath . "/" . $itemDir; 
										if (file_exists($sidePath) && is_dir($sidePath)){
									        $scanSideDir = scandir($sidePath); // scan item directory to fetch all side folders //
											$scanSideDirSide = $scanSideDir;
												if(is_array($scanSideDir)){
												    foreach ($scanSideDir as $sideDir){
													    if($sideDir != '.' && $sideDir != '..' && is_dir($orderFolderPath . "/" . $itemDir. "/". $sideDir)){
															$i = str_replace("side_","",$sideDir);  //to fetch only side folders//
															if (file_exists($orderFolderPath."/".$itemDir."/".$sideDir."/preview_0".$i.".svg")){
																// with product svg file exists or not//
																if ($fd == 1){
																	$reqSvgFile = XEPATH."designer-tool".ORDER_PATH_DIR."/".$order_id."/".$itemDir."/".$sideDir."/preview_0".$i.".svg";
																	$item_id = $itemDir;
																	//check name and number exists or not
													
																	if (!empty($resultDesign['nameNumberData'])) {
																		$this->creteNameAndNumberSeparatedSvg($reqSvgFile,$resultDesign, $order_id, $item_id, $sizeArr[$item_id],1);
																	}else{
																		$this->createWithoutProductSvg($reqSvgFile, $order_id, $item_id, $resultDesign, 1);
																	}
																	$msg = 'Success';
																}else{
																	if (!file_exists($orderFolderPath . "/" . $itemDir . "/" . $sideDir. "/". $sideDir."_".$itemDir."_".$order_id . ".svg") && $is_create == 1){
																	/* check if without product svg file exists or not.if not exist, then create the file */
																	$reqSvgFile = XEPATH."designer-tool".ORDER_PATH_DIR."/".$order_id."/".$itemDir."/".$sideDir."/preview_0".$i.".svg";
																	$item_id = $itemDir;
																	//check name and number exit or not
													
																	if (!empty($resultDesign['nameNumberData'])) {
																		$this->creteNameAndNumberSeparatedSvg($reqSvgFile,$resultDesign, $order_id, $item_id, $sizeArr[$item_id],1);
																	}else{
																		$this->createWithoutProductSvg($reqSvgFile, $order_id, $item_id, $resultDesign, 1);
																	}
																	
																	$msg = 'Success';
																	}
																}
															}
														}
													}
												}
											}
										}
										// for old file structure //
										else if($orderTypeFlag == 0){
											//to fetch only item id folders //
											$kounter = 1;
											for($i=1;$i<=15;$i++){
												if(file_exists($orderFolderPath."/".$itemDir."/preview_0".$i.".svg")){// with product svg file exists or not//	
												    if($fd == 1){
													    $reqSvgFile = XEPATH."designer-tool".ORDER_PATH_DIR."/".$order_id."/".$itemDir."/preview_0".$i.".svg";
														$item_id = $itemDir;
														//check name and number exit or not
														$designState = file_get_contents(XEPATH."designer-tool".ORDER_PATH_DIR."/".$order_id."/".$item_id."/designState.json");
														$resultDesign = $this->formatJSONToArray($designState);
														if(!empty($resultDesign['nameNumberData'])){
															$this->creteNameAndNumberSeparatedSvg($reqSvgFile,$resultDesign,$item_id,$order_id,$sizeArr[$item_id],0);
														}else{
															$this->createWithoutProductSvg($reqSvgFile, $order_id, $item_id, $resultDesign, 0);
														}
														$msg = 'Success';
													 }
													 else{
														 if(!file_exists($orderFolderPath."/".$itemDir."/".$i.".svg") && $is_create == 1){ 
															/* check if without product svg file exists or not. if not exist, then create the file */
															$reqSvgFile = XEPATH."designer-tool".ORDER_PATH_DIR."/".$order_id."/".$itemDir."/preview_0".$i.".svg";
															$item_id = $itemDir;
															//check name and number exit or not
															$designState = file_get_contents(XEPATH."designer-tool".ORDER_PATH_DIR."/".$order_id."/".$item_id."/designState.json");
															$resultDesign = $this->formatJSONToArray($designState);
															if(!empty($resultDesign['nameNumberData'])){
																$this->creteNameAndNumberSeparatedSvg($reqSvgFile,$resultDesign,$item_id,$order_id,$sizeArr[$item_id],0);
															}else{
																$this->createWithoutProductSvg($reqSvgFile, $order_id, $item_id, $resultDesign, 0);
															}
															$msg = 'Success';
														}
													 }
												 }
											}	
										}
									}
                                }
                            }
                        }
                    }
                }
            //exit;
            $orderFolderPath = $this->getOrdersPath();
            $zipName = 'orders_' . time() . '.zip';
            $zip = new \ZipArchive;
            $res = $zip->open($orderFolderPath . '/' . $zipName, ZipArchive::CREATE);
            // check if zip file created //
            if ($res === true) {
                $zipCheckKounter = 0;
                $tarray = array(" ", "\n", "\r");
                if (file_exists($this->getBasePath() . "localsettings.js")) {
                    $contents = file_get_contents($this->getBasePath() . "/localsettings.js");
                    $contents = trim(str_replace($tarray, "", $contents));
                    $contents = substr($contents, 0, -1);
                    $contents = explode("localSettings=", $contents);
                    $contents = json_decode($contents['1']);
                    $temp_array = array();
                    $store_url = $settings_arry['base_url'];
                    $temp_array['base_url'] = $contents->base_url . $contents->service_api_url;
                    $temp_array['api_key'] = $contents->api_key;
                    $new_json = json_encode($temp_array);
                    $zip->addFromString('settings.json', $new_json);
                }
                $order_list_arr = array();
                $temp_arry212 = array();
                // loop all order ids //
                foreach ($orders as $order_id_arry2) {
                    $orderNo = $order_id_arry2['order_id'];
                    $order_incremental_id = $order_id_arry2['order_incremental_id'];
					
					$orderFolderPath = $this->getOrdersPath();
					// fetch order_id or incremental_id //
					if (file_exists($orderFolderPath . "/" . $order_incremental_id) && is_dir($orderPath . "/" . $order_incremental_id)) { 
						$orderTypeFlag = 1;
						$orderNo = $order_incremental_id;
					}else{
						$orderTypeFlag = 0;
					}
					
                    if (file_exists($orderFolderPath . "/" . $orderNo)) {
                        array_push($order_list_arr, array("order_id" => $order_id_arry2['order_id'], "order_incremental_id" => $order_incremental_id, "order_type" => $orderTypeFlag));
                        if (file_exists($orderFolderPath . '/' . $orderNo . '/order.json')) {
                            $order_json = file_get_contents($orderFolderPath . '/' . $orderNo . '/order.json');
                            $json_content = json_decode($order_json, true);
                            $noOfRefIds = count($json_content['order_details']['order_items']);
                            if ($noOfRefIds > 0) {
                                $zip->addEmptyDir($orderNo);
                                $zip->addFile($orderFolderPath . '/' . $orderNo . '/order.json', $orderNo . '/order.json');
                                $zip->addFile($orderFolderPath . '/' . $orderNo . '/info.html', $orderNo . '/info.html');
                                $item_kounter = 1;
                                foreach ($json_content['order_details']['order_items'] as $item_details) {
                                    $item_id = $item_details['item_id'];
									$ref_id = $item_details['ref_id'];
									$scanDirArr = scandir($orderFolderPath . "/" . $orderNo. "/". $item_id); //for name and number folder scan
									
									
									// check if side_index folder exists or not //
									$sidePath = $orderFolderPath . "/" . $orderNo. "/" . $item_id;
									$scanSidePath = scandir($sidePath);
									$scanSideDir = $scanSidePath;
									$orderTypeFlag = 0;
									if(is_array($scanSideDir)) {
										foreach($scanSideDir as $sidecheckPath){
											if(strpos($sidecheckPath, "side_") !== false){
												$orderTypeFlag = 1;
												continue;
											}
										}
									}
									
									if ($item_id != null && $item_id > 0 && $ref_id != null && $ref_id > 0) {
										$zip->addEmptyDir($orderNo . "/" . $item_id);
										if($orderTypeFlag == 1){
											// add side folders inside item directory //
											//Fetch the design state json details //
											$designState = file_get_contents(XEPATH . "designer-tool" . ORDER_PATH_DIR . "/" . $orderNo . "/" . $item_id . "/designState.json");
											$resultDesign = $this->formatJSONToArray($designState);
											//echo "<pre>"; print_r($resultDesign['sides']);
											$sidesCount = count($resultDesign['sides']);
											for($flag=1;$flag<=$sidesCount;$flag++){
												if(is_dir($orderFolderPath . "/" . $orderNo. "/". $item_id."/side_".$flag)){
													$zip->addEmptyDir($orderNo . "/" . $item_id."/side_".$flag);
												}
												
												$scanDirArr = scandir($orderFolderPath . "/" . $orderNo. "/". $item_id."/side_".$flag);
												if (count($scanDirArr) > 2) {
													//for name and number folder scan
													foreach ($scanDirArr as $nameAndNumberDir) {
														if ($nameAndNumberDir != '.' && $nameAndNumberDir != '..' && is_dir($orderFolderPath . "/" . $orderNo. "/". $item_id . "/side_".$flag.'/' . $nameAndNumberDir)) {
															$zip->addEmptyDir($orderNo . "/" . $item_id."/side_".$flag);
															$from_url = $orderFolderPath . "/" . $orderNo. "/". $item_id."/side_".$flag.'/' . $nameAndNumberDir;
															$options = array('add_path' => $orderNo . "/" . $item_id ."/side_".$flag.'/' . $nameAndNumberDir . "/", 'remove_path' => $from_url);
															$zip->addGlob($from_url . '/*{svg}', GLOB_BRACE, $options);
														}
													}
												}
												
												//copy to side folder //
												$fromUrlSide = $orderFolderPath . "/" .$orderNo. "/". $item_id."/side_".$flag;
												$optionsSide = array('add_path' => $orderNo . "/" . $item_id . "/side_".$flag."/", 'remove_path' => $fromUrlSide);
												$zip->addGlob($fromUrlSide . '/*{svg,json,html,pdf,png,jpg}', GLOB_BRACE, $optionsSide);
												
												//copy to asset folder //
												if(is_dir($orderFolderPath . "/" . $orderNo. "/". $item_id."/side_".$flag."/assets")){
													$zip->addEmptyDir($orderNo . "/" . $item_id."/side_".$flag."/assets");
													
													$fromUrlAsset = $orderFolderPath . "/" .$orderNo. "/". $item_id."/side_".$flag."/assets";
													$optionsAsset = array('add_path' => $orderNo . "/" . $item_id . "/side_".$flag."/assets/", 'remove_path' => $fromUrlAsset);
													$zip->addGlob($fromUrlAsset . '/*{svg,json,html,pdf,png,jpg,jpeg,PNG,bmp,BMP}', GLOB_BRACE, $optionsAsset);
												}
												
												//copy to preview folder //
												if(is_dir($orderFolderPath . "/" . $orderNo. "/". $item_id."/side_".$flag."/preview")){
													$zip->addEmptyDir($orderNo . "/" . $item_id."/side_".$flag."/preview");
												}
												
												$fromUrlPreview = $orderFolderPath . "/" . $orderNo. "/". $item_id."/side_".$flag."/preview";
												$optionsPreview = array('add_path' => $orderNo . "/" . $item_id . "/side_".$flag."/preview/", 'remove_path' => $fromUrlPreview);
												$zip->addGlob($fromUrlPreview . '/*{png,PNG}', GLOB_BRACE, $optionsPreview);
												
												//delete preview svg from zip //
												$zip->deleteName($orderNo . "/" . $item_id."/side_".$flag."/preview_0".$flag.".svg");
											}
											
											$from_url = $orderFolderPath . "/" . $orderNo. "/". $item_id;
											$options = array('add_path' => $orderNo . "/" . $item_id . "/", 'remove_path' => $from_url);
											$zip->addGlob($from_url . '/*{svg,json,html,pdf,png,jpg}', GLOB_BRACE, $options);
											$zipCheckKounter++;
											
										}else if($orderTypeFlag == 0){
										    $scanDirArr = scandir($orderFolderPath."/".$orderNo. "/".$item_id);//for name and number folder scan
											if(count($scanDirArr) >2){//for name and number folder scan
											    foreach($scanDirArr as $nameAndNumberDir){
											        if($nameAndNumberDir != '.' && $nameAndNumberDir != '..' && is_dir($orderFolderPath."/".$orderNo. "/".$item_id."/".$nameAndNumberDir)){
														$zip->addEmptyDir($orderNo."/".$item_id."/".$nameAndNumberDir);
														$from_url = $orderFolderPath."/".$orderNo. "/".$item_id."/".$nameAndNumberDir;
														$options = array('add_path' => $orderNo."/".$item_id."/".$nameAndNumberDir."/",'remove_path' => $from_url);
														$zip->addGlob($from_url.'/*{svg}', GLOB_BRACE, $options);
													}
												}
											}//end for name and number zip download
											$from_url = $orderFolderPath."/".$orderNo."/".$item_id;
											$options = array('add_path' => $orderNo."/".$item_id."/",'remove_path' => $from_url);
											$zip->addGlob($from_url.'/*{svg,json,html,pdf,png,jpg}', GLOB_BRACE, $options);
											$zipCheckKounter++;
										}
									}
                                    $item_kounter++;
                                }
                            }
                        }
                    }
                }
                $order_ary = array();
                $order_ary['orderList'] = $order_list_arr;
                // create Orderlist json //
                $zip->addFromString('orderList.json', json_encode($order_ary));
                $zip->close();
                //Check if download option is enabled //
                if ($zipCheckKounter > 0) {
                    if ($download == 1) {
                        $this->zipDownload($orderFolderPath . '/' . $zipName, $zipCheckKounter);
                    } else {
                        $store_url = XEPATH . "designer-tool/custom-assets/orders/";
                        $msg = $store_url . $zipName;
                    }
                } else {
                    $msg = 'No Order files Found on server to download';
                    if (file_exists($orderFolderPath . '/' . $zipName)) {
                        unlink($orderFolderPath . '/' . $zipName);
                    }
                }
            } else {
                $msg = 'Zip Creation Failed';
            }
        } else {
            $msg = 'No Orders Found to download';
        }
        $msg2 = array("Response" => $msg);
        $this->response($this->json($msg2), 200);

        
    }
    /**
     * Creates a Order JSOn and Info.html when order is placed.
     * Creating and adding the info.html to the corresponding reference id of the order.
     * @param integer integer order_id
     * @return null
     */
    public function downloadOrderDetail()
    {
        $orderId = (isset($this->_request['order_id'])) ? $this->_request['order_id'] : '';
        $this->_request['orderId'] = $orderId;
        $result = $this->getOrderDetails(1);
        if (!empty($result)) {
            $result = json_decode($result);
            $value = $result->order_details;
            $absPath = getcwd();
            $final = str_replace('\\', '/', $absPath);
            $final = $final . '/../../';
            $langsFile = file_get_contents($final . "/designer-tool/localsettings.js");
            $tarray = array(" ", "\n", "\r");
            $contents = trim(str_replace($tarray, "", $langsFile));
            $contents = substr($contents, 0, -1);
            $contents = explode("localSettings=", $contents);
            $contents = json_decode($contents['1'], true);
            $lang = $contents['language'];
            if ($lang) {
                $langFile = 'locale-' . $lang . '.json';
            } else {
                $langFile = 'locale-en.json';
            }
            $langPath = $final . "/designer-tool/designer-app/languages/" . $langFile;
            $languageJson = file_get_contents($langPath);
            if (empty($languageJson)) {
                $languageJson = file_get_contents($final . "/designer-tool/designer-app/languages/locale-en.json");
            }
            $languageJson1 = json_decode($languageJson, true);
            $langOrderId = (!empty($languageJson1['ORDER_ID'])) ? $languageJson1['ORDER_ID'] : 'Order Id';
            $langOrderDate = (!empty($languageJson1['ORDER_DATE'])) ? $languageJson1['ORDER_DATE'] : 'Order Date';
            $langOrderTime = (!empty($languageJson1['ORDER_TIME'])) ? $languageJson1['ORDER_TIME'] : 'Order Time';
            $langQuantity = (!empty($languageJson1['QUANTITY'])) ? $languageJson1['QUANTITY'] : 'Quantity';
            $langCategory = (!empty($languageJson1['CATEGORY'])) ? $languageJson1['CATEGORY'] : 'Category';
            $langPrintMethod = (!empty($languageJson1['PRINT_METHOD'])) ? $languageJson1['PRINT_METHOD'] : 'Print Method';
            $langSize = (!empty($languageJson1['SIZE'])) ? $languageJson1['SIZE'] : 'Size';
            $langColor = (!empty($languageJson1['COLOR'])) ? $languageJson1['COLOR'] : 'Color';
            $langPrintSize = (!empty($languageJson1['PRINT_SIZE'])) ? $languageJson1['PRINT_SIZE'] : 'Print Size';
            $langColorName = (!empty($languageJson1['COLOR_NAME'])) ? $languageJson1['COLOR_NAME'] : 'Color Name';
            $langCmyk = (!empty($languageJson1['CMYK'])) ? $languageJson1['CMYK'] : 'CMYK';
            $langHex = (!empty($languageJson1['HEX'])) ? $languageJson1['HEX'] : 'Hex';
            $langBRIP = (!empty($languageJson1['BROWSER_IP'])) ? $languageJson1['BROWSER_IP'] : 'Browser IP';
            $langBRLan = (!empty($languageJson1['BROWSER_LANGUAGE'])) ? $languageJson1['BROWSER_LANGUAGE'] : 'Accept Language';
            $langBRName = (!empty($languageJson1['BROWSER_NAME'])) ? $languageJson1['BROWSER_NAME'] : 'Browser Name';
            $langBRAgent = (!empty($languageJson1['BROWSER_AGENT'])) ? $languageJson1['BROWSER_AGENT'] : 'Browser Agent';
            $langBRHt = (!empty($languageJson1['BROWSER_HEIGHT'])) ? $languageJson1['BROWSER_HEIGHT'] : 'Browser Height';
            $langBRWd = (!empty($languageJson1['BROWSER_WIDTH'])) ? $languageJson1['BROWSER_WIDTH'] : 'Browser Width';
            $colorincrment = 1;
            $html = '<html><title>Order APP</title><style>body,p,td,tr,th,h1,h2,h3,h4,h5,h6{font-family:arial; }h1,h2,h3,h4,h5,h6{margin:0px; padding:0px; font-weight:normal;}.wrapper{width: 1200px; margin:auto;}.customer-info{clear:both;height:100px; background-color:; border-bottom:1px solid #ccc; margin-bottom: 20px;}.customer-details{float:left; padding:20px 0px 20px 0px; }    .order-code{float:right; padding:30px 20px 0px 0px;}.size-30{font-size:30px;}.size-25{font-size:25px;}.size-20{font-size:20px;} .size-17{font-size:17px;}.size-15{font-size:15px;}.size-12{font-size:12px;}.dark{color:#333;}.grey2{color:#ababab;}.grey{color:#ccc;}.light{color:#e6e6e6;}.items{border: 2px solid #4CAF50; margin-bottom:20px; border-radius:5px; float:left; width:1200px;}.product-name{background-color:#4CAF50; padding:15px;border-radius:2px 2px 0px 0px; font-size:20px; color:#fff;}      .product-desc{padding:20px; background-color:#efefef; margin:0px 20px 20px 20px;}.product-desc-2{padding:0px 20px 20px 20px;width:45%; text-align: center; float:left; background-color:#efefef; margin:20px 0px 20px 20px;min-height:810px;}.barcode{margin-right:50px; margin-top:20px;}.barcode img{width:100%; height:50px;}.m-b-5{margin-bottom:5px;}.m-b-7{margin-bottom:7px;}.m-b-10{margin-bottom:10px;}.m-b-15{margin-bottom:15px;}.m-b-20{margin-bottom:20px;}.m-b-25{margin-bottom:25px;}.padding{padding:20px;}.product{margin-bottom:20px; min-height:500px; min-width:500px;}table {width:100%;}table, th, td {border: 1px solid #ccc; border-collapse: collapse; }th, td {padding: 8px;text-align: left; color:#333; }table.t01 tr:nth-child(even){background-color: #efefef;}table.t01 tr:nth-child(odd) {background-color:#efefef;}table.t01 th {background-color: #666;color: white;}.table-scroll{height: 141px;overflow: auto;}.notes{border: 0px;width: 100%;resize: vertical;padding-left: 15px;font-family: inherit;}.notes-padding{padding: 0px 20px 0px 20px;};</style><body><div style="width: 1200px; margin:auto;">';
            $generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();
            $html .= '<div style="clear:both;height:100px; background-color:; border-bottom:1px solid #ccc; margin-bottom: 20px;">
                        <div style="float:left; padding:20px 0px 20px 0px;">
                            <h1 style="font-size:20px;">' . $value->billing_address->first_name . ' ' . $value->billing_address->last_name . '</h1><h2 style="margin:0px; padding:0px; font-weight:normal;font-size: 17px;">' . $langOrderId . ': <span style="color:#333;">' . $value->order_incremental_id . '</span></h2> <h3 style="margin:0px; padding:0px; font-weight:normal;font-size: 15px;">' . $langOrderDate . ': ' . gmdate('Y-m-d', strtotime($value->order_date)) . '&nbsp;<span style="color:#ababab;">(YYYY-MM-DD)</span> &nbsp; &nbsp;' . $langOrderTime . ': ' . gmdate('H:i:s', strtotime($value->order_date)) . '</h3>
                        </div>
                        <div style="float:right; padding:30px 20px 0px 0px;"><img src="data:image/png;base64,' . base64_encode($generatorPNG->getBarcode($order_id, $generatorPNG::TYPE_CODE_128)) . '"/></div></div>';
            $orderDetails = [];
            $orderDetails['shipping_address'] = $value->shipping_address;
            $orderDetails['billing_address'] = $value->billing_address;
            $orderDetails['order_id'] = $orderId;
            $orderDetails['order_incremental_id'] = $value->order_incremental_id;
            $orderDetails['order_status'] = $value->order_status;
            $orderDetails['order_date'] = $value->order_date;
            $orderDetails['customer_name'] = $value->customer_name;
            $orderDetails['customer_email'] = $value->customer_email;
            $orderDetails['shipping_method'] = $value->shipping_method;
            $ref_status = 0;
            $simpindex = 0;
            $toMail = $orderDetails['customer_email'];
            foreach ($value->order_items as $line_key => $line_items) {
                if ($line_items->ref_id != '' && $line_items->ref_id != 0) {
                    if ($ref_status == 0) {
                        $ref_status = 1;
                    }
                    $refid = $line_items->ref_id;
                    $item_id = $line_items->item_id;
                    $quantity = (int) $line_items->quantity;
                    $productname = $line_items->product_name;
                    $product_sku = $line_items->product_sku;
                    $size = $line_items->xe_size;
                    $color = $line_items->xe_color;
                    $pid = $line_items->product_id;
                    $attributes = $line_items->attribute;
                    $orderDetails['order_items'][$simpindex]['itemStatus'] = $line_items->itemStatus;
                    $orderDetails['order_items'][$simpindex]['ref_id'] = $refid;
                    $orderDetails['order_items'][$simpindex]['item_id'] = $item_id;
                    $orderDetails['order_items'][$simpindex]['print_status'] = $line_items->print_status;
                    $orderDetails['order_items'][$simpindex]['product_price'] = $line_items->product_price;
                    $orderDetails['order_items'][$simpindex]['config_product_id'] = $line_items->config_product_id;
                    $orderDetails['order_items'][$simpindex]['product_id'] = $pid;
                    $orderDetails['order_items'][$simpindex]['product_sku'] = $product_sku;
                    $orderDetails['order_items'][$simpindex]['product_name'] = $productname;
                    $orderDetails['order_items'][$simpindex]['quantity'] = $quantity;
                    $orderDetails['order_items'][$simpindex]['xe_size'] = $size;
                    $orderDetails['order_items'][$simpindex]['xe_color'] = $color;
                    $base64 = ($product_sku != '') ? $product_sku : $productname;
                    $product_barcode = base64_encode($generatorPNG->getBarcode($base64, $generatorPNG::TYPE_CODE_128));
                    $return = 1;
                    $this->_request['refids'] = $refid;
                    $refPath = $final . "designer-tool" . ASSET_PATH . "/previewimg/" . $refid . "/svg";
                    $itemPath = $final . "designer-tool" . ORDER_PATH_DIR . "/" . $orderId . "/" . $item_id;
                    if (!is_dir($itemPath)) {
                        $mkDir = "";
                        $tags = explode('/', $itemPath);
                        foreach ($tags as $folder) {
                            $mkDir .= $folder . "/";
                            if (!file_exists($mkDir)) {
                                mkdir($mkDir, 0755, true);
                            }
                        }
                    }
                    if (is_dir($itemPath)) {
                        @copy("$refPath/designState.json", "$itemPath/designState.json");
                    }
                    $designPath = $item_id . "/";
                    $designState_json = file_get_contents($final . "designer-tool" . ASSET_PATH . "/previewimg/" . $refid . "/svg/designState.json");
                    $json_content = json_decode($designState_json, true);
                    $noOfsides = count($json_content['sides']);
                    $printColorNames = "";
                    $printColors = "";
                    $cmykValue = "";
                    $printColorCategories = "";
                    $k = 1;
                    //$color                = ($colorincrment % 2 == 0) ? 'red' : 'blue';
                    $odd = 1;
                    $clear = ($colorincrment > 1) ? 'clear:both' : 'clear:none';
                    $printType = (isset($json_content['printType']) && $json_content['printType'] != '') ? $json_content['printType'] : "No Printtype";
                    $productData = (isset($json_content['productInfo']['productdata']) && $json_content['productInfo']['productdata'] != '') ? $json_content['productInfo']['productdata'] : "";
                    $notes = (isset($json_content['notes']) && $json_content['notes'] != '') ? $json_content['notes'] : "";
                    $browserIp = (isset($json_content['envInfo']) && $json_content['envInfo']['browserIp'] != '') ? $json_content['envInfo']['browserIp'] : "-";
                    $browserHeight = (isset($json_content['envInfo']) && $json_content['envInfo']['browserHeight'] != '') ? $json_content['envInfo']['browserHeight'] : "-";
                    $browserWidth = (isset($json_content['envInfo']) && $json_content['envInfo']['browserWidth'] != '') ? $json_content['envInfo']['browserWidth'] : "-";
                    $browserLang = (isset($json_content['envInfo']) && $json_content['envInfo']['browserLang'] != '') ? $json_content['envInfo']['browserLang'] : "-";
                    $userAgent = (isset($json_content['envInfo']) && $json_content['envInfo']['userAgent'] != '') ? $json_content['envInfo']['userAgent'] : "-";
                    $browserName = (isset($json_content['envInfo']) && $json_content['envInfo']['browserName'] != '') ? $json_content['envInfo']['browserName'] : "-";
                    if (!empty($productData)) {
                        $html .= '<div style="border: 2px solid #4CAF50; margin-bottom:20px; border-radius:5px; float:left; width:1180px;"><div style="background-color:#4CAF50; padding:15px;border-radius:2px 2px 0px 0px; font-size:20px; color:#fff;">' . $productname . '</div>
                         <div style="padding:20px;"><table style="border: 1px solid #ccc; border-collapse: collapse; width: 100%;"><tbody><tr><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $langQuantity . '</th><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $langPrintMethod . '</th>';
                        foreach ($productData as $key => $pattribute) {
                            foreach ($attributes as $attribute) {
                                $attributeCode = $attribute->attributeCode;
                                if ($attributeCode == $key) {
                                    $attributeLabel = $attribute->label;
                                    $html .= '<th style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . ltrim($attributeLabel, 'XE_') . '</th>';
                                }
                            }
                        }
                        $html .= '</tr><tr><td style = "border: 1px solid #ccc; border-collapse: collapse; padding: 8px; text-align: left; color: #333;">' . $quantity . '</td><td style = "border: 1px solid #ccc; border-collapse: collapse; padding: 8px; text-align: left; color: #333;">' . $printType . '</td>';
                        foreach ($productData as $key => $pattribute) {
                            foreach ($attributes as $attribute) {
                                $attributeCode = $attribute->attributeCode;
                                if ($attributeCode == $key) {
                                    $value = $attribute->value;
                                    $orderDetails['order_items'][$simpindex][$key] = $value;
                                    $html .= '<td style = "border: 1px solid #ccc; border-collapse: collapse; padding: 8px; text-align: left; color: #333;">' . $value . '</td>';
                                }
                            }
                        }
                        $html .= '</tr></tbody></table></div>';
                    } else {
                        $html .= '<div style="border: 2px solid #4CAF50; margin-bottom:20px; border-radius:5px; float:left; width:1180px;"><div style="background-color:#4CAF50; padding:15px;border-radius:2px 2px 0px 0px; font-size:20px; color:#fff;">' . $productname . '</div><div style="padding:20px;"><table style="border: 1px solid #ccc; border-collapse: collapse; width: 100%;"><tbody><tr><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $langQuantity . '</th><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $langSize . '</th><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $langColor . '</th><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $langPrintMethod . '</th></tr><tr><td style = "border: 1px solid #ccc; border-collapse: collapse; padding: 8px; text-align: left; color: #333;">' . $quantity . '</td><td style = "border: 1px solid #ccc; border-collapse: collapse; padding: 8px; text-align: left; color: #333;">' . $size . '</td><td style = "border: 1px solid #ccc; border-collapse: collapse; padding: 8px; text-align: left; color: #333;">' . $color . '</td><td style = "border: 1px solid #ccc; border-collapse: collapse; padding: 8px; text-align: left; color: #333;">' . $printType . '</td></tr></tbody></table></div>';
                    }
                    if (!empty($notes)) {
                        $html .= '<div style="padding: 0px 20px 0px 20px;"><b>Notes:</b><br/><textarea rows="4" style="border: 0px;width: 100%;resize: vertical;padding-left: 15px;font-family: inherit;" readonly>' . $notes . '</textarea></div>';
                    }
                    foreach ($json_content['sides'] as $key => $sides) {
						if(isset($sides['svg']) && !empty($sides['svg'])){
							/**** Creation Of order files Started ****/
							$sideValue = $key + 1;
							$previewPath = $refPath . "/preview_0" . $sideValue . ".svg";
							$sidePath = $itemPath . "/side_" . $sideValue . "";
							if (!is_dir($sidePath)) {
								$mkDir = "";
								$tags = explode('/', $sidePath);
								foreach ($tags as $folder) {
									$mkDir .= $folder . "/";
									if (!file_exists($mkDir)) {
										mkdir($mkDir, 0755, true);
									}
								}
							}
							if (file_exists($previewPath)) {
								copy($previewPath, $sidePath . "/preview_0" . $sideValue . ".svg");
							}
							//For assets folder image file
							$assetPath = $final . "designer-tool" . ASSET_PATH . "/previewimg/" . $refid . "/assets/" . $sideValue;
							$asesetItemPath = $itemPath . "/side_" . $sideValue . "/assets/";
							if (!is_dir($asesetItemPath)) {
								$mkDirs = "";
								$tag = explode('/', $asesetItemPath);
								foreach ($tag as $folders) {
									$mkDirs .= $folders . "/";
									if (!file_exists($mkDirs) && file_exists($assetPath)) {
										mkdir($mkDirs, 0755, true);
									}
								}
							}
							if (file_exists($assetPath)) {
								$scanDir = scandir($assetPath);
								foreach ($scanDir as $k => $asset) {
									if (file_exists($assetPath . "/" . $asset)) {
										if ($asset != "." && $asset != "..") {
											copy($assetPath . "/" . $asset, $asesetItemPath . "/" . $asset);
										}
									}
								}
							}
							//for preview folder image file
							$customizeImage = file_get_contents($sides['customizeImage']);
							$previewItemPath = $itemPath . "/side_" . $sideValue . "/preview";
							$pngFile = $previewItemPath . "/side_" . $sideValue . "_" . $item_id . "_" . $orderId . "_preview.png";
							if (!is_dir($previewItemPath)) {
								$mkDirPreviw = "";
								$exp = explode('/', $previewItemPath);
								foreach ($exp as $dir) {
									$mkDirPreviw .= $dir . "/";
									if (!file_exists($mkDirPreviw)) {
										mkdir($mkDirPreviw, 0755, true);
									}
								}
							}
							if (is_dir($previewItemPath)) {
								if (!file_exists($pngFile)) {
									$svgFileStatus = file_put_contents($pngFile, $customizeImage);
								}
							}
							/**** Creation Of order files End ****/

							$pUrl = $sides['customizeImage'];
							$onesidewidth = ($noOfsides <= 1) ? 'width:93%;' : '';
							/* $clear = ($odd%2 == 0)? 'clear:none': 'clear:both'; */
							$printUnit = (isset($sides['printUnit']) && $sides['printUnit'] != '') ? $sides['printUnit'] : "No Unit";
							$dimension = $sides['PrintDimension']['boundheight'] . 'x' . $sides['PrintDimension']['boundwidth'];
							$html .= '<div style="padding: 0px 20px 20px 20px; width: 45%; text-align: center; float: left; background-color: #efefef; margin: 20px 0px 20px 13px;' . $onesidewidth . '"><div class="m-b-7 barcode"><img style = "width: 100%; height: 50px;" src="data:image/png;base64,' . $product_barcode . '" height="50px" alt="" /></div><div style="margin-bottom:10px;"><h3>' . $base64 . '</h3></div><div style="margin-bottom:20px; min-height:500px; min-width:500px;"><img class="product-img" src="' . $pUrl . '" alt=""></div>';
							if (isset($sides['printSize']) && $sides['printSize'] != '') {
								$printValue = $sides['printSize'];
								if ($printValue[0] != 'A') {
									$printSize = $dimension . ' (' . $printUnit . ')';
								} else {
									$printSize = $sides['printSize'];
									$printSize .= ': ' . $dimension . ' (' . $printUnit . ')';
								}
								$html .= '<div><h2 style="margin-bottom:10px;">' . $langPrintSize . ':   ' . $printSize . '</h2></div>';
							} else {
								$html .= '<div style="margin-bottom:10px;"> &nbsp; </div>';
							}
							$printColorNames = (isset($sides['printColorNames'])) ? count($sides['printColorNames']) : 0;
							$height = $printColorNames * 30;
							if ($printColorNames > 0) {
								$html .= '<div style="height: 141px;overflow: auto;"><table style="width: 100%; height:' . $height . 'px" class="repeattd t01"><tbody> <tr>
							<th style="border: 1px solid #ccc; border-collapse: collapse; padding: 8px; text-align: left; background-color: #666; color: white;">' . $langColorName . '</th> <th style="border: 1px solid #ccc; border-collapse: collapse; padding: 8px; text-align: left; background-color: #666; color: white;">' . $langCategory . '</th>
							<th style="border: 1px solid #ccc; border-collapse: collapse; padding: 8px; text-align: left; background-color: #666; color: white;">' . $langCmyk . '</th> <th style="border: 1px solid #ccc; border-collapse: collapse; padding: 8px; text-align: left; background-color: #666; color: white;">' . $langHex . '</th> </tr>';
								foreach ($sides['printColorNames'] as $y => $printcolornames) {
									$printcolornames = (!empty($printcolornames)) ? $printcolornames : '-';
									$printColors[$y] = (!empty($sides['printColors'])) ? $sides['printColors'][$y] : '-';
									$printColors[$y] = ($printColors[$y][0] == "#") ? $printColors[$y] : '<img src="' . $printColors[$y] . '" width="20" height="20" />';
									if (!empty($sides['cmykValue']) && $sides['cmykValue'][$y] != "") {
										$content_svg = json_encode(array_change_key_case($sides['cmykValue'][$y], CASE_UPPER));
										$cmykValue[$y] = substr($content_svg, 0, -1);
										$cmykValue[$y] = ltrim($cmykValue[$y], '{');
										$cmykValue[$y] = str_replace('"', '', $cmykValue[$y]);
									} else {
										$cmykValue[$y] = '-';
									}
									$printColorCategories[$y] = (!empty($sides['printColorCategories'])) ? $sides['printColorCategories'][$y] : '-';
									$html .= '<tr> <td style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . str_replace('No Name', '-', $printcolornames) . '</td> <td style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . str_replace('No Category', '-', $printColorCategories[$y]) . '</td>
								<td style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $cmykValue[$y] . '</td>
								<td style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $printColors[$y] . '</td> </tr>';
									// $html .= '</div>';
								}
							} else {
								if (isset($sides['printSize']) && $sides['printSize'] != '') {
									$html .= '<div style="height: 141px;overflow: auto;"><table style="width: 100%; height:' . $height . 'px" class="repeattd t01"><tbody><div style=" text-align: center; padding-top: 55px;"> &nbsp; </div></tbody>';
								} else {
									$html .= '<div style="height: 141px;overflow: auto;"><table style="width: 100%; height:' . $height . 'px" class="repeattd t01"><tbody><div style=" text-align: center; padding-top: 55px;"> &nbsp; <br> </div></tbody>';
								}
							}
							$html .= '</tbody></table></div>';
							$html .= '</div>';
							$k++;
							$odd++;
						}
                    }
                    $colorincrment++;
                    $html .= '</div>';
                }
                $simpindex++;
            }
            $html .= '<div style="width: 1200px; margin:auto;"><h1 style="margin:0px; padding:0px; font-weight:normal;font-size: 20px;padding-bottom: 8px;">Environment Information :</h1><div style="clear:both;height:100px; background-color:; border-bottom:1px solid #ccc; margin-bottom: 20px;border-bottom: 0px;"><table style="width:100%;border: 1px solid #ccc; border-collapse: collapse;"><tbody><tr><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $langBRIP . '</th><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 6px;text-align: left; color:#333;">' . $langBRLan . '</th><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 6px;text-align: left; color:#333;">' . $langBRAgent . '</th><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 6px;text-align: left; color:#333;">' . $langBRName . '</th><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 6px;text-align: left; color:#333;">' . $langBRWd . '</th><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 6px;text-align: left; color:#333;">' . $langBRHt . '</th></tr><tr><td style="border: 1px solid #ccc; border-collapse: collapse;padding: 6px;text-align: left; color:#333;">' . $browserIp . '</td><td style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $browserLang . '</td><td style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $userAgent . '</td><td style="border: 1px solid #ccc; border-collapse: collapse;padding: 6px;text-align: left; color:#333;">' . $browserName . '</td><td style="border: 1px solid #ccc; border-collapse: collapse;padding: 6px;text-align: left; color:#333;">' . $browserWidth . 'px' . '</td><td style="border: 1px solid #ccc; border-collapse: collapse;padding: 6px;text-align: left; color:#333;">' . $browserHeight . 'px' . '</td></tr></tbody></table></div></div>';
            $html .= '</div></body></html>';
            if ($ref_status) {
                $result = [];
                $result['order_details'] = $orderDetails;
                $path = $final . "designer-tool" . ORDER_PATH_DIR . "/" . $orderId;
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                $ourFileName = $path . "/order.json";
                $myfile = fopen($ourFileName, "w") or die("Unable to open file!");
                fwrite($myfile, json_encode($result));
                fclose($myfile);
                $myFile = $path . "/info.html"; // or .php
                $fh = fopen($myFile, 'w'); // or die("error");
                fwrite($fh, $html);
                $htmlUpdated = str_ireplace('class="m-b-7 barcode"', 'style = "display:none;"', $html);
                $htmlUpdated = str_ireplace('Environment Information :', '', $htmlUpdated);
                $this->sentCustomerMail($toMail, $htmlUpdated);
            }
            $msg = array('status' => 'success');
            $this->response($this->json($msg), 200);
        } else {
            $msg = array('status' => 'failed', 'error' => json_encode($result));
            $this->response($this->json($msg), 200);
        }

    }

}
