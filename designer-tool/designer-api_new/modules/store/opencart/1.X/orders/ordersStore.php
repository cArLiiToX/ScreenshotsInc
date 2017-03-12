<?php
/* Check Un-authorize Access */
if (!defined('accessUser')) {
    die("Error");
}

class OrdersStore extends UTIL
{
    public function __construct()
    {
        parent::__construct();
        $this->datalayer = new Datalayer();
        $this->helper = new Helper();
    }

    /**
     * Get Order list
     *
     * @param   nothing
     * @return  order list in json format
     */
    public function getOrders($download = 0)
    {
        $error = false;

        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            try {
                $start = isset($this->_request['start']) ? '&start=' . trim($this->_request['start']) : '';
                $limit = isset($this->_request['limit']) ? '&limit=' . trim($this->_request['limit']) : '';
                $last_order_id = isset($this->_request['lastOrderId']) ? '&last_order_id=' . trim($this->_request['lastOrderId']) : '';
                $limit = ($limit == '' && isset($this->_request['range'])) ? '&limit=' . trim($this->_request['range']) : $limit;
                $url = $this->getCurrentUrl() . '/?route=' . $this->extensionPath() . 'feed/web_api/getOrders' . $start . '' . $limit . '' . $last_order_id;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $output = curl_exec($ch);
                curl_close($ch);
                $result = json_decode($output, true);
                $order_list = $result['order_list'];
                foreach ($result['order_list'] as $k => $order) {
                    $order_id = $order['order_incremental_id'];
                    $select_sql = "SELECT order_status FROM " . TABLE_PREFIX . "sync_order  WHERE orderId=$order_id";
                    $rows = $this->executeGenericDQLQuery($select_sql);
                    if (!empty($rows)) {
                        $order['print_status'] = $rows[0]['order_status'];
                    } else {
                        $order['print_status'] = 0;
                    }

                    $result['order_list'][$k] = $order;
                }
            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $error = true;
            }
            if (!$error) {
                if ($download) {
                    return json_encode(array("order_list" => $order_list));
                } else {
                    $this->response(json_encode($result), 200);
                }

            } else {
                $this->response(json_decode($result), 200);
            }
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
            $this->response($this->json($msg), 200);
        }

    }

    /**
     * Get Order list
     *
     * @param   nothing
     * @return  order list in json format
     */
    public function getOrdersGraph()
    {
        $error = false;

        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {

            $from = (isset($this->_request['from']) && trim($this->_request['from']) != '') ? trim($this->_request['from']) : '';
            $to = (isset($this->_request['to']) && trim($this->_request['to']) != '') ? trim($this->_request['to']) : '';
            $from_date = ($from != '') ? gmdate('Y-m-d', strtotime($from)) : gmdate('Y-m-d', strtotime("-30 days"));
            $to_date = ($to != '') ? gmdate('Y-m-d', strtotime($to)) : gmdate('Y-m-d', strtotime("+1 days"));
            try {
                $url = $this->getCurrentUrl() . '/?route=' . $this->extensionPath() . 'feed/web_api/getOrdersGraph&from=' . $from_date . '&to=' . $to_date;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $output = curl_exec($ch);
                curl_close($ch);
                $this->response($output, 200);
            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $error = true;
            }
            if (!$error) {
                $this->response($result, 200);
            } else {
                $this->response(json_decode($result), 200);
            }
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
            $this->response($this->json($msg), 200);
        }
    }

    /**
     * Get details of a order
     *
     * @param   orderIncrementId
     * @return  order detail in json format
     */
    public function getOrderDetails($return = 0)
    {
        $error = false;

        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            $order_id = isset($this->_request['order_id']) ? trim($this->_request['order_id']) : trim($this->_request['orderIncrementId']);
            $url = $this->getCurrentUrl() . '/?route=' . $this->extensionPath() . 'feed/web_api/getOrderDetails&order_id=' . $order_id;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $output = curl_exec($ch);

            curl_close($ch);
            if ($return) {
                return $output;
            } else {
                $this->response($output, 200);
            }

        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
            $this->response($this->json($msg), 200);
        }

    }

    /**
     * Creates a Order JSOn and Info.html when order is placed.
     * Creating and adding the info.html to the corresponding reference id of the order.
     * @param integer integer order_id
     * @return null
     */
    public function downloadOrderDetail()
    {
        $order_id = (isset($this->_request['order_id'])) ? $this->_request['order_id'] : '';
        $orderId = $order_id;
        $result = $this->getOrderDetails(1);
        $json = $result;
        if (!empty($result)) {
            $result = json_decode($result);
            $value = $result->order_details;
            $toMail = $value->customer_email;
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
            $html = '<html><title>Order APP</title><body style="font-family:arial;"><div style="width: 1200px; margin:auto;">';
            $generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();
            $html .= '<div style="clear:both;height:100px; background-color:; border-bottom:1px solid #ccc; margin-bottom: 20px;">
                        <div style="float:left; padding:20px 0px 20px 0px;">
                            <h1 style="margin:0px; padding:0px; font-weight:normal;font-size: 20px;">' . $value->billing_address->first_name . ' ' . $value->billing_address->last_name . '</h1>
                            <h2 style="margin:0px; padding:0px; font-weight:normal;font-size: 17px;">' . $langOrderId . ': <span style="color:#333;">' . $order_id . '</span></h2>
                            <h3 style="margin:0px; padding:0px; font-weight:normal;font-size: 15px;">' . $langOrderDate . ': ' . gmdate('Y-m-d', strtotime($value->order_date)) . ' <span style="color:#ababab;">(YYYY-MM-DD)</span> &nbsp; &nbsp; ' . $langOrderTime . ': ' . gmdate('H:i:s', strtotime($value->order_date)) . '</h3>
                        </div>
                        <div style="float:right; padding:30px 20px 0px 0px;"><img src="data:image/png;base64,' . base64_encode($generatorPNG->getBarcode($order_id, $generatorPNG::TYPE_CODE_128)) . '"/></div></div>';
            $ref_status = 0;
            $nameCount = 0;
            $temp = array();
            foreach ($value->order_items as $line_key => $line_items) {
                if ($line_items->ref_id != '' && $line_items->ref_id != 0) {
                    if ($ref_status == 0) {
                        $ref_status = 1;
                    }                    
                    $refid = $line_items->ref_id;
                    $item_id = $line_items->order_product_id;
                    $quantity = $line_items->quantity;
                    $productname = $line_items->product_name;
                    $product_sku = $line_items->product_sku;
                    $size = $line_items->xe_size;
                    $color = $line_items->xe_color;
                    $pid = $line_items->product_id;
                    $product_id = $this->datalayer->getProductParent($pid);
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
					// Creating attribute lists
					$attrKey = "";
                    $attrvalue = "";
					foreach ($line_items->attributes as $key => $value) {
                        if ($key == 'xe_color') {
                            $attrKey .= '<th style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;"> Color </th>';
                            $attrvalue .= '<td style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $value . '</td>';
                        } else if ($key == 'xe_size') {
							if (empty($json_content['nameNumberData'])) {
								$attrKey .= '<th style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;"> Size </th>';
								$attrvalue .= '<td style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $value . '</td>';
							}
                        } else {
                            $attrKey .= '<th style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $key . '</th>';
                            $attrvalue .= '<td style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $value . '</td>';
                        }
                    }
					foreach ($json_content['sides'] as $key => $sides) {
                        /**** Creation Of order files Started ****/
						if(isset($sides['svg']) && !empty($sides['svg'])){
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
						}
                        if (file_exists($previewPath)) {
                            copy($previewPath, $sidePath . "/preview_0" . $sideValue . ".svg");
                        }
                        //For assets folder image file
                        $assetPath = $final . "designer-tool" . ASSET_PATH . "/previewimg/" . $refid . "/assets/" . $sideValue;
                        $asesetItemPath = $itemPath . "/side_" . $sideValue . "/assets/";
                        if (!is_dir($asesetItemPath) && file_exists($assetPath)) {
                            $mkDirs = "";
                            $tag = explode('/', $asesetItemPath);
                            foreach ($tag as $folders) {
                                $mkDirs .= $folders . "/";
                                if (!file_exists($mkDirs)) {
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
					}
                    //Check for repeated name and number
                    if (in_array($refid, $temp)) {
                        if (!empty($json_content['nameNumberData']) && $nameCount != 0) {
                            continue;
                        }
                    }
                    $temp[] = $refid;
                    $printColorNames = "";
                    $printColors = "";
                    $cmykValue = "";
                    $printColorCategories = "";
                    $k = 1;
                    $color = ($colorincrment % 2 == 0) ? 'red' : 'blue';
                    $odd = 1;
                    $clear = ($colorincrment > 1) ? 'clear:both' : 'clear:none';
                    $printType = (isset($json_content['printType']) && $json_content['printType'] != '') ? $json_content['printType'] : "No Printtype";
                    $notes = (isset($json_content['notes']) && $json_content['notes'] != '') ? $json_content['notes'] : "";
                    $browserIp = (isset($json_content['envInfo']) && $json_content['envInfo']['browserIp'] != '') ? $json_content['envInfo']['browserIp'] : "-";
                    $browserHeight = (isset($json_content['envInfo']) && $json_content['envInfo']['browserHeight'] != '') ? $json_content['envInfo']['browserHeight'] : "-";
                    $browserWidth = (isset($json_content['envInfo']) && $json_content['envInfo']['browserWidth'] != '') ? $json_content['envInfo']['browserWidth'] : "-";
                    $browserLang = (isset($json_content['envInfo']) && $json_content['envInfo']['browserLang'] != '') ? $json_content['envInfo']['browserLang'] : "-";
                    $userAgent = (isset($json_content['envInfo']) && $json_content['envInfo']['userAgent'] != '') ? $json_content['envInfo']['userAgent'] : "-";
                    $browserName = (isset($json_content['envInfo']) && $json_content['envInfo']['browserName'] != '') ? $json_content['envInfo']['browserName'] : "-";

                    $html .= '<div style="border: 2px solid #4CAF50; margin-bottom:20px; border-radius:5px; float:left; width:1200px;"><div style="background-color:#4CAF50; padding:15px;border-radius:2px 2px 0px 0px; font-size:20px; color:#fff;">' . $productname . '</div><div style="padding:20px;"><table style="width:100%;border: 1px solid #ccc; border-collapse: collapse;"><tbody><tr>';
                    //Hide heading of size attribute and quantity for name and number case
                    if (empty($json_content['nameNumberData'])) {
                        $html .= '<th style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $langQuantity . '</th>';
                    }
                    $html .= $attrKey . '<th style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $langPrintMethod . '</th></tr><tr>';
                    //Hide value of size attribute and quantity for name and number case
                    if (empty($json_content['nameNumberData'])) {
                        $html .= '<td style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $quantity . '</td>';
                    }
                    $html .= $attrvalue . '<td style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $printType . '</td></tr></tbody></table></div>';
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
                        $html .= '<div style="padding: 0px 20px 0px 20px;"><b>Notes:</b><br/><textarea rows="4" style="border: 0px;width: 100%;resize: vertical;padding-left: 15px;font-family: inherit;" readonly>' . $notes . '</textarea></div>';
                    }
                    foreach ($json_content['sides'] as $key => $sides) {                    
						/* if(isset($sides['svg']) && !empty($sides['svg'])){ */
							$pUrl = $sides['customizeImage'];
							$onesidewidth = ($noOfsides <= 1) ? 'width:93%;' : 'width:45%;';
                            $onesidepadding = ($noOfsides <= 1) ? 'padding:22px;' : 'padding:15px;';
							$barcodewidth = ($noOfsides <= 1) ? 'width: inherit;' : '';
							/* $clear = ($odd%2 == 0)? 'clear:none': 'clear:both'; */
							$printUnit = (isset($sides['printUnit']) && $sides['printUnit'] != '') ? $sides['printUnit'] : "No Unit";
							$dimension = $sides['PrintDimension']['boundheight'] . 'x' . $sides['PrintDimension']['boundwidth'];

							$html .= '<div style="' .$onesidepadding. ' text-align: center; float:left; background-color:#efefef; margin:20px 0px 20px 20px;min-height:830px;' . $onesidewidth . '"><div style="margin-bottom:7px; margin-right:50px; margin-top:20px;"><img src="data:image/png;base64,' . $product_barcode . '" height="50px" alt="" style="' . $barcodewidth . '" /></div><div style="margin-bottom:10px;"><h3 style="margin:0px; padding:0px; font-weight:normal;"><h3>' . $base64 . '</h3></div><div style="margin-bottom:20px; min-height:500px; min-width:500px;"><img class="product-img" src="' . $pUrl . '" alt=""></div>';
							if (isset($sides['printSize']) && $sides['printSize'] != '') {
								$printValue = $sides['printSize'];
								if ($printValue[0] != 'A') {
									$printSize = $dimension . ' (' . $printUnit . ')';
								} else {
									$printSize = $sides['printSize'];
									$printSize .= ': ' . $dimension . ' (' . $printUnit . ')';
								}
								$html .= '<div><h2 style="margin:0px; padding:0px; font-weight:normal;margin-bottom:10px;">' . $langPrintSize . ':  ' . $printSize . '</h2></div>';
							} else {
								$html .= '<div style="margin-bottom:10px"> &nbsp; </div>';
							}

							$printColorNames = (isset($sides['printColorNames'])) ? count($sides['printColorNames']) : 0;
							$height = $printColorNames * 30;

							if ($printColorNames > 0) {
								$html .= '<div style="height: 141px;overflow: auto;"><table style="width:100%;border: 1px solid #ccc; border-collapse: collapse;"><tbody><tr><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; background-color: #666;color: white;">' . $langColorName . '</th><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; background-color: #666;color: white;">' . $langCategory . '</th><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; background-color: #666;color: white;">' . $langCmyk . '</th><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; background-color: #666;color: white;">' . $langHex . '</th></tr>';
								foreach ($sides['printColorNames'] as $y => $printcolornames) {
									$printcolornames = (!empty($printcolornames)) ? $printcolornames : '-';
									$printColors[$y] = (!empty($sides['printColors'])) ? $sides['printColors'][$y] : '-';
									$printColors[$y] = ($printColors[$y][0] == "#") ? $printColors[$y] : '<img src="' . $printColors[$y] . '" width="20" height="20" />';
									if (!empty($sides['cmykValue'][$y])) {
										$content_svg = json_encode(array_change_key_case($sides['cmykValue'][$y], CASE_UPPER));
										$cmykValue[$y] = substr($content_svg, 1, -1);
										$cmykValue[$y] = str_replace('"', '', $cmykValue[$y]);
									} else {
										$cmykValue[$y] = '-';
									}
									$printColorCategories[$y] = (!empty($sides['printColorCategories'])) ? $sides['printColorCategories'][$y] : '-';
									$html .= '<tr><td style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $printcolornames . '</td><td style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $printColorCategories[$y] . '</td><td style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $cmykValue[$y] . '</td><td style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $printColors[$y] . '</td></tr>';
								}
								$html .= '</tbody></table></div>';
							}
							$html .= '</div>';
							$k++;
							$odd++;
						//}
                    }
                    $colorincrment++;
                    $html .= '</div>';
                }

            }
            $html .= '<div style="width: 1200px; margin:auto;"><h1 style="margin:0px; padding:0px; font-weight:normal;font-size: 20px;padding-bottom: 8px;">Environment Information :</h1><div style="clear:both;height:100px; background-color:; border-bottom:1px solid #ccc; margin-bottom: 20px;border-bottom: 0px;"><table style="width:100%;border: 1px solid #ccc; border-collapse: collapse;"><tbody><tr><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $langBRIP . '</th><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $langBRLan . '</th><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $langBRAgent . '</th><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $langBRName . '</th><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $langBRWd . '</th><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $langBRHt . '</th></tr><tr><td style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $browserIp . '</td><td style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $browserLang . '</td><td style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $userAgent . '</td><td style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $browserName . '</td><td style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $browserWidth . 'px' . '</td><td style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $browserHeight . 'px' . '</td></tr></tbody></table></div></div>';
            $html .= '</div></body></html>';
            if ($ref_status) {
                $path = $final . "designer-tool" . ORDER_PATH_DIR . "/" . $orderId;
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }

                $ourFileName = $path . "/order.json";
                $myfile = fopen($ourFileName, "w") or die("Unable to open file!");
                fwrite($myfile, $json);
                fclose($myfile);
                $myFile = $path . "/info.html"; // or .php
                $fh = fopen($myFile, 'w'); // or die("error");
                fwrite($fh, $html);
            }
            $this->sentCustomerMail($toMail, $html);
            $msg = array('status' => 'success');
            $this->response($this->json($msg), 200);
        } else {
            $msg = array('status' => 'failed', 'error' => json_encode($result));
            $this->response($this->json($msg), 200);
        }

    }

    /*
     * Creates a Zip file.
     * adding the svg files from the corresponding reference id to the zip.
     * Generate a temporary zip file of the item with all svg file and info.html.
     * @param integer refid, integer item_id, integer order_id
     * @return null
     */
    public function downloadSvg()
    {
        $refid = (isset($this->_request['refid'])) ? $this->_request['refid'] : '';
        $item_id = (isset($this->_request['item_id'])) ? $this->_request['item_id'] : '';
        $order_id = (isset($this->_request['order_id'])) ? $this->_request['order_id'] : '';
        $apiKey = $this->_request['apikey'];
        $return = 1;
        $this->_request['refids'] = $refid;
        $cartArrs = $this->getCartPreviewImages($apiKey, $refid, $return);
        $side = 0;
        foreach ($cartArrs[$refid] as $previewSvg) {
            $previewImage = $previewSvg['svg'];
            $this->_request['svgFile'] = $previewSvg['svg'];
            $this->changeSvg(1);
        }
        $previewImagePath = $this->getPreviewImagePath();
        $previewImagePath = str_replace("//", "/", $previewImagePath);
        $zipName = $item_id . ".zip";
        $zip = new ZipArchive();
        $res = $zip->open($previewImagePath . '' . $zipName, ZipArchive::CREATE);
        $from_url = $previewImagePath . '' . $refid;
        //$zip->addFile($from_url.'/info.html', $refid.'/info.html');
        if ($res === true) {
            $options = array('add_path' => $refid . '/', 'remove_path' => $from_url);
            $zip->addGlob($from_url . '/{svg,withoutProduct}/*{svg}', GLOB_BRACE, $options);
            $zipCheckKounter = 1;
            $zip->close();
            $this->zipDownload($previewImagePath . '' . $zipName, $zipCheckKounter);
        } else {
            $msg = 'Zip Creation Fialed';
        }

        $msg = array("Response" => $msg);
        $this->response($this->json($msg), 200);
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
        $this->_request['start'] = '';
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
                $orderList = $this->getOrders(1);

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

}
