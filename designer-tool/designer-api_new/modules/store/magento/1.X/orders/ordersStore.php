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
                $fromDate = (isset($this->_request['fromDate']) && trim($this->_request['fromDate']) != '') ? trim($this->_request['fromDate']) : '';
                $toDate = (isset($this->_request['toDate']) && trim($this->_request['toDate']) != '') ? trim($this->_request['toDate']) : '';

                try {
                    $filters = array('lastOrderId' => $lastOrderId, 'store' => $this->getDefaultStoreId(), 'range' => $range, 'fromDate' => $fromDate, 'toDate' => $toDate);
                    $result = $this->proxy->call($key, 'cedapi_cart.getOrders', $filters);

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
                print_r(json_encode($result));
                //$this->response($this->json($result), 200);
            } else {
                print_r(json_decode($result));
            }
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
            $this->response($this->json($msg), 200);
        }
        exit;
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
            if ($this->checkSendQuote()) {
                try {
                    $sql = 'Select id,order_date from ' . TABLE_PREFIX . 'customer_order_info where order_date > DATE_SUB(NOW(), INTERVAL 1 MONTH)';
                    $order_data = $this->executeFetchAssocQuery($sql);
                    $res = array();
                    $tempId = 0;
                    $count = -1;
                    foreach ($order_data as $k => $v) {
                        $s = $v['order_date'];
                        $dt = new DateTime($s);
                        $date = $dt->format('Y-m-d');
                        if ($tempId != $date) {
                            $i = 0;
                            $count++;
                            $tempId = $date;
                            $res[$count]['date'] = $date;
                            $res[$count]['sales'] = $i + 1;
                        } else {
                            $i++;
                            $res[$count]['date'] = $date;
                            $res[$count]['sales'] = $i + 1;
                        }
                    }
                    $result = json_encode($res);

                } catch (Exception $e) {
                    $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                    $error = true;
                }
            } else {
                $from = (isset($this->_request['from']) && trim($this->_request['from']) != '') ? trim($this->_request['from']) : '';
                $to = (isset($this->_request['to']) && trim($this->_request['to']) != '') ? trim($this->_request['to']) : '';

                try {
                    $filters = array('from' => $from, 'to' => $to, 'store' => $this->getDefaultStoreId());
                    $result = $this->proxy->call($key, 'cedapi_cart.getOrdersGraph', $filters);

                } catch (Exception $e) {
                    $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                    $error = true;
                }
            }
            if (!$error) {
                print_r($result);
            } else {
                print_r(json_decode($result));
            }
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
            $this->response($this->json($msg), 200);
        }
        exit;
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
                print_r($result);
            } else {
                print_r(json_decode($result));
            }
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
            $this->response($this->json($msg), 200);
        }
        exit;
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
                $filters = array('lastOrderId' => $lasID, 'range' => 0, 'store' => $this->getDefaultStoreId());
                $storeResult = $this->proxy->call($key, 'cedapi_cart.orderIdFromStore', $filters);
                if ($storeResult) {
                    $storeResult = json_decode($storeResult, true);
                    foreach ($storeResult['order_list'] as $k => $order) {
                        $select_sql = 'SELECT order_status FROM ' . TABLE_PREFIX . 'sync_order  WHERE orderId="' . $order['order_incremental_id'] . '"';
                        $rows = $this->executeGenericDQLQuery($select_sql);
                        $pendingOrderCount['order_list'][$k] = $order;
                    }
                }
                $result['lastOrderID'] = $lasID;
                $result['pendingOrderCount'] = count($pendingOrderCount['order_list']);
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
}
