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
            if ($this->checkSendQuote()) {
                $lastOrderId = (isset($this->_request['start']) && trim($this->_request['start']) != '') ? trim($this->_request['start']) : 0;
                $range = (isset($this->_request['range']) && trim($this->_request['range']) != '') ? trim($this->_request['range']) : 0;
                try {
                    $sql = 'Select id,order_date,customer_name from ' . TABLE_PREFIX . 'customer_order_info order by id desc';
                    $sql .= " LIMIT $lastOrderId, $range";
                    $result['is_Fault'] = 0;
                    $result['order_list'] = $this->executeFetchAssocQuery($sql);
                    foreach ($result['order_list'] as $k => $order) {
                        $statusSql = 'SELECT order_status FROM ' . TABLE_PREFIX . 'sync_order  WHERE orderId="' . $order['id'] . '"';
                        $orderStatus = $this->executeGenericDQLQuery($statusSql);
                        $order['print_status'] = $orderStatus[0]['order_status'];
                        $order['order_incremental_id'] = $order['id'];
                        $order['order_id'] = $order['id'];
                        $order['order_status'] = 'pending';
                        $result['order_list'][$k] = $order;
                    }
                } catch (Exception $e) {
                    $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                    $error = true;
                }
            } else {
                $lastOrderId = (isset($this->_request['lastOrderId']) && trim($this->_request['lastOrderId']) != '') ? trim($this->_request['lastOrderId']) : 0;
                $range = (isset($this->_request['range']) && trim($this->_request['range']) != '') ? trim($this->_request['range']) : 0;
                $start = (isset($this->_request['start']) && trim($this->_request['start']) != '') ? trim($this->_request['start']) : 0;
                $fromDate = (isset($this->_request['fromDate']) && trim($this->_request['fromDate']) != '') ? trim($this->_request['fromDate']) : '';
                $toDate = (isset($this->_request['toDate']) && trim($this->_request['toDate']) != '') ? trim($this->_request['toDate']) : '';

                try {

                    $result = $this->datalayer->getAllOrders($start, $range, $lastOrderId);
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
            }
            if (!$error) {
                print_r(json_encode($result));exit;
            } else {
                print_r(json_decode($result));exit;
            }
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
                $result = $this->proxy->call($key, 'cedapi_cart.getOrdersGraph', $filters);
            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $error = true;
            }
            if (!$error) {
                print_r($result);exit;
            } else {
                print_r(json_decode($result));exit;
            }
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
            $this->response($this->json($msg), 200);
        }
    }

    ####################################################
    ############## syncOrdersZip ###################
    ####################################################
    public function getOrderDetails()
    {
        $error = false;
        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];
            $orderId = 0;
            if (isset($this->_request['orderIncrementId']) && trim($this->_request['orderIncrementId']) != '') {
                $orderId = trim($this->_request['orderIncrementId']);
            }
            try {
                $filters = array('orderIncrementId' => $orderId, 'store' => $this->getDefaultStoreId());
                $result = $this->proxy->call($key, 'cedapi_cart.getOrderDetails', $filters);
            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $error = true;
            }
            if (!$error) {
                print_r($result);exit;
            } else {
                print_r(json_decode($result));exit;
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
        ini_set('memory_limit', '18000M');
        $downloadUrl = '';
        $msg = "Success";
        $last_order_id = (isset($this->_request['last_order_id'])) ? $this->_request['last_order_id'] : 0;
        $range = (isset($this->_request['range'])) ? $this->_request['range'] : 20;
        $fd = (isset($this->_request['fd'])) ? $this->_request['fd'] : 0; // force Download flag//
        $download = (isset($this->_request['download'])) ? $this->_request['download'] : '';
        $is_create = (isset($this->_request['is_create'])) ? $this->_request['is_create'] : 0;

        // get Orders id from Store (returns an array with all selected orders) //
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
                    } else {
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
                                if ($itemDir != '.' && $itemDir != '..' && is_dir($orderFolderPath . "/" . $itemDir)) {
                                    // check the item folders under the product folder //
                                    //Fetch the design state json details //
                                    $designState = file_get_contents(XEPATH . "designer-tool" . ORDER_PATH_DIR . "/" . $order_id . "/" . $itemDir . "/designState.json");
                                    $resultDesign = $this->formatJSONToArray($designState);

                                    // check if side_index folder exists or not //
                                    $sidePath = $orderFolderPath . "/" . $itemDir;
                                    $scanSidePath = scandir($sidePath);
                                    $scanSideDir = $scanSidePath;
                                    $orderTypeFlag = 0;
                                    if (is_array($scanSideDir)) {
                                        foreach ($scanSideDir as $sidecheckPath) {
                                            if (strpos($sidecheckPath, "side_") !== false) {
                                                $orderTypeFlag = 1;
                                                continue;
                                            }
                                        }
                                    }

                                    // for new file structure //
                                    if ($orderTypeFlag == 1) {
                                        //check and find the sides of each item //
                                        $sidePath = $orderFolderPath . "/" . $itemDir;
                                        if (file_exists($sidePath) && is_dir($sidePath)) {
                                            $scanSideDir = scandir($sidePath); // scan item directory to fetch all side folders //
                                            $scanSideDirSide = $scanSideDir;
                                            if (is_array($scanSideDir)) {
                                                foreach ($scanSideDir as $sideDir) {
                                                    if ($sideDir != '.' && $sideDir != '..' && is_dir($orderFolderPath . "/" . $itemDir . "/" . $sideDir)) {
                                                        $i = str_replace("side_", "", $sideDir); //to fetch only side folders//
                                                        if (file_exists($orderFolderPath . "/" . $itemDir . "/" . $sideDir . "/preview_0" . $i . ".svg")) {
                                                            // with product svg file exists or not//
                                                            if ($fd == 1) {
                                                                $reqSvgFile = XEPATH . "designer-tool" . ORDER_PATH_DIR . "/" . $order_id . "/" . $itemDir . "/" . $sideDir . "/preview_0" . $i . ".svg";
                                                                $item_id = $itemDir;
                                                                //check name and number exists or not

                                                                if (!empty($resultDesign['nameNumberData'])) {
                                                                    $this->creteNameAndNumberSeparatedSvg($reqSvgFile, $resultDesign, $order_id, $item_id, $sizeArr[$item_id], 1);
                                                                } else {
                                                                    $this->createWithoutProductSvg($reqSvgFile, $order_id, $item_id, $resultDesign, 1);
                                                                }
                                                                $msg = 'Success';
                                                            } else {
                                                                if (!file_exists($orderFolderPath . "/" . $itemDir . "/" . $sideDir . "/" . $sideDir . "_" . $itemDir . "_" . $order_id . ".svg") && $is_create == 1) {
                                                                    /* check if without product svg file exists or not.if not exist, then create the file */
                                                                    $reqSvgFile = XEPATH . "designer-tool" . ORDER_PATH_DIR . "/" . $order_id . "/" . $itemDir . "/" . $sideDir . "/preview_0" . $i . ".svg";
                                                                    $item_id = $itemDir;
                                                                    //check name and number exit or not

                                                                    if (!empty($resultDesign['nameNumberData'])) {
                                                                        $this->creteNameAndNumberSeparatedSvg($reqSvgFile, $resultDesign, $order_id, $item_id, $sizeArr[$item_id], 1);
                                                                    } else {
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
                                    else if ($orderTypeFlag == 0) {
                                        //to fetch only item id folders //
                                        $kounter = 1;
                                        for ($i = 1; $i <= 15; $i++) {
                                            if (file_exists($orderFolderPath . "/" . $itemDir . "/preview_0" . $i . ".svg")) {
// with product svg file exists or not//
                                                if ($fd == 1) {
                                                    $reqSvgFile = XEPATH . "designer-tool" . ORDER_PATH_DIR . "/" . $order_id . "/" . $itemDir . "/preview_0" . $i . ".svg";
                                                    $item_id = $itemDir;
                                                    //check name and number exit or not
                                                    $designState = file_get_contents(XEPATH . "designer-tool" . ORDER_PATH_DIR . "/" . $order_id . "/" . $item_id . "/designState.json");
                                                    $resultDesign = $this->formatJSONToArray($designState);
                                                    if (!empty($resultDesign['nameNumberData'])) {
                                                        $this->creteNameAndNumberSeparatedSvg($reqSvgFile, $resultDesign, $item_id, $order_id, $sizeArr[$item_id], 0);
                                                    } else {
                                                        $this->createWithoutProductSvg($reqSvgFile, $order_id, $item_id, $resultDesign, 0);
                                                    }
                                                    $msg = 'Success';
                                                } else {
                                                    if (!file_exists($orderFolderPath . "/" . $itemDir . "/" . $i . ".svg") && $is_create == 1) {
                                                        /* check if without product svg file exists or not. if not exist, then create the file */
                                                        $reqSvgFile = XEPATH . "designer-tool" . ORDER_PATH_DIR . "/" . $order_id . "/" . $itemDir . "/preview_0" . $i . ".svg";
                                                        $item_id = $itemDir;
                                                        //check name and number exit or not
                                                        $designState = file_get_contents(XEPATH . "designer-tool" . ORDER_PATH_DIR . "/" . $order_id . "/" . $item_id . "/designState.json");
                                                        $resultDesign = $this->formatJSONToArray($designState);
                                                        if (!empty($resultDesign['nameNumberData'])) {
                                                            $this->creteNameAndNumberSeparatedSvg($reqSvgFile, $resultDesign, $item_id, $order_id, $sizeArr[$item_id], 0);
                                                        } else {
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
                    } else {
                        $orderTypeFlag = 0;
                    }

                    if (file_exists($orderFolderPath . "/" . $orderNo)) {
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
                                    $scanDirArr = scandir($orderFolderPath . "/" . $orderNo . "/" . $item_id); //for name and number folder scan

                                    // check if side_index folder exists or not //
                                    $sidePath = $orderFolderPath . "/" . $orderNo . "/" . $item_id;
                                    $scanSidePath = scandir($sidePath);
                                    $scanSideDir = $scanSidePath;
                                    $orderTypeFlag = 0;
                                    if (is_array($scanSideDir)) {
                                        foreach ($scanSideDir as $sidecheckPath) {
                                            if (strpos($sidecheckPath, "side_") !== false) {
                                                $orderTypeFlag = 1;
                                                continue;
                                            }
                                        }
                                    }

                                    if ($item_id != null && $item_id > 0 && $ref_id != null && $ref_id > 0) {
                                        $zip->addEmptyDir($orderNo . "/" . $item_id);
                                        if ($orderTypeFlag == 1) {
                                            // add side folders inside item directory //
                                            //Fetch the design state json details //
                                            $designState = file_get_contents(XEPATH . "designer-tool" . ORDER_PATH_DIR . "/" . $orderNo . "/" . $item_id . "/designState.json");
                                            $resultDesign = $this->formatJSONToArray($designState);
                                            //echo "<pre>"; print_r($resultDesign['sides']);
                                            $sidesCount = count($resultDesign['sides']);
                                            for ($flag = 1; $flag <= $sidesCount; $flag++) {
                                                if (is_dir($orderFolderPath . "/" . $orderNo . "/" . $item_id . "/side_" . $flag)) {
                                                    $zip->addEmptyDir($orderNo . "/" . $item_id . "/side_" . $flag);
                                                }

                                                $scanDirArr = scandir($orderFolderPath . "/" . $orderNo . "/" . $item_id . "/side_" . $flag);
                                                if (count($scanDirArr) > 2) {
                                                    //for name and number folder scan
                                                    foreach ($scanDirArr as $nameAndNumberDir) {
                                                        if ($nameAndNumberDir != '.' && $nameAndNumberDir != '..' && is_dir($orderFolderPath . "/" . $orderNo . "/" . $item_id . "/side_" . $flag . '/' . $nameAndNumberDir)) {
                                                            $zip->addEmptyDir($orderNo . "/" . $item_id . "/side_" . $flag);
                                                            $from_url = $orderFolderPath . "/" . $orderNo . "/" . $item_id . "/side_" . $flag . '/' . $nameAndNumberDir;
                                                            $options = array('add_path' => $orderNo . "/" . $item_id . "/side_" . $flag . '/' . $nameAndNumberDir . "/", 'remove_path' => $from_url);
                                                            $zip->addGlob($from_url . '/*{svg}', GLOB_BRACE, $options);
                                                        }
                                                    }
                                                }

                                                //copy to side folder //
                                                $fromUrlSide = $orderFolderPath . "/" . $orderNo . "/" . $item_id . "/side_" . $flag;
                                                $optionsSide = array('add_path' => $orderNo . "/" . $item_id . "/side_" . $flag . "/", 'remove_path' => $fromUrlSide);
                                                $zip->addGlob($fromUrlSide . '/*{svg,json,html,pdf,png,jpg}', GLOB_BRACE, $optionsSide);

                                                //copy to asset folder //
                                                if (is_dir($orderFolderPath . "/" . $orderNo . "/" . $item_id . "/side_" . $flag . "/assets")) {
                                                    $zip->addEmptyDir($orderNo . "/" . $item_id . "/side_" . $flag . "/assets");

                                                    $fromUrlAsset = $orderFolderPath . "/" . $orderNo . "/" . $item_id . "/side_" . $flag . "/assets";
                                                    $optionsAsset = array('add_path' => $orderNo . "/" . $item_id . "/side_" . $flag . "/assets/", 'remove_path' => $fromUrlAsset);
                                                    $zip->addGlob($fromUrlAsset . '/*{svg,json,html,pdf,png,jpg,jpeg,PNG,bmp,BMP}', GLOB_BRACE, $optionsAsset);
                                                }

                                                //copy to preview folder //
                                                if (is_dir($orderFolderPath . "/" . $orderNo . "/" . $item_id . "/side_" . $flag . "/preview")) {
                                                    $zip->addEmptyDir($orderNo . "/" . $item_id . "/side_" . $flag . "/preview");
                                                }

                                                $fromUrlPreview = $orderFolderPath . "/" . $orderNo . "/" . $item_id . "/side_" . $flag . "/preview";
                                                $optionsPreview = array('add_path' => $orderNo . "/" . $item_id . "/side_" . $flag . "/preview/", 'remove_path' => $fromUrlPreview);
                                                $zip->addGlob($fromUrlPreview . '/*{png,PNG}', GLOB_BRACE, $optionsPreview);

                                                //delete preview svg from zip //
                                                $zip->deleteName($orderNo . "/" . $item_id . "/side_" . $flag . "/preview_0" . $flag . ".svg");
                                            }

                                            $from_url = $orderFolderPath . "/" . $orderNo . "/" . $item_id;
                                            $options = array('add_path' => $orderNo . "/" . $item_id . "/", 'remove_path' => $from_url);
                                            $zip->addGlob($from_url . '/*{svg,json,html,pdf,png,jpg}', GLOB_BRACE, $options);
                                            $zipCheckKounter++;

                                        } else if ($orderTypeFlag == 0) {
                                            $scanDirArr = scandir($orderFolderPath . "/" . $orderNo . "/" . $item_id); //for name and number folder scan
                                            if (count($scanDirArr) > 2) {
//for name and number folder scan
                                                foreach ($scanDirArr as $nameAndNumberDir) {
                                                    if ($nameAndNumberDir != '.' && $nameAndNumberDir != '..' && is_dir($orderFolderPath . "/" . $orderNo . "/" . $item_id . "/" . $nameAndNumberDir)) {
                                                        $zip->addEmptyDir($orderNo . "/" . $item_id . "/" . $nameAndNumberDir);
                                                        $from_url = $orderFolderPath . "/" . $orderNo . "/" . $item_id . "/" . $nameAndNumberDir;
                                                        $options = array('add_path' => $orderNo . "/" . $item_id . "/" . $nameAndNumberDir . "/", 'remove_path' => $from_url);
                                                        $zip->addGlob($from_url . '/*{svg}', GLOB_BRACE, $options);
                                                    }
                                                }
                                            } //end for name and number zip download
                                            $from_url = $orderFolderPath . "/" . $orderNo . "/" . $item_id;
                                            $options = array('add_path' => $orderNo . "/" . $item_id . "/", 'remove_path' => $from_url);
                                            $zip->addGlob($from_url . '/*{svg,json,html,pdf,png,jpg}', GLOB_BRACE, $options);
                                            $zipCheckKounter++;
                                        }
                                    }
                                    $item_kounter++;
                                }
                            }
                        }
                        array_push($order_list_arr, array("order_id" => $order_id_arry2['order_id'], "order_incremental_id" => $order_incremental_id, "order_type" => $orderTypeFlag));
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
     *
     *date 1st_Feb-2017
     *Get pending order count for order app
     *
     * @param (int)order_id
     * @return json
     *
     */
    public function getPendingOrdersCount()
    {
        $error = false;
        $result = $this->storeApiLogin();
        $lasID = (isset($this->_request['last_id']) && trim($this->_request['last_id']) != '') ? trim($this->_request['last_id']) : 0;
        if ($this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];
            try {
                $filters = array('sinceID' => $lasID, 'store' => $this->getDefaultStoreId());
                $result = $this->proxy->call($key, 'cedapi_product.getPendingOrders', $filters);
            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $error = true;
            }
            if (!$error) {
                $this->response($this->json($result), 200);
            } else {
                $msg = array('status' => 'failed', 'error' => 'Shopify Store Error');
                $this->response($this->json($msg), 200);
            }
        }
    }

    /**
     * Sent mail to customer while order placed
     * @param (String)toMail
     * @param (String)html
     * @return status successful and failure messages.
     */
    public function sendCustomerMail($toMail, $html)
    {
        $boundary = str_replace(" ", "", date('l jS \of F Y h i s A'));
        $fromMail = SENDER_EMAIL;
        $msg = array();
        $subjectMail = "Thank you for Your Order";
        $headersMail .= 'From: ' . $fromMail . "\r\n" . 'Reply-To: ' . $fromMail . "\r\n";
        $headersMail .= "MIME-Version: 1.0\r\n";
        $headersMail .= "Content-Type: multipart/alternative; boundary = \"" . $boundary . "\"\r\n";
        $headersMail .= "--" . $boundary . "\r\n";
        $headersMail .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        $headersMail .= "Content-Transfer-Encoding: base64\r\n";
        $headersMail .= rtrim(chunk_split(base64_encode($html)));

        if (mail($toMail, $subjectMail, "", $headersMail)) {
            $msg = array("status" => 'Your mail has been sent successfully for Orders.');
        } else {
            $msg = array("status" => 'Unable to send email. Please try again.');
        }
        return $msg;
    }

    /**
     *
     *date 15th_Apr-2016
     *Create details of order placed through webhook call
     *
     * @param (int)order_id
     * @param (int)range
     * @param (int)fd
     * @param (int)download
     * @return zip file link or zip download
     *
     */
    public function orderGenerate()
    {
        $error = false;
        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];
            $order_id = $this->_request['order_id'];
            $order_num = $this->_request['order_num'];
            //insert to webhook_data table //
            $insSql = "INSERT INTO " . TABLE_PREFIX . "webhook_data SET order_id = '" . $order_id . "', order_number = '" . $order_num . "', date_created = '" . date("Y:m:d h:i:s") . "'";
            $id = $this->executeGenericInsertQuery($insSql);
            // Get new order details
            $response = $this->proxy->call($key, 'cedapi_cart.orderDetailsFromId', array("order_id" => $order_id));

            // new order-code
            $refIDs = array();
            $itemArray = array();
            //echo "<pre>"; print_r($response);
            if (!empty($response['line_items'])) {
                foreach ($response['line_items'] as $item) {
                    if (!empty($item['properties'])) {
                        foreach ($item['properties'] as $prop) {
                            if ($prop['value'] && $prop['name'] == '_refid') {
                                $isOrder = 1;
                                array_push($itemArray, array('item_id' => $item['id'], 'ref_id' => $prop['value']));
                            }
                        }
                    }
                }
            }
            //echo "<pre>"; print_r($itemArray); exit;

            $folder = TOOL_DIR . 'custom-assets/orders';
            if (!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }
            // create a folder for the order
            $order_num = $response['order_number'];
            $orderDir = $folder . '/' . $order_num;
            if (!file_exists($orderDir) && $isOrder) {
                mkdir($orderDir, 0755);
            }
            //create order.json file
            $orderDetails = array();
            $orderDetails['billing_address']['first_name'] = $response['billing_address']['first_name'];
            $orderDetails['billing_address']['telephone'] = $response['billing_address']['phone'];
            $orderDetails['billing_address']['email'] = $response['customer']['email'];
            $orderDetails['billing_address']['street'] = $response['billing_address']['address1'];
            $orderDetails['billing_address']['city'] = $response['billing_address']['city'];
            $orderDetails['billing_address']['postcode'] = $response['billing_address']['zip'];

            $orderDetails['shipping_address']['first_name'] = $response['shipping_address']['first_name'];
            $orderDetails['shipping_address']['telephone'] = $response['shipping_address']['phone'];
            $orderDetails['shipping_address']['email'] = $response['customer']['email'];
            $orderDetails['shipping_address']['street'] = $response['shipping_address']['address1'];
            $orderDetails['shipping_address']['city'] = $response['shipping_address']['city'];
            $orderDetails['shipping_address']['postcode'] = $response['shipping_address']['zip'];

            $orderDetails['order_id'] = $response['id'];
            $orderDetails['order_incremental_id'] = $response['order_number'];
            $orderDetails['order_status'] = "Pending";
            $orderDetails['order_date'] = date('Y-m-d H:i:s', strtotime($response['created_at']));
            $orderDetails['customer_name'] = $response['customer']['first_name'] . " " . $response['customer']['last_name'];
            $orderDetails['customer_email'] = $response['customer']['email'];
            $orderDetails['shipping_method'] = $response['shipping_lines']['title'];

            $orderDetails['order_items'] = array();
            foreach ($response['line_items'] as $item) {
                $itemDetails = array();
                $itemDetails['itemStatus'] = "Ordered";
                $itemDetails['ref_id'] = $item['properties'][0]['value'];
                $itemDetails['item_id'] = $item['id'];
                $itemDetails['print_status'] = null;
                $itemDetails['product_price'] = $item['price'];
                $itemDetails['config_product_id'] = $item['variant_id'];
                $itemDetails['product_id'] = $item['product_id'];
                $itemDetails['product_sku'] = $item['sku'];
                $itemDetails['product_name'] = $item['title'];
                $itemDetails['quantity'] = $item['quantity'];
                $variant = explode(" / ", $item['variant_title']);
                $itemDetails['xe_color'] = $variant[1];
                $itemDetails['xe_size'] = $variant[0];
                $orderDetails['order_items'][] = $itemDetails;
            }
            $orderContent = json_encode(array('order_details' => $orderDetails));
            if (file_exists($orderDir)) {
                file_put_contents($orderDir . '/' . 'order.json', $orderContent);
            }

            // make folder for each item in the order
            if (count($itemArray) > 0) {
                foreach ($itemArray as $item) {
                    $item_dir = $orderDir . '/' . $item['item_id'] . '/';
                    $refIDs[] = $item['ref_id'];
                    $svg_root = TOOL_DIR . ASSET_PATH . '/previewimg/' . $item['ref_id'] . '/svg/';
                    $this->recurse_copy($svg_root, $item_dir);
                }
            }
            $ref_ids = '';
            if (!empty($refIDs)) {
                $ref_ids = implode(",", $refIDs);
            }
            $imgApiUrl = XEPATH . 'designer-tool/designer-api/index.php?reqmethod=getCustomPreviewImages&refids=' . $ref_ids;
            $imgResult = file_get_contents($imgApiUrl);
            if ($imgResult) {
                $capturedImages = json_decode($imgResult, true);
            }

            if (count($itemArray) > 0) {
                // info.html creation
                // get lang details
                $langsFile = file_get_contents(TOOL_DIR . "localsettings.js");
                $pos = strpos($langsFile, '"');
                $langsFile = substr($langsFile, $pos);
                $langsFile = '{' . str_replace(';', '', $langsFile);
                $langsFile = json_decode($langsFile, true);
                $lang = $langsFile['language'];
                if ($lang) {
                    $langFile = 'locale-' . $lang . '.json';
                } else {
                    $langFile = 'locale-en.json';
                }

                $langPath = TOOL_DIR . "designer-app/languages/" . $langFile;
                if (!file_exists($langPath)) {
                    $this->debug_to_console('File not found :' . $langPath);
                }

                $languageJson = file_get_contents($langPath);
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

                $html = '<html><style>table, th, td {border: 1px solid #ccc; border-collapse: collapse;} th, td {padding: 8px;text-align: left; color:#333;} table.t01 tr:nth-child(even) {background-color: #efefef;}
                table.t01 tr:nth-child(odd) {background-color:#efefef;} table.t01 th {background-color: #666;color: white; width: 100%;} .barcode{margin-right:50px; margin-top:20px;} .m-b-7{margin-bottom:7px;}</style><title>Order Info</title><body style="font-family:arial;"><div style="width: 1200px; margin:auto;"><div style="clear:both; height:100px; background-color:; border-bottom:1px solid #ccc; margin-bottom: 20px;">';
                $generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();
                $orderDate = $response['created_at'];
                $Date = explode("T", $orderDate);

                // ordr details(header part)
                $custName = $response['customer']['first_name'] . ' ' . $response['customer']['last_name'];

                $html .= '<div style="float:left; padding:20px 0px 20px 0px;"><h1 style="margin:0px; padding:0px; font-weight:normal;font-size: 20px;">' . $custName . '</h1><h2 style="margin:0px; padding:0px; font-weight:normal;font-size: 17px;">' . $langOrderId . ': <span style="color:#333;">#' .
                $response['order']['order_number'] . '</span></h2> <h3 style="margin:0px; padding:0px; font-weight:normal;font-size: 15px;">' . $langOrderDate . ':&nbsp;' . $Date[0] . '&nbsp;<span style="color:#ababab;">(YYYY-MM-DD)</span> &nbsp; &nbsp;' . $langOrderTime . ':&nbsp;' . $Date[1] . '</h3></div><div style="float:right; padding:30px 20px 0px 0px;"><img src="data:image/png;base64,' . base64_encode($generatorPNG->getBarcode($response['order']['order_number'], $generatorPNG::TYPE_CODE_128)) . '" alt="" height="35px" /></div></div>';

                // product name
                $sizePos = $colorPos = $extraPos = $extraAtr = "";
                $toMail = $response['order']['customer']['email'];

                foreach ($response['line_items'] as $lt) {
                    $prodUrl = 'https://' . APIUSER . ':' . APIPASS . '@' . COOKIE_DOMAIN . '/admin/products/' . $lt['product_id'] . '.json';
                    $prodDet = file_get_contents($prodUrl);
                    $prodJson = json_decode($prodDet, true);
                    foreach ($prodJson['product']['options'] as $option) {
                        if (strtolower($option['name']) == 'size') {
                            $sizePos = $option['position'];
                        } elseif (strtolower($option['name']) == 'color') {
                            $colorPos = $option['position'];
                        } elseif (strtolower($option['name']) !== 'color' || strtolower($option['name']) !== 'size' || strtolower($option['name']) !== 'quantity') {
                            $extraPos = $option['position'];
                            $extraAtr = $option['name'];
                        }
                    }
                    $designPath = XEPATH . "designer-tool/custom-assets/orders/" . $order_id . "/" . $lt['id'] . "/";
                    $ref_id = $lt['properties'][0]['value'];
                    $quantity = (int) $lt['quantity'];
                    $productname = $lt['title'];
                    $sku = $lt['sku'];
                    $skuwithname = ($sku != '') ? $sku : $productname;
                    $product_barcode = base64_encode($generatorPNG->getBarcode($skuwithname, $generatorPNG::TYPE_CODE_128));

                    $designState_json = file_get_contents(TOOL_DIR . ASSET_PATH . "/previewimg/" . $ref_id . "/svg/designState.json");
                    $json_content = json_decode($designState_json, true);
                    $noOfsides = count($json_content['sides']);
                    // folder create
                    foreach ($json_content['sides'] as $key => $sides) {
                        $sideNum = $key + 1;
                        if ($sides['svg'] != '') {
                            $newSideDir = $orderDir . '/' . $lt['id'] . '/side_' . $sideNum;
                            if (!file_exists($newSideDir)) {
                                mkdir($newSideDir, 0755);
                            }
                            $prevDir = $newSideDir . '/preview';
                            if (!file_exists($prevDir)) {
                                mkdir($prevDir, 0755);
                            }

                            $imgContent = file_get_contents($sides['customizeImage']);
                            // put captured image in preview folder.
                            $previewImg = fopen($newSideDir . "/preview/side_" . $sideNum . "_" . $lt['id'] . "_" . $order_id . ".png", "w");
                            fwrite($previewImg, $imgContent);
                            fclose($previewImg);
                            // Move  preview svg to side folder
                            $prevSVGDir = $orderDir . '/' . $lt['id'] . '/preview_0' . $sideNum . '.svg';
                            rename($prevSVGDir, $newSideDir . '/preview_0' . $sideNum . '.svg');
                            // Getting Asset files for each side.
                            $assetDir = TOOL_DIR . ASSET_PATH . "/previewimg/" . $ref_id . "/assets/" . $sideNum . "/";
                            if (file_exists($assetDir)) {
                                mkdir($newSideDir . "/assets/", 0755);
                                $this->recurse_copy($assetDir, $newSideDir . "/assets/");
                            }
                        } else {
                            $unDesignedPrevSVG = $orderDir . '/' . $lt['id'] . '/preview_0' . $sideNum . '.svg';
                            unlink($unDesignedPrevSVG);
                        }
                    }
                    // folder create
                    //Check for repeated name and number
                    if (in_array($ref_id, $temp)) {
                        if (!empty($json_content['nameNumberData']) && $nameCount != 0) {
                            continue;
                        }
                    }
                    $printColorNames = $printColors = $cmykValue = $printColorCategories = "";
                    $k = 0;
                    $odd = 1;
                    $color = ($colorincrment % 2 == 0) ? 'red' : 'blue';

                    $printType = (isset($json_content['printType']) && $json_content['printType'] != '') ? $json_content['printType'] : "No Printtype";
                    $notes = (isset($json_content['notes']) && $json_content['notes'] != '') ? $json_content['notes'] : "";
                    $browserIp = $response['order']['client_details']['browser_ip'];
                    $browserHeight = (isset($json_content['envInfo']) && $json_content['envInfo']['browserHeight'] != '') ? $json_content['envInfo']['browserHeight'] : "-";
                    $browserWidth = (isset($json_content['envInfo']) && $json_content['envInfo']['browserWidth'] != '') ? $json_content['envInfo']['browserWidth'] : "-";
                    $browserLang = (isset($json_content['envInfo']) && $json_content['envInfo']['browserLang'] != '') ? $json_content['envInfo']['browserLang'] : "-";
                    $userAgent = (isset($json_content['envInfo']) && $json_content['envInfo']['userAgent'] != '') ? $json_content['envInfo']['userAgent'] : "-";
                    $browserName = (isset($json_content['envInfo']) && $json_content['envInfo']['browserName'] != '') ? $json_content['envInfo']['browserName'] : "-";
                    $variant_title = $lt['variant_title'];
                    $Attrs = explode("/", $variant_title);
                    if ($ref_id) {
                        $html .= '<div style="border: 2px solid #4CAF50; margin-bottom:20px; border-radius:5px; float:left; width:100%;"><div style="background-color:#4CAF50; padding:15px;border-radius:2px 2px 0px 0px; font-size:20px; color:#fff;">' . $productname . '</div>
                             <div style="padding:20px;"><table style="border: 1px solid #ccc; border-collapse: collapse; width: 100%;"><tbody><tr>';
                        //Hide heading of size attribute and quantity for name and number case
                        if (empty($json_content['nameNumberData'])) {
                            $html .= '<th style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $langQuantity . '</th><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $langSize . '</th>';
                        }
                        $html .= '<th style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $langColor . '</th>';
                        if ($extraPos && $extraPos > 0 && strtolower($extraAtr) !== "quantity") {
                            $html .= '<th style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $extraAtr . '</th>';
                        }
                        $html .= '<th style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $langPrintMethod . '</th></tr><tr>';
                        //Hide value of size attribute and quantity for name and number case
                        if (empty($json_content['nameNumberData'])) {
                            $html .= '<td style = "border: 1px solid #ccc; border-collapse: collapse; padding: 8px; text-align: left; color: #333;">' . $quantity . '</td><td style = "border: 1px solid #ccc; border-collapse: collapse; padding: 8px; text-align: left; color: #333;">' . $Attrs[($sizePos - 1)] . '</td>';
                        }
                        $html .= '<td style = "border: 1px solid #ccc; border-collapse: collapse; padding: 8px; text-align: left; color: #333;">' . $Attrs[($colorPos - 1)] . '</td>';
                        if ($extraPos && $extraPos > 0 && strtolower($extraAtr) !== "quantity") {
                            $html .= '<td style = "border: 1px solid #ccc; border-collapse: collapse; padding: 8px; text-align: left; color: #333;">' . $Attrs[($extraPos - 1)] . '</td>';
                        }
                        $html .= '<td style = "border: 1px solid #ccc; border-collapse: collapse; padding: 8px; text-align: left; color: #333;">' . $printType . '</td></tr></tbody></table></div>';
                    }
                    //Name and number table start
                    if (!empty($json_content['nameNumberData'])) {
                        $nameCount++;
                        $langNameFrontText = "-";
                        $langNameBackText = "-";
                        if ($json_content['nameNumberData']['front']) {
                            if ($json_content['nameNumberData']['frontView'] == "name_num") {
                                $frontView = "Name & Number";
                            } elseif ($json_content['nameNumberData']['frontView'] == "name") {
                                $frontView = "Name Only";
                            } else {
                                $frontView = "Number Only";
                            }
                            $langNameFrontText = $frontView;
                        }
                        if ($json_content['nameNumberData']['back']) {
                            if ($json_content['nameNumberData']['backView'] == "name_num") {
                                $backView = "Name & Number";
                            } elseif ($json_content['nameNumberData']['backView'] == "name") {
                                $backView = "Name Only";
                            } else {
                                $backView = "Number Only";
                            }
                            $langNameBackText = $backView;
                        }
                        $html .= '<div style="padding: 0px 20px 15px 20px;"><b>Name & Number Details:</b><br/><div style="height: 140px;overflow: auto;"><table style="border: 1px solid #ccc; border-collapse: collapse; width: 100%;"><tbody><tr><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;"> Name' . $langName . '</th><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">Number' . $langNumber . '</th><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $langSize . '</th><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">Front' . $langNameFront . '</th><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">Back' . $langNameBack . '</th></tr>';
                        foreach ($json_content['nameNumberData']['list'] as $singleName) {
                            $html .= '<tr><td style = "border: 1px solid #ccc; border-collapse: collapse; padding: 8px; text-align: left; color: #333;">' . $singleName['name'] . '</td><td style = "border: 1px solid #ccc; border-collapse: collapse; padding: 8px; text-align: left; color: #333;">' . $singleName['number'] . '</td><td style = "border: 1px solid #ccc; border-collapse: collapse; padding: 8px; text-align: left; color: #333;">' . $singleName['size'] . '</td><td style = "border: 1px solid #ccc; border-collapse: collapse; padding: 8px; text-align: left; color: #333;">' . $langNameFrontText . '</td><td style = "border: 1px solid #ccc; border-collapse: collapse; padding: 8px; text-align: left; color: #333;">' . $langNameBackText . '</td>';
                        }
                        $html .= '</tr></tbody></table></div></div>';
                    }
                    if (!empty($notes)) {
                        $html .= '<div style="padding: 0px 20px 0px 20px;"><b>Notes:</b><br/><textarea rows="4" class="notes" readonly>' . $notes . '</textarea></div>';
                    }
                    // all faces details...one by one
                    foreach ($json_content['sides'] as $key => $sides) {
                        $onesidewidth = ($noOfsides <= 1) ? 'width:93%;' : '';
                        $barcodewidth = ($noOfsides <= 1) ? 'width: inherit;' : '';
                        $printUnit = (isset($sides['printUnit']) && $sides['printUnit'] != '') ? $sides['printUnit'] : "No Unit";
                        $dimension = $sides['PrintDimension']['boundheight'] . 'x' . $sides['PrintDimension']['boundwidth'];
                        $addClearDiv = ($odd % 2 == 0) ? '<div style="clear:both;"/> &nbsp; </div>' : '';
                        if (isset($sides['printSize']) && $sides['printSize'] != '') {
                            $printSize = $sides['printSize'];
                            $printSize .= ': ' . $dimension . ' (' . $printUnit . ')';
                        } else {
                            $printSize = "No PrintSize";
                        }

                        $printColorNames = (isset($sides['printColorNames'])) ? count($sides['printColorNames']) : 0;
                        $height = $printColorNames * 30;

                        $html .= '<div style="padding: 0px 20px 20px 20px; width: 45%; text-align: center; float: left; background-color: #efefef; margin: 20px 0px 20px 13px;' . $onesidewidth . '"><div class="m-b-7 barcode"><img style = "width: 100%; height: 50px;" src="data:image/png;base64,' . $product_barcode . '" height="50px" alt="" style = "' . $barcodewidth . '" /></div><div style="margin-bottom:10px;"><h3 style="margin:0px; padding:0px; font-weight:normal;"><h3>' . $skuwithname . '</h3></div><div style="margin-bottom:20px; min-height:500px; min-width:500px;"><img src="' . $capturedImages[$ref_id][$k]['customImageUrl'] . '" alt="" /> </div>';

                        if (isset($sides['printSize']) && $sides['printSize'] != '') {
                            $printSize = $sides['printSize'];
                            $printSize .= ': ' . $dimension . ' (' . $printUnit . ')';
                            $html .= '<div><h2 style="margin:0px; padding:0px; font-weight:normal;margin-bottom:10px;">' . $langPrintSize . ':  ' . str_replace('No Label:', '', $printSize) . '</h2></div>';
                        }

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
                        $html .= '</tbody> </table></div></div>' . $addClearDiv;
                        $odd++;
                        $k++;
                    }
                    if ($ref_id) {
                        $html .= '</div>';
                    }
                }
                $simpindex++;
                $colorincrment++;
                $html .= '<div style="width: 1200px; margin:auto;"><h1 style="margin:0px; padding:0px; font-weight:normal;font-size: 20px;padding-bottom: 8px;">Environment Information :</h1><div style="clear:both;height:100px; background-color:; border-bottom:1px solid #ccc; margin-bottom: 20px;border-bottom: 0px;"><table style="width:100%;border: 1px solid #ccc; border-collapse: collapse;"><tbody><tr><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $langBRIP . '</th><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 6px;text-align: left; color:#333;">' . $langBRLan . '</th><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 6px;text-align: left; color:#333;">' . $langBRAgent . '</th><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 6px;text-align: left; color:#333;">' . $langBRName . '</th><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 6px;text-align: left; color:#333;">' . $langBRWd . '</th><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 6px;text-align: left; color:#333;">' . $langBRHt . '</th></tr><tr><td style="border: 1px solid #ccc; border-collapse: collapse;padding: 6px;text-align: left; color:#333;">' . $browserIp . '</td><td style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $browserLang . '</td><td style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $userAgent . '</td><td style="border: 1px solid #ccc; border-collapse: collapse;padding: 6px;text-align: left; color:#333;">' . $browserName . '</td><td style="border: 1px solid #ccc; border-collapse: collapse;padding: 6px;text-align: left; color:#333;">' . $browserWidth . 'px' . '</td><td style="border: 1px solid #ccc; border-collapse: collapse;padding: 6px;text-align: left; color:#333;">' . $browserHeight . 'px' . '</td></tr></tbody></table></div></div>';
                $html .= '</div></body></html>';
                $htmlPath = TOOL_DIR . "custom-assets/orders/";
                $myFile = $htmlPath . $order_num . "/info.html";
                $fh = fopen($myFile, 'w');
                fwrite($fh, $html);
                $htmlUpdated = str_ireplace('class="m-b-7 barcode"', 'style = "display:none;"', $html);
                $htmlUpdated = str_ireplace('Environment Information :', '', $htmlUpdated);
                $this->sendCustomerMail($toMail, $htmlUpdated);
            }
            if (count($itemArray) > 0) {
                foreach ($response['order']['line_items'] as $item) {
                    $custProdID = $item['product_id'];
                    $apiUrl = XEPATH . 'designer-tool/designer-api/index.php?reqmethod=editCustomProduct&pid=' . $custProdID;
                    $res = file_get_contents($apiUrl);
                }
            }

        }
    }

    public function debug_to_console($data)
    {
        if (is_array($data)) {
            $output = "<script>console.log( 'Debug Objects: " . implode(',', $data) . "' );</script>";
        } else {
            $output = "<script>console.log( 'Debug Objects: " . $data . "' );</script>";
        }
        echo $output;
    }
    public function recurse_copy($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    @recurse_copy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    @copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
}
