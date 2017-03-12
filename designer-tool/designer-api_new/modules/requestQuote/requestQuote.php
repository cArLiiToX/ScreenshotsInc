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
    public function addToQuote()
    {
        $error = false;
        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            $apikey = $this->_request['apikey'];
            $designData = $this->_request['designData'];
            $refid = $this->_request['refid'];
            $cartObj = Flight::carts();
            $refid = $cartObj->saveDesignStateCart($apikey, $refid, $designData);
            if ($refid > 0) {
                $dbstat = $cartObj->saveProductPreviewSvgImagesOnAddToCart($apikey, $refid, $designData);
            } else {
                $msg = array('status' => 'invalid refid', 'error' => $refid);
                $this->response($this->json($msg), 200);
            }
            $msg = array('status' => 'success', 'refid' => $refid);
            $this->response($this->json($msg), 200);
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
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
    public function addCustomerInfo()
    {
        $this->_request = json_decode(stripslashes($this->_request['formData']), true);
        $refId = $this->_request['refid'];
        $productData = $this->_request['productData'];
        $customerData = $this->_request['customerData'];
        $customerName = $customerData['name'];
        $email = $customerData['email'];
        $address = json_encode($customerData);
        $sql = "INSERT INTO " . TABLE_PREFIX . "customer_order_info (refid, customer_name, email, address, product_info, order_date) VALUES ('" . $refId . "','" . $customerName . "','" . $email . "','" . $address . "','" . $postProductData . "',now())";
        $orderrId = $this->executeGenericInsertQuery($sql);
        if ($orderrId) {
            $filepath = '';
            if (isset($_FILES) && $_FILES['customerFile']['name'] != '') {
                $orderPath = $this->getOrdersPath() . "/" . $orderrId; //copy to
                if (!is_dir($orderPath)) {
                    $mkDir = "";
                    $tags = explode('/', $orderPath);
                    foreach ($tags as $folder) {
                        $mkDir .= $folder . "/";
                        if (!file_exists($mkDir)) {
                            mkdir($mkDir, 0755, true);
                        }

                    }
                }
                if (is_uploaded_file($_FILES["customerFile"]["tmp_name"])) {
                    if (move_uploaded_file($_FILES["customerFile"]["tmp_name"], $orderPath . "/" . $_FILES['customerFile']['name'])) {
                        $filepath = $_FILES['customerFile']['name'];
                    }
                }
            }
            $createHtml = $this->createHtml($orderrId, $refId, $customerData, $productData, $filepath);
            $msg = array("status" => "success", "orderid" => $orderrId, "mail" => $createHtml);
        } else {
            $msg = array("status" => "failed", "sql" => $sql);
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
    public function createHtml($orderrId, $refId, $customerData, $productData, $filepath)
    {
        $orderId = $orderrId;
        $itemId = 1;
        $productname = $productData[0]['product_name'];
        $sku = $productData[0]['product_sku'];
        $productSize = $productData[0]['xe_size'];
        $customerName = $customerData['name'];
        $customerEmail = $customerData['email'];
        $quantity = $customerData['quantity'];
        $base64 = ($sku != '') ? $sku : $productname;
        $date = date("d/m/Y");
        $absPath = getcwd();
        $final = str_replace('\\', '/', $absPath);
        $final = $final . '/..';
        $langsFile = file_get_contents($final . "/localsettings.js");
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
        $langPath = $final . "/designer-app/languages/" . $langFile;
        $languageJson = file_get_contents($langPath);
        if (empty($languageJson)) {
            $languageJson = file_get_contents($final . "/designer-app/languages/locale-en.json");
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
        $html = '';
        $html = '<html><title>Order APP</title><style>body{background-color:#fff!important;margin:0;padding:0;font-size:18px;color:#333}.page-wrap{width:1024px;margin:auto}.topbar{margin-top:20px}.topbar .left{float:left;width:50%}.topbar .right{float:left}.topbar .right img{max-width:100%;}.topbar h2{margin:0;padding:0;text-transform:uppercase}.topbar p{margin:0 0 10px;padding:0;font-size:20px}.product-box{margin-top:20px;display:inline-block;clear:both;border:1px solid blue;}.product-box .leftbar{float:left;width:50%}.product-box .leftbar .header{background:green;padding:10px 20px;font-size:24px;color:#fff}.product-box .leftbar .product{padding:10px 20px;background:#fff;}.product-box .leftbar .product .product-stage{width:100%;height:auto;clear:both;padding:20px 0}.product-box .leftbar .product .barcode-img{margin-bottom:10px;display:inline-block}.product-box .leftbar .product .barcode-img img{max-width:100%;float:left}.product-box .leftbar .product .product-img{width:100%;height:auto}.product-box .rightbar{float:left;width:50%}.product-box .rightbar .header{padding:10px 20px;font-size:24px;color:#fff}.product-box .rightbar .product{padding:10px 20px;background:#fff;text-align:center}.product-box .rightbar .product .product-stage{width:100%;height:auto;clear:both;padding:20px 0}.product-box .rightbar .product .barcode-img{margin-bottom:10px;display:block}.product-box .rightbar .product .barcode-img img{max-width: 100%;float:left}.product-box .rightbar .product .product-img{width:100%;height:auto}.bold{font-weight:700} .p-r-15 { padding-right:15px; text-align:left;} .repeattd{color: #333;border-collapse: collapse;border-spacing: 0; width: 100%;}.repeattd td, th {border: 1px solid transparent;
		height: 30px; text-align: left;	border-bottom: 1px solid #ddd;	padding: 3px;} .repeattd th {background: #DFDFDF; font-weight: bold;}
		.repeattd tr:nth-child(even) td { background: #F1F1F1; } .repeattd tr:nth-child(odd) td { background: #FEFEFE; }
		.leftbar .clear { clear:both; }
		</style><body><div class="page-wrap"><div class="topbar">';
        $generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();
        $html .= '<div class="left"><h2>' . $customerName . '</h2><strong>' . $date . '</strong><p>'
        . $orderId . '</p></div><div class="right"><img src="data:image/png;base64,' . base64_encode($generatorPNG->getBarcode($orderId, $generatorPNG::TYPE_CODE_128)) . '" alt="" height="35px" /></div></div><div class="product-box">';
        $colorincrment = 1;
        $refPaths = $final . PREVIEW_IMAGE_DIR . "/" . $refId . "/svg";
        $itemPaths = $final . ORDER_PATH_DIR . "/" . $orderId . "/" . $itemId;
        $currentUrl = $this->getCurrentUrl(); //$currentUrl = substr($currentUrl,0,-1);
        $designPath = $currentUrl . "/designer-tool/custom-assets/orders/" . $orderId . "/" . $itemId . "/";
        $orderpath = $final . ORDER_PATH_DIR . "/" . $orderId;
        if ($refId != null) {
            if (!is_dir($itemPaths)) {
                $mkDir = "";
                $tags = explode('/', $itemPaths);
                foreach ($tags as $folder) {
                    $mkDir .= $folder . "/";
                    if (!file_exists($mkDir)) {
                        mkdir($mkDir, 0755, true);
                    }

                }
            }
            $dir_handle = @opendir($refPaths) or die("Unable to open");
            while ($file = readdir($dir_handle)) {
                if ($file != "." && $file != ".." && !is_dir("$refPaths/$file")) {
                    copy("$refPaths/$file", "$itemPaths/$file");
                }

            }
            closedir($dir_handle);
        }
        $designState_json = file_get_contents($refPaths . '/designState.json');
        $json_content = json_decode($designState_json, true);
        //For preview folder image file
        foreach ($json_content['sides'] as $k => $value) {
            if (isset($value['svg']) && !empty($value['svg'])) {
                $sideValue = $k + 1;
                $refPath = $final . PREVIEW_IMAGE_DIR . "/" . $refId . "/svg/preview_0" . $sideValue . ".svg";
                $itemPath = $itemPaths . "/side_" . $sideValue . "";
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
                if (file_exists($refPath)) {
                    copy($refPath, $itemPath . "/preview_0" . $sideValue . ".svg");
                }
                //For assets folder image file
                $assetPath = $final . PREVIEW_IMAGE_DIR . $ref_id . "/assets/" . $sideValue;
                $asesetItemPath = $itemPaths . "/side_" . $sideValue . "/assets/";
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
                $scanDir = scandir($assetPath);
                foreach ($scanDir as $k => $asset) {
                    if (file_exists($assetPath . "/" . $asset)) {
                        if ($asset != "." && $asset != "..") {
                            copy($assetPath . "/" . $asset, $asesetItemPath . "/" . $asset);
                        }
                    }
                }
                //for preview folder image file
                $customizeImage = file_get_contents($value['customizeImage']);
                $previewItemPath = $itemPaths . "/side_" . $sideValue . "/preview/";
                $pngFile = $itemPaths . "/side_" . $sideValue . "/preview/side_" . $sideValue . "_" . $item_id . "_" . $orderId . "_preview.png";
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
            }
        }
        //Check for repeated name and number
        if (in_array($ref_id, $temp)) {
            if (!empty($json_content['nameNumberData']) && $nameCount != 0) {
                continue;
            }
        }
        $product_barcode = base64_encode($generatorPNG->getBarcode($base64, $generatorPNG::TYPE_CODE_128));
        $temp[] = $ref_id;
        $noOfsides = count($json_content['sides']);
        $printColorNames = "";
        $printColors = "";
        $cmykValue = "";
        $printColorCategories = "";
        $k = 1;
        $odd = 1;
        $printType = (isset($json_content['printType']) && $json_content['printType'] != '') ? $json_content['printType'] : "No Printtype";
        $notes = (isset($json_content['notes']) && $json_content['notes'] != '') ? $json_content['notes'] : "";
        $browserIp = (isset($json_content['envInfo']) && $json_content['envInfo']['browserIp'] != '') ? $json_content['envInfo']['browserIp'] : "-";
        $browserHeight = (isset($json_content['envInfo']) && $json_content['envInfo']['browserHeight'] != '') ? $json_content['envInfo']['browserHeight'] : "-";
        $browserWidth = (isset($json_content['envInfo']) && $json_content['envInfo']['browserWidth'] != '') ? $json_content['envInfo']['browserWidth'] : "-";
        $browserLang = (isset($json_content['envInfo']) && $json_content['envInfo']['browserLang'] != '') ? $json_content['envInfo']['browserLang'] : "-";
        $userAgent = (isset($json_content['envInfo']) && $json_content['envInfo']['userAgent'] != '') ? $json_content['envInfo']['userAgent'] : "-";
        $browserName = (isset($json_content['envInfo']) && $json_content['envInfo']['browserName'] != '') ? $json_content['envInfo']['browserName'] : "-";
        $html .= '<div style="border: 2px solid #4CAF50; margin:0px auto 20px; border-radius:5px;  width:1200px;"><div style="background-color:#4CAF50; padding:15px;border-radius:2px 2px 0px 0px; font-size:20px; color:#fff;">' . $productname . '</div>
		<div style="padding:20px;"><table style="border: 1px solid #ccc; border-collapse: collapse; width: 100%;"><tbody><tr>';
        //Hide heading of size attribute and quantity for name and number case
        if (empty($json_content['nameNumberData'])) {
            $html .= ' <th style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $langQuantity . '</th><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $langSize . '</th>';
        }
        $html .= '<th style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $langColor . '</th><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $langPrintMethod . '</th></tr><tr>';
        //Hide value of size attribute and quantity for name and number case
        if (empty($json_content['nameNumberData'])) {
            $html .= '<td style = "border: 1px solid #ccc; border-collapse: collapse; padding: 8px; text-align: left; color: #333;">' . $quantity . '</td><td style = "border: 1px solid #ccc; border-collapse: collapse; padding: 8px; text-align: left; color: #333;">' . $productSize . '</td>';
        }
        $html .= '<td style = "border: 1px solid #ccc; border-collapse: collapse; padding: 8px; text-align: left; color: #333;">' . $productColor . '</td><td style = "border: 1px solid #ccc; border-collapse: collapse; padding: 8px; text-align: left; color: #333;">' . $printType . '</td></tr></tbody></table></div>';
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
            $pUrl = $sides['customizeImage'];
            $addClearDiv = ($odd % 2 == 0) ? '<div style="clear:both;"/> &nbsp; </div>' : '';
            $onesidewidth = ($noOfsides <= 1) ? 'width:93%;' : '';
            $barcodewidth = ($noOfsides <= 1) ? 'width:40%' : '';
            $printUnit = (isset($sides['printUnit']) && $sides['printUnit'] != '') ? $sides['printUnit'] : "No Unit";
            $dimension = $sides['PrintDimension']['boundwidth'] . 'x' . $sides['PrintDimension']['boundheight'];
            $html .= '<div style="padding: 0px 20px 20px 20px; width: 45%; text-align: center; float: left; background-color: #efefef; margin: 20px 0px 20px 13px;' . $onesidewidth . '"><div class="m-b-7 barcode">
			<img src="data:image/png;base64,' . $product_barcode . '" height="50px" alt="" style="' . $barcodewidth . '" /></div>
			<div style="margin-bottom:10px;"><h3>' . $skuwithname . '</h3></div><div style="margin-bottom:20px; min-height:500px; min-width:500px;"><img class="product-img" src="' . $pUrl . '" alt="" /> </div>';

            if (isset($sides['printSize']) && $sides['printSize'] != '') {
                $printValue = $sides['printSize'];
                if ($printValue[0] != 'A') {
                    $printSize = $dimension . ' (' . $printUnit . ')';
                } else {
                    $printSize = $sides['printSize'];
                    $printSize .= ': ' . $dimension . ' (' . $printUnit . ')';
                }
                $html .= '<div><h2 style="margin-bottom:10px;">' . $langPrintSize . ':  ' . $printSize . '</h2></div>';
            } else {
                $html .= '<div style="margin-bottom:10px;"> &nbsp; </div>';
            }

            $printColorNames = (isset($sides['printColorNames'])) ? count($sides['printColorNames']) : 0;
            $height = $printColorNames * 30;

            $html .= '<div style="height: 141px;overflow: auto;">';
            if ($printColorNames > 0) {
                $html .= '<table style="width: 100%; height:' . $height . 'px" class="repeattd t01"><tbody> <tr><th style="border: 1px solid #ccc; border-collapse: collapse; padding: 8px; text-align: left; background-color: #666; color: white;">' . $langColorName . '</th> <th style="border: 1px solid #ccc; border-collapse: collapse; padding: 8px; text-align: left; background-color: #666; color: white;">' . $langCategory . '</th><th style="border: 1px solid #ccc; border-collapse: collapse; padding: 8px; text-align: left; background-color: #666; color: white;">' . $langCmyk . '</th> <th style="border: 1px solid #ccc; border-collapse: collapse; padding: 8px; text-align: left; background-color: #666; color: white;">' . $langHex . '</th> </tr>';
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
                    $html .= '<tr> <td style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . str_replace('No Name', '-', $printcolornames) . '</td> <td style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . str_replace('No Category', '-', $printColorCategories[$y]) . '</td><td style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $cmykValue[$y] . '</td><td style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $printColors[$y] . '</td> </tr>';
                }
                $html .= '</tbody></table>'; //end tablecell div
            }
            $html .= '</tbody> </table></div></div>';
            $k++;
            $odd++;
        }
        $html .= '<div style="width: 1200px; margin:auto;"><h1 style="margin:0px; padding:0px; font-weight:normal;font-size: 20px;padding-bottom: 8px;">Environment Information :</h1><div style="clear:both;height:100px; background-color:; border-bottom:1px solid #ccc; margin-bottom: 20px;border-bottom: 0px;"><table style="width:100%;border: 1px solid #ccc; border-collapse: collapse;"><tbody><tr><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $langBRIP . '</th><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 6px;text-align: left; color:#333;">' . $langBRLan . '</th><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 6px;text-align: left; color:#333;">' . $langBRAgent . '</th><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 6px;text-align: left; color:#333;">' . $langBRName . '</th><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 6px;text-align: left; color:#333;">' . $langBRWd . '</th><th style="border: 1px solid #ccc; border-collapse: collapse;padding: 6px;text-align: left; color:#333;">' . $langBRHt . '</th></tr><tr><td style="border: 1px solid #ccc; border-collapse: collapse;padding: 6px;text-align: left; color:#333;">' . $browserIp . '</td><td style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $browserLang . '</td><td style="border: 1px solid #ccc; border-collapse: collapse;padding: 8px;text-align: left; color:#333;">' . $userAgent . '</td><td style="border: 1px solid #ccc; border-collapse: collapse;padding: 6px;text-align: left; color:#333;">' . $browserName . '</td><td style="border: 1px solid #ccc; border-collapse: collapse;padding: 6px;text-align: left; color:#333;">' . $browserWidth . 'px' . '</td><td style="border: 1px solid #ccc; border-collapse: collapse;padding: 6px;text-align: left; color:#333;">' . $browserHeight . 'px' . '</td></tr></tbody></table></div></div>';
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
        $orderFileName = $orderpath . "/order.json";
        $myfile = fopen($orderFileName, "w") or die("Unable to open file!");
        fwrite($myfile, json_encode($decodeResult));
        fclose($myfile);
        $html .= '</div></div></body></html>';
        $myFile = $orderpath . "/info.html"; // or .php
        $fh = fopen($myFile, 'w'); // or die("error");
        fwrite($fh, $html);
        return $msg = $this->emailSend($orderId, $customerName, $customerEmail, $filepath);
    }

    /**
     *Custom function to send mail for Request a Quote
     *
     *@param customer email, order id, customer name
     *
     * @return boolean true/false
     *
     */
    public function emailSend($orderId, $customerName, $customerEmail, $filepath)
    {
        $orderId = $orderId;
        $orderPath = $this->getOrdersPath() . "/" . $orderId . "/"; //copy to
        $my_file = "info.html";
        $my_path = $orderPath;
        $my_name = COMPANY_NAME;
        $my_mail = SENDER_EMAIL;
        $my_replyto = SENDER_EMAIL;
        $my_subject = SUBJECT;
        $my_message = "Hi " . $customerName . ",<br/>";
        $my_message .= "Request a quote successful. Thank you! <br/><br/>";
        $my_message .= "We deal with your message as quickly as possible . If you have not received a reply to the message within a few days , please check the spam folder and , if necessary, to contact again. <br/><br/>";
        $my_message .= "Regards,  <br/><br/>";
        $my_message .= "Customer service | <a href='http://inkxe.com/'>inkxe.com.sg</a> <br/><br/>";
        $my_message .= "Tel: (+65) 9000 0000 <br/><br/>";
        $my_message .= "Email: enquiry@inkxe.com <br/><br/>";
        $msg = $this->mail_attachment($my_file, $my_path, $customerEmail, $my_mail, $my_name, $my_subject, $my_message, $my_replyto);
        if ($msg == 'send') {
            $message = "Hi ,<br/>";
            $message .= $customerName . " leave a quote request .<br/><br/>";
            $subject = "Request a Quote inkxe.com designer tool.";
            if ($filepath != '') {
                $msg = $this->mail_attachment($filepath, $my_path, SENDER_EMAIL, $customerEmail, $customerName, $subject, $message, $customerEmail);
                return $msg;
            } else {
                $mailHeaders = "From: " . $customerName . "<" . $customerEmail . "> \r\n";
                $mailHeaders .= "Reply-To: " . $customerEmail . "\r\n";
                $mailHeaders .= "Content-type: text/html; charset: utf8\r\n";
                $mailHeaders .= "MIME-Version: 1.0 ";
                $mail = mail(SENDER_EMAIL, $subject, $message, $mailHeaders);
                if ($mail) {
                    return $msg = 'send'; // or use booleans here
                } else {
                    return $msg = 'failed' . "-3";
                }
            }

        } else {
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
    private function mail_attachment($filename, $path, $mailto, $from_mail, $from_name, $subject, $body, $replyto)
    {
        $file = $path . $filename;
        $file_size = filesize($file);
        $handle = fopen($file, "r");
        $content = fread($handle, $file_size);
        fclose($handle);
        $content = chunk_split(base64_encode($content));
        $uid = md5(uniqid(time()));
        $eol = PHP_EOL;
        $header = "From: " . $from_name . " <" . $from_mail . ">" . $eol;
        $header .= "Reply-To: " . $replyto . "\r\n";
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-Type: multipart/mixed; boundary=\"" . $uid . "\"\r\n";
        $header .= "This is a multi-part message in MIME format.\r\n";
        $message = "--" . $uid . $eol;
        $message .= "Content-type: text/html; charset: utf8\r\n" . $eol;
        $message .= $body . $eol;
        $message .= "--" . $uid . $eol;
        $message .= "Content-Type: application/pdf; name=\"" . $filename . "\"" . $eol; // use different content types here
        $message .= "Content-Transfer-Encoding: base64" . $eol;
        $message .= "Content-Disposition: attachment; filename=\"" . $filename . "\"" . $eol;
        $message .= $content . $eol;
        $message .= "--" . $uid . "--";
        error_reporting(0);
        if (mail($mailto, $subject, $message, $header)) {
            return $msg = 'send'; // or use booleans here
        } else {
            return $msg = 'failed';
        }
    }
}
