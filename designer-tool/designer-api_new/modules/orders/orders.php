<?php
class Orders extends OrdersStore
{

    /**
     *
     * date 28th_Apr-2016
     * generate design svg files and download in zip orderwise
     *
     * @param (String)order_id (request method)
     * @return zip file
     *
     */
    public function downloadOrderZipAdmin()
    {
        $msg            = '';
        $order_id2      = (isset($this->_request['order_id'])) ? $this->_request['order_id'] : 0;
        $orderIncIdList = (isset($this->_request['increment_id'])) ? $this->_request['increment_id'] : 0;
        $orderPath      = $this->getOrdersPath();

        $order_id_arr    = explode(',', $order_id2);
        $orderIncIdArray = explode(',', $orderIncIdList);
        if (count($order_id_arr) > 0) {
            $zipName = 'orders.zip';
            $zip     = new \ZipArchive;
            $res     = $zip->open($orderPath . '/' . $zipName, ZipArchive::CREATE);
            // check if zip file created //
            if ($res === true) {
                $zipCheckKounter = 0;
                $loopKounter     = 0;
                foreach ($orderIncIdArray as $increment_id) {
                    ////////////////////////////
                    //// CREATE SVG FILES //////
                    ////////////////////////////

                    //echo $order_id; echo "<br/>";
                    if ($increment_id != "" && $increment_id != 0) {
                        // fetch order_id or incremental_id //
                        if (file_exists($orderPath . "/" . $increment_id) && is_dir($orderPath . "/" . $increment_id)) {
                            $orderFolderPath = $orderPath . "/" . $increment_id; // increment_id //
                            $orderTypeFlag   = 1;
                            $order_id        = $increment_id;
                        } else {
                            $order_id        = $order_id_arr[$loopKounter]; // order_id //
                            $orderFolderPath = $orderPath . "/" . $order_id;
                            $orderTypeFlag   = 0;
                        }
                        //echo $orderTypeFlag; exit;

                        if (file_exists($orderFolderPath) && is_dir($orderFolderPath)) {
                            //// Create SVG files for the Order /////
                            $scanProductDir = scandir($orderFolderPath); // scan directory to fetch all items folder //
                            if (file_exists($orderFolderPath . '/order.json')) {
                                $order_json   = file_get_contents($orderFolderPath . '/order.json');
                                $json_content = $this->formatJSONToArray($order_json);
                                foreach ($json_content['order_details']['order_items'] as $item_details) {
                                    $item_id           = $item_details['item_id'];
                                    $sizeArr[$item_id] = $item_details['xe_size'];
                                }
                            }
                            if (is_array($scanProductDir)) {
                                // check the item folders under the product folder //
                                foreach ($scanProductDir as $itemDir) {
                                    if ($itemDir != '.' && $itemDir != '..' && is_dir($orderFolderPath . "/" . $itemDir)) {
                                        //to fetch only item id folders//

                                        //Fetch the design state json details //
                                        $designState  = file_get_contents(XEPATH . "designer-tool" . ORDER_PATH_DIR . "/" . $order_id . "/" . $itemDir . "/designState.json");
                                        $resultDesign = $this->formatJSONToArray($designState);

                                        // check if side_index folder exists or not //
                                        $sidePath      = $orderFolderPath . "/" . $itemDir;
                                        $scanSidePath  = scandir($sidePath);
                                        $scanSideDir   = $scanSidePath;
                                        $orderTypeFlag = 0;
                                        if (is_array($scanSideDir)) {
                                            foreach ($scanSideDir as $sidecheckPath) {
                                                if (strpos($sidecheckPath, "side_") !== false) {
                                                    $orderTypeFlag = 1;
                                                    continue;
                                                }
                                            }
                                        }
                                        //echo $orderTypeFlag;  echo $order_id; exit;

                                        // for new file structure //
                                        if ($orderTypeFlag == 1) {
                                            //check and find the sides of each item //
                                            $sidePath = $orderFolderPath . "/" . $itemDir;
                                            if (file_exists($sidePath) && is_dir($sidePath)) {
                                                $scanSideDir     = scandir($sidePath); // scan item directory to fetch all side folders //
                                                $scanSideDirSide = $scanSideDir;
                                                //print_r($scanSideDir);
                                                if (is_array($scanSideDir)) {
                                                    foreach ($scanSideDir as $sideDir) {
                                                        if ($sideDir != '.' && $sideDir != '..' && is_dir($orderFolderPath . "/" . $itemDir . "/" . $sideDir)) {
                                                            //to fetch only side folders//
                                                            $i = str_replace("side_", "", $sideDir);
                                                            //echo $orderFolderPath."/".$itemDir."/".$sideDir."/preview_0".$i.".svg"; exit;
                                                            if (file_exists($orderFolderPath . "/" . $itemDir . "/" . $sideDir . "/preview_0" . $i . ".svg")) {
                                                                // with product svg file exists or not//

                                                                if (!file_exists($orderFolderPath . "/" . $itemDir . "/" . $sideDir . "/" . $sideDir . "_" . $itemDir . "_" . $order_id . ".svg")) {
                                                                    /* check if without product svg file exists or not.
                                                                    if not exist, then create the file
                                                                     */
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
                                        // for old file structure //
                                        else if ($orderTypeFlag == 0) {
                                            //to fetch only item id folders//
                                            // fetch all with product svg files //
                                            $kounter = 1;
                                            for ($i = 1; $i <= 15; $i++) {
                                                if (file_exists($orderFolderPath . "/" . $itemDir . "/preview_0" . $i . ".svg")) {
// with product svg file exists or not//
                                                    if (!file_exists($orderFolderPath . "/" . $itemDir . "/" . $i . ".svg")) {
                                                        /* check if without product svg file exists or not.
                                                        if not exist, then create the file
                                                         */
                                                        $reqSvgFile = XEPATH . "designer-tool" . ORDER_PATH_DIR . "/" . $order_id . "/" . $itemDir . "/preview_0" . $i . ".svg";
                                                        $item_id    = $itemDir;
                                                        //check name and number exit or not
                                                        $designState  = file_get_contents(XEPATH . "designer-tool" . ORDER_PATH_DIR . "/" . $order_id . "/" . $item_id . "/designState.json");
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

                    $tarray         = array(" ", "\n", "\r");
                    $order_list_arr = array();
                    $temp_arry212   = array();
                    $orderNo        = $order_id;

                    if (file_exists($orderFolderPath)) {
                        if (file_exists($orderFolderPath . '/order.json')) {
                            $order_json   = file_get_contents($orderFolderPath . '/order.json');
                            $json_content = $this->formatJSONToArray($order_json);
                            $noOfRefIds   = count($json_content['order_details']['order_items']);
                            if ($noOfRefIds > 0) {
                                $zip->addEmptyDir($orderNo);
                                $zip->addFile($orderFolderPath . '/order.json', $orderNo . '/order.json');

                                // add info.html file //
                                if (file_exists($orderFolderPath . '/info.html')) {
                                    $zip->addFile($orderFolderPath . '/info.html', $orderNo . '/info.html');
                                }

                                $item_kounter = 1;
                                foreach ($json_content['order_details']['order_items'] as $item_details) {
                                    $item_id    = $item_details['item_id'];
                                    $ref_id     = $item_details['ref_id'];
                                    $scanDirArr = scandir($orderFolderPath . "/" . $item_id); //for name and number folder scan
                                    if ($item_id != null && $item_id > 0 && $ref_id != null && $ref_id > 0) {
                                        if ($orderTypeFlag == 1) {
                                            // add side folders inside item directory //
                                            //Fetch the design state json details //
                                            $designState  = file_get_contents(XEPATH . "designer-tool" . ORDER_PATH_DIR . "/" . $orderNo . "/" . $item_id . "/designState.json");
                                            $resultDesign = $this->formatJSONToArray($designState);
                                            //echo "<pre>"; print_r($resultDesign['sides']);
                                            $sidesCount = count($resultDesign['sides']);
                                            for ($flag = 1; $flag <= $sidesCount; $flag++) {
                                                if (is_dir($orderFolderPath . "/" . $item_id . "/side_" . $flag)) {
                                                    $zip->addEmptyDir($orderNo . "/" . $item_id . "/side_" . $flag);
                                                    $scanDirArr = scandir($orderFolderPath . "/" . $item_id . "/side_" . $flag);
                                                }
                                                if (count($scanDirArr) > 2) {
                                                    //for name and number folder scan
                                                    foreach ($scanDirArr as $nameAndNumberDir) {
                                                        if ($nameAndNumberDir != '.' && $nameAndNumberDir != '..' && is_dir($orderFolderPath . "/" . $item_id . "/side_" . $flag . '/' . $nameAndNumberDir)) {
                                                            $zip->addEmptyDir($orderNo . "/" . $item_id . "/side_" . $flag);
                                                            $from_url = $orderFolderPath . "/" . $item_id . "/side_" . $flag . '/' . $nameAndNumberDir;
                                                            $options  = array('add_path' => $orderNo . "/" . $item_id . "/side_" . $flag . '/' . $nameAndNumberDir . "/", 'remove_path' => $from_url);
                                                            $zip->addGlob($from_url . '/*{svg}', GLOB_BRACE, $options);
                                                        }

                                                    }
                                                }
                                                //copy to side folder //
                                                $fromUrlSide = $orderFolderPath . "/" . $item_id . "/side_" . $flag;
                                                $optionsSide = array('add_path' => $orderNo . "/" . $item_id . "/side_" . $flag . "/", 'remove_path' => $fromUrlSide);
                                                $zip->addGlob($fromUrlSide . '/*{svg,json,html,pdf,png,jpg}', GLOB_BRACE, $optionsSide);

                                                //copy to asset folder //
                                                if (is_dir($orderFolderPath . "/" . $item_id . "/side_" . $flag . "/assets")) {
                                                    $zip->addEmptyDir($orderNo . "/" . $item_id . "/side_" . $flag . "/assets");

                                                    $fromUrlAsset = $orderFolderPath . "/" . $item_id . "/side_" . $flag . "/assets";
                                                    $optionsAsset = array('add_path' => $orderNo . "/" . $item_id . "/side_" . $flag . "/assets/", 'remove_path' => $fromUrlAsset);
                                                    $zip->addGlob($fromUrlAsset . '/*{svg,json,html,pdf,png,jpg,jpeg,PNG,bmp,BMP}', GLOB_BRACE, $optionsAsset);
                                                }

                                                //copy to preview folder //
                                                if (is_dir($orderFolderPath . "/" . $item_id . "/side_" . $flag . "/preview")) {
                                                    $zip->addEmptyDir($orderNo . "/" . $item_id . "/side_" . $flag . "/preview");
                                                    $fromUrlPreview = $orderFolderPath . "/" . $item_id . "/side_" . $flag . "/preview";

                                                    $optionsPreview = array('add_path' => $orderNo . "/" . $item_id . "/side_" . $flag . "/preview/", 'remove_path' => $fromUrlPreview);
                                                    $zip->addGlob($fromUrlPreview . '/*{png,PNG}', GLOB_BRACE, $optionsPreview);
                                                }

                                                //delete preview svg from zip //
                                                $zip->deleteName($orderNo . "/" . $item_id . "/side_" . $flag . "/preview_0" . $flag . ".svg");
                                            }

                                            $from_url = $orderFolderPath . "/" . $item_id;
                                            $options  = array('add_path' => $orderNo . "/" . $item_id . "/", 'remove_path' => $from_url);
                                            $zip->addGlob($from_url . '/*{svg,json,html,pdf,png,jpg}', GLOB_BRACE, $options);
                                            $zipCheckKounter++;
                                        } else if ($orderTypeFlag == 0) {
                                            $scanDirArr = scandir($orderFolderPath . "/" . $item_id); //for name and number folder scan
                                            if (count($scanDirArr) > 2) {
//for name and number folder scan
                                                foreach ($scanDirArr as $nameAndNumberDir) {
                                                    if ($nameAndNumberDir != '.' && $nameAndNumberDir != '..' && is_dir($orderFolderPath . "/" . $item_id . "/" . $nameAndNumberDir)) {
                                                        $zip->addEmptyDir($orderNo . "/" . $item_id . "/" . $nameAndNumberDir);
                                                        $from_url = $orderFolderPath . "/" . $item_id . "/" . $nameAndNumberDir;
                                                        $options  = array('add_path' => $orderNo . "/" . $item_id . "/" . $nameAndNumberDir . "/", 'remove_path' => $from_url);
                                                        $zip->addGlob($from_url . '/*{svg}', GLOB_BRACE, $options);
                                                    }
                                                }
                                            } //end for name and number zip download
                                            $zip->addEmptyDir($orderNo . "/" . $item_id);
                                            $from_url = $orderFolderPath . "/" . $item_id;
                                            $options  = array('add_path' => $orderNo . "/" . $item_id . "/", 'remove_path' => $from_url);
                                            $zip->addGlob($from_url . '/*{svg,json,html,pdf,png,jpg}', GLOB_BRACE, $options);
                                            $zipCheckKounter++;
                                        }
                                    }
                                    $item_kounter++;
                                }
                            }
                        }
                    }
                    $loopKounter++;
                }
                $zip->close();
                //echo $orderPath.'/'.$zipName; exit;
                $this->zipDownload($orderPath . '/' . $zipName, $zipCheckKounter);
            } else {
                $msg = 'Zip Creation Failed';
            }
        } else {
            $msg = 'Order not found to download';
        }
        $response = array("Response" => $msg);
        $this->response($this->json($response), 200);
    }

    /**
     *
     *date of created 20-5-2016(dd-mm-yy)
     *date of Modified (dd-mm-yy)
     *to sync Orders fro Order App
     *
     * @return response
     *
     */
    public function syncOrderAppZip()
    {
        if (isset($_FILES['Filedata']['name']) && $_FILES['Filedata']['name'] != '') {
            $zipFileName = basename($_FILES['Filedata']['name']);
            $extn        = explode(".", $zipFileName);
            $extn        = $extn[count($extn) - 1];
            $zipFile     = str_replace('.' . $extn, '', $zipFileName); //file name without extension //
            $copy_status = 0;
            if ($extn == 'zip') {
                $tempDir = $this->getOrdersPath() . '/temp_' . $zipFile;
                if (!file_exists($tempDir)) {
                    mkdir($tempDir, 0777, true);
                }
                $tmp               = $_FILES['Filedata']['tmp_name'];
                $status            = move_uploaded_file($tmp, $tempDir . '/' . $zipFileName);
                $zip_upload_status = ($status) ? 1 : 0;
                if ($zip_upload_status == 1) {
                    $zip = new \ZipArchive;
                    $res = $zip->open($tempDir . '/' . $zipFileName);
                    if ($res === true) {
                        $zip->extractTo($tempDir . '/');
                        if (file_exists($tempDir . '/' . $zipFileName)) {
                            unlink($tempDir . '/' . $zipFileName);
                        }
                        if (file_exists($tempDir)) {
                            $scanDir = scandir($tempDir);
                            if (count($scanDir) > 2) {
                                $oderList   = '';
                                $ord_detxx  = '';
                                $flag       = '';
                                $dirKounter = 0;
                                foreach ($scanDir as $scandir) {
                                    if ($dirKounter >= 2) {
                                        if (false !== ($file = is_dir($tempDir . '/' . $scandir))) {
                                            $orderNo = $scandir;
                                            $oderList .= $orderNo . ',';
                                            $order_folder = $this->getOrdersPath() . '/' . $scandir;
                                            if (file_exists($order_folder)) {
                                                if (file_exists($order_folder . '/order.json')) {
                                                    $order_json           = file_get_contents($order_folder . '/order.json');
                                                    $json_content         = $this->formatJSONToArray($order_json);
                                                    $total_items_in_order = count($json_content['order_details']['order_items']);
                                                    if ($total_items_in_order > 0) {
                                                        $kk    = 0;
                                                        $kkNew = 0;
                                                        foreach ($json_content['order_details']['order_items'] as $item) {
                                                            $kkNew   = $kkNew + 1;
                                                            $iNew    = 0;
                                                            $color   = ($item['xe_color'] != '') ? $item['xe_color'] : 'none';
                                                            $size    = $item['xe_size'];
                                                            $ref_id  = $item['ref_id'];
                                                            $item_id = $item['item_id'];
                                                            if (file_exists($order_folder . '/' . $item_id)) {
                                                                for ($i = 1; $i <= 15; $i++) {
                                                                    // Copy png Files //
                                                                    $temp_png_file_path = $tempDir . '/' . $scandir . '/' . $item_id . '/' . $i . '.png';
                                                                    $new_png_file_path  = $order_folder . '/' . $item_id . '/' . $i . '.png';
                                                                    if (file_exists($temp_png_file_path)) {
                                                                        if (copy($temp_png_file_path, $new_png_file_path)) {
                                                                            $copy_status = 1;
                                                                        }
                                                                    }
                                                                    // Copy pdf Files //
                                                                    $temp_pdf_file_path = $tempDir . '/' . $scandir . '/' . $item_id . '/' . $i . '.pdf';
                                                                    $new_pdf_file_path  = $order_folder . '/' . $item_id . '/' . $i . '.pdf';
                                                                    if (file_exists($temp_pdf_file_path)) {
                                                                        if (copy($temp_pdf_file_path, $new_pdf_file_path)) {
                                                                            $copy_status = 1;
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            } else {
                                                $msg = 'Can\'t fetch Order Details';
                                            }
                                        } else {
                                            $msg = 'Directory is blank';
                                        }
                                    }
                                    $dirKounter++;
                                }
                            } else {
                                $msg = 'Zip Extract Fialed';
                            }
                        } else {
                            $msg = 'Can\'t open Zip file';
                        }
                        $zip->close();
                    } else {
                        $msg = 'Zip file can\'t be uploaded';
                    }
                    $this->deleteZipFileFolder($tempDir);
                } else {
                    $msg = 'Invalid File, Error:' . $_FILES['Filedata']['error'] . ' order zip path = ' . $this->getOrdersPath() . '/' . $zipFileName . ', ======temp_path=' . $tmp . ' ====path=' . $tempDir . '/' . $zipFileName;
                }
            } else {
                $msg = 'Upload a zip file';
            }
        } else {
            $msg = 'No zip file found';
        }
        $msg2 = array("Response" => $msg);
        $this->log('syncZipFile: ' . $msg, true, 'syncZipFile.log');
        $this->response($this->json($msg2), 200);
    }

    /**
     *
     *date 15th_Apr-2016
     *creates svg file without product
     *
     * @param (String)reqSvgFile
     * @param (int)order_id
     * @param (int)item_id
     * @return blank
     *
     */
   public function createWithoutProductSvg($reqSvgFile, $order_id, $item_id, $resultDesign, $folderStr = 1)
    {
        $unit = $resultDesign['sides'][0]['printUnit'];
        //Sart to check for svg output in storesettings.json file
        $storesettings['enable_boundary_clip'] = '';
        $storesettingsPath                     = XEPATH . 'designer-tool/storesettings.json';
        $newData                               = file_get_contents($storesettingsPath);
        $pos                                   = strpos($newData, '"');
        $newData                               = substr($newData, $pos);
        $newData                               = '{' . str_replace(';', '', $newData);
        $storesettings                         = $this->formatJSONToArray($newData);
        //end
        $default_print_size = array('A1', 'A2', 'A3', 'A4', 'A5', 'A6', 'A7', 'A8');
        $status             = 'failed';
        $clipPathFileName   = '_clip_off.svg';
        if ($reqSvgFile != '') {
            $resSvgFile = $reqSvgFile;
            $req        = @file_get_contents($reqSvgFile);
            $req        = str_ireplace('data: png', 'data:image/png', $req);
            $req        = str_ireplace('data: jpg', 'data:image/jpg', $req);
            if ($req) {
                $pathWithFile  = str_replace(XEPATH, '', $reqSvgFile);
                $sideArr       = explode("/", $pathWithFile);
                $sideId        = str_replace("preview_0", "", str_replace(".svg", "", $sideArr[count($sideArr) - 1]));
                $fileName      = "side_" . $sideId . "_" . $item_id . "_" . $order_id . ".svg";
                $fileNameMulti = "multi_side_" . $sideId . "_" . $item_id . "_" . $order_id . ".svg";
                $baseImagePath = $this->getOrdersPath();
                $savePath      = $baseImagePath . '/' . $order_id . '/' . $item_id . '/side_' . $sideId . '/';
                $svgSavePath   = $savePath;

                $resultStr = $req;
                $html      = new simple_html_dom();
                $html->load($resultStr, false);
                $count = substr_count($html, 'layer_area');
                if ($count > 1) {
                    $this->createMultipleBoundarySvg($html, $svgSavePath, $fileNameMulti);
                }

                $baseImagePath = $this->getOrdersPath();

                if ($folderStr == 1) {
                    $fileName  = "side_" . $sideId . "_" . $item_id . "_" . $order_id . ".svg";
                    $savePath  = $baseImagePath . '/' . $order_id . '/' . $item_id . '/side_' . $sideId . '/';
                    $fileNames = "side_" . $sideId . "_" . $item_id . "_" . $order_id . $clipPathFileName;
                } else {
                    $fileName  = $sideId . ".svg";
                    $savePath  = $baseImagePath . '/' . $order_id . '/' . $item_id . '/';
                    $fileNames = $sideId . $clipPathFileName;
                }
                $svgSavePath = $savePath;

                $resStr = $req;
                $html   = new simple_html_dom();
                $html->load($resStr, false);
                $g = $html->find('g#productBgColor', 0);
                if ($g) {
                    $g->outertext = '';
                }
                $layer_background = $html->find('g#layer_background', 0);
                if (isset($layer_background) && $layer_background != '') {
                    $bleedM                = $layer_background->find('g#bleedM', 0);
                    $cropM                 = $bleedM->find('rect#cropM', 0);
                    $reactDisplay          = $cropM->isbleed;
                    $hAlignTleft           = $bleedM->find('rect#hAlignTleft', 0);
                    $reactDisplayOn        = $hAlignTleft->display;
                    $hAlignTleft->display  = 'block';
                    $hAlignBleft           = $bleedM->find('rect#hAlignBleft', 0);
                    $hAlignBleft->display  = 'block';
                    $vAlignBleft           = $bleedM->find('rect#vAlignBleft', 0);
                    $vAlignBleft->display  = 'block';
                    $vAlignTleft           = $bleedM->find('rect#vAlignTleft', 0);
                    $vAlignTleft->display  = 'block';
                    $hAlignTright          = $bleedM->find('rect#hAlignTright', 0);
                    $hAlignTright->display = 'block';
                    $hAlignBright          = $bleedM->find('rect#hAlignBright', 0);
                    $hAlignBright->display = 'block';
                    $vAlignTright          = $bleedM->find('rect#vAlignTright', 0);
                    $vAlignTright->display = 'block';
                    $vAlignBright          = $bleedM->find('rect#vAlignBright', 0);
                    $vAlignBright->display = 'block';
                    $vAlignBrightDisplay   = $vAlignBright->display;
                    $vAlignWidth           = $vAlignBright->width;
                    $vAlignHeight          = $vAlignBright->height;
                    $bleedMarkMaxValue     = max($vAlignWidth, $vAlignHeight);
                    $vAlignWidthInch       = (2 * $bleedMarkMaxValue) / 90;

                    $cropMMask        = $bleedM->find('path#cropMMask', 0);//For bleedmark 
                    $cropMMaskDisplay = $cropMMask->display;
                    if ($cropMMaskDisplay == 'block' || $reactDisplay == 'true') {
                        $layer_backgrounds           = $layer_background->outertext;
                        $layer_stage                 = $html->find('g#layer_stage', 0);
						if(isset($layer_stage) && !empty($layer_stage)){
							$layer_stages                = $layer_stage->outertext;
							$layer_stage->outertext      = $layer_backgrounds;
							$layer_background->outertext = $layer_stages;
						}
						$layer_area = $html->find("g[id^=layer_area_]");
						if(isset($layer_area) && !empty($layer_area)){
							foreach ($layer_area as $k => $g) {
								$id       = $layer_area[$k]->id;
								if ($id == "layer_area_" . $k . "") {
									$layer_backgrounds           = $layer_background->outertext;
									$layer_stages                = $layer_area[$k]->outertext;
									$layer_area[$k]->outertext      = $layer_backgrounds;
									$layer_background->outertext = $layer_stages;
								}
							}
						}
					}
                }
                $html->save();
                $html2 = new simple_html_dom();
                $html2->load($html, false);
                $svg = $html2->find('image#svg_1', 0);
                if ($svg) {
                    $g = $html2->find('g.mainContent', 0);
                    if ($g) {

                        $rectPath = $html2->find('rect#boundRectangle', 0);
                        if ($rectPath) {
                            $x       = $rectPath->x;
                            $y       = $rectPath->y;
                            $width   = $rectPath->width;
                            $height  = $rectPath->height;
                            $aHeight = $rectPath->aHeight;
                            $aWidth  = $rectPath->aWidth;
                            $svgroot = $html2->find('svg#svgroot', 0);
                            foreach ($resultDesign['sides'] as $k => $v) {
                                if (in_array($v['printSize'], $default_print_size)) {
                                    if (intval($height) > intval($width)) {
                                        $temp    = '';
                                        $temp    = $aHeight;
                                        $aHeight = $aWidth;
                                        $aWidth  = $temp;
                                        if (intval($aWidth) > intval($aHeight)) {
                                            $temp    = '';
                                            $temp    = $aHeight;
                                            $aHeight = $aWidth;
                                            $aWidth  = $temp;
                                        }
                                    }
                                }
                            }
                            if ($aHeight == 'null' || $aHeight == 0) {
                                $svgroot->width  = 500;
                                $svgroot->height = 500;
                                $svg->outertext  = '<g id="rect" transform="scale(1) translate(0,0)">';
                            } else {
                                if (intval($height) < intval($width)) {
                                    if (intval($aWidth) < intval($aHeight)) {
                                        $temp    = '';
                                        $temp    = $aHeight;
                                        $aHeight = $aWidth;
                                        $aWidth  = $temp;
                                    }
                                } else if (intval($height) > intval($width)) {
                                    if (intval($aWidth) > intval($aHeight)) {
                                        $temp    = '';
                                        $temp    = $aHeight;
                                        $aHeight = $aWidth;
                                        $aWidth  = $temp;
                                    }
                                }
                                $aWidth  = $this->convertAllToInch($unit, $aWidth);
                                $aHeight = $this->convertAllToInch($unit, $aHeight);
                                if ($cropMMaskDisplay == 'block' || $reactDisplay == 'true') {
                                    $x               = -$x + $bleedMarkMaxValue;
                                    $y               = -$y + $bleedMarkMaxValue;
                                    $svgroot->width  = $aWidth + $vAlignWidthInch . 'in';
                                    $svgroot->height = $aHeight + $vAlignWidthInch . 'in';
                                    $height          = $height + ($bleedMarkMaxValue * 2);
                                    $acHeight        = $aHeight * 90;
                                    $acHeight        = $acHeight / $height;
                                    $svg->outertext  = '<g id="rect" transform="scale(' . $acHeight . ') translate( ' . $x . ', ' . $y . ')">';
                                } else {
                                    $svgroot->width  = $aWidth . 'in';
                                    $svgroot->height = $aHeight . 'in';
                                    $acHeight        = $aHeight * 90;
                                    $acHeight        = $acHeight / $height;
                                    $svg->outertext  = '<g id="rect" transform="scale(' . $acHeight . ') translate(-' . $x . ',-' . $y . ')">';
                                }
                            }
                            $html2->save();
                            $html2 = str_replace('</svg>', '', $html2);
                            $data         = '</g></svg>';
                            $html2        = $html2 . $data;
                            $html2        = str_replace("'", '"', $html2);
                            if ($storesettings['enable_boundary_clip'] == 0 || $storesettings['enable_boundary_clip'] == '' || $storesettings['enable_boundary_clip'] == 2) {
                                if (!file_exists($svgSavePath)) {
                                    mkdir($svgSavePath, 0777, true);
                                    chmod($svgSavePath, 0777);
                                }

                                $svgFilePath   = $svgSavePath . $fileName;
                                $svgFileStatus = file_put_contents($svgFilePath, $html2);
                            }
                            //for clipPath separated
                            if ($storesettings['enable_boundary_clip'] == 1 || $storesettings['enable_boundary_clip'] == 2) {
                                $svgFilePaths = $svgSavePath . $fileNames;
                                $html5        = new simple_html_dom();
                                $html5->load($html2, false);
                                $svgroots            = $html5->find('svg#svgroot', 0);
                                $acHeight            = $aHeight * 90;
                                $acHeight            = $acHeight / $height;
                                $svgroots->width     = ($acHeight * 500) / 90 . 'in';
                                $svgroots->height    = ($acHeight * 500) / 90 . 'in';
                                $gRect               = $html5->find('g#rect', 0);
                                $gRect->transform    = 'scale(' . $acHeight . ') translate(0,0)';
                                $clipPath            = $html5->find('clipPath.boundryClipPath', 0);
                                $clipPath->outertext = '';
                                $svgFileStatus       = file_put_contents($svgFilePaths, $html5);
                            }
                        } else {
                            $html2->save();
                            $html2 = str_replace('</svg>', '', $html2);
                            $html2 = explode(" ", $html2);
                            $html2 = implode(" ", $html2);
                            $htmlN = new simple_html_dom();
                            $htmlN->load($html2, false);
                            $svgMainContent = $htmlN->find('g[class]', 0);
                            $htmlN->save();
                            $path = $htmlN->find('path#boundMask', 0); //find mask actual height and width
							if(isset($path) && !empty($path)){
								$x1   = $path->aX;
								$y1   = $path->aY;
								$x    = $path->x;
								$y    = $path->y;
								if (!$x1 || !isset($x1) || ($x1 == null)) {
									$x1 = $x;
									$y1 = $y;
								}
								if (($x1 < 0) || ($y1 < 0)) {
									$svgroot->width  = 500;
									$svgroot->height = 500;
									$acHeight        = 1;
									$x1              = 0;
									$y1              = 0;
								} else {
									$width   = $path->width;
									$height  = $path->height;
									$aHeight = $path->aHeight;
									$aWidth  = $path->aWidth;
									if (intval($aWidth) != 0 || intval($aHeight) != 0) {
										$svgroot         = $htmlN->find('svg#svgroot', 0);
										$aWidth          = $this->convertAllToInch($unit, $aWidth);
										$aHeight         = $this->convertAllToInch($unit, $aHeight);
										$svgroot->width  = $aWidth . 'in';
										$svgroot->height = $aHeight . 'in';
										$acHeight        = $aHeight * 90;
										$acHeight        = $acHeight / $height;
										$acWidth        = $aWidth * 90;
										$acWidth        = $acWidth / $width;
									} else {
										$svgroot         = $htmlN->find('svg#svgroot', 0);
										$svgroot->width  = 500;
										$svgroot->height = 500;
										$acHeight        = 1;
									}
								}
							}else{
								$svgroot->width  = 500;//For multiple boundary
								$svgroot->height = 500;
								$acHeight        = 1;
								$acWidth         = 1;
								$x1              = 0;
								$y1              = 0;
							}
							$svgs = $htmlN->find('image#svg_1', 0);
                            if($acHeight != $acWidth){
                                $svgs->outertext = '<g id="rect" transform="scale(' . $acWidth . ','.$acHeight.') translate(-' . $x1 . ',-' . $y1 . ')">';
                            }else{
								$svgs->outertext = '<g id="rect" transform="scale(' . $acHeight . ') translate(-' . $x1 . ',-' . $y1 . ')">';
                            }
							$data  = '</g></svg>';
							$htmlN = $htmlN . $data;
							$htmlN = str_replace("'", '"', $htmlN);
							if ($storesettings['enable_boundary_clip'] == 0 || $storesettings['enable_boundary_clip'] == '' || $storesettings['enable_boundary_clip'] == 2) {
								if (!file_exists($svgSavePath)) {
									mkdir($svgSavePath, 0777, true);
									chmod($svgSavePath, 0777);
								}

								$svgFilePath   = $svgSavePath . $fileName;
								$svgFileStatus = file_put_contents($svgFilePath, $htmlN);
							}
							//for clipPath separated
							if ($storesettings['enable_boundary_clip'] == 1 || $storesettings['enable_boundary_clip'] == 2) {
								$svgFilePaths = $svgSavePath . $fileNames;
								$htmlNs       = new simple_html_dom();
								$htmlNs->load($htmlN, false);
								$svgroots            = $htmlNs->find('svg#svgroot', 0);
								if (intval($aWidth) != 0 || intval($aHeight) != 0) {
									$acHeight            = $aHeight * 90;
									$acHeight            = $acHeight / $height;
									$svgroots->width     = ($acHeight * 500) / 90 . 'in';
									$svgroots->height    = ($acHeight * 500) / 90 . 'in';
								} else {
                                    $svgroots->width  = 500;
                                    $svgroots->height = 500;
                                    $acHeight        = 1;
                                }
								$gRect               = $htmlNs->find('g#rect', 0);
								$gRect->transform    = 'scale(' . $acHeight . ') translate(0,0)';
								$clipPath            = $htmlNs->find('clipPath.boundryClipPath', 0);
								$clipPath->outertext = '';
								$svgFileStatus       = file_put_contents($svgFilePaths, $htmlNs);
							}
                        }
                    }
                }
                $resOnlyImageSvgFile = XEPATH . $resOnlyImageSvgFile;
                $msg['status']       = 'success';
            }
        } else {
            $msg['status'] = "File doesn't exists.";
        }
        return $msg;
    }
    /**
     *
     *created date 6-12-2016(dd-mm-yy)
     *created date 6-12-2016(dd-mm-yy)
     * convert unit cm,ft,mm to inch
     *
     * @param (String)unit
     * @param (Float)value
     * @return Float
     *
     */
    public function convertAllToInch($unit, $value)
    {
        $result = 0;
        switch ($unit) {
            case 'cm':
                return $result = ($value / 2.54);
                break;
            case 'mm':
                return $result = ($value / 25.4);
                break;
            case 'ft':
                return $result = ($value * 12);
                break;
            default:
                return $value;
                break;
        }
    }
    /**
     *
     *created date 1-05-2017(dd-mm-yy)
     *created date 5-8-2016(dd-mm-yy)
     * To check name and number for back and front enable or not from designState json
     *
     * @param (String)jsondata
     * @param (Int)order_id
     * @param (Int)refids
     * @param (Int)folderStr
     * @return string
     *
     */
    public function creteNameAndNumberSeparatedSvg($reqSvgFile, $jsondata, $order_id, $refids, $size, $folderStr = 1)
    {
        $i        = 1;
        $svgData1 = '';
        $svgData2 = '';
        $req      = @file_get_contents($reqSvgFile);
        $req      = str_ireplace('data: png', 'data:image/png', $req);
        $req      = str_ireplace('data: jpg', 'data:image/jpg', $req);
        if ($req) {
            $pathWithFile  = str_replace(XEPATH, '', $reqSvgFile);
            $sideArr       = explode("/", $pathWithFile);
            $sideId        = str_replace("preview_0", "", str_replace(".svg", "", $sideArr[count($sideArr) - 1]));
            $baseImagePath = $this->getOrdersPath();
        }
        foreach ($jsondata['nameNumberData']['list'] as $k1 => $v1) {
            if ($size == $v1['size']) {
                $nnFolderName = 'NN_' . $i;
                if ($jsondata['nameNumberData']['front'] == true) {
                    $svgUrl = XEPATH . "designer-tool" . ORDER_PATH_DIR . "/" . $order_id . "/" . $refids . "/side_1/preview_01.svg";
                    if ($jsondata['nameNumberData']['frontView'] == 'name_num') {
                        $svgData1 = $v1['nameGrpTxtFront'];
                        $svgData2 = $v1['numGrpTxtFront'];
                        //$foldername = $i . '_' . $v1['name'] . '_' . $v1['number'];
                        $this->newSvgCreateByGroup($svgUrl, $svgData1, $svgData2, $nnFolderName, $refids, $order_id, $folderStr, $jsondata);
                    }
                    if ($jsondata['nameNumberData']['frontView'] == 'name') {
                        $svgData1 = $v1['nameGrpTxtFront'];
                        //$foldername = $i . '_' . $v1['name'];
                        $this->newSvgCreateByGroup($svgUrl, $svgData1, $svgData2, $nnFolderName, $refids, $order_id, $folderStr, $jsondata);
                    }
                    if ($jsondata['nameNumberData']['frontView'] == 'num') {
                        $svgData2 = $v1['numGrpTxtFront'];
                        //$foldername = $i . '_' . $v1['number'];
                        $this->newSvgCreateByGroup($svgUrl, $svgData1, $svgData2, $nnFolderName, $refids, $order_id, $folderStr, $jsondata);
                    }
                }
                if ($jsondata['nameNumberData']['back'] == true) {
                    $svgUrl = XEPATH . "designer-tool" . ORDER_PATH_DIR . "/" . $order_id . "/" . $refids . "/side_2/preview_02.svg";
                    if ($jsondata['nameNumberData']['backView'] == 'name_num') {
                        $svgData1 = $v1['numGrpTxtBack'];
                        $svgData2 = $v1['nameGrpTxtBack'];
                        // $foldername = $i . '_' . $v1['name'] . '_' . $v1['number'];
                        $this->newSvgCreateByGroup($svgUrl, $svgData1, $svgData2, $nnFolderName, $refids, $order_id, $folderStr, $jsondata);
                    }
                    if ($jsondata['nameNumberData']['backView'] == 'name') {
                        $svgData1 = $v1['nameGrpTxtBack'];
                        //$foldername = $i . '_' . $v1['name'];
                        $this->newSvgCreateByGroup($svgUrl, $svgData1, $svgData2, $nnFolderName, $refids, $order_id, $folderStr, $jsondata);
                    }
                    if ($jsondata['nameNumberData']['backView'] == 'num') {
                        $svgData2 = $v1['numGrpTxtBack'];
                        //$foldername = $i . '_' . $v1['number'];
                        $this->newSvgCreateByGroup($svgUrl, $svgData1, $svgData2, $nnFolderName, $refids, $order_id, $folderStr, $jsondata);
                    }
                }
                if (($jsondata['nameNumberData']['back']) == false || ($jsondata['nameNumberData']['back'] == true)) {
                    $folderName = 'NN_' . $i;
                    if ($folderStr == 1) {
                        $savePath = $baseImagePath . '/' . $order_id . '/' . $refids . '/side_' . $sideId . '/' . $folderName . '/';
                    } else {
                        $savePath = $baseImagePath . '/' . $order_id . '/' . $refids . '/' . $folderName . '/';
                    }
                    if (!file_exists($savePath)) {
                        $this->createWithoutProductSvg($reqSvgFile, $order_id, $refids, $jsondata, $folderStr);
                    }
                }
                if (($jsondata['nameNumberData']['front'] == false) || ($jsondata['nameNumberData']['front'] == true)) {
                    $foldersName = 'NN_' . $i;
                    if ($folderStr == 1) {
                        $savePath = $baseImagePath . '/' . $order_id . '/' . $refids . '/side_' . $sideId . '/' . $foldersName . '/';
                    } else {
                        $savePath = $baseImagePath . '/' . $order_id . '/' . $refids . '/' . $foldersName . '/';
                    }
                    if (!file_exists($savePath)) {
                        $this->createWithoutProductSvg($reqSvgFile, $order_id, $refids, $jsondata, $folderStr);
                    }
                }
                $i++;
            }
        }
    }

    /**
     *
     * created date 1-5-2016(dd-mm-yy)
     * created date 5-8-2016(dd-mm-yy)
     * generate separaetd svg files for name and number
     *
     * @param (String)reqSvgFile
     * @param (String)resultSvg
     * @param (Int)order_id
     * @param (Int)item_id
     * @return string
     *
     */
    public function newSvgCreateByGroup($reqSvgFile, $svgData1 = '', $svgData2 = '', $folderName, $item_id, $order_id, $folderStr, $resultDesign)
    {
        $unit = $resultDesign['sides'][0]['printUnit'];
        //Sart to check for svg output in storesettings.json file
        $storesettings['enable_boundary_clip'] = '';
        $storesettingsPath                     = XEPATH . 'designer-tool/storesettings.json';
        $newData                               = file_get_contents($storesettingsPath);
        $pos                                   = strpos($newData, '"');
        $newData                               = substr($newData, $pos);
        $newData                               = '{' . str_replace(';', '', $newData);
        $storesettings                         = $this->formatJSONToArray($newData);
        //end
        $default_print_size = array('A1', 'A2', 'A3', 'A4', 'A5', 'A6', 'A7', 'A8');
        $status             = 'failed';
        $clipPathFileName   = '_clip_off.svg';
        $status             = 'failed';
        if ($reqSvgFile != '') {
            $req = @file_get_contents($reqSvgFile);
            $req = str_ireplace('data: png', 'data:image/png', $req);
            $req = str_ireplace('data: jpg', 'data:image/jpg', $req);
            if ($req) {
                //start new code
                $pathWithFile = str_replace(XEPATH, '', $reqSvgFile);
                $sideArr      = explode("/", $pathWithFile);
                $sideId       = str_replace("preview_0", "", str_replace(".svg", "", $sideArr[count($sideArr) - 1]));

                $baseImagePath = $this->getOrdersPath();

                if ($folderStr == 1) {
                    $fileName  = "side_" . $sideId . "_" . $item_id . "_" . $order_id . ".svg";
                    $savePath  = $baseImagePath . '/' . $order_id . '/' . $item_id . '/side_' . $sideId . '/' . $folderName . '/';
                    $fileNames = "side_" . $sideId . "_" . $item_id . "_" . $order_id . $clipPathFileName;
                } else {
                    $fileName  = $sideId . ".svg";
                    $savePath  = $baseImagePath . '/' . $order_id . '/' . $item_id . '/' . $folderName . '/';
                    $fileNames = $sideId . $clipPathFileName;
                }
                $svgSavePath = $savePath;
                //end
                if (!file_exists($svgSavePath)) {
                    mkdir($svgSavePath, 0777, true);
                    chmod($svgSavePath, 0777);
                }
                $svgFilePath     = $svgSavePath . $fileName;
                $svgPath         = $baseImagePath . '/' . $order_id . '/' . $item_id . '/'; //unlink svg file if already exit
                $svgFilePathExit = $svgPath . $fileName;
                if (file_exists($svgFilePathExit)) {
                    unlink($svgFilePathExit);
                }
                $resStr = $req;
                $html   = new simple_html_dom();
                $html->load($resStr, false);
                $g = $html->find('g#productBgColor', 0);
                if ($g) {
                    $g->outertext = '';
                }
                $layer_background = $html->find('g#layer_background', 0);
                if (isset($layer_background) && $layer_background != '') {
                    $bleedM       = $layer_background->find('g#bleedM', 0);
                    $react        = $bleedM->find('rect#cropM', 0);
                    $reactDisplay = $react->display;

                    $reacts              = $bleedM->find('rect#hAlignTleft', 0);
                    $reactDisplayOn      = $reacts->display;
                    $vAlignBright        = $bleedM->find('rect#vAlignBright', 0);
                    $vAlignBrightDisplay = $vAlignBright->display;
                    $vAlignWidth         = $vAlignBright->width;
                    $vAlignHeight        = $vAlignBright->height;
                    $bleedMarkMaxValue   = max($vAlignWidth, $vAlignHeight);
                    $vAlignWidthInch     = (2 * $bleedMarkMaxValue) / 90;

                    $cropMMask        = $bleedM->find('path#cropMMask', 0);
                    $cropMMaskDisplay = $cropMMask->display;
                    if ($cropMMaskDisplay == 'block' || $reactDisplay == 'block') {
                        $layer_backgrounds           = $layer_background->outertext;
                        $layer_stage                 = $html->find('g#layer_stage', 0);
                        $layer_stages                = $layer_stage->outertext;
                        $layer_stage->outertext      = $layer_backgrounds;
                        $layer_background->outertext = $layer_stages;
                    }
                }
                $html->save();
                $html2 = new simple_html_dom();
                $html2->load($html, false);
                $svg = $html2->find('image#svg_1', 0);
                if ($svg) {
                    $g = $html2->find('g.mainContent', 0);
                    if ($g) {
                        $type = $html2->find('g[type]');
                        foreach ($type as $k => $v) {
                            $dataType      = $type[$k]->attr;
                            $nameAndNumber = $dataType['type'];
                            if ($nameAndNumber == 'nameText' && $svgData1 != '') {
                                $name            = $html2->find('g[type="nameText"]', 0);
                                $name->outertext = $svgData1;
                            }
                            if ($nameAndNumber == 'numberText' && $svgData2 != '') {

                                $number = $html2->find('g[type="numberText"]');
                                foreach ($number as $k1 => $v1) {
                                    $numbe2            = $html2->find('g[type="numberText"]', 0);
                                    $numbe2->outertext = $svgData2;
                                }
                            }
                            $html2->save();
                            $html3 = new simple_html_dom();
                            $html3->load($html2, false);
                            $svg1     = $html3->find('image#svg_1', 0);
                            $rectPath = $html3->find('rect#boundRectangle', 0);
                            if ($rectPath) {
                                $x       = $rectPath->x;
                                $y       = $rectPath->y;
                                $width   = $rectPath->width;
                                $height  = $rectPath->height;
                                $aHeight = $rectPath->aHeight;
                                $aWidth  = $rectPath->aWidth;
                                $svgroot = $html3->find('svg#svgroot', 0);
                                foreach ($resultDesign['sides'] as $k => $v) {
                                    if (in_array($v['printSize'], $default_print_size)) {
                                        if (intval($height) > intval($width)) {
                                            $temp    = '';
                                            $temp    = $aHeight;
                                            $aHeight = $aWidth;
                                            $aWidth  = $temp;
                                            if (intval($aWidth) > intval($aHeight)) {
                                                $temp    = '';
                                                $temp    = $aHeight;
                                                $aHeight = $aWidth;
                                                $aWidth  = $temp;
                                            }
                                        }
                                    }
                                }
                                if ($aHeight == 'null' || $aHeight == 0) {
                                    $svgroot->width  = 500;
                                    $svgroot->height = 500;
                                    $svg1->outertext = '<g id="rect" transform="scale(1) translate(0,0)">';
                                } else {
                                    if (intval($height) < intval($width)) {
                                        if (intval($aWidth) < intval($aHeight)) {
                                            $temp    = '';
                                            $temp    = $aHeight;
                                            $aHeight = $aWidth;
                                            $aWidth  = $temp;
                                        }
                                    } else if (intval($height) > intval($width)) {
                                        if (intval($aWidth) > intval($aHeight)) {
                                            $temp    = '';
                                            $temp    = $aHeight;
                                            $aHeight = $aWidth;
                                            $aWidth  = $temp;
                                        }
                                    }
                                    $aWidth  = $this->convertAllToInch($unit, $aWidth);
                                    $aHeight = $this->convertAllToInch($unit, $aHeight);
                                    if ($reactDisplayOn == 'block' && $vAlignBrightDisplay == 'block') {
                                        $x               = -$x + $bleedMarkMaxValue;
                                        $y               = -$y + $bleedMarkMaxValue;
                                        $svgroot->width  = $aWidth + $vAlignWidthInch . 'in';
                                        $svgroot->height = $aHeight + $vAlignWidthInch . 'in';
                                        $height          = $height + ($bleedMarkMaxValue * 2);
                                        $acHeight        = $aHeight * 90;
                                        $acHeight        = $acHeight / $height;
                                        $svg1->outertext = '<g id="rect" transform="scale(' . $acHeight . ') translate( ' . $x . ', ' . $y . ')">';
                                    } else {
                                        $svgroot->width  = $aWidth . 'in';
                                        $svgroot->height = $aHeight . 'in';
                                        $acHeight        = $aHeight * 90;
                                        $acHeight        = $acHeight / $height;
                                        $svg1->outertext = '<g id="rect" transform="scale(' . $acHeight . ') translate(-' . $x . ',-' . $y . ')">';
                                    }
                                }
                                $html3->save();
                                $html3 = str_replace('</svg>', '', $html3);
                                $html3 = explode(" ", $html3);
                                $html3 = implode(" ", $html3);

                                $html3 = explode(" ", $html3);
                                $html3 = implode(" ", $html3);
                                $data  = '</g></svg>';
                                $html3 = $html3 . $data;
                                $html3 = str_replace("'", '"', $html3);
                                if ($storesettings['enable_boundary_clip'] == 0 || $storesettings['enable_boundary_clip'] == '' || $storesettings['enable_boundary_clip'] == 2) {
                                    if (!file_exists($svgSavePath)) {
                                        mkdir($svgSavePath, 0777, true);
                                        chmod($svgSavePath, 0777);
                                    }

                                    $svgFilePath   = $svgSavePath . $fileName;
                                    $svgFileStatus = file_put_contents($svgFilePath, $html3);
                                }
                                //for clipPath separated
                                if ($storesettings['enable_boundary_clip'] == 1 || $storesettings['enable_boundary_clip'] == 2) {

                                    $svgFilePaths = $svgSavePath . $fileNames;
                                    $html5        = new simple_html_dom();
                                    $html5->load($html3, false);
                                    $svgroots            = $html5->find('svg#svgroot', 0);
                                    $acHeight            = $aHeight * 90;
                                    $acHeight            = $acHeight / $height;
                                    $svgroots->width     = ($acHeight * 500) / 90 . 'in';
                                    $svgroots->height    = ($acHeight * 500) / 90 . 'in';
                                    $gRect               = $html5->find('g#rect', 0);
                                    $gRect->transform    = 'scale(' . $acHeight . ') translate(0,0)';
                                    $clipPath            = $html5->find('clipPath.boundryClipPath', 0);
                                    $clipPath->outertext = '';
                                    $svgFileStatus       = file_put_contents($svgFilePaths, $html5);
                                }
                                $svg1->outertext = '<g id="rect" transform="translate(0,0)">';
                            } else {
                                $svg1->outertext = '<g id="rect" transform="translate(0,0)">';
                                $html3->save();
                                $html3 = str_replace('</svg>', '', $html3);
                                $html3 = explode(" ", $html3);
                                $html3 = implode(" ", $html3);
                                $htmlN = new simple_html_dom();
                                $htmlN->load($html3, false);
                                $svgMainContent = $htmlN->find('g[class]', 0);
                                $htmlN->save();
                                $path = $htmlN->find('path#boundMask', 0); //find mask actual height and width
                                $x1   = $path->aX;
                                $y1   = $path->aY;
                                $x    = $path->x;
                                $y    = $path->y;
                                if (!$x1 || !isset($x1) || ($x1 == null)) {
                                    $x1 = $x;
                                    $y1 = $y;
                                }
                                if (($x1 < 0) || ($y1 < 0)) {
                                    $svgroot->width  = 500;
                                    $svgroot->height = 500;
                                    $acHeight        = 1;
                                    $x1              = 0;
                                    $y1              = 0;
                                } else {
                                    $width   = $path->width;
                                    $height  = $path->height;
                                    $aHeight = $path->aHeight;
                                    $aWidth  = $path->aWidth;
                                    if (intval($aWidth) != 0 || intval($aHeight) != 0) {
                                        $svgroot         = $htmlN->find('svg#svgroot', 0);
                                        $aWidth          = $this->convertAllToInch($unit, $aWidth);
                                        $aHeight         = $this->convertAllToInch($unit, $aHeight);
                                        $svgroot->width  = $aWidth . 'in';
                                        $svgroot->height = $aHeight . 'in';
                                        $acHeight        = $aHeight * 90;
                                        $acHeight        = $acHeight / $height;
                                    } else {
                                        $svgroot         = $htmlN->find('svg#svgroot', 0);
                                        $svgroot->width  = 500;
                                        $svgroot->height = 500;
                                        $acHeight        = 1;
                                    }
                                }
                                if ($svgMainContent) {
                                    $htmlN     = explode(" ", $htmlN);
                                    $htmlN[10] = 'transform="scale(' . $acHeight . ') translate(-' . $x1 . ',-' . $y1 . ')">';
                                    unset($htmlN[11], $htmlN[12], $htmlN[13], $htmlN[14], $htmlN[15], $htmlN[16]);
                                    $htmlN = implode(" ", $htmlN);
                                    $data  = '</svg>';
                                    $htmlN = $htmlN . $data;
                                    $htmlN = str_replace("'", '"', $htmlN);
                                    if ($storesettings['enable_boundary_clip'] == 0 || $storesettings['enable_boundary_clip'] == '' || $storesettings['enable_boundary_clip'] == 2) {
                                        if (!file_exists($svgSavePath)) {
                                            mkdir($svgSavePath, 0777, true);
                                            chmod($svgSavePath, 0777);
                                        }

                                        $svgFilePath   = $svgSavePath . $fileName;
                                        $svgFileStatus = file_put_contents($svgFilePath, $htmlN);
                                    }
                                    //for clipPath separated
                                    if ($storesettings['enable_boundary_clip'] == 1 || $storesettings['enable_boundary_clip'] == 2) {
                                        $svgFilePaths = $svgSavePath . $fileNames;
                                        $htmlNs       = new simple_html_dom();
                                        $htmlNs->load($htmlN, false);
                                        $svgroots            = $htmlNs->find('svg#svgroot', 0);
                                        $acHeight            = $aHeight * 90;
                                        $acHeight            = $acHeight / $height;
                                        $svgroots->width     = ($acHeight * 500) / 90 . 'in';
                                        $svgroots->height    = ($acHeight * 500) / 90 . 'in';
                                        $gRect               = $htmlNs->find('g#rect', 0);
                                        $gRect->transform    = 'scale(' . $acHeight . ') translate(0,0)';
                                        $clipPath            = $htmlNs->find('clipPath.boundryClipPath', 0);
                                        $clipPath->outertext = '';
                                        $svgFileStatus       = file_put_contents($svgFilePaths, $htmlNs);
                                    }
                                } else {
                                    $html2     = explode(" ", $html2);
                                    $html2[10] = 'transform="scale(' . $acHeight . ') translate(-' . $x1 . ',-' . $y1 . ')">';
                                    unset($html2[11], $html2[12], $html2[13], $html2[14], $html2[15], $html2[16]);
                                    $html2        = implode(" ", $html2);
                                    $data         = '</g></svg>';
                                    $html2        = $html2 . $data;
                                    $html2        = str_replace("'", '"', $html2);
                                    $pathWithFile = substr($pathWithFile, 0, -4);
                                    if ($storesettings['enable_boundary_clip'] == 0 || $storesettings['enable_boundary_clip'] == '' || $storesettings['enable_boundary_clip'] == 2) {
                                        if (!file_exists($svgSavePath)) {
                                            mkdir($svgSavePath, 0777, true);
                                            chmod($svgSavePath, 0777);
                                        }

                                        $svgFilePath   = $svgSavePath . $fileName;
                                        $svgFileStatus = file_put_contents($svgFilePath, $html2);
                                    }
                                    //for clipPath separated
                                    if ($storesettings['enable_boundary_clip'] == 1 || $storesettings['enable_boundary_clip'] == 2) {
                                        $svgFilePaths = $svgSavePath . $fileNames;
                                        $html6        = new simple_html_dom();
                                        $html6->load($html2, false);
                                        $svgroots            = $html6->find('svg#svgroot', 0);
                                        $acHeight            = $aHeight * 90;
                                        $acHeight            = $acHeight / $height;
                                        $svgroots->width     = ($acHeight * 500) / 90 . 'in';
                                        $svgroots->height    = ($acHeight * 500) / 90 . 'in';
                                        $gRect               = $html6->find('g#rect', 0);
                                        $gRect->transform    = 'scale(' . $acHeight . ') translate(0,0)';
                                        $clipPath            = $html6->find('clipPath.boundryClipPath', 0);
                                        $clipPath->outertext = '';
                                        $svgFileStatus       = file_put_contents($svgFilePaths, $html6);
                                    }
                                }
                            }
                        }
                    }
                }
                $msg['status'] = $svgFileStatus ? 'success' : 'failed in svg created';
            }
        } else {
            $msg['status'] = "File doesn't exists.";
        }
        return $msg;
    }

    /**
     *
     * date 15th_Apr-2016
     * delete the zip file created in server
     *
     * @param (String)zipUrl (request method)
     * @return blank
     *
     */
    public function deleteOrderAppZip()
    {
        $zipUrl        = (isset($this->_request['zipUrl'])) ? $this->_request['zipUrl'] : "";
        $order_dir_url = XEPATH . "designer-tool" . ASSET_PATH . "/orders/";
        $orderZipFile  = str_replace($order_dir_url, "", $zipUrl);
        echo $this->getOrdersPath() . '/' . $orderZipFile;
        if (file_exists($this->getOrdersPath() . '/' . $orderZipFile)) {
            unlink($this->getOrdersPath() . '/' . $orderZipFile);
            $msg      = "Delete Successfully";
            $response = "1";
        } else {
            $msg      = "Can't find the zip file in server.";
            $response = "0";
        }
        $msg = array("msg" => $msg, "Response" => $response);
        $this->response($this->json($msg), 200);
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Update orders print status by oredrid
     *
     *@param (String)apikey
     *@param (in)orderId
     *@param (String))orderStaus
     *@return json data
     *
     */
    public function updateOrderPrintStatus()
    {
        if (isset($this->_request['orderId']) && $this->_request['orderId'] != '') {
            $orderId      = $this->_request['orderId'];
            $order_status = $this->_request['orderStaus'];
            try {
                $check_sql = 'SELECT * FROM ' . TABLE_PREFIX . 'sync_order WHERE orderId="' . $orderId . '"';
                $row       = $this->executeFetchAssocQuery($check_sql);
                if (count($row)) {
                    $update_sql = 'UPDATE ' . TABLE_PREFIX . 'sync_order SET order_status = "' . $order_status . '" WHERE orderId="' . $orderId . '"';
                    $rows       = $this->executeGenericDMLQuery($update_sql);
                } else {
                    $insert_sql = 'INSERT INTO ' . TABLE_PREFIX . 'sync_order(orderId,order_status) VALUES("' . $orderId . '","' . $order_status . '")';
                    $rows       = $this->executeGenericDMLQuery($insert_sql);
                }
                if (count($rows)) {
                    $msg = array("status" => $order_status);
                }
            } catch (Exception $e) {
                $msg = array('Caught exception:' => $e->getMessage());
            }
        } else {
            $msg = array("status" => "Invalid");
        }
        $this->response($this->json($msg), 200);
    }

    /**
     *
     *date of created 2-2-2016(dd-mm-yy)
     *date of Modified 13-4-2016(dd-mm-yy)
     *Syncronyze Oders Zip Folder
     *
     *@param (String)apikey
     *@param (File)Filedata
     *@return json data
     *
     */
    public function syncOrdersZip()
    {
        if (isset($_FILES['Filedata']['name']) && $_FILES['Filedata']['name'] != '') {
            try {
                $zipFileName = basename($_FILES['Filedata']['name']);
                $extn        = explode(".", $zipFileName);
                $extn        = $extn[count($extn) - 1];
                $zipFile     = str_replace('.' . $extn, '', $zipFileName); //file name without extension //
                if ($extn == 'zip') {
                    $tempDir = $this->getOrdersPath() . '/temp_' . $zipFile;
                    if (!file_exists($tempDir)) {
                        mkdir($tempDir, 0777, true);
                    }
                    $tmp               = $_FILES['Filedata']['tmp_name'];
                    $status            = move_uploaded_file($tmp, $tempDir . '/' . $zipFileName);
                    $zip_upload_status = ($status) ? 1 : 0;
                    if ($zip_upload_status == 1) {
                        $zip = new \ZipArchive;
                        $res = $zip->open($tempDir . '/' . $zipFileName);
                        if ($res === true) {
                            $zip->extractTo($tempDir . '/');
                            if (file_exists($tempDir . '/' . $zipFile)) {
                                $scanDir = scandir($tempDir . '/' . $zipFile);
                                if (count($scanDir) > 2) {
                                    $oderList   = '';
                                    $ord_detxx  = '';
                                    $flag       = '';
                                    $dirKounter = 0;
                                    foreach ($scanDir as $scandir) {
                                        if ($dirKounter >= 2) {
                                            if (false !== ($file = is_dir($tempDir . '/' . $zipFile . '/' . $scandir))) {
                                                $orderNo = $scandir;
                                                $oderList .= $orderNo . ',';
                                                $order_folder = $this->getOrdersPath() . '/' . $scandir;
                                                if (file_exists($order_folder)) {
                                                    $temp_infopdf_file_path = $tempDir . '/' . $zipFile . '/' . $scandir . '/' . $scandir . '.pdf';
                                                    $new_infopdf_file_path  = $order_folder . '/' . $scandir . '.pdf';
                                                    if (file_exists($temp_infopdf_file_path)) {
                                                        copy($temp_infopdf_file_path, $new_infopdf_file_path);
                                                    }

                                                    if (file_exists($order_folder . '/order.json')) {
                                                        $order_json           = file_get_contents($order_folder . '/order.json');
                                                        $json_content         = $this->formatJSONToArray($order_json);
                                                        $total_items_in_order = count($json_content['order_details']['order_items']);
                                                        if ($total_items_in_order > 0) {
                                                            $kk    = 0;
                                                            $kkNew = 0;
                                                            foreach ($json_content['order_details']['order_items'] as $item) {
                                                                $kkNew  = $kkNew + 1;
                                                                $iNew   = 0;
                                                                $color  = ($item['xe_color'] != '') ? $item['xe_color'] : 'none';
                                                                $size   = $item['xe_size'];
                                                                $ref_id = $item['ref_id'];
                                                                $sql    = "Select json_data from " . TABLE_PREFIX . "design_state where id=" . $ref_id;
                                                                $rows   = $this->executeGenericDQLQuery($sql);
                                                                if (!empty($rows)) {
                                                                    $jsonData   = $rows[0]['json_data'];
                                                                    $jsonData   = $this->formatJSONToArray($jsonData);
                                                                    $item_sides = sizeof($jsonData['sides']);
                                                                    for ($i = 0; $i <= $item_sides - 1; $i++) {
                                                                        $iNew               = $iNew + 1;
                                                                        $temp_png_file_path = $tempDir . '/' . $zipFile . '/' . $scandir . '/item_' . $kkNew . '/' . $iNew . '.png';
                                                                        $png_file_name      = $orderNo . '_' . $color . '_' . $size . '_' . $kk . $i . '.png';
                                                                        $new_png_file_path  = $order_folder . '/' . $png_file_name;
                                                                        if (file_exists($temp_png_file_path)) {
                                                                            if (copy($temp_png_file_path, $new_png_file_path)) {
                                                                                $sql  = 'SELECT pk_id FROM ' . TABLE_PREFIX . 'sync_order WHERE orderId="' . $orderNo . '" AND fileName = "' . $png_file_name . '"';
                                                                                $rows = $this->executeFetchAssocQuery($sql);
                                                                                if (count($rows) == 0) {
                                                                                    $sql = 'INSERT INTO ' . TABLE_PREFIX . 'sync_order(orderId,fileName,last_sync_on,status) VALUES("' . $orderNo . '","' . $png_file_name . '",NOW(),"' . $upload_status . '")';
                                                                                    $this->executeGenericDMLQuery($sql);
                                                                                }
                                                                            }
                                                                        }
                                                                        $temp_pdf_file_path = $tempDir . '/' . $zipFile . '/' . $scandir . '/item_' . $kkNew . '/' . $iNew . '.pdf';
                                                                        $pdf_file_name      = $orderNo . '_' . $color . '_' . $size . '_' . $kk . $i . '.pdf';
                                                                        $new_pdf_file_path  = $order_folder . '/' . $pdf_file_name;
                                                                        if (file_exists($temp_pdf_file_path)) {
                                                                            if (copy($temp_pdf_file_path, $new_pdf_file_path)) {
                                                                                $sql  = 'SELECT pk_id FROM ' . TABLE_PREFIX . 'sync_order WHERE orderId="' . $orderNo . '" AND fileName = "' . $pdf_file_name . '"';
                                                                                $rows = $this->executeFetchAssocQuery($sql);
                                                                                if (count($rows) == 0) {
                                                                                    $sql = 'INSERT INTO ' . TABLE_PREFIX . 'sync_order(orderId,fileName,last_sync_on,status) VALUES("' . $orderNo . '","' . $pdf_file_name . '",NOW(),"' . $upload_status . '")';
                                                                                    $this->executeGenericDMLQuery($sql);
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                    $kk++;
                                                                } else {
                                                                    $msg = 'No sides found in State json';
                                                                }
                                                            }
                                                        }
                                                    }
                                                } else {
                                                    $msg = 'Can\'t fetch Order Details';
                                                }
                                            } else {
                                                $msg = 'Directory is blank';
                                            }
                                        }
                                        $dirKounter++;
                                    }
                                } else {
                                    $msg = 'Zip Extract Fialed';
                                }
                            } else {
                                $msg = 'Can\'t open Zip file';
                            }
                            $zip->close();
                        } else {
                            $msg = 'Zip file can\'t be uploaded';
                        }
                        $this->deleteZipFileFolder($tempDir);
                    } else {
                        $msg = 'Invalid File, Error:' . $_FILES['Filedata']['error'] . ' order zip path = ' . $this->getOrdersPath() . '/' . $zipFileName . ', ======temp_path=' . $tmp . ' ====path=' . $tempDir . '/' . $zipFileName;
                    }
                } else {
                    $msg = 'Upload a zip file';
                }
            } catch (Exception $e) {
                $msg = array('Caught exception:' => $e->getMessage());
            }
        } else {
            $msg = 'No zip file found';
        }
        $msg2 = array("Response" => $msg);
        $this->log('syncZipFile: ' . $msg, true, 'syncZipFile.log');
        $this->response($this->json($msg2), 200);
    }

    /**
     *
     *date of created 2-2-2016(dd-mm-yy)
     *date of Modified 13-4-2016(dd-mm-yy)
     *Download Order Zip Folder
     *
     *@param (String)apikey
     *@param (Int)orders
     *@return JSON  data
     *
     */
    public function downloadOrdersZip()
    {
        $order_list = (isset($this->_request['orders'])) ? $this->_request['orders'] : '';
        $orders     = explode(',', $order_list);
        $fromDate   = (isset($this->_request['fromDate'])) ? date('Y-m-d', strtotime($this->_request['fromDate'])) : '';
        $toDate     = (isset($this->_request['toDate'])) ? date('Y-m-d', strtotime($this->_request['toDate'])) : '';
        if (($fromDate != '' && $toDate == '') || ($fromDate == '' && $toDate != '')) {
            // Condition for Single Date //
            $singleDate = ($fromDate != '') ? $fromDate : $toDate;
        }
        if (is_array($orders) && count($orders) > 0) {
            $orderFolderPath = $this->getOrdersPath();
            $zipName         = (isset($singleDate) && $singleDate != '') ? 'Orders_' . $singleDate . 'zip' : 'Orders_' . $fromDate . '_' . $toDate . '.zip';
            $zip             = new \ZipArchive;
            $res             = $zip->open($orderFolderPath . '/' . $zipName, ZipArchive::CREATE);
            if ($res === true) {
                $zipCheckKounter    = 0;
                $settings_json_path = $this->getBasePath() . LANGUAGE_PATH . '/settings.json';
                try {
                    $settings_json          = file_get_contents($settings_json_path);
                    $settings_arry          = $this->formatJSONToArray($settings_json);
                    $temp_array             = array();
                    $temp_array['base_url'] = $settings_arry['base_url'] . $settings_arry['service_api_url'];
                    $temp_array['api_key']  = $settings_arry['api_key'];
                    $new_json               = json_encode($temp_array);
                    $zip->addFromString('settings.json', $new_json);
                    foreach ($orders as $orderNo) {
                        if (file_exists($orderFolderPath . '/' . $orderNo)) {
                            if (file_exists($orderFolderPath . '/' . $orderNo . '/order.json')) {
                                $order_json   = file_get_contents($orderFolderPath . '/' . $orderNo . '/order.json');
                                $json_content = $this->formatJSONToArray($order_json);
                                $noOfRefIds   = count($json_content['order_details']['order_items']);
                                if ($noOfRefIds > 0) {
                                    $zip->addEmptyDir($orderNo);
                                    $zip->addFile($orderFolderPath . '/' . $orderNo . '/order.json', $orderNo . '/order.json');
                                    $ref_kounter = 1;
                                    foreach ($json_content['order_details']['order_items'] as $ref_details) {
                                        $ref_id = $ref_details['ref_id'];
                                        if ($ref_id != null && $ref_id > 0) {
                                            $zip->addEmptyDir($orderNo . "/item_" . $ref_kounter);
                                            $from_url = $this->getPreviewImagePath() . $ref_id . "/svg";
                                            $options  = array('add_path' => $orderNo . '/item_' . $ref_kounter . "/", 'remove_path' => $from_url);
                                            $zip->addGlob($from_url . '/*{svg,json}', GLOB_BRACE, $options);
                                            $zipCheckKounter++;
                                        }
                                        $ref_kounter++;
                                    }
                                }
                            }
                        }
                    }
                } catch (Exception $e) {
                    $msg = array('Caught exception:' => $e->getMessage());
                }
                $zip->close();
                $this->zipDownload($orderFolderPath . '/' . $zipName, $zipCheckKounter);
            } else {
                $msg = 'Zip Creation Fialed';
            }
        } else {
            $msg = 'No Orders Found to download';
        }
        $msg = array("Response" => $msg);
        $this->response($this->json($msg), 200);
    }

    /**
     *
     *date created 4-1-2016(dd-mm-yy)
     *date modified 14-4-2016(dd-mm-yy)
     *separaetd svg created and to chechk for name and number by index
     *
     * @param (int)refids
     * @param (String)apikey
     * @param (String)index
     * @param (int)side
     * @return json data
     *
     */
    public function textFxSvgChangeNew()
    {
        if (!empty($this->_request) && isset($this->_request['apikey']) && $this->isValidCall($this->_request['apikey'])) {
            $apiKey = $this->_request['apikey'];
            $refids = $this->_request['refids'];
            $side   = $this->_request['side'];
            $index  = $this->_request['index'];
            $return = 1;
            try {
                $cartObj      = Flight::carts();
                $cartArrs     = $cartObj->getCartPreviewImages($apiKey, $refids, $return);
                $resultDesign = $cartObj->getDesignStateDetails($apiKey, $refids, $return);
                $resultDesign = $this->formatJSONToArray($resultDesign);
                extract($resultDesign);
                if ($index == '') {
                    $svgUrl = $cartArrs[$refids][$side]['svg'];
                } else {
                    foreach ($jsondata['nameNumberData']['list'] as $k1 => $v1) {
                        if ($v1['index'] == $index) {
                            if ($side == 0) {
                                if ($jsondata['nameNumberData']['front'] == true) {
                                    if ($jsondata['nameNumberData']['frontView'] == 'name_num') {
                                        $svgData1  = $v1['numGrpTxtFront'];
                                        $svgData2  = $v1['nameGrpTxtFront'];
                                        $resultSvg = $svgData1 . $svgData2;
                                        $svgUrl    = $cartArrs[$refids][$side]['svg'];
                                        $this->newSvgCreateByGroup($svgUrl, $resultSvg, $index, $refids);
                                    }
                                    if ($jsondata['nameNumberData']['frontView'] == 'name') {
                                        $resultSvg = $v1['nameGrpTxtFront'];
                                        $svgUrl    = $cartArrs[$refids][$side]['svg'];
                                        $this->newSvgCreateByGroup($svgUrl, $resultSvg, $index, $refids);
                                    }
                                    if ($jsondata['nameNumberData']['frontView'] == 'num') {
                                        $svgUrl    = $cartArrs[$refids][$side]['svg'];
                                        $resultSvg = $v1['numGrpTxtFront'];
                                        $this->newSvgCreateByGroup($svgUrl, $resultSvg, $index, $refids);
                                    }
                                } else {
                                    $svg = $cartArrs[$refid][0]['svg'];
                                }

                            }
                            if ($side == 1) {
                                if ($jsondata['nameNumberData']['back'] == true) {
                                    if ($jsondata['nameNumberData']['backView'] == 'name_num') {
                                        $svgData1  = $v1['nameGrpTxtBack'];
                                        $svgData2  = $v1['numGrpTxtBack'];
                                        $resultSvg = $svgData1 . $svgData2;
                                        $svgUrl    = $cartArrs[$refids][$side]['svg'];
                                        $this->newSvgCreateByGroup($svgUrl, $resultSvg, $index, $refids);
                                    }
                                    if ($jsondata['nameNumberData']['backView'] == 'name') {
                                        $resultSvg = $v1['nameGrpTxtBack'];
                                        $svgUrl    = $cartArrs[$refids][$side]['svg'];
                                        $this->newSvgCreateByGroup($svgUrl, $resultSvg, $index, $refids);
                                    }
                                    if ($jsondata['nameNumberData']['backView'] == 'num') {
                                        $resultSvg = $v1['numGrpTxtBack'];
                                        $svgUrl    = $cartArrs[$refids][$side]['svg'];
                                        $this->newSvgCreateByGroup($svgUrl, $resultSvg, $index, $refids);
                                    }
                                } else {
                                    $svgUrl = $cartArrs[$refid][1]['svg'];
                                }

                            }
                        }
                    }
                }
                if ($svgUrl) {
                    $this->_request['svgFile'] = $svgUrl;
                    $this->_request['refid']   = $refids;
                    $result                    = array();
                    $result['url']             = $this->changeSvg();
                }
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
            }
        } else {
            $result = array('stattus' => 'invaliedkey');
        }
        $this->response($this->json($result), 200);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Fetch orders initials statsus by oredrid
     *
     *@param (String)apikey
     *@param (in)orderId
     *@return json data
     *
     */
    public function getOrderIntialStatus()
    {
        try {
            if (isset($this->_request['orderId']) && $this->_request['orderId'] != '') {
                $orderId = $this->_request['orderId'];
                $sql     = 'SELECT order_status FROM ' . TABLE_PREFIX . 'sync_order WHERE orderId="' . $orderId . '" ORDER BY last_sync_on DESC limit 1';
                $row     = $this->executeGenericDQLQuery($sql);
                $status  = $row[0]['order_status'];

                if (count($row)) {
                    $msg = array("order_status" => $status);
                } else {
                    $msg = array("status" => "Order Id does not Match");
                }
            } else {
                $msg = array("status" => "Invalid");
            }
            $this->response($this->json($msg), 200);
        } catch (Exception $e) {
            $msg = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($msg), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Fetch last orders
     *
     *@param (String)apikey
     *@return json data
     *
     */
    public function getlastorderid()
    {
        try {
            $sql  = 'SELECT orderId FROM ' . TABLE_PREFIX . 'sync_order WHERE status= "1" ORDER BY last_sync_on DESC limit 1';
            $rows = $this->executeFetchAssocQuery($sql);
            if (count($rows)) {
                $this->response($this->json($rows), 200);
            } else {
                $orderId        = 0;
                $lastorderid[0] = array("orderId" => $orderId);
                $this->response($this->json($lastorderid), 200);
            }
        } catch (Exception $e) {
            $msg = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($msg), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Fetch failed orders
     *
     *@param (String)apikey
     *@param (String)upload_status
     *@return json data
     *
     */
    public function failedOrders()
    {
        try {
            $sql  = 'SELECT * FROM ' . TABLE_PREFIX . 'sync_order WHERE status="' . $upload_status . '"';
            $rows = $this->executeFetchAssocQuery($sql);
        } catch (Exception $e) {
            $rows = array('Caught exception:' => $e->getMessage());
        }
        $this->response($this->json($rows), 200);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *orders syncronyce by order id
     *
     *@param (String)apikey
     *@param (int)orderId
     *@param (File)Filedata
     *@return json data
     *
     */
    public function syncOrders()
    {
        try {
            if (isset($_GET['orderId']) && $_GET['orderId'] != '') {
                $orderId        = $_GET['orderId'];
                $msg['orderId'] = $orderId;
                $fname          = $_FILES['Filedata']['name'];

                $dir = $this->getOrdersPath();
                $dir = $dir . '/' . $orderId;
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                    //chmod($dir, 0777);
                }
                $tmp                  = $_FILES['Filedata']['tmp_name'];
                $status               = move_uploaded_file($tmp, $dir . '/' . $fname);
                $upload_status        = ($status) ? 1 : 0;
                $msg['upload_status'] = $upload_status;

                $sql  = 'SELECT * FROM ' . TABLE_PREFIX . 'sync_order WHERE orderId="' . $orderId . '" AND fileName = "' . $fname . '"';
                $rows = $this->executeFetchAssocQuery($sql);
                if (count($rows) == 0) {
                    $sql = 'INSERT INTO ' . TABLE_PREFIX . 'sync_order(orderId,fileName,last_sync_on,status) VALUES("' . $orderId . '","' . $fname . '",NOW(),"' . $upload_status . '")';
                    $this->executeGenericDMLQuery($sql);
                }
            }
        } catch (Exception $e) {
            $msg = array('Caught exception:' => $e->getMessage());
        }
        $this->response($this->json($msg), 200);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Fetch orders by order id
     *
     *@param (String)apikey
     *@param (int)orderId
     *@return json data
     *
     */
    public function fetchOrders($orderId = '')
    {
        //$this->_request['orderId'] = '100000056';
        $orderId = (isset($this->_request['orderId']) && $this->_request['orderId'] != '') ? $this->_request['orderId'] : $orderId;
        if ($orderId) {
            try {
                $dir = $this->getOrdersPath() . '/' . $orderId;

                if (file_exists($dir)) {
                    if ($handle = opendir($dir)) {
                        while (false !== ($entry = readdir($handle))) {
//scandir()
                            if ($entry != "." && $entry != ".." && strtolower(substr($entry, -4)) == '.svg') {
                                $this->checkSvg($entry, $dir);
                            }
                        }
                        closedir($handle);
                    }
                }
            } catch (Exception $e) {
                $msg = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($msg), 200);
            }
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Delete order by orderid
     *
     *@param (String)apikey
     *@param (int)orderId
     *@return json data
     *
     */
    public function deleteOrders()
    {
//Done
        //$this->_request['orderId'] = '100000056';
        try {
            $status = 0;
            if (isset($this->_request['orderId']) && $this->_request['orderId'] != '') {
                $orderId = $this->_request['orderId'];
                $dir     = $this->getOrdersPath() . '/' . $orderId;

                if (file_exists($dir . $orderId)) {
                    $this->rrmdir($dir . $orderId);
                    $sql    = "DELETE FROM " . TABLE_PREFIX . "sync_order WHERE orderId='" . $orderId . "'";
                    $status = $this->executeGenericDMLQuery($sql);
                }
                $msg['status'] = ($status) ? 'success' : 'failed';
            }
        } catch (Exception $e) {
            $msg = array('Caught exception:' => $e->getMessage());
        }
        $this->response($this->json($msg), 200);
    }

    /**
     *
     *date of created 2-2-2016(dd-mm-yy)
     *date of Modified 13-4-2016(dd-mm-yy)
     *Change Oders Svg data
     *
     *@return json data
     *
     */
    protected function orderSvgChange()
    {
        $this->changeSvg();
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *create sepated svg file from design products
     *
     * @param (int)refid
     * @param (int)return
     * @param (String)svgFile
     * @return json data
     *
     */
    private function changeSvg($return = 0)
    {
        $status = 'failed';
        if (isset($this->_request['svgFile']) && $this->_request['svgFile']) {
            $refid      = $this->_request['refid'];
            $reqSvgFile = $this->_request['svgFile'];
            $resSvgFile = $reqSvgFile;
            try {
                $req = @file_get_contents($reqSvgFile);
                $req = str_ireplace('data: png', 'data:image/png', $req);
                $req = str_ireplace('data: jpg', 'data:image/jpg', $req);
                if ($req) {
                    $pathWithFile = str_replace(XEPATH, '', $reqSvgFile);
                    $ds           = DIRECTORY_SEPARATOR;
                    $path         = str_replace('designer-tool' . $ds . 'designer-api' . $ds . 'app-service', '', getcwd());
                    $res          = '';
                    $resStr       = $req;
                    preg_match_all('/(https?:\/\/\S+\.(?:svg))/', $req, $svgMatch);
                    if (!empty($svgMatch) && !empty($svgMatch[0])) {
                        $svgMatch = $svgMatch[0];
                        $html     = new simple_html_dom();
                        $html->load($req);
                        $fststr  = '';
                        $lastStr = '';
                        foreach ($svgMatch as $k => $v) {
                            $main      = $html->find('image[xlink:href=' . $v . ']');
                            $imgData   = $main[0]->attr;
                            $Imgwidth  = $imgData['width'];
                            $Imgheight = $imgData['height'];
                            $ImgX      = $imgData['x'];
                            $ImgY      = $imgData['y'];

                            $x         = explode($v, $req);
                            $disectStr = strrchr($x[0], '<image');
                            if ($k == 0) {
                                $fststr = str_ireplace($disectStr, '', $x[0]);
                            }
                            if ($k == count($svgMatch) - 1) {
                                $s2      = strpos($x[1], '</g>');
                                $lastStr = substr($x[1], $s2);
                            }
                            $fileContent[$k] = file_get_contents($v);
                            $html1           = new simple_html_dom();
                            $html1->load($fileContent[$k]);
                            $viewBox = $html1->find('svg[viewBox]');
                            if (!empty($viewBox)) {
                                $viewBox  = $viewBox[0]->attr;
                                $viewBox  = $viewBox['viewbox'];
                                $viewBox  = explode(' ', $viewBox);
                                $vBwidth  = $viewBox[2];
                                $vBheight = $viewBox[3];
                                $width    = $Imgwidth / $vBwidth;
                                $height   = $Imgheight / $vBheight;
                            } else {
                                $width  = $Imgwidth;
                                $height = $Imgheight;
                            }
                            $rstr[$k]        = stripos($fileContent[$k], '<svg ');
                            $fileContent[$k] = substr($fileContent[$k], $rstr[$k]);
                            $fileContent[$k] = str_ireplace(array('<svg', '/svg>'), array('<g', '/g>'), $fileContent[$k]);
                            $res .= '<g id="xe_ua9o" transform="translate(' . $ImgX . ', ' . $ImgY . ') scale(' . $width . ', ' . $height . ')">' . $fileContent[$k] . '</g>';
                        }
                        $resStr     = $fststr . $res . $lastStr;
                        $resSvgFile = str_ireplace('.svg', '_converted.svg', $pathWithFile);
                        $h          = fopen($path . $resSvgFile, 'w+');
                        chmod($path . $resSvgFile, 0777);
                        fwrite($h, $resStr);
                        $resSvgFile = XEPATH . $resSvgFile;

                    } else {
                        $resStr = $req;
                    }

                    $html2 = new simple_html_dom();
                    $html2->load($resStr, false);
                    $svg = $html2->find('image#svg_1', 0);
                    if ($svg) {
                        $rectPath = $html2->find('rect#boundRectangle', 0);
                        if ($rectPath) {
                            $rectPath = str_replace('"', '', $rectPath);
                            $matches  = array();
                            preg_match('/x=(\d*(?:\.\d+)?)/', $rectPath, $matches);
                            $x = $matches[1];
                            preg_match('/y=(\d*(?:\.\d+)?)/', $rectPath, $matches);
                            $y = $matches[1];
                            preg_match('/width=(\d*(?:\.\d+)?)/', $rectPath, $matches);
                            $width = $matches[1];
                            preg_match('/height=(\d*(?:\.\d+)?)/', $rectPath, $matches);
                            $height = $matches[1];
                            /* preg_match('/aHeight=(\d*(?:\.\d+)?)/', $rectPath, $matches);
                            $aHeight = $matches[1];
                            $aHeight = $aHeight *'74.97';
                            $aHeight = $aHeight / $height; */
                            $svg->outertext = '<g id="rect" transform="translate(-' . $x . ',-' . $y . ')">';
                            $html2->save();
                            $html2    = str_replace('</svg>', '', $html2);
                            $html2    = explode(" ", $html2);
                            $html2[4] = 'width="' . $width . '"';
                            $html2[5] = 'height="' . $height . '"';
                            //$html2[5] = 'height="'.$height.'" style="zoom:'.$aHeight.'"';
                            $html2 = implode(" ", $html2);
                            $html3 = new simple_html_dom();
                            $html3->load($html2, false);

                            $svgMainContent = $html3->find('g[class]', 0);
                            $html3->save();
                            if ($svgMainContent) {
                                $html3     = explode(" ", $html3);
                                $html3[10] = 'transform="translate(-' . $x . ',-' . $y . ')">';
                                unset($html3[11], $html3[12], $html3[13], $html3[14], $html3[15], $html3[16]);
                                $html3         = implode(" ", $html3);
                                $data          = '</svg>';
                                $html3         = $html3 . $data;
                                $html3         = str_replace("'", '"', $html3);
                                $pathWithFile  = substr($pathWithFile, 0, -4);
                                $sideId        = substr($pathWithFile, -1);
                                $fileName      = $sideId . '_image.svg';
                                $baseImagePath = $this->getPreviewImagePath();
                                $savePath      = $baseImagePath . $refid . '/';
                                $svgSavePath   = $savePath . 'withoutProduct/';
                                $baseImageURL  = $this->getPreviewImageURL();
                                $imageURL      = $baseImageURL . $refid . '/';
                                if (!file_exists($svgSavePath)) {
                                    mkdir($svgSavePath, 0777, true);
                                    chmod($svgSavePath, 0777);
                                }
                                $svgFilePath   = $svgSavePath . $fileName;
                                $svgFileStatus = file_put_contents($svgFilePath, $html3);
                                $ressult       = $imageURL . 'withoutProduct/' . $fileName;

                                $html4 = new simple_html_dom();
                                $html4->load($html3, false);
                                $clipPath = $html4->find('clipPath.boundryClipPath', 0);
                                if ($clipPath) {
                                    $clipPath->outertext = '';
                                    $htmlResult          = new simple_html_dom();
                                    $htmlResult->load($html4, false);
                                    $htmlResult->save();
                                    $svgSavePath  = $savePath . 'originalDesign/';
                                    $baseImageURL = $this->getPreviewImageURL();
                                    $imageURL     = $baseImageURL . $refid . '/';
                                    if (!file_exists($svgSavePath)) {
                                        mkdir($svgSavePath, 0777, true);
                                        chmod($svgSavePath, 0777);
                                    }
                                    $svgFilePath    = $svgSavePath . $fileName;
                                    $svgFileStatus  = file_put_contents($svgFilePath, $htmlResult);
                                    $originalDesign = $imageURL . 'originalDesign/' . $fileName;
                                }
                            } else {
                                $html2     = explode(" ", $html2);
                                $html2[10] = 'transform="translate(-' . $x . ',-' . $y . ')">';
                                unset($html2[11], $html2[12], $html2[13], $html2[14], $html2[15], $html2[16]);
                                $html2         = implode(" ", $html2);
                                $data          = '</g></svg>';
                                $html2         = $html2 . $data;
                                $html2         = str_replace("'", '"', $html2);
                                $pathWithFile  = substr($pathWithFile, 0, -4);
                                $sideId        = substr($pathWithFile, -1);
                                $fileName      = $sideId . '_image.svg';
                                $baseImagePath = $this->getPreviewImagePath();
                                $savePath      = $baseImagePath . $refid . '/';
                                $svgSavePath   = $savePath . 'withoutProduct/';
                                $baseImageURL  = $this->getPreviewImageURL();
                                $imageURL      = $baseImageURL . $refid . '/';
                                if (!file_exists($svgSavePath)) {
                                    mkdir($svgSavePath, 0777, true);
                                    chmod($svgSavePath, 0777);
                                }
                                $svgFilePath   = $svgSavePath . $fileName;
                                $svgFileStatus = file_put_contents($svgFilePath, $html2);
                                $ressult       = $imageURL . 'withoutProduct/' . $fileName;

                                $html5 = new simple_html_dom();
                                $html5->load($html2, false);
                                $clipPath = $html5->find('clipPath.boundryClipPath', 0);
                                if ($clipPath) {
                                    $clipPath->outertext = '';
                                    $htmlNew             = new simple_html_dom();
                                    $htmlNew->load($html5, false);
                                    $htmlNew->save();
                                    $svgSavePath  = $savePath . 'originalDesign/';
                                    $baseImageURL = $this->getPreviewImageURL();
                                    $imageURL     = $baseImageURL . $refid . '/';
                                    if (!file_exists($svgSavePath)) {
                                        mkdir($svgSavePath, 0777, true);
                                        chmod($svgSavePath, 0777);
                                    }
                                    $svgFilePath    = $svgSavePath . $fileName;
                                    $svgFileStatus  = file_put_contents($svgFilePath, $htmlNew);
                                    $originalDesign = $imageURL . 'originalDesign/' . $fileName;
                                }
                            }
                        } else {
                            $svg->outertext = '<g id="rect" transform="translate(0,0)">';
                            $html2->save();
                            $html2 = str_replace('</svg>', '', $html2);
                            $html2 = explode(" ", $html2);
                            $html2 = implode(" ", $html2);
                            $htmlN = new simple_html_dom();
                            $htmlN->load($html2, false);
                            $svgMainContent = $htmlN->find('g[class]', 0);
                            $htmlN->save();
                            if ($svgMainContent) {
                                $htmlN     = explode(" ", $htmlN);
                                $htmlN[10] = 'transform="translate(-' . $x . ',-' . $y . ')">';
                                unset($htmlN[11], $htmlN[12], $htmlN[13], $htmlN[14], $htmlN[15], $htmlN[16]);
                                $htmlN = implode(" ", $htmlN);
                                $data  = '</svg>';
                                $htmlN = $htmlN . $data;
                                $htmlN = str_replace("'", '"', $htmlN);

                                $pathWithFile  = substr($pathWithFile, 0, -4);
                                $sideId        = substr($pathWithFile, -1);
                                $fileName      = $sideId . '_image.svg';
                                $baseImagePath = $this->getPreviewImagePath();
                                $savePath      = $baseImagePath . $refid . '/';
                                $svgSavePath   = $savePath . 'withoutProduct/';
                                $baseImageURL  = $this->getPreviewImageURL();
                                $imageURL      = $baseImageURL . $refid . '/';
                                if (!file_exists($svgSavePath)) {
                                    mkdir($svgSavePath, 0777, true);
                                    chmod($svgSavePath, 0777);
                                }
                                $svgFilePath   = $svgSavePath . $fileName;
                                $svgFileStatus = file_put_contents($svgFilePath, $htmlN);
                                $ressult       = $imageURL . 'withoutProduct/' . $fileName;

                                $html6 = new simple_html_dom();
                                $html6->load($htmlN, false);
                                $clipPath = $html6->find('clipPath.boundryClipPath', 0);
                                if ($clipPath) {
                                    $clipPath->outertext = '';
                                    $html7               = new simple_html_dom();
                                    $html7->load($html6, false);
                                    $html7->save();
                                    $svgSavePath  = $savePath . 'originalDesign/';
                                    $baseImageURL = $this->getPreviewImageURL();
                                    $imageURL     = $baseImageURL . $refid . '/';
                                    if (!file_exists($svgSavePath)) {
                                        mkdir($svgSavePath, 0777, true);
                                        chmod($svgSavePath, 0777);
                                    }
                                    $svgFilePath    = $svgSavePath . $fileName;
                                    $svgFileStatus  = file_put_contents($svgFilePath, $html7);
                                    $originalDesign = $imageURL . 'originalDesign/' . $fileName;
                                }
                            } else {
                                $html2     = explode(" ", $html2);
                                $html2[10] = 'transform="translate(-' . $x . ',-' . $y . ')">';
                                unset($html2[11], $html2[12], $html2[13], $html2[14], $html2[15], $html2[16]);
                                $html2         = implode(" ", $html2);
                                $data          = '</g></svg>';
                                $html2         = $html2 . $data;
                                $html2         = str_replace("'", '"', $html2);
                                $pathWithFile  = substr($pathWithFile, 0, -4);
                                $sideId        = substr($pathWithFile, -1);
                                $fileName      = $sideId . '_image.svg';
                                $baseImagePath = $this->getPreviewImagePath();
                                $savePath      = $baseImagePath . $refid . '/';
                                $svgSavePath   = $savePath . 'withoutProduct/';
                                $baseImageURL  = $this->getPreviewImageURL();
                                $imageURL      = $baseImageURL . $refid . '/';
                                if (!file_exists($svgSavePath)) {
                                    mkdir($svgSavePath, 0777, true);
                                    chmod($svgSavePath, 0777);
                                }
                                $svgFilePath   = $svgSavePath . $fileName;
                                $svgFileStatus = file_put_contents($svgFilePath, $html2);
                                $ressult       = $imageURL . 'withoutProduct/' . $fileName;

                                $html4 = new simple_html_dom();
                                $html4->load($html2, false);
                                $clipPath = $html4->find('clipPath.boundryClipPath', 0);
                                if ($clipPath) {
                                    $clipPath->outertext = '';
                                    $html9               = new simple_html_dom();
                                    $html9->load($html4, false);
                                    $html9->save();
                                    $svgSavePath  = $savePath . 'originalDesign/';
                                    $baseImageURL = $this->getPreviewImageURL();
                                    $imageURL     = $baseImageURL . $refid . '/';
                                    if (!file_exists($svgSavePath)) {
                                        mkdir($svgSavePath, 0777, true);
                                        chmod($svgSavePath, 0777);
                                    }
                                    $svgFilePath    = $svgSavePath . $fileName;
                                    $svgFileStatus  = file_put_contents($svgFilePath, $html9);
                                    $originalDesign = $imageURL . 'originalDesign/' . $fileName;
                                }
                            }

                        }
                    }
                    $resOnlyImageSvgFile = XEPATH . $resOnlyImageSvgFile;
                    $status              = 'success';

                    $msg['withProduct']    = $resSvgFile;
                    $msg['withoutProduct'] = $ressult;
                    $msg['originalDesign'] = $originalDesign;
                } else {
                    $msg['message'] = "File doesn't exists.";
                }
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        }
        $msg['status'] = $status;
        if ($return == 0) {
            $this->response($this->json($msg), 200);
        }

    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Create Print Package
     *
     *@param (String)apikey
     *@param (Int)refid
     *@param (Int)orderId
     *@param (Int)itemId
     *@param (Int)printOptions
     *@return json data
     *
     */
    public function generatePrintPackage()
    {
        $apiKey  = $this->_request['apikey'];
        $refid   = $this->_request['refid'];
        $orderId = $this->_request['orderId'];
        $itemId  = $this->_request['itemId'];
        $options = $this->_request['printOptions'];
        $this->log('options:' . $options);
        $options = explode(",", $options);
        if ($this->isValidCall($apiKey)) {
            try {
                $sql  = "Select json_data,status from  " . TABLE_PREFIX . "design_state where id=" . $refid;
                $rows = $this->executeFetchAssocQuery($sql);
                if (!empty($rows)) {
                    $jsonData = $rows[0]['json_data'];
                    if ($jsonData) {
                        $fileName = 'order' . $orderId . '' . $itemId . '.zip';
                        $tempDir  = 'temp';
                        $filePath = $tempDir . '/' . $fileName;
                        if (!file_exists($tempDir)) {
                            mkdir($tempDir, 0777, true);
                            chmod($tempDir, 0777);
                        }
                        $zip     = new ZipArchive;
                        $openZip = $zip->open($filePath, ZipArchive::CREATE);
                        if ($openZip === true) {
                            $htmlFileName = 'index.html';
                            $htmlFilePath = 'temp/' . $htmlFileName;
                            $this->generateSVG($jsonData, $zip);
                            $this->createHTMLPackage($refid, $htmlFileName, $zip);
                            if (file_exists($htmlFilePath)) {
                                $zip->addFile($htmlFilePath, $htmlFileName);
                            }

                            $zip->close();
                            header("Content-type: application/zip");
                            header("Content-Disposition: attachment; filename=$fileName");
                            header("Pragma: no-cache");
                            header("Expires: 0");
                            readfile($filePath);
                            // Remove file after download
                            if (file_exists($htmlFilePath)) {
                                unlink($htmlFilePath);
                            }

                            unlink($filePath);
                        } else {
                            $this->log('OpenZip Failed');
                            echo 'Failed';
                        }
                    } else {
                        $this->log('No JSON Data');
                        $msg = array("status" => "nodata");
                        $this->response($this->json($msg), 200);
                    }
                } else {
                    $this->log('No rows:' . $sql);
                }
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        } else {
            $this->log('Invalid KEY');
            $msg = array("status" => "invalid");
            $this->response($this->json($msg), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Create Html Package
     *
     *@param (String)apikey
     *@param (Int)refid
     *@param (String)fileName
     *@param (String)zip
     *@return string
     *
     */
    protected function createHTMLPackage($refid, $fileName, $zip)
    {
        try {
            $orderId = $this->_request['orderId'];
            $itemId  = $this->_request['itemId'];
            // on the beginning of your script save original memory limit
            $original_mem = ini_get('memory_limit');
            // then set it to the value you think you need (experiment)
            ini_set('memory_limit', '256M');
            set_time_limit(0);
            $rootPath = dirname(__FILE__);
            $tempDir  = $rootPath . '/temp/';
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0777, true);
            }
            $order = $this->getSalesOrderDetails();
            if (!array_key_exists('isFault', $order)) {
                foreach ($order['items'] as $key => $items) {
                    if ($items['item_id'] == $itemId) {
                        $productId      = $items['product_id'];
                        $productOptions = $items['product_options'];
                        $productOptions = unserialize($productOptions);
                        break;
                    }
                }
                $previewImageList   = $this->getPreviewImageArray($refid);
                $printType          = $this->getPrintType();
                $customerMiddleName = $order['customer_middlename'];
                if (!$customerMiddleName) {
                    $customerMiddleName = '';
                } else {
                    $customerMiddleName .= ' ';
                }

                $customerName    = $order['customer_firstname'] . ' ' . $customerMiddleName . $order['customer_lastname'];
                $email           = $order['customer_email'];
                $telephone       = $order['shipping_address']['telephone'];
                $shippingAddress = $order['shipping_address']['street'];
                $shippingAddress .= ',' . $order['shipping_address']['postcode'];
                $shippingAddress .= '<br/> &nbsp; ' . $order['shipping_address']['city'];
                $shippingAddress .= '<br/> &nbsp; ' . $order['shipping_address']['country_id'];
                foreach ($order['items'] as $key => $items) {
                    if ($items['item_id'] == $itemId) {
                        //$itemId = $items['item_id'];
                        $orderDate      = $items['created_at'];
                        $productId      = $items['product_id'];
                        $productOptions = $items['product_options'];
                        $productOptions = unserialize($productOptions);
                        $qty            = $productOptions['info_buyRequest']['qty'];
                        $customPrice    = $productOptions['info_buyRequest']['custom_price'];
                        $refid          = $productOptions['info_buyRequest']['custom_design'];
                        $attributesList = $productOptions['attributes_info'];
                        $productName    = $productOptions['simple_name'];
                        foreach ($attributesList as $key => $value) {
                            if ($value['label'] == "Size") {
                                $size = $value['value'];
                            }

                            if ($value['label'] == "Color" || $value['label'] == "Colors") {
                                $color1 = $value['value'];
                            }

                            if ($value['label'] == "Colors2" || $value['label'] == "Colors 2" || $value['label'] == "Color2" || $value['label'] == "Color 2") {
                                $color2 = $value['value'];
                            }

                        }
                        break;
                    }
                }
                $assestsPath = 'assets/';
                $html        = '<table width="100%" cellpadding="5">'
                    . '<tr  bgcolor="#212122" color="#fff"><td colspan="3"> <b>' . $customerName . '</b> </td> <td> Order # ' . $orderId . ' </td> </tr>'
                    . '</table>';
                $html = '<h2>Invoice</h2>'
                    . '<table width="100%" cellpadding="5">'
                    . ' <tr class="table-head"><td colspan="3"> <strong class="text-u-c"> Customer Name </strong></td><td> &nbsp; </td><td style="text-align:right"> Order No: 1000042 </td></tr>'
                    . '<tr><td colspan="3"> <strong class="text-u-c"> Contact Details </strong> </td> <td> &nbsp; </td><td style="text-align:right">Order date: ' . $orderDate . ' </td> </tr>'
                    . '<tr><td colspan="3"> Tel: ' . $telephone . ' <br/> Email: ' . $email . '</td> <td> &nbsp; </td><td> &nbsp; </td>  </tr>'
                    . '<tr><td colspan="3" ><strong class="text-u-c">Shipping Address </strong> </td> <td> &nbsp;</td><td> &nbsp; </td> </tr>'
                    . '<tr><td colspan="3" rowspan="2"> ' . $shippingAddress . ' </td> <td> &nbsp;</td><td> &nbsp; </td> </tr>'
                    . '<tr>  <td> &nbsp;</td><td> &nbsp; </td> </tr>'
                    . '</table>';

                $html0 = $html;
                $html  = "<h2>Product Information</h2>";
                //Add Item Details
                $imageTitleHtml = '<tr>';
                $imageHtml      = '<tr>';
                foreach ($previewImageList as $k => $v) {
                    $svgImgName         = $v['side'] . '.svg';
                    $svgImagePath       = $assestsPath . 'svg/' . $svgImgName;
                    $tempImgName        = $v['side'] . '.png';
                    $product_image_file = file_get_contents($v['productUrl']);
                    $imagePath          = $assestsPath . 'images/' . $tempImgName;
                    $zip->addFromString($imagePath, $product_image_file);
                    $imageHtml .= '<td> '
                        . '<img src="' . $imagePath . '" alt="' . $v['side'] . '" width="150" height="150" border="0"  style="float:left"/> '
                        . '<img src="' . $svgImagePath . '" alt="' . $v['side'] . '" width="150" height="150" border="0" style="float:left;margin-left:-150px" /> '
                        . ' </td> ';
                    $imageTitleHtml .= '<td align="left"> <span class="side-name" >' . $v['side'] . ' </span> </td> ';
                }
                $imageHtml .= '</tr>';
                $imageTitleHtml .= '</tr>';
                $html .= '<table width="100%" cellpadding="0" border="0">'
                    . '<tr class="table-head"><td>'
                    . '<table width="100%" cellpadding="5" class="orderno-table">'
                    . '<tr><td> <strong class="text-u-c">ITEM: </strong> ' . $itemId . ' | <strong class="text-u-c"> PRODUCT ID: </strong>' . $productId . ' | ' . $productName . ' </td> </tr>'
                    . '</table>'
                    . ' </td></tr>'

                    . '<tr><td>'
                    . '<table width="100%" cellpadding="5" class="inner-table">'
                    . '<tr><td> <b>Size:</b> ' . $size . ' </td>  <td rowspan="3">'
                    . '' . $color1 . '<br/>'
                    . '<table  style="background: ' . $color1 . ';" width="40" height="40" border="0"> <tr> <td> &nbsp; <br/> </td></tr> </table> '
                    . '</td> <td rowspan="3"> '
                    . '' . $color2 . '<br/>'
                    . '<table style="background: ' . $color2 . ';" width="40" height="40" border="0"> <tr> <td> &nbsp; <br/> </td></tr> </table> '
                    . ' </td> '
                    . '</tr>'
                    . '<tr><td> <b>Color:</b> ' . $color1 . ' </td> </tr>'
                    . '<tr><td> <b>Color 2:</b> ' . $color2 . ' </td> </tr>'
                    . '<tr><td> <b>Print Type:</b> ' . $printType . ' </td> </tr>'
                    . '</table>'
                    . ' </td></tr>'
                    . '<tr><td>'
                    . '<table width="100%" cellpadding="5" class="inner-table border-top">'
                    . $imageTitleHtml
                    . $imageHtml
                    . '</table>'
                    . ' </td></tr>'
                    . '</table>';
                $cssFileName = "print.css";
                $cssFilePath = "css/print.css";
                $htmlStart   = "<html>"
                    . "<head>"
                    . "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $assestsPath . "css/" . $cssFileName . "\" />"
                    . "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $assestsPath . "svg/fonts/fonts.css\" />"
                    . "</head>"
                    . "<body>";

                $htmlEnd = "</body>"
                    . "</html>";
                $finalHtml = $htmlStart . $html0 . $html . $htmlEnd;
                $zip->addFile($cssFilePath, 'assets/' . $cssFilePath);
                $zip->addFromString('index.html', $finalHtml);
            } else {
                echo 'error';
            }
            // at the end of the script set it to it's original value
            // (if you forget this PHP will do it for you when performing garbage collection)
            ini_set('memory_limit', $original_mem);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Check svg file
     *
     *@param (String)req_entry
     *@param (String)dir
     *@return json data
     *
     */
    public function checkSvg($req_entry, $dir)
    {
        try {
            $h      = fopen($dir . '/' . $req_entry, 'w+');
            $xmlDoc = new DOMDocument();
            //$xmlDoc->loadXML($req);
            $xmlDoc->load($dir . '/' . $req_entry);

            $searchNode = $xmlDoc->getElementsByTagName('image');
            foreach ($searchNode as $search) {
                $url = $search->getAttribute('xlink:href');
                $url = str_replace('#', '', $url);

                if ($search->hasAttribute('xlink:href') && $url != '#' && strtolower(substr($url, -4)) == '.svg') {
                    $fileContent = file_get_contents($url);

                    $xmlDoc1 = new DOMDocument();
                    $xmlDoc1->loadXML($fileContent);
                    $viewBoxArr = $xmlDoc1->getElementsByTagName('svg');
                    foreach ($viewBoxArr as $v) {
                        $viewBox = $v->getAttribute('viewBox');
                    }
                    $viewBox  = explode(' ', $viewBox);
                    $vBwidth  = $viewBox[2];
                    $vBheight = $viewBox[3];

                    $imgWidth  = $search->getAttribute('width');
                    $imgHeight = $search->getAttribute('height');
                    $imgX      = $search->getAttribute('x');
                    $imgY      = $search->getAttribute('y');

                    $width  = (real) $imgWidth / (real) $vBwidth;
                    $height = (real) $imgHeight / (real) $vBheight;

                    $rstr        = stripos($fileContent, '<svg');
                    $fileContent = substr($fileContent, $rstr);
                    $fileContent = str_ireplace(array('<svg', '</svg'), array('<g', '</g'), $fileContent);

                    $fragmentXml = '<g id="xe_ua9o" transform="translate(' . $imgX . ', ' . $imgY . ') scale(' . $width . ', ' . $height . ')">' . $fileContent . '</g>';
                    $dom_sxe     = dom_import_simplexml(simplexml_load_string($fragmentXml));
                    $dom_sxe     = $xmlDoc->importNode($dom_sxe, true);
                    $search->parentNode->replaceChild($dom_sxe, $search);

                    $content = $xmlDoc->saveXML();
                    fwrite($h, $content);
                }
            }
        } catch (Exception $e) {
            $msg = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($msg), 200);
        }

    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Generate Svg file
     *
     *@param (String)apikey
     *@param (String)jsonData
     *@param (String)zip
     *@return string
     *
     */
    protected function generateSVG($jsonData, $zip)
    {
        try {
            $zipSVGDir      = 'assets/svg/';
            $fontFamilyList = $this->extractString($jsonData, 'font-family="', '"');
            if (empty($fontFamilyList)) {
                $fontFamilyList = $this->extractString($jsonData, 'font-family=\"', '"');
            }

            $rootPath = dirname(dirname(__FILE__));
            //create getWebFontPath() and replace below $fontDirPath
            $fontDirPath     = $rootPath . '' . TABLE_PREFIX . self::HTML5_WEBFONTS_DIR;
            $fontsCSSContent = '';
            foreach ($fontFamilyList as $fontFamily) {
                $fontPath     = $fontDirPath . $fontFamily . '/' . $fontFamily . '.ttf';
                $fontPathEOT  = $fontDirPath . $fontFamily . '/' . $fontFamily . '.eot';
                $fontPathSVG  = $fontDirPath . $fontFamily . '/' . $fontFamily . '.svg';
                $fontPathWOFF = $fontDirPath . $fontFamily . '/' . $fontFamily . '.woff';
                if (file_exists($fontPath)) {
                    $zip->addFile($fontPath, $zipSVGDir . 'fonts/' . $fontFamily . '.ttf');
                    $zip->addFile($fontPathEOT, $zipSVGDir . 'fonts/' . $fontFamily . '.eot');
                    $zip->addFile($fontPathSVG, $zipSVGDir . 'fonts/' . $fontFamily . '.svg');
                    $zip->addFile($fontPathWOFF, $zipSVGDir . 'fonts/' . $fontFamily . '.woff');
                    $cssContent = "@font-face {
						font-family: '$fontFamily';
						src: url('$fontFamily.eot?') format('eot'),
						url('$fontFamily.woff') format('woff'),
						url('$fontFamily.ttf')  format('truetype'),
						url('$fontFamily.svg#$fontFamily') format('svg');
					}";
                    $fontsCSSContent .= $cssContent;
                }
            }
            $zip->addFromString($zipSVGDir . 'fonts/fonts.css', $fontsCSSContent);
            $count             = 0;
            $jsonDecodedObject = $this->formatJSONToArray($jsonData, false);
            if ($jsonDecodedObject) {
                foreach (get_object_vars($jsonDecodedObject) as $property => $value) {
                    if ($this->startsWith($property, 'side')) {
                        $side = trim($jsonDecodedObject->$property);
                        if ($side != "") {
                            $count++;
                            $side = str_replace(array("\\r\\n", "\\r", "\\n"), "<br />", $side);
                            $side = stripslashes($side);
                            $side = str_replace(array("<br />"), "\\n", $side);
                            $side = str_replace(array("\""), "'", $side);
                            //Add CSS Definition to SVG
                            $fontText    = '<defs><style type="text/css">@import url(fonts/fonts.css);</style></defs>';
                            $newContents = preg_replace("/<xhtml[^>]+\>/i", "$fontText", $side);
                            $svgFileName = $zipSVGDir . 'side' . $count . '.svg';
                            $zip->addFromString($svgFileName, $newContents);
                        }
                    }
                }
            }
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Extract String
     *
     *@param (String)apikey
     *@param (String)string
     *@param (Int)start
     *@param (Int)end
     *@return string
     *
     */
    protected function extractString($string, $start, $end)
    {
        $string = stripcslashes($string);
        $found  = array();
        $pos    = 0;
        while (true) {
            $pos = strpos($string, $start, $pos);
            if ($pos === false) {
                // Zero is not exactly equal to false...
                return $found;
            }
            $pos += strlen($start);
            $len  = strpos($string, $end, $pos) - $pos;
            $text = substr($string, $pos, $len);
            if (!in_array($text, $found)) {
                $found[] = $text;
            }

        }
    }

    /**
     * Check if Request a Quote is Enabled
     *
     * @param   nothing
     * @return  boolean true/false
     */
    public function checkSendQuote()
    {
        //if(APPNAME != '')$url = $this->getCurrentUrl().'/designer-tool/localsettings.js';
        $url      = $this->getCurrentUrl() . '/designer-tool/localsettings.js';
        $tarray   = array(" ", "\n", "\r");
        $contents = $this->getFileContents($url);
        $contents = trim(str_replace($tarray, "", $contents));
        $contents = substr($contents, 0, -1);
        $contents = explode("localSettings=", $contents);
        $contents = json_decode($contents['1'], true);
        $isQuote  = $contents['is_send_a_quote'];
        return $isQuote;
    }
    /**
     * download single order zip for orderApp
     *
     * @param   nothing
     * @return  boolean true/false
     */
	public function downloadSingleOrderZipApp(){
        $msg = '';
        $order_id = (isset($this->_request['order_id'])) ? $this->_request['order_id'] : 0;
		$increment_id = (isset($this->_request['increment_id'])) ? $this->_request['increment_id'] : 0;
        $orderPath = $this->getOrdersPath();
		
		if ((is_dir($orderPath . "/" . $increment_id)) || is_dir($orderPath . "/" . $order_id)) {

			if ($increment_id > 0){
	
				//echo $order_id; echo "<br/>";
				if ($increment_id != "" && $increment_id != 0) {
					$zipName = 'orders_'.time().'.zip';
					$zip = new \ZipArchive;
					$res = $zip->open($orderPath . '/' . $zipName, ZipArchive::CREATE);
					// check if zip file created //
					if ($res === true) {
						$zipCheckKounter = 0;
						$loopKounter = 0;
						
						// fetch order_id or incremental_id //
						if (file_exists($orderPath . "/" . $increment_id) && is_dir($orderPath . "/" . $increment_id)) { 
							$orderFolderPath = $orderPath . "/" . $increment_id; // increment_id //
							$orderTypeFlag = 1;
							$order_id = $increment_id;
						}else{
							//$order_id = $order_id_arr[$loopKounter];  // order_id // 
							$orderFolderPath = $orderPath . "/" . $order_id;
							$orderTypeFlag = 0;
						}
						//echo $orderTypeFlag; exit;
						//echo $order_id; exit;
						//echo $orderPath."/".$zipName; exit;
						
						if (file_exists($orderFolderPath) && is_dir($orderFolderPath)) {
							//// Create SVG files for the Order /////
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
								// check the item folders under the product folder //
								foreach ($scanProductDir as $itemDir) {
									if ($itemDir != '.' && $itemDir != '..' && is_dir($orderFolderPath . "/" . $itemDir)) { //to fetch only item id folders//
										
										//Fetch the design state json details //
										$designState = file_get_contents(XEPATH . "designer-tool" . ORDER_PATH_DIR . "/" . $order_id . "/" . $itemDir . "/designState.json");
										$resultDesign = $this->formatJSONToArray($designState);
										
										// check if side_index folder exists or not //
										$sidePath = $orderFolderPath . "/" . $itemDir;
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
										//echo $orderTypeFlag;  echo $order_id; exit;
										
										// for new file structure //
										if($orderTypeFlag == 1){
											//check and find the sides of each item //
											$sidePath = $orderFolderPath . "/" . $itemDir; 
											if (file_exists($sidePath) && is_dir($sidePath)) {
												$scanSideDir = scandir($sidePath); // scan item directory to fetch all side folders //
												$scanSideDirSide = $scanSideDir;
												//print_r($scanSideDir);
													if(is_array($scanSideDir)) {
														foreach ($scanSideDir as $sideDir) {
															if($sideDir != '.' && $sideDir != '..' && is_dir($orderFolderPath . "/" . $itemDir. "/". $sideDir)) {
															//to fetch only side folders//
															$i = str_replace("side_","",$sideDir);
															//echo $orderFolderPath."/".$itemDir."/".$sideDir."/preview_0".$i.".svg"; exit;
															if (file_exists($orderFolderPath."/".$itemDir."/".$sideDir."/preview_0".$i.".svg")) {
															// with product svg file exists or not//
														
																if (!file_exists($orderFolderPath . "/" . $itemDir . "/" . $sideDir. "/". $sideDir."_".$itemDir."_".$order_id . ".svg")) {
																/* check if without product svg file exists or not.
																if not exist, then create the file
																 */
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
										// for old file structure //
										else if($orderTypeFlag == 0){
											//to fetch only item id folders//
											// fetch all with product svg files //
											$kounter = 1;
											for($i=1;$i<=15;$i++){
												if(file_exists($orderFolderPath."/".$itemDir."/preview_0".$i.".svg")){// with product svg file exists or not//		
													if(!file_exists($orderFolderPath."/".$itemDir."/".$i.".svg")){ 
														/* check if without product svg file exists or not.
														   if not exist, then create the file
														*/
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
						}else{
							$msg = 'No file to download';
						}
					}
					else {
						$msg = 'Zip Creation Failed';
					}
				}
	
						$tarray = array(" ", "\n", "\r");
						$order_list_arr = array();
						$temp_arry212 = array();
						$orderNo = $order_id;
						
						//echo $orderFolderPath; exit;
						if (file_exists($orderFolderPath)) {
							if (file_exists($orderFolderPath . '/order.json')) {
								$order_json = file_get_contents($orderFolderPath . '/order.json');
								$json_content = $this->formatJSONToArray($order_json);
								$noOfRefIds = count($json_content['order_details']['order_items']);
								if ($noOfRefIds > 0) {
									$zip->addEmptyDir($orderNo);
									$zip->addFile($orderFolderPath . '/order.json', $orderNo . '/order.json');
	
									// add info.html file //
									if (file_exists($orderFolderPath . '/info.html')) {
										$zip->addFile($orderFolderPath . '/info.html', $orderNo . '/info.html');
									}
	
									$item_kounter = 1;
									foreach ($json_content['order_details']['order_items'] as $item_details) {
										$item_id = $item_details['item_id'];
										$ref_id = $item_details['ref_id'];
										$scanDirArr = scandir($orderFolderPath . "/" . $item_id); //for name and number folder scan
										if ($item_id != null && $item_id > 0 && $ref_id != null && $ref_id > 0) {
											if($orderTypeFlag == 1){
												// add side folders inside item directory //
												//Fetch the design state json details //
												$designState = file_get_contents(XEPATH . "designer-tool" . ORDER_PATH_DIR . "/" . $orderNo . "/" . $item_id . "/designState.json");
												$resultDesign = $this->formatJSONToArray($designState);
												//echo "<pre>"; print_r($resultDesign['sides']);
												$sidesCount = count($resultDesign['sides']);
												for($flag=1;$flag<=$sidesCount;$flag++){
													if(is_dir($orderFolderPath . "/" . $item_id."/side_".$flag)){
														$zip->addEmptyDir($orderNo . "/" . $item_id."/side_".$flag);
													}
													$scanDirArr = scandir($orderFolderPath . "/" . $item_id."/side_".$flag);
													if (count($scanDirArr) > 2) {
														//for name and number folder scan
														foreach ($scanDirArr as $nameAndNumberDir) {
															if ($nameAndNumberDir != '.' && $nameAndNumberDir != '..' && is_dir($orderFolderPath . "/" . $item_id . "/side_".$flag.'/' . $nameAndNumberDir)) {
																$zip->addEmptyDir($orderNo . "/" . $item_id."/side_".$flag);
																$from_url = $orderFolderPath . "/" . $item_id."/side_".$flag.'/' . $nameAndNumberDir;
																$options = array('add_path' => $orderNo . "/" . $item_id ."/side_".$flag.'/' . $nameAndNumberDir . "/", 'remove_path' => $from_url);
																$zip->addGlob($from_url . '/*{svg}', GLOB_BRACE, $options);
															}
	
														}
													}
													//copy to side folder //
													$fromUrlSide = $orderFolderPath . "/" . $item_id."/side_".$flag;
													$optionsSide = array('add_path' => $orderNo . "/" . $item_id . "/side_".$flag."/", 'remove_path' => $fromUrlSide);
													$zip->addGlob($fromUrlSide . '/*{svg,json,html,pdf,png,jpg}', GLOB_BRACE, $optionsSide);
													
													//copy to asset folder //
													if(is_dir($orderFolderPath . "/" . $item_id."/side_".$flag."/assets")){
														$zip->addEmptyDir($orderNo . "/" . $item_id."/side_".$flag."/assets");
														
														$fromUrlAsset = $orderFolderPath . "/" . $item_id."/side_".$flag."/assets";
														$optionsAsset = array('add_path' => $orderNo . "/" . $item_id . "/side_".$flag."/assets/", 'remove_path' => $fromUrlAsset);
														$zip->addGlob($fromUrlAsset . '/*{svg,json,html,pdf,png,jpg,jpeg,PNG,bmp,BMP}', GLOB_BRACE, $optionsAsset);
													}
													
													//copy to preview folder //
													if(is_dir($orderFolderPath . "/" . $item_id."/side_".$flag."/preview")){
														$zip->addEmptyDir($orderNo . "/" . $item_id."/side_".$flag."/preview");
													}
													
													$fromUrlPreview = $orderFolderPath . "/" . $item_id."/side_".$flag."/preview";
													$optionsPreview = array('add_path' => $orderNo . "/" . $item_id . "/side_".$flag."/preview/", 'remove_path' => $fromUrlPreview);
													$zip->addGlob($fromUrlPreview . '/*{png,PNG}', GLOB_BRACE, $optionsPreview);
													
													//delete preview svg from zip //
													$zip->deleteName($orderNo . "/" . $item_id."/side_".$flag."/preview_0".$flag.".svg");
												}
												
												$from_url = $orderFolderPath . "/" . $item_id;
												$options = array('add_path' => $orderNo . "/" . $item_id . "/", 'remove_path' => $from_url);
												$zip->addGlob($from_url . '/*{svg,json,html,pdf,png,jpg}', GLOB_BRACE, $options);
												$zipCheckKounter++;
											}else if($orderTypeFlag == 0){
												$scanDirArr = scandir($orderFolderPath."/".$item_id);//for name and number folder scan
												if(count($scanDirArr) >2){//for name and number folder scan
													foreach($scanDirArr as $nameAndNumberDir){
														if($nameAndNumberDir != '.' && $nameAndNumberDir != '..' && is_dir($orderFolderPath."/".$item_id."/".$nameAndNumberDir)){
															$zip->addEmptyDir($orderNo."/".$item_id."/".$nameAndNumberDir);
															$from_url = $orderFolderPath."/".$item_id."/".$nameAndNumberDir;
															$options = array('add_path' => $orderNo."/".$item_id."/".$nameAndNumberDir."/",'remove_path' => $from_url);
															$zip->addGlob($from_url.'/*{svg}', GLOB_BRACE, $options);
														}
													}
												}//end for name and number zip download
												$zip->addEmptyDir($orderNo."/".$item_id);
												$from_url = $orderFolderPath."/".$item_id;
												$options = array('add_path' => $orderNo."/".$item_id."/",'remove_path' => $from_url);
												$zip->addGlob($from_url.'/*{svg,json,html,pdf,png,jpg}', GLOB_BRACE, $options);
												$zipCheckKounter++;
											}	
										}
										$item_kounter++;
									}
								}
							}else{
								$msg = "order.json file not found";
							}
						}
					$zip->close();
					//echo $orderPath.'/'.$zipName; exit;
					//$this->zipDownload($orderPath . '/' . $zipName, $zipCheckKounter);
					if(file_exists($orderPath . '/' . $zipName)){
						$store_url = XEPATH . "designer-tool/custom-assets/orders/";
						$msg = $store_url . $zipName;
					}else{
						$msg = "No zip file found to download";
					}
			} else {
				$msg = 'Order not found to download';
			}
		}
		else{
				$msg = 'file not found to download';
		}
		$msg2 = array("Response" => $msg);
        $this->response($this->json($msg2), 200);
	 }

    /**
     * To create output files for multiple boundary products
     *
     * @param   (string)$fileNameMultiBound
     * @param   (string)$svgSavePath
     * @param   (string)$req
     */
    public function createMultipleBoundarySvg($req, $svgSavePath, $fileNameMultiBound)
    {
        $html = new simple_html_dom();
        $html->load($req);
        $svg     = $html->find('image#svg_1', 0);
        $svgroot = $html->find('svg#svgroot', 0);
        if ($svg) {
            $mainContent      = $html->find('g.mainContent', 0);
            $defs             = $html->find('defs', 0);
            $oldArr 		  = array('<clippath','<fecolormatrix','patternunits=','preserveaspectratio','</fecolormatrix','</clippath');
			$replceArr 		  = array('<clipPath','<feColorMatrix','patternUnits=','preserveAspectRatio','</feColorMatrix','</clipPath');
			$defs             = str_replace($oldArr,$replceArr,$defs);
            $layer_background = $html->find('g#layer_background', 0);
            if ($mainContent) {
                $main = $html->find("g[id^=layer_area_]");
                foreach ($main as $k => $g) {
                    $id       = $main[$k]->id;
                    $name     = $main[$k]->name;
                    $main[$k] = str_replace('preserveaspectratio', 'preserveAspectRatio', $main[$k]);
                    if ($id == "layer_area_" . $k . "") {
						if (strpos($main[$k], "xe_p")!==false){
							$main[$k]->style = 'display:block;';
							$html2           = '<svg xmlns="http://www.w3.org/2000/svg" id="svgroot" xlinkns="http://www.w3.org/1999/xlink" width="' . $svgroot->width . '" height="' . $svgroot->height . '" x="0" y="0" overflow="visible">
							<g xmlns="http://www.w3.org/2000/svg" xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:xlink="http://www.w3.org/1999/xlink" width="' . $mainContent->width . '" height="' . $mainContent->height . '" class="mainContent">' . $defs . $layer_background . '
							' . $main[$k] . '
							</g></svg>';
							$oldStr = array('<fegaussianblur','<clippath','<fecolormatrix','</fegaussianblur','</clippath','</fecolormatrix');
							$replaceStr = array('<feGaussianBlur','<clipPath','<feColorMatrix','</feGaussianBlur','</clipPath','</feColorMatrix');
							$html2 = str_replace($oldStr,$replaceStr,$html2);
							if (!file_exists($svgSavePath)) {
								mkdir($svgSavePath, 0777, true);
								chmod($svgSavePath, 0777);
							}
							$svgFilePath   = $svgSavePath . $name . '_' . $k . '_' . $fileNameMultiBound;
							$svgFileStatus = @file_put_contents($svgFilePath, $html2);
						}
                    }
                }
            }
        }
    }
}
