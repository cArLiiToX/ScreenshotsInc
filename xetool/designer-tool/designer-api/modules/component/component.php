<?php
class Component extends ComponentStore
{

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get base path
     *
     *@param (String)apikey
     *@return base path
     *
     */
    public function getBasePath()
    {
        $basePath = realpath(dirname(__FILE__) . self::ROOT_PATH);
        $basePath = str_replace('\\', '/', $basePath);
        $basePath = strstr($basePath, '/designer-api/', true);
        return $basePath;
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get Print image path
     *
     *@param (String)apikey
     *@return base image path
     *
     */
    protected function getPrintMethodImagePath()
    {
        return $this->getBasePath() . self::HTML5_PRINT_METHOD_DIR;
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get Design image path
     *
     *@param (String)apikey
     *@return base image path
     *
     */
    protected function getDesignImagePath()
    {
        $baseImagePath = $this->getBasePath() . self::HTML5_DESIGN_DIR;
        return $baseImagePath;
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get Shape image path
     *
     *@param (String)apikey
     *@return base image path
     *
     */
    protected function getShapeImagePath()
    {
        $baseImagePath = $this->getBasePath() . self::HTML5_SHAPE_DIR;
        return $baseImagePath;
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get Distress image path
     *
     *@param (String)apikey
     *@return base image path
     *
     */
    protected function getDistressImagePath()
    {
        $baseImagePath = $this->getBasePath() . self::HTML5_DISTRESS_DIR;
        return $baseImagePath;
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get Paletta image path
     *
     *@param (String)apikey
     *@return base image path
     *
     */
    protected function getPaletteImagePath()
    {
        $baseImagePath = $this->getBasePath() . self::HTML5_PALETTE_DIR;
        return $baseImagePath;
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get getWordCloud image path
     *
     *@param (String)apikey
     *@return base image path
     *
     */
    protected function getWordcloudImagePath()
    {
        $baseImagePath = $this->getBasePath() . self::HTML5_WORDCLOUD_DIR;
        return $baseImagePath;
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get Textfx image path
     *
     *@param (String)apikey
     *@return base image path
     *
     */
    protected function getTextFXImagePath()
    {
        $baseImagePath = $this->getBasePath() . self::HTML5_TEXTFX_DIR;
        return $baseImagePath;
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get webfonts image path
     *
     *@param (String)apikey
     *@return base image path
     *
     */
    protected function getWebfontsPath()
    {
        $baseImagePath = $this->getBasePath() . self::HTML5_WEBFONTS_DIR;
        return $baseImagePath;
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get user image path
     *
     *@param (String)apikey
     *@return base image path
     *
     */
    protected function getUserImagePath()
    {
        $baseImagePath = $this->getBasePath() . '/' . self::USER_IMAGE_DIR . '/';
        return $baseImagePath;
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get preview image path
     *
     *@param (String)apikey
     *@return base image path
     *
     */
    protected function getPreviewImagePath()
    {
        $baseImagePath = $this->getBasePath() . '/' . self::PREVIEW_IMAGE_DIR . '/';
        return $baseImagePath;
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get slot preview url
     *
     *@param (String)apikey
     *@return base image path
     *
     */
    protected function getSlotsPreviewPath()
    {
        $baseImagePath = $this->getBasePath() . '/' . self::HTML5_USERSLOTS_DIR . '/';
        return $baseImagePath;
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get user image url
     *
     *@param (String)apikey
     *@return base image path
     *
     */
    protected function getUserImageURL()
    {
        $baseImagePath = $this->getCurrentUrl() . self::ASSETS_CONTAINER_DIR . self::USER_IMAGE_DIR . '/';
        return $baseImagePath;
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get slot image url
     *
     *@param (String)apikey
     *@return base image path
     *
     */
    protected function getSlotsPreviewURL()
    {
        $baseImagePath = $this->getCurrentUrl() . self::ASSETS_CONTAINER_DIR . self::HTML5_USERSLOTS_DIR . '/';
        return $baseImagePath;
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get design image url
     *
     *@param (String)apikey
     *@return base image path
     *
     */
    protected function getDesignImageURL()
    {
        $baseImagePath = $this->getCurrentUrl() . self::ASSETS_CONTAINER_DIR . self::HTML5_DESIGN_DIR;
        return $baseImagePath;
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get distress image url
     *
     *@param (String)apikey
     *@return base image path
     *
     */
    protected function getDistressImageURL()
    {
        $baseImagePath = $this->getCurrentUrl() . self::ASSETS_CONTAINER_DIR . self::HTML5_DISTRESS_DIR;
        return $baseImagePath;
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get shape image url
     *
     *@param (String)apikey
     *@return base image path
     *
     */
    protected function getShapeImageURL()
    {
        $baseImagePath = $this->getCurrentUrl() . self::ASSETS_CONTAINER_DIR . self::HTML5_SHAPE_DIR;
        return $baseImagePath;
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get preview image url
     *
     *@param (String)apikey
     *@return base image path
     *
     */
    protected function getPreviewImageURL()
    {
        $baseImagePath = $this->getCurrentUrl() . self::ASSETS_CONTAINER_DIR . self::PREVIEW_IMAGE_DIR . '/';
        return $baseImagePath;
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get oredr image path
     *
     *@param (String)apikey
     *@return base image path
     *
     */
    protected function getOrdersPath()
    {
        $baseImagePath = $this->getBasePath() . self::ORDER_PATH_DIR;
        return $baseImagePath;
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get texton image path
     *
     *@param (String)apikey
     *@return base image path
     *
     */
    protected function getTextonImagePath()
    {
        $baseImagePath = $this->getBasePath() . '/' . self::HTML5_TEXTONPATH_DIR;
        return $baseImagePath;
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get mask image path
     *
     *@param (String)apikey
     *@return base image path
     *
     */
    protected function getMaskImagePath()
    {
        $baseImagePath = $this->getBasePath() . '/' . self::HTML5_MASK_IMAGE_DIR;
        return $baseImagePath;
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get mask image Url
     *
     *@param (String)apikey
     *@return String
     *
     */
    protected function getMaskImageURL()
    {
        $baseImagePath = $this->getCurrentUrl() . self::ASSETS_CONTAINER_DIR . self::HTML5_MASK_IMAGE_DIR;
        return $baseImagePath;
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get template image path
     *
     *@param (String)apikey
     *@return base image path
     *
     */
    protected function getTemplateImagePath()
    {
        $baseImagePath = $this->getBasePath() . '/' . self::TEMPLATE_IMAGE_DIR . '/';
        return $baseImagePath;
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get template image url
     *
     *@param (String)apikey
     *@return base image url
     *
     */
    protected function getTemplateImageURL()
    {
        $baseImagePath = $this->getCurrentUrl() . self::ASSETS_CONTAINER_DIR . self::TEMPLATE_IMAGE_DIR . '/';
        return $baseImagePath;
    }

    /**
     *
     *date of created 2-2-2016(dd-mm-yy)
     *date of Modified 13-4-2016(dd-mm-yy)
     *Delete Zip Folder
     *
     *@param (String)apikey
     *@param (String)path
     *@return string data
     *
     */
    public function deleteZipFileFolder($path)
    {
        try {
            if (is_dir($path) === true) {
                $files = array_diff(scandir($path), array('.', '..'));
                foreach ($files as $file) {
                    $this->deleteZipFileFolder(realpath($path) . '/' . $file);
                }
                return rmdir($path);
            } else if (is_file($path) === true) {
                return unlink($path);
            }
            return false;
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            return $result;
        }
    }

    /**
     *
     *date of created 2-2-2016(dd-mm-yy)
     *date of Modified 13-4-2016(dd-mm-yy)
     *Download Zip Folder
     *
     * @param (String)apikey
     * @param (String)fileToDownload
     * @param (Int)zipCheckKounter
     * @return JSON  data
     *
     */
    public function zipDownload($fileToDownload, $zipCheckKounter)
    {
        try {
            if ($zipCheckKounter > 0) {
                if (file_exists($fileToDownload)) {
                    header('Content-Description: File Transfer');
                    header("Content-type: application/x-msdownload", true, 200);
                    header('Content-Disposition: attachment; filename=' . basename($fileToDownload));
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header("Pragma: no-cache");
                    header('Content-Length: ' . filesize($fileToDownload));
                    readfile($fileToDownload);
                    if (file_exists($fileToDownload)) {
                        unlink($fileToDownload);
                    }
                    exit;
                } else {
                    $msg = "file not found to download";
                }
            } else {
                if (file_exists($fileToDownload)) {
                    unlink($fileToDownload);
                }
                $msg = "No data found to download Zip";
            }
        } catch (Exception $e) {
            $msg = 'Caught exception: ' . $e->getMessage();
        }
        $msg = array("Response" => $msg);
        $this->response($this->json($msg), 200);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 14-4-2016(dd-mm-yy)
     *get laguage path
     *
     * @return language path
     *
     */
    protected function getLanguagePath()
    {
        return $this->getBasePath() . self::LANGUAGE_DIR;
    }

    //////////////*************** OTHER UNDECIDED METHODS **************************//////////////////

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Get Units
     *
     *@param (String)apikey
     *@return json data
     *
     */
    public function getUnits()
    {
        try {
            $sql = "Select * from " . TABLE_PREFIX . "units";
            $result = $this->executeFetchAssocQuery($sql);
            if (!empty($result)) {
                $responseData = array();
                foreach ($result as $rows) {
                    $unit_id = intval($rows['id']);
                    $unit_name = $rows['name'];
                    $unit_view = $rows['view_name'];
                    $data = array("unit_id" => $unit_id, "unit_name" => $unit_name, "unit_view" => $unit_view);
                    $responseData[] = $data;
                }
                $this->response($this->json($responseData), 200);
            } else {
                $msg = array("unit_id" => 1, "unit_name" => 'in', "unit_view" => 'inches', "status" => "nodata");
                $this->response($this->json($msg), 200);
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
     *Create Share Image
     *
     *@param (String)apikey
     *@param (Int)refid
     *@param (Array)designData
     *@param (Array)previewImgData
     *@return json data
     *
     */
    public function createShareImage()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('!POST', 406);
        }
        $apiKey = $this->_request['apikey'];
        $refid = $this->_request['refid'];
        $designData = $this->_request['designData'];
        $previewImgData = $this->_request['previewImgData'];

        $productURL = $previewImgData["productURL"];
        $svgData = $previewImgData["svgData"];
        $qp = htmlqp("sharedPreview.html");
        if ($qp) {
            $elmntContent = '<img src="' . $productURL . '"/>' . $svgData;
            $qp->find('div#capture_product')->html($elmntContent)->writeHTML('sharedPreview.html');
        } else {
            $this->log(" generateShareImage :: Invalid QP html:");
        }
        $refid = $this->saveDesignStateCart($apikey, $refid, $designData);

        if ($refid > 0) {
            $ext = 'png';
            $fileName = '1.' . $ext;

            $baseImagePath = $this->getPreviewImagePath();
            $savePath = $baseImagePath . $refid . '/';
            $baseImageURL = $this->getPreviewImageURL();
            $imageURL = $baseImageURL . $refid . '/';
            try {
                if (!file_exists($savePath)) {
                    mkdir($savePath, 0777, true);
                    chmod($savePath, 0777);
                }
                $filePath = $savePath . $fileName;
                $fileURL = $imageURL . $fileName;

                $phantomPath = dirname(__FILE__) . '/';
                $filestatus = exec($phantomPath . 'phantomjs webCapture.js sharedPreview.html ' . $filePath);
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
            if ($filestatus) {
                $msg = array("status" => "success", "refid" => $refid, "previewImage" => $fileURL, "filePath" => $filePath);
                $this->response($this->json($msg), 200);
            } else {
                $msg = array("status" => "failed", "error" => $filestatus);
                $this->response($this->json($msg), 200);
            }
        } else {
            $msg = array("status" => "failed", "refid" => $refid);
            $this->response($this->json($msg), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Save Design State
     *
     *@param (String)apikey
     *@param (Int)refid
     *@param (Array)data
     *@return json data
     *
     */
    public function saveDesignState()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('!POST', 406);
        }
        $apiKey = $this->_request['apikey'];
        $refid = $this->_request['refid'];
        $jsonData = $this->_request['data'];
        $jsonData = $this->executeEscapeStringQuery($jsonData);
        if ($this->isValidCall($apiKey)) {
            try {
                if ($refid == 0) {
                    $sql = "INSERT INTO " . TABLE_PREFIX . "design_state (json_data,  date_created) VALUES ('$jsonData', now())";
                    $id = $this->executeGenericInsertQuery($sql);
                    if ($id) {
                        $msg = array("status" => "success", "refid" => $id);
                        $this->response($this->json($msg), 200);
                    } else {
                        $msg = array("status" => "failed");
                        $this->response($this->json($msg), 200);
                    }
                } else {
                    $sql = "update " . TABLE_PREFIX . "design_state set json_data='$jsonData' where id=" . $refid;
                    $status = $this->executeGenericDMLQuery($sql);
                    if ($status) {
                        $msg = array("status" => "success", "refid" => $refid);
                        $this->response($this->json($msg), 200);
                    } else {
                        $msg = array("status" => "failed");
                        $this->response($this->json($msg), 200);
                    }
                }
                $this->response('', 204);
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Get printtype name
     *
     *@param (String)apikey
     *@return string
     *
     */
    public function getPrintType()
    {
        try {
            $sql = "SELECT name FROM " . TABLE_PREFIX . "printing_details where status=true";
            $rows = $this->executeFetchAssocQuery($sql);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
        $name = $rows[0]['name'];
        return $name;
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Append png image file
     *
     *@param (String)apikey
     *@param (Int)refid
     *@param (String)zip
     *@return trye or false
     *
     */
    public function appendPNG($refid, $zip)
    {
        try {
            $basePath = $this->getPreviewImagePath();
            $baseImagePath = $basePath . $refid . '/';
            $sql = "Select image,side from  " . TABLE_PREFIX . "preview_image_data where refid=" . $refid . ' order by side';
            $result = $this->executeFetchAssocQuery($sql);
            //$this->log('result:'.json_encode($result), true, 'jitendra.txt');
            if (!empty($result)) {
                foreach ($result as $rows) {
                    $image = $rows['image'];
                    $side = $rows['side'];
                    $imagePath = $baseImagePath . $image;
                    $designImagePath = $baseImagePath . 'design/' . $image;
                    if (file_exists($imagePath)) {
                        $zip->addFile($imagePath, 'preview/' . $image);
                    } else {
                        $this->log('FNF:' . $imagePath);
                    }

                    if (file_exists($designImagePath)) {
                        $zip->addFile($designImagePath, 'png/' . $image);
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
     *Gets Design state data
     *
     *@param (String)apikey
     *@param (Int)refid
     *@return json data
     *
     */
    public function getDesignState()
    {
        $apiKey = $this->_request['apikey'];
        $id = $this->_request['refid'];
        $id = $this->executeEscapeStringQuery($id);
        if ($this->isValidCall($apiKey)) {
            try {
                $jsonData = '';
                $fileName = 'designState.json';
                $baseImagePath = $this->getPreviewImagePath();
                $savePath = $baseImagePath . $id . '/';
                $stateDesignPath = $savePath . 'svg/';
                $stateDesignPath = $stateDesignPath . $fileName;
                if (isset($id) && $id != '') {
                    if (!file_exists($stateDesignPath)) {
                        $sql = "Select json_data,status from " . TABLE_PREFIX . "design_state where id=" . $id;
                        $rows = $this->executeFetchAssocQuery($sql);
                        $jsonData = $rows[0]['json_data'];
                        $status = $rows[0]['status'];
                        $jsonData = $this->formatJSONToArray($jsonData); // converting json string to array
                    } else {
                        $jsonData = $this->formatJSONToArray(file_get_contents($stateDesignPath));
                        $status = 0;
                    }
                    if ($jsonData != '') {
                        if (isset($this->_request['side_index'])) {
                            $index = $this->_request['side_index'];
                            $arr = $jsonData['sides'][$index];
                            $jsonData['sides'] = array();
                            $jsonData['sides'][$index] = $arr;
                        } else { $index = 0;}

                        $sql = "SELECT side,scale_ratio FROM " . TABLE_PREFIX . "mask_data WHERE productid=" . $jsonData['productInfo']['productId'] . ' ORDER BY side';
                        $res = $this->executeFetchAssocQuery($sql);

                        $msg = array('jsondata' => $jsonData, 'islocked' => $status);
                        $msg = $this->svgJSON($msg);
                        if (isset($this->_request['printType'])) {
                            for ($i = $index; $i < sizeof($jsonData['sides']); $i++) {
                                $jsonData['sides'][$i]['side'] = $res[$i]['side'];
                                $jsonData['sides'][$i]['scale_ratio'] = $res[$i]['scale_ratio'];
                                if ($this->_request['printType'] == 1) {
                                    $sideUrl = $this->parsePrintSVG($jsonData['sides'][$i]['svg']);
                                    $jsonData['sides'][$i]['svg'] = rawurlencode($sideUrl['svgStringwithImageURL']);
                                } else {
                                    $jsonData['sides'][$i]['svg'] = rawurlencode($jsonData['sides'][$i]['svg']);
                                }

                            }
                            if (isset($this->_request['side_index'])) {
                                $jsonData['sides'] = $jsonData['sides'][$this->_request['side_index']];
                            }
                            $msg = array('jsondata' => $jsonData, 'islocked' => $status);
                            $msg = $this->svgJSON($msg);
                        } else {
                            for ($i = $index; $i < sizeof($jsonData['sides']); $i++) {
                                $jsonData['sides'][$i]['svg'] = rawurlencode($jsonData['sides'][$i]['svg']);
                            }
                            $msg = array('jsondata' => $jsonData, 'islocked' => $status);
                            $msg = $this->svgJSON($msg);
                        }
                        $this->response($msg, 200);
                    } else {
                        $msg = array("status" => "nodata");
                        $this->response($this->json($msg), 200);
                    }
                } else {
                    $msg = array("status" => "norefid");
                    $this->response($this->json($msg), 200);
                }
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Update design state status
     *
     *@param (String)apikey
     *@param (Int)refid
     *@return json data
     *
     */
    public function updateDesignStateStatus()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }
        $refid = $this->_request['refid'];
        $apiKey = $this->_request['apikey'];
        if ($this->isValidCall($apiKey)) {
            $msg = "";
            try {
                $sql = "update " . TABLE_PREFIX . "design_state set status=1 where  id=" . $refid;
                $status = $this->executeGenericDMLQuery($sql);
                $msg['status'] = ($status) ? 'success' : 'failed';
            } catch (Exception $e) {
                $msg = array('Caught exception:' => $e->getMessage());
            }
            $this->response($this->json($msg), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 21-4-2016 (dd-mm-yy)
     *Get Gellery image Images
     *
     *@param (String)apikey
     *@param (Int)refid
     *@param (Array)data
     *@return json data
     *
     */
    public function getGalleryImages()
    {
        try {
            $apiKey = $this->_request['apikey'];
            $customerId = $this->_request['customerId'];
            $baseImageURL = $this->getUserImageURL();
            $imageURL = $baseImageURL . $customerId . '/';

            if ($this->isValidCall($apiKey)) {
                $sql = "Select image,thumbnail,type,refid from  " . TABLE_PREFIX . "image_data where customer_id=" . $customerId;
                $result = mysqli_query($this->db, $sql);
                if (mysqli_num_rows($result) > 0) {
                    $images = array();
                    while ($rows = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                        $refid = $rows['refid'];
                        $image = $rows['image'];
                        $thumbnail = $rows['thumbnail'];
                        $type = $rows['type'];
                        $data = array("filename" => $image, "thumbnail" => $thumbnail, "filepath" => $imageURL, "type" => $type);
                        $images[] = $data;
                    }
                    $this->response($this->json($images), 200);
                } else {
                    $msg = array("status" => "nodata");
                    $this->response($this->json($msg), 200);
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
     *Save Preview Images
     *
     *@param (String)apikey
     *@param (Int)refid
     *@param (Array)data
     *@return json data
     *
     */
    public function savePreviewImages()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $apiKey = $this->_request['apikey'];
        $refid = $this->_request['refid'];
        $jsonData = $this->_request['data'];
        if ($this->isValidCall($apiKey)) {
            try {
                $fileList = array();
                $dataArray = $this->formatJSONToArray($jsonData);
                $ext = 'png';
                $baseImagePath = $this->getPreviewImagePath();
                $savePath = $baseImagePath . $refid . '/';
                $baseImageURL = $this->getPreviewImageURL();
                $imageURL = $baseImageURL . $refid . '/';
                if (!file_exists($savePath)) {
                    mkdir($savePath, 0777, true);
                }
                foreach ($dataArray as $side => $base64Data) {
                    if (strpos($base64Data, 'image/') !== false) {
                        list(, $base64Data) = explode(',', $base64Data);
                    }
                    if (!$base64Data || $base64Data == "") {
                        continue;
                    }

                    $base64Data = base64_decode($base64Data);
                    $maxId = substr($side, 4);
                    $fileName = $maxId . '.' . $ext;
                    $filePath = $savePath . $fileName;
                    $status = file_put_contents($filePath, $base64Data);
                    $msg = '';
                    if ($status) {
                        $sql0 = "Select COUNT(*) AS nos from " . TABLE_PREFIX . "preview_image_data where refid=" . $refid . " and side='$side'";
                        $result0 = $this->executeFetchAssocQuery($sql0);

                        if (!empty($result0) && $result0[0]['nos']) {
                            $sql = "UPDATE " . TABLE_PREFIX . "preview_image_data set image='$fileName', type='$ext', date_created=now() where refid=" . $refid . " and side='$side'";
                            $status = $this->executeGenericDMLQuery($sql);
                            if ($status) {
                                $msg = array("status" => "success", "filename" => $fileName, "filepath" => $imageURL);
                                array_push($fileList, $msg);
                            } else {
                                $msg = array("status" => "updateFailed");
                                array_push($fileList, $msg);
                            }
                        } else {
                            $sql = "INSERT INTO " . TABLE_PREFIX . "preview_image_data (refid, side, image, type, date_created) VALUES ($refid, '$side', '$fileName','$ext', now())";
                            $status = $this->executeGenericDMLQuery($sql);
                            if ($status) {
                                $msg = array("status" => "success", "filename" => $fileName, "filepath" => $imageURL);
                                array_push($fileList, $msg);
                            } else {
                                $msg = array("status" => "insertFailed");
                                array_push($fileList, $msg);
                            }
                        }
                    } else {
                        $msg = array("status" => "failed");
                        array_push($fileList, $msg);
                    }
                }
                $this->response($this->json($fileList), 200);
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Get cart Preview Images
     *
     *@param (String)apikey
     *@param (Int)refids
     *@return json data
     *
     */
    public function getCartPreviewImages($apiKey = 0, $refids = 0, $return = 0)
    {
        $apiKey = $this->_request['apikey'];
        $refids = $this->_request['refids'];
        try {
            $refids = $this->executeEscapeStringQuery($refids);
            if ($refids) {
                $regidArr = explode(',', $refids);
                $baseImageURL = $this->getPreviewImageURL();
                $sql = "Select refid,svg,print_id,product_url,design_status from " . TABLE_PREFIX . "preview_image_data where refid in ($refids)";
                $result = $this->executeGenericDQLQuery($sql);
                if (!empty($result)) {
                    $images = array();
                    foreach ($result as $rows) {
                        $svg = $rows['svg'];
                        $productUrl = $rows['product_url'];
                        $refid = $rows['refid'];
                        $printid = $rows['print_id'];
                        $svgURL = $baseImageURL . $refid . '/svg/' . $svg;
                        $images[$refid][] = array('design_status' => $rows['design_status'], 'svg' => $svgURL, 'productImageUrl' => $productUrl, 'printid' => $printid);
                    }
                    $finalArray = array();
                    foreach ($regidArr as $keys => $values) {
                        if (array_key_exists($values, $images)) {
                            $finalArray[$values] = $images[$values];
                        } else {
                            $finalArray[$values] = array();
                        }

                    }
                    if ($return) {
                        return $finalArray;
                    } else {
                        $this->response($this->json($finalArray), 200);
                    }
                } else {
                    $msg = array("status" => "nodata");
                    $this->response($this->json($msg), 200);
                }
            } else {
                $this->log('getCartPreviewImages :: invalid refids : ' . $refids);
                $msg = array("status" => "invalid refids");
                $this->response($this->json($msg), 200);
            }
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 21-4-2016 (dd-mm-yy)
     *Get First Preview Images
     *
     *@param (String)apikey
     *@param (Int)refids
     *@return json data
     *
     */
    public function getFirstPreviewImages()
    {
        try {
            $apiKey = $this->_request['apikey'];
            $refids = $this->_request['refids'];
            $refids = mysqli_real_escape_string($this->db, $refids);
            $regidArr = explode(',', $refids);
            $baseImageURL = $this->getPreviewImageURL();
            $sql = "Select refid,image,type from  " . TABLE_PREFIX . "preview_image_data where refid in ($refids) and side='side1' ";
            $result = $this->executeFetchAssocQuery($sql);
            if (!empty($result)) {
                $images = array();
                foreach ($result as $k => $v) {
                    $image = $v['image'];
                    $refid = $v['refid'];
                    $ipath = $baseImageURL . $refid . '/' . $image;
                    $images[$refid] = $ipath;
                }
                $finalArray = array();
                foreach ($regidArr as $keys => $values) {
                    if (array_key_exists($values, $images)) {
                        $finalArray[] = $images[$values];
                    } else {
                        $finalArray[] = "";
                    }

                }
                $this->response($this->json($finalArray), 200);
            } else {
                $msg = array("status" => "nodata");
                $this->response($this->json($msg), 200);
            }
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 21-4-2016 (dd-mm-yy)
     *Get Preview Images
     *
     *@param (String)apikey
     *@param (Int)refids
     *@return json data
     *
     */
    public function getPreviewImages()
    {
        try {
            $apiKey = $this->_request['apikey'];
            $refid = $this->_request['refid'];
            $imageReturnType = 'image';
            if (isset($this->_request['type'])) {
                $imageReturnType = $this->_request['type'];
            }

            $refid = mysqli_real_escape_string($this->db, $refid);
            // $baseImagePath = $this->getBaseImagePath();
            // $savePath=$baseImagePath.$refid.'/';
            $previewImagePath = $this->getPreviewImagePath();
            $imagePath = $previewImagePath . $refid . '/';
            $baseImageURL = $this->getPreviewImageURL();
            $imageURL = $baseImageURL . $refid . '/';
            if ($this->isValidCall($apiKey)) {
                $sql = "Select image,type,side from  " . TABLE_PREFIX . "preview_image_data where refid=" . $refid . ' order by side';
                $result = $this->executeFetchAssocQuery($sql);
                if (!empty($result)) {
                    $images = array();
                    foreach ($result as $k => $v) {
                        $image = $v['image'];
                        $type = $v['type'];
                        $side = $v['side'];
                        //$data=array("filename"=>$image,"filepath" => $imageURL,"type"=>$type);
                        // $images[$side] = $data;
                        $fullFilePath = $imageURL . $image;
                        if ($imageReturnType == 'base64') {
                            $path = $imagePath . $image;
                            //$type = pathinfo($path, PATHINFO_EXTENSION);
                            $data = file_get_contents($path);
                            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                            $images[$side] = $base64;
                        } else {
                            $images[$side] = $fullFilePath;
                        }

                    }
                    $this->response($this->json($images), 200);
                } else {
                    $msg = array("status" => "nodata");
                    $this->response($this->json($msg), 200);
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
     *Svae mask data
     *
     *@param (String)apikey
     *@param (Int)productid
     *@param (Array)jsondata
     *@return json data
     *
     */
    public function saveMaskData()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('!POST', 406);
        }
        if (isset($this->_request['isTemplate'])) {
            $isTemplate = $this->_request['isTemplate'];
        }
        $apiKey = $this->_request['apikey'];
        $productid = $this->_request['productid'];
        $jsonData = $this->_request['jsondata'];
        $apiKey = $this->executeEscapeStringQuery($apiKey);
        $productid = $this->executeEscapeStringQuery($productid);
        $jsonData1 = $this->formatJSONToArray($jsonData);
        if ($this->isValidCall($apiKey)) {
            try {
                $sql = "delete from " . TABLE_PREFIX . "mask_data where productid='$productid'";
                $result = $this->executeGenericDMLQuery($sql);
                $values = '';
                $status = 0;
                foreach ($jsonData1 as $side => $value) {
                    $maskJSON = ($value['mask']) ? json_encode($value['mask']) : '';
                    $boundsJSON = ($value['bounds']) ? json_encode($value['bounds']) : '';
                    $customJSON = ($value['customsize']) ? json_encode($value['customsize']) : '';
                    $customMask = ($value['custom_mask']) ? json_encode($value['custom_mask']) : '';
		    $value['custom_min_height'] = (isset($value['custom_min_height']) && $value['custom_min_height'])?$value['custom_min_height']:0.00; 
		    $value['custom_min_width'] = (isset($value['custom_min_width']) && $value['custom_min_width'])?$value['custom_min_width']:0.00; 
		    $value['custom_max_height'] = (isset($value['custom_max_height']) && $value['custom_max_height'])?$value['custom_max_height']:0.00; 
		    $value['custom_max_width'] = (isset($value['custom_max_width']) && $value['custom_max_width'])?$value['custom_max_width']:0.00; 
		    $value['custom_boundary_price'] = (isset($value['custom_boundary_price']) && $value['custom_boundary_price'])?$value['custom_boundary_price']:0.00; 
		    $value['custom_boundary_unit'] = (isset($value['custom_boundary_unit']) && $value['custom_boundary_unit'])?$value['custom_boundary_unit']:0.00; 
		    $value['custom_min_height_mask'] = (isset($value['custom_min_height_mask']) && $value['custom_min_height_mask'])?$value['custom_min_height_mask']:0.00; 
		    $value['custom_min_width_mask'] = (isset($value['custom_min_width_mask']) && $value['custom_min_width_mask'])?$value['custom_min_width_mask']:0.00; 
		    $value['custom_max_height_mask'] = (isset($value['custom_max_height_mask']) && $value['custom_max_height_mask'])?$value['custom_max_height_mask']:0.00; 
		    $value['custom_max_width_mask'] = (isset($value['custom_max_width_mask']) && $value['custom_max_width_mask'])?$value['custom_max_width_mask']:0.00;
                    $values .= ",('" . $value['mask_id'] . "','" . $value['mask_name'] . "','$productid', '$side', '" . addslashes($maskJSON) . "', '$boundsJSON', '$customJSON','" . $this->_request['maskScalewidth'][$side] . "','" . $this->_request['maskScaleHeight'][$side] . "','" . $this->_request['maskPrice'][$side] . "','" . $this->_request['scaleRatio'][$side] . "','" . $value['is_cropMark'] . "','" . $value['is_safeZone'] . "','" . $value['cropValue'] . "','" . $value['safeValue'] . "','" . $this->_request['scaleRatio_unit'][$side] . "','" . $value['custom_min_height'] . "','" . $value['custom_min_width'] . "','" . $value['custom_max_height'] . "','" . $value['custom_max_width'] . "','" . $value['custom_boundary_price'] . "','" . $value['custom_boundary_unit'] . "','" . $customMask . "','" . $value['custom_min_height_mask'] . "','" . $value['custom_min_width_mask'] . "','" . $value['custom_max_height_mask'] . "','" . $value['custom_max_width_mask'] . "','" . $value['isBorderEnable'] . "','" . $value['isSidesAdded'] . "','" . $value['sidesAllowed'] . "')";
                }
                if (strlen($values)) {
                    $sql = "INSERT INTO " . TABLE_PREFIX . "mask_data (mask_id,mask_name,productid, side, mask_json_data,bounds_json_data,custom_size_data, mask_width,mask_height,mask_price,scale_ratio,is_cropMark,is_safeZone,cropValue,safeValue,scaleRatio_unit,cust_min_height,cust_min_width,cust_max_height,cust_max_width,cust_bound_price,custom_boundary_unit,custom_mask,custom_mask_min_height,custom_mask_min_width,custom_mask_max_height,custom_mask_max_width,isBorderEnable,isSidesAdded,sidesAllowed) VALUES" . substr($values, 1);
                    $status = $this->executeGenericDMLQuery($sql);
                }
                if ($status) {
                    $printtypeStatus = $this->setPrintareaType();
                    $msg = array("printtypeStatus" => $printtypeStatus);
                    $msg = array("status" => "success");
                } else {
                    $msg = array("status" => "failed");
                }
                if ($isTemplate == 1) {
                    return $this->json($msg);
                } else {
                    $this->response($this->json($msg), 200);
                }
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 21-4-2016 (dd-mm-yy)
     *Save mask test data
     *
     *@param (String)apikey
     *@param (Int)productid
     *@return json data
     *
     */
    public function saveMaskDataTest()
    {
        try {
            $apiKey = $this->_request['apikey'];
            $productid = '';
            if (isset($this->_request['productid'])) {
                $productid = $this->_request['productid'];
            }

            $variantid = $this->_request['variantid'];
            $jsonData = $this->_request['jsondata'];
            $apiKey = mysqli_real_escape_string($this->db, $apiKey);
            $productid = mysqli_real_escape_string($this->db, $productid);
            $variantid = mysqli_real_escape_string($this->db, $variantid);
            $maskArray = $this->formatJSONToArray($jsonData);
            if ($this->isValidCall($apiKey)) {
                $msg = array("status" => "failed");
                foreach ($maskArray as $side => $value) {
                    $mask = $value['mask'];
                    $bounds = $value['bounds'];
                    $maskJSON = json_encode($mask);
                    $boundsJSON = json_encode($bounds);
                    $sql = "Select id from  " . TABLE_PREFIX . "mask_data_test where productid='$productid' and variantid='$variantid' and side='$side'";
                    $result = mysqli_query($this->db, $sql);
                    if (mysqli_num_rows($result) > 0) {
                        $sql = "update " . TABLE_PREFIX . "mask_data_test  set mask_json_data='$maskJSON',bounds_json_data='$boundsJSON' where productid='$productid' and variantid='$variantid' and side='$side'";
                        $status = $this->executeGenericDMLQuery($sql);
                        if ($status) {
                            $msg = array("status" => "success");
                        } else {
                            $msg = array("status" => "failed");
                        }
                    } else {
                        $sql = "INSERT INTO " . TABLE_PREFIX . "mask_data_test (productid, variantid, side, mask_json_data,bounds_json_data) VALUES ('$productid', '$variantid', '$side', '$maskJSON', '$boundsJSON')";
                        $status = $this->executeGenericDMLQuery($sql);
                        if ($status) {
                            $msg = array("status" => "success");
                        } else {
                            $msg = array("status" => "failed");
                        }
                    }
                }
                $this->closeConnection();
                $this->response($this->json($msg), 200);
            }
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }
    /**
     * @param String apikey
     * @param Base64String data Base64 repesentation of the image/svg
     * @param String category design/pattern/textfx
     * @param String name file name
     *
     * @return JSON  success/failed
     *
     * <p>Receives image source from Flex Destop/web Admin and write the image (image/svg) to the server file System</p>
     *
     */

    public function saveAppImage()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $apiKey = $this->_request['apikey'];
        $data = $this->_request['data'];
        $category = $this->_request['category']; // design/pattern/textfx
        $name = $this->_request['name']; //file name
        if ($this->isValidCall($apiKey)) {
            try {
                $base64Data = base64_decode($data);
                $dir = '';
                if ($category == 'design') {
                    $dir = $this->getDesignImagePath();
                }

                if ($category == 'shape') {
                    $dir = $this->getShapeImagePath();
                } else if ($category == 'pattern') {
                    $dir = $this->getPaletteImagePath();
                } else if ($category == 'textfx') {
                    $dir = $this->getTextFXImagePath();
                    list($path, $name) = explode('/', $name); //textfx filename comes with the font directory name (eg: font2/A.svg)
                    $dir = $dir . $path . '/';
                }
                if (!$dir) {
                    $this->response('', 204);
                }
                //204 - immediately termiante this request
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }

                $filePath = $dir . $name;
                $status = file_put_contents($filePath, $base64Data);
                $status = ($status) ? "success" : "failed";
                $msg = array("status" => $status);
                $this->closeConnection();
                $this->response($this->json($msg), 200);
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get maximum file name
     *
     *@param (String)apikey
     *@param (String)dir
     *@return file name
     *
     */
    public function getMaxFileName($dir)
    {
        try {
            $fileList = scandir($dir);
            $noList = array();
            for ($i = 0; $i < count($fileList); $i++) {
                $fileName = $fileList[$i];
                if (!in_array($fileName, array(".", ".."))) {
                    $number = $this->extractNumber($fileName);
                    array_push($noList, $number);
                }
            }
            $max = max($noList);
            return $max + 1;
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Save  Text on path
     *
     *@param (String)apikey
     *@param (String)name
     *@param (Float)price
     *@param (String)maskdata
     *@param (String)thumbdata
     *@param (Int)id
     *@return json data
     *
     */
    public function saveTextonPath()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $apiKey = $this->_request['apikey'];
        $name = $this->_request['name'];
        $price = $this->_request['price'];
        $maskdata = $this->_request['maskdata'];
        $thumbdata = $this->_request['thumbdata'];
        $id = $this->_request['id']; //file name
        if ($this->isValidCall($apiKey)) {
            try {
                $maskBase64Data = base64_decode($maskdata);
                $thumbBase64Data = base64_decode($thumbdata);
                $dir = $this->getTextonImagePath();
                if (!$dir) {
                    $this->response('', 204);
                }
                //204 - immediately termiante this request
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }

                $imageName = $id . '_0.png';
                $svgName = $id . '_0.svg';
                $thumbImageFilePath = $dir . $imageName;
                $svgImageFilePath = $dir . $svgName;
                $msg = '';
                if ($thumbdata != '') {
                    $status = file_put_contents($thumbImageFilePath, $thumbBase64Data);
                }
                $status = file_put_contents($svgImageFilePath, $maskBase64Data);
                $sql = "INSERT INTO " . TABLE_PREFIX . "textonpath (name,price,svg_image, thumb_image, textonpath_id, date_created) VALUES ('$name','$price','$svgName', '$imageName', '$id', now())";
                $status = $this->executeGenericDMLQuery($sql);
                if ($status) {
                    $msg = array("status" => "success", "thumb_image" => $imageName, "svg_image" => $svgName);
                } else {
                    $msg = array("status" => "failed");
                }

                $this->closeConnection();
                $this->response($this->json($msg), 200);
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 21-4-2016 (dd-mm-yy)
     *Get  Text on path details
     *
     *@param (String)apikey
     *@param (Int)srtIndex
     *@param (Int)range
     *@return json data
     *
     */
    public function getTextonpathDetails()
    {
        $apiKey = $this->_request['apikey'];
        if ($this->isValidCall($apiKey)) {
            try {
                $srtIndex = (isset($this->_request['srtIndex'])) ? $this->_request['srtIndex'] : 0;
                $range = (isset($this->_request['range'])) ? $this->_request['range'] : 30;
                $sql = "Select name,price,textonpath_id,thumb_image,svg_image from  " . TABLE_PREFIX . "textonpath LIMIT $srtIndex,$range";
                $this->log('getTextonpathDetails :: ' . $sql);
                $result = mysqli_query($this->db, $sql);
                if (mysqli_num_rows($result) > 0) {
                    $maskedImageData = array();
                    while ($rows = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                        $textonpath_id = $rows['textonpath_id'];
                        $name = $rows['name'];
                        $price = $rows['price'];
                        $thumb_image = $rows['thumb_image'];
                        $svg_image = $rows['svg_image'];
                        $data = array("name" => $name, "price" => $price, "textonpath_id" => $textonpath_id, "thumb_image" => $thumb_image, "svg_image" => $svg_image);
                        $maskedImageData[] = $data;
                    }
                    $this->closeConnection();
                    $this->response($this->json($maskedImageData), 200);
                } else {
                    //$msg=array("status"=>"nodata");
                    $msg = array();
                    $this->closeConnection();
                    $this->response($this->json($msg), 200);
                }
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        } else {
            $msg = array("status" => "invalid");
            $this->response($this->json($msg), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 21-4-2016 (dd-mm-yy)
     *Remove  Textonpath
     *
     *@param (String)apikey
     *@param (Array)textonpathIds
     *@return json data
     *
     */
    public function removeTextonPaths()
    {
        $apiKey = $this->_request['apikey'];
        if ($this->isValidCall($apiKey)) {
            try {
                $textonpathIdsArray = $this->_request['textonpathIds'];
                $svgPathArray = array();
                $thumbPathArray = array();
                for ($j = 0; $j < sizeof($textonpathIdsArray); $j++) {
                    $sql = "Select thumb_image,svg_image from  " . TABLE_PREFIX . "textonpath where textonpath_id=" . $textonpathIdsArray[$j];
                    $result = mysqli_query($this->db, $sql);
                    if (mysqli_num_rows($result) > 0) {
                        while ($rows = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                            $svg_image = $rows['svg_image'];
                            $thumb_image = $rows['thumb_image'];
                            $svgPathArray[$j] = $this->getTextonImagePath() . $svg_image;
                            $thumbPathArray[$j] = $this->getTextonImagePath() . $thumb_image;
                        }
                    }
                }
                $status = 0;
                $ids = implode(',', $textonpathIdsArray);
                $sql = "DELETE FROM " . TABLE_PREFIX . "textonpath WHERE textonpath_id in ($ids)";
                $this->log('removeDesignMasks:' . $sql);
                $status .= $this->executeGenericDMLQuery($sql);
                if ($status) {
                    for ($i = 0; $i < sizeof($textonpathIdsArray); $i++) {
                        if (file_exists($svgPathArray[$i])) {
                            unlink($svgPathArray[$i]);
                        }

                        if (file_exists($thumbPathArray[$i])) {
                            unlink($thumbPathArray[$i]);
                        }

                    }
                    $msg = array("status" => "success");
                    $this->closeConnection();
                    $this->response($this->json($msg), 200);
                } else {
                    $msg = array("status" => "failed", "sql" => $sql);
                    $this->closeConnection();
                    $this->response($this->json($msg), 200);
                }
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        } else {
            $msg = array("status" => "invalid");
            $this->response($this->json($msg), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Update  Text on path data
     *
     *@param (String)apikey
     *@param (Int)textonpath_id
     *@param (String)oldSvg
     *@param (String)newSvg
     *@param (String)thumbdata
     *@param (String)oldThumb
     *@param (String)newThumb
     *@param (String)name
     *@param (Float)price
     *@return json data
     *
     */
    public function updateTextonPath()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $apiKey = $this->_request['apikey'];
        $textonpath_id = $this->_request['textonpath_id'];
        if ($this->_request['maskdata']) {
            $maskdata = $this->_request['maskdata'];
            $oldSvg = $this->_request['oldSvg'];
            $newSvg = $this->_request['newSvg'];
        }
        if ($this->_request['thumbdata']) {
            $thumbdata = $this->_request['thumbdata'];
            $oldThumb = $this->_request['oldThumb'];
            $newThumb = $this->_request['newThumb'];
        }
        $name = $this->_request['name'];
        $price = $this->_request['price'];
        if ($this->isValidCall($apiKey)) {
            try {
                $flag = false;
                $dir = $this->getTextonImagePath();
                if (!$dir) {
                    $this->response('', 204);
                }
                //204 - immediately termiante this request
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }

                $sql = "update " . TABLE_PREFIX . "textonpath set";
                $sql = $sql . $this->getSqlSeparator($flag) . " name='$name',price='$price'";
                $flag = true;
                $msg = array("status" => "success", "textonpath_id" => $textonpath_id, "sqlQuery" => $sql);
                if ($thumbdata) {
                    $thumbBase64Data = base64_decode($thumbdata);
                    //$thumbImage = $textonpath_id.'.png';
                    $thumbImageFilePath = $dir . $oldThumb;
                    $newThumbImageFilePath = $dir . $newThumb;
                    if (file_exists($thumbImageFilePath)) {
                        unlink($thumbImageFilePath);
                    }

                    $status = file_put_contents($newThumbImageFilePath, $thumbBase64Data);
                    $sql = $sql . $this->getSqlSeparator($flag) . " thumb_image='$newThumb'";
                    $flag = true;
                    $msg = array("status" => "success", "textonpath_id" => $textonpath_id, "sqlQuery" => $sql);
                }
                if ($maskdata) {
                    $maskBase64Data = base64_decode($maskdata);
                    //$svgImage = $textonpath_id.'.svg';
                    $svgImageFilePath = $dir . $oldSvg;
                    $newSvgImageFilePath = $dir . $newSvg;
                    if (file_exists($svgImageFilePath)) {
                        unlink($svgImageFilePath);
                    }

                    $status = file_put_contents($newSvgImageFilePath, $maskBase64Data);
                    $sql = $sql . $this->getSqlSeparator($flag) . " svg_image='$newSvg'";
                    $flag = true;

                    $msg = array("status" => "success", "textonpath_id" => $textonpath_id, "sqlQuery" => $sql);
                }
                $sql .= " where textonpath_id=" . $textonpath_id;
                $status = $this->executeGenericDMLQuery($sql);
                if (!$status) {
                    $msg = array("status" => "failed", "textonpath_id" => $textonpath_id, "sqlQuery" => $sql);
                }

                $this->closeConnection();
                $this->response($this->json($msg), 200);
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Remove  mask image by  mask id
     *
     *@param (String)apikey
     *@param (Int)mask_id
     *@return json data
     *
     */
    public function removeMaskImage()
    {
        $mask_id = $this->_request['mask_id'];
        $apiKey = $this->_request['apikey'];
        if ($this->isValidCall($apiKey)) {
            try {
                $sql = "Select thumb_image,svg_image from  " . TABLE_PREFIX . "mask_paths where mask_id='$mask_id'";
                $result = $this->executeGenericDQLQuery($sql);
                if (!empty($result)) {
                    foreach ($result as $rows) {
                        $thumb_image = $rows['thumb_image'];
                        $svg_image = $rows['svg_image'];
                        $thumb_imageURL = $this->getMaskImagePath() . $thumb_image;
                        $svg_imageURL = $this->getMaskImagePath() . $svg_image;
                        if (file_exists($thumb_imageURL)) {
                            unlink($thumb_imageURL);
                        }
                        if (file_exists($svg_imageURL)) {
                            unlink($svg_imageURL);
                        }
                    }
                    $sql = "DELETE FROM " . TABLE_PREFIX . "mask_paths WHERE  mask_id='$mask_id'";
                    $result = $this->executeGenericDMLQuery($sql);
                    $msg = array();
                    if ($result) {
                        $msg = array("status" => "success", "mask_id" => $mask_id);
                    } else {
                        $msg = array("status" => "failed", "mask_id" => $mask_id);
                    }

                    $this->closeConnection();
                    $this->response($this->json($msg), 200);
                } else {
                    $msg = array("status" => "nodata");
                    $this->closeConnection();
                    $this->response($this->json($msg), 200);
                }
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        }
    }

    /**
     *
     *date created 9-9-2015 (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Get design by category
     *
     *@param (String)apikey
     *@param (Int)lastLoaded
     *@param (Int)loadCount
     *@param (String)selectedCatagory
     *@return json data
     *
     */
    public function fetchDesignsByCatagory()
    {
        $designArray = array();
        try {
            $designLastLoaded = $this->_request['lastLoaded'];
            $designLimit = $this->_request['loadCount'];
            $selectedCatagory = $this->_request['selectedCatagory'];
            if ($selectedCatagory == 'All' || $selectedCatagory == "") {
                $sql = "SELECT * FROM " . TABLE_PREFIX . "designs ";
            } else {
                $sql = "SELECT * FROM " . TABLE_PREFIX . "designs d left join " . TABLE_PREFIX . "category c on (d.category_id=c.id) where c.category_name='$selectedCatagory'";
            }

            $sql .= " LIMIT $designLastLoaded, $designLimit";
            $allDesigns = mysqli_query($this->db, $sql);
            $i = 0;
            while ($row = mysqli_fetch_array($allDesigns)) {
                $designArray[$i]['id'] = $row['id'];
                $designArray[$i]['name'] = $row['design_name'];
                $designArray[$i]['url'] = $this->getDesignImageURL() . $row['file_name'];
                $i++;
            }
            $this->closeConnection();
            $this->response($this->json($designArray), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    //Method to get mask list
    ####################################################
    ############## syncOrdersZip ###### unused#############
    ####################################################
    public function getMaskList() // Will be replaced by : getMaskImageList()

    {
        try {
            $maskName = array();
            $sql = "SELECT file_name FROM " . TABLE_PREFIX . "mask_list";
            $result = mysqli_query($this->db, $sql);
            $i = 0;
            while ($row = mysqli_fetch_array($result)) {
                $maskName[$i] = $row['file_name'];
                $i++;
            }
            echo json_encode($maskName);
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
        $this->closeConnection();
    }

    /**
     *
     *date created 9-9-2015 (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Uplaod url images
     *
     *@param (String)apikey
     *@param (Int)customerId
     *@param (String)url
     *@param (Int)refId
     *@return json data
     *
     */
    public function uploadUrlImg()
    {
        try {
            $url = $this->_request['url'];
            $customerId = $this->_request['customerId'];
            $img = file_get_contents($url);
            $refId = $this->_request['refId'];
            $imgName = substr(strrchr($url, "/"), 1);
            $localUrl = $this->getTempImagePath() . $customerId . '/' . $imgName;
            $serverUrl = $this->getTempImageURL() . $customerId . '/' . $imgName;
            if (!file_exists($this->getTempImagePath() . $customerId)) {
                mkdirs($this->getTempImagePath() . $customerId, 0777, true);
            }
            file_put_contents($localUrl, $img);
            $size = filesize($localUrl);
            echo $serverUrl . '^' . $customerId . '^' . $size;
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }

    /**
     *
     *date created 9-9-2015 (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *getSize
     *
     *@param (String)apikey
     *@param (Int)customerId
     *@return json data
     *
     */
    public function getSize()
    {
        try {
            $customerId = $this->_request['customerId'];
            $url = $this->_request['url'];
            $imgName = substr(strrchr($url, "/"), 1);
            $localUrl = $this->getUserImagePath() . $customerId . '/' . $imgName;
            $size = filesize($localUrl);
            echo $size;
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }

    /**
     *
     *date created 9-9-2015 (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Move to user folder
     *
     *@param (String)apikey
     *@param (Int)customerId
     *@param (String)url
     *@param (Int)refid
     *@return json data
     *
     */
    public function moveToUserFolder()
    {
        try {
            $customerId = $this->_request['customerId'];
            $url = $this->_request['url'];
            $refId = $this->_request['refid'];
            $filename = substr(strrchr($url, "/"), 1);
            list(, $ext) = explode('.', $filename);
            $count = 0;
            $sql = "SELECT MAX(id) from  " . TABLE_PREFIX . "image_data where refid=" . $refId;
            $result = mysqli_query($this->db, $sql);
            if ($row = mysqli_fetch_array($result)) {
                $count = $row[0];
            }
            $count = $count + 1;
            $imgName = $refId . '_' . $count . '.' . $ext;
            $destination = $this->getUserImagePath() . $customerId . '/' . $imgName;
            $localUrl = $this->getTempImagePath() . $customerId . '/' . $filename;
            $status = rename($localUrl, $destination);
            if ($status) {
                $sql0 = "INSERT INTO " . TABLE_PREFIX . "image_data (customer_id, refid, image, type, date_created) VALUES ($customerId, $refId, '$imgName','$ext', now())";
                $this->executeGenericDMLQuery($sql0);
                echo 'move success';
                delete_files($this->getTempImagePath() . $customerId . '/');
            } else {
                echo 'failed to move';
            }
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }

    /**
     *
     *date created 9-9-2015 (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *delete folder
     *
     *@param (String)apikey
     *@param (String)target
     *@return json data
     *
     */
    public function delete_files($target)
    {
        try {
            if (is_dir($target)) {
                $files = glob($target . '*', GLOB_MARK); //GLOB_MARK adds a slash to directories returned
                foreach ($files as $file) {
                    delete_files($file);
                }
            } elseif (is_file($target)) {
                unlink($target);
            }
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created 9-9-2015 (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *calculate folder size
     *
     *@param (String)apikey
     *@param (Int)customerId
     *@return json data
     *
     */
    public function calculateFolderSize()
    {
        try {
            $customerId = $this->_request['customerId'];
            $count_size = 0;
            $count = 0;
            $dir = $this->getUserImagePath() . $customerId . '/';
            if (file_exists($dir)) //new
            {
                $dir_array = scandir($dir);
                foreach ($dir_array as $key => $filename) {
                    if ($filename != ".." && $filename != ".") {
                        if (is_dir($dir . "/" . $filename)) {
                            echo $dir . "/" . $filename;
                            $new_foldersize = foldersize($dir . "/" . $filename);
                            $count_size = $count_size + $new_foldersize;
                        } else if (is_file($dir . "/" . $filename)) {
                            $count_size = $count_size + filesize($dir . "/" . $filename);
                            $count++;
                        }
                    }
                }
            }
            echo $count_size;
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }

    /**
     *
     *date created 9-9-2015 (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get Available size
     *
     *@param (String)apikey
     *@param (Int)customerId
     *@return json data
     *
     */
    public function getAvailableSize()
    {
        $customerId = $this->_request['customerId'];
        try {
            $sql = "SELECT max_size FROM " . TABLE_PREFIX . "upload_space_details WHERE customer_id = " . $customerId;
            $result = mysqli_query($this->db, $sql);
            if ($row = mysqli_fetch_array($result)) {
                echo $row['max_size'];
            }
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
        $this->closeConnection();
    }

    /**
     *
     *date created 9-9-2015 (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Update Customer
     *
     *@param (String)apikey
     *@param (Int)customerId
     *@param (Int)maxSize
     *@param (Float)payment
     *@return json data
     *
     */
    public function updateCustomer()
    {
        $customerId = $this->_request['customerId'];
        $maxSize = $this->_request['maxSize'];
        $payment = $this->_request['payment'];
        $defaultSize = 20;
        try {
            $sql = "SELECT customer_id FROM " . TABLE_PREFIX . "upload_space_details WHERE customer_id = " . $customerId;
            $result = mysqli_query($this->db, $sql);
            if (mysqli_num_rows($result) > 0) {
                $sql = "UPDATE " . TABLE_PREFIX . "upload_space_details SET max_size=max_size+" . $maxSize . ",date_modified = now() WHERE customer_id = " . $customerId;
                $update = $this->executeGenericDMLQuery($sql);
            } else {
                $sql = "INSERT  INTO " . TABLE_PREFIX . "upload_space_details(customer_id,max_size,date_modified,payment) VALUES($customerId,$maxSize + $defaultSize,now(),'$payment')";
                $insert = $this->executeGenericDMLQuery($sql);
            }
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
        $this->closeConnection();
    }

    /**
     *
     *date created 9-9-2015 (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Remove images
     *
     *@param (String)apikey
     *@param (String)url
     *@param (Int)customerId
     *@return json data
     *
     */
    public function deleteImages()
    {
        $customerId = $this->_request['customerId'];
        $url = $this->_request['url'];
        $urls = explode(",", $url);
        $imgNameList = array();
        foreach ($urls as $value) {
            $imgNameList[] = substr(strrchr($value, "/"), 1);
        }
        $imgNames = implode("','", $imgNameList);
        try {
            $sql = "DELETE FROM " . TABLE_PREFIX . "image_data WHERE customer_id = " . $customerId . " AND image in('" . $imgNames . "')";
            $status = $this->executeGenericDMLQuery($sql);
            if ($status) {
                foreach ($imgNameList as $img) {
                    $fileName = $this->getUserImagePath() . $customerId . '/' . $img;
                    unlink($fileName);
                }
                $msg = array("status" => "success");
                $this->response($this->json($msg), 200);
            } else {
                $this->log("deleteImage() :: SQL failed:" . $sql);
                $msg = array("status" => "failed", "error" => "SQL operation failed");
                $this->response($this->json($msg), 200);
            }
        } catch (Exception $e) {
            $this->log("deleteImage() :: Exception:" . $e->getMessage());
            $msg = array("status" => "failed", "error" => $e->getMessage());
            $this->response($this->json($msg), 200);
        }
        $this->closeConnection();
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *load quotes
     *
     *@param (String)apikey
     *@param (String)condition
     *@param (Int)startIndex
     *@param (Int)rangeIndex
     *@return json data
     *
     */
    public function loadQuotes()
    {
        try {
            $condition = $this->_request["condition"];
            $startIndex = $this->_request["startIndex"];
            $rangeIndex = $this->_request["rangeIndex"];
            $cond = explode(',', $condition);
            $category = $cond[0];
            $tag = $cond[1];
            $searchCategoryQuery = ($category == 'none' || $category == "undefined") ? '' : " and ct.category like '%$category'";
            $searchTagQuery = ($tag == 'none' || $tag == "undefined") ? '' : " and t.tag_name like '%$tag'";
            $sql = "select distinct quote from " . TABLE_PREFIX . "quote_category_rel qcr, " . TABLE_PREFIX . "quote_category ct, " . TABLE_PREFIX . "quote_tag_rel qtr,  " . TABLE_PREFIX . "quote_tags t, " . TABLE_PREFIX . "quotes q where q.id = qcr.quote_id and q.id = qtr.quote_id and qcr.category_id = ct.id and qtr.tag_id = t.id$searchCategoryQuery$searchTagQuery LIMIT $startIndex,$rangeIndex";
            $result = mysqli_query($this->db, $sql);
            if (mysqli_num_rows($result) != 0) {
                while ($row = mysqli_fetch_array($result)) {
                    $quoteArr[] = $row['quote'];
                }
                $this->closeConnection();
                $this->response($this->json(array_unique($quoteArr)), 200);
            } else {
                $msg['status'] = 'nodata';
            }
        } catch (Exception $e) {
            $msg = array('Caught exception:' => $e->getMessage());
        }
        $this->response($this->json($msg), 200);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get Tags
     *
     *@param (String)apikey
     *@param (String)condition
     *@return json data
     *
     */
    public function loadTags()
    {
        try {
            $condition = $this->_request["condition"];
            $searchCondition = ($condition == '') ? "" : "where tag_name like '%$condition%'";
            $sql = "select tag_name from " . TABLE_PREFIX . "quote_tags $searchCondition";
            $result = mysqli_query($this->db, $sql);
            while ($row = mysqli_fetch_array($result)) {
                $tagArr[] = $row['tag_name'];
            }
            $this->closeConnection();
            $this->response($this->json($tagArr), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get Category
     *
     *@param (String)apikey
     *@param (String)condition
     *@return json data
     *
     */
    public function loadCategories()
    {
        try {
            $condition = $this->_request["condition"];
            $searchCondition = ($condition == '') ? "" : " where category  like '%$condition%'";
            $sql = "select category from " . TABLE_PREFIX . "quote_category $searchCondition";
            $result = mysqli_query($this->db, $sql);
            while ($row = mysqli_fetch_array($result)) {
                $catagoryArr[] = $row['category'];
            }
            $this->closeConnection();
            $this->response($this->json($catagoryArr), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get Template
     *
     *@param (String)apikey
     *@param (String)condition
     *@return json data
     *
     */
    public function loadTemplates()
    {
        try {
            $condition = $this->_request["condition"];
            if ($condition == '') {
                $sql = "select json from " . TABLE_PREFIX . "itextpattern";
                $row = $this->executeGenericDQLQuery($sql);
                while ($row) {
                    $jsonArr[] = $this->formatJSONToArray($row['json']);
                }
                $this->response($this->json($jsonArr), 200);
                $this->response($jsonArr, 200);
            }
            $this->closeConnection();
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get module price status
     *
     *@param (String)apikey
     *@return json data
     *
     */
    public function getModulePriceStatus()
    {
        $apiKey = $this->_request['apikey'];
        if ($this->isValidCall($apiKey)) {
            try {
                $sql = "Select * from " . "module_price";
                $result = $this->executeGenericDQLQuery($sql);
                if (!empty($result)) {
                    $modulePriceStatusData = array();
                    foreach ($result as $rows) {
                        $name = $rows['name'];
                        $id = $rows['id'];
                        $status = $rows['status'];
                        $data = array("id" => $id, "name" => $name, "status" => $status);
                        $modulePriceStatusData[] = $data;
                    }
                    $this->closeConnection();
                    $this->response($this->json($modulePriceStatusData), 200);
                } else {
                    $msg = array("status" => "nodata");
                    $this->closeConnection();
                    $this->response($this->json($msg), 200);
                }
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        } else {
            $msg = array("status" => "invalid");
            $this->response($this->json($msg), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Update product feature
     *
     *@param (String)apikey
     *@param (Int)productId
     *@param (Int)featureIds
     *@param (Array)statusArray
     *@return json data
     *
     */
    public function updateProductFeature()
    {
        $apiKey = $this->_request['apikey'];
        if ($this->isValidCall($apiKey)) {
            try {
                $productId = $this->_request['productId'];
                $featureIds = $this->_request['featureIds'];
                $statusArray = $this->_request['statusArray'];
                for ($i = 0; $i < sizeof($featureIds); $i++) {
                    $featureId = $featureIds[$i];
                    $featureStatus = $statusArray[$i];
                    $sql = "SELECT * FROM " . TABLE_PREFIX . "product_feature_rel WHERE product_id= " . $productId . " && feature_id = " . $featureId;
                    $productFeatureFromValue = mysqli_query($this->db, $sql);
                    if (mysqli_num_rows($productFeatureFromValue) > 0) {
                        $sql = "UPDATE " . TABLE_PREFIX . "product_feature_rel SET status = " . $featureStatus . " WHERE product_id= " . $productId . " && feature_id = " . $featureId;
                        $status = $this->executeGenericDMLQuery($sql);
                    } else {
                        $sql = "insert into " . TABLE_PREFIX . "product_feature_rel(product_id, feature_id, status)values($productId,$featureId,$featureStatus)";
                        $status = $this->executeGenericDMLQuery($sql);
                    }
                    if ($status) {
                        $msg = array("status" => "success");
                    } else {
                        $msg = array("status" => "failed");
                    }

                }
                $this->closeConnection();
                $this->response($this->json($msg), 200);
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        } else {
            $msg = array("status" => "invalid");
            $this->response($this->json($msg), 200);
        }
    }

    ####################################################
    ############## syncOrdersZip ########unused###########
    ####################################################
    public function fetchProductCategoryFeatures()
    {
        $apiKey = $this->_request['apikey'];
        if ($this->isValidCall($apiKey)) {
            $productCategoryId = $this->_request['productCategoryId'];

            $sql = "SELECT id,name,type FROM " . TABLE_PREFIX . "features WHERE mandatory_status=0 && status=1 && category_level_status=1";
            $featureIdsArray = array();
            $featureNamesArray = array();
            $featureTypesArray = array();
            $featuresFromValue = $this->executeGenericDQLQuery($sql);
            foreach ($featuresFromValue as $row) {
                array_push($featureIdsArray, $row['id']);
                array_push($featureNamesArray, $row['name']);
                array_push($featureTypesArray, $row['type']);
            }
            $productCategoryFeaturesStatusData = array();

            for ($i = 0; $i < sizeof($featureIdsArray); $i++) {
                $name = $featureNamesArray[$i];
                $type = $featureTypesArray[$i];

                $sql = "SELECT status FROM " . TABLE_PREFIX . "productcategory_feature_rel WHERE product_category_id= " . $productCategoryId . " && feature_id = " . $featureIdsArray[$i];
                $rows = $this->executeGenericDQLQuery($sql);
                if (!empty($rows)) {
                    $status = $rows[0]['status'];
                } else {
                    $status = 1;
                }

                $data = array("featureId" => $featureIdsArray[$i], "featureName" => $name, "featureType" => $type, "featureStatus" => $status);
                $productCategoryFeaturesStatusData[] = $data;
            }
            $this->closeConnection();
            $this->response($this->json($productCategoryFeaturesStatusData), 200);
        } else {
            $msg = array("status" => "invalid");
            $this->response($this->json($msg), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Update product category feature
     *
     *@param (String)apikey
     *@param (Int)productCategoryId
     *@param (Int)pcFeatureIds
     *@param (Array)pcStatusArray
     *@return json data
     *
     */
    public function updateProductCategoryFeature()
    {
        $apiKey = $this->_request['apikey'];
        if ($this->isValidCall($apiKey)) {
            try {
                $productCategoryId = $this->_request['productCategoryId'];
                $pcFeatureIds = $this->_request['pcFeatureIds'];
                $pcStatusArray = $this->_request['pcStatusArray'];
                for ($i = 0; $i < sizeof($pcFeatureIds); $i++) {
                    $featureId = $pcFeatureIds[$i];
                    $featureStatus = $pcStatusArray[$i];
                    $sql = "SELECT * FROM " . TABLE_PREFIX . "productcategory_feature_rel WHERE product_category_id= " . $productCategoryId . " && feature_id = " . $featureId;
                    $categoryFeatureFromValue = $this->executeFetchAssocQuery($sql);
                    if (!empty($categoryFeatureFromValue)) {
                        $sql = "UPDATE " . TABLE_PREFIX . "productcategory_feature_rel SET status = " . $featureStatus . " WHERE product_category_id= " . $productCategoryId . " && feature_id = " . $featureId;
                        $status = $this->executeGenericDMLQuery($sql);
                    } else {
                        $sql = "insert into " . TABLE_PREFIX . "productcategory_feature_rel(product_category_id, feature_id, status)values($productCategoryId,$featureId,$featureStatus)";
                        $status = $this->executeGenericDMLQuery($sql);
                    }
                    $msg['status'] = ($status) ? 'success' : 'failed';
                }
                $this->closeConnection();
                $this->response($this->json($msg), 200);
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        } else {
            $msg = array("status" => "invalid");
            $this->response($this->json($msg), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *additional printing prices of each color variants
     *
     *@param (String)apikey
     *@param (Int)productid
     *@param (Int)variantid
     *@param (Int)printtypes
     *@param (Int)printprices
     *@return json data
     *
     */
    public function setAdditionalPrintingPriceOfVariants()
    {
        $apiKey = $this->_request['apikey'];
        if ($this->isValidCall($apiKey)) {
            try {
                $productid = $this->_request['productid'];
                $variantid = $this->_request['variantid'];
                $printtypeArray = $this->_request['printtypes'];
                $priceArray = $this->_request['printprices'];
                $failedArray = array();
                for ($j = 0; $j < sizeof($printtypeArray); $j++) {
                    $sql = "SELECT * from " . TABLE_PREFIX . "variant_additional_prices WHERE productid = '" . $productid . "' && variantid='" . $variantid . "' && print_type='" . $printtypeArray[$j] . "'";
                    $presentData = mysqli_query($this->db, $sql);
                    if (mysqli_num_rows($presentData) > 0) {
                        $sql = "UPDATE " . TABLE_PREFIX . "variant_additional_prices SET price = '" . $priceArray[$j] . "' WHERE productid = '" . $productid . "' && variantid='" . $variantid . "' && print_type='" . $printtypeArray[$j] . "'";
                        $status = $this->executeGenericDMLQuery($sql);
                    } else {
                        $sql = "insert into " . TABLE_PREFIX . "variant_additional_prices(productid, variantid, price, print_type)values('$productid','$variantid',$priceArray[$j],'$printtypeArray[$j]')";
                        $status = $this->executeGenericDMLQuery($sql);
                    }
                    if (!$status) {
                        array_push($failedArray, $variantidsArray[$j]);
                    }

                }
                if (sizeof($failedArray) == 0) {
                    $msg = array("status" => "success");
                } else {
                    $msg = array("status" => "incomplete", "variants" => $failedArray);
                }

                $this->response($this->json($msg), 200);
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        } else {
            $msg = array("status" => "invalid");
            $this->response($this->json($msg), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Set dtg print size of product side wise
     *
     *@param (String)apikey
     *@param (Int)productid
     *@param (String)printsizes
     *@return json data
     *
     */
    public function setDtgPrintSizesOfProductSides()
    {
        $apiKey = $this->_request['apikey'];
        if (isset($this->_request['isTemplate'])) {
            $isTemplate = $this->_request['isTemplate'];
        }
        $status = false;
        if ($this->isValidCall($apiKey) && isset($this->_request['productid']) && !empty($this->_request['printsizes'])) {
            try {
                $productid = $this->_request['productid'];
                $dataArray = $this->_request['printsizes'];
                if (is_array($dataArray)) {
                    $printsizesArray = $dataArray;
                } else {
                    $printsizesArray = $this->formatJSONToArray($dataArray);
                }
                $side = '';
                $values = '';
                foreach ($printsizesArray as $v) {
                    if (isset($v)) {
                        $v = (array) $v;
                        $side .= ',' . $v['side'];
                        $values .= ",('" . $productid . "','" . $v['side'] . "','" . $v['size'] . "','" . $v['is_transition'] . "')";
                    }
                }
                if (strlen($side) && strlen($values)) {
                    $sql = "delete from " . TABLE_PREFIX . "product_sides_sizes where productid = '" . $productid . "' && side IN(" . substr($side, 1) . ")";
                    $result = $this->executeGenericDMLQuery($sql);
                    $sql = "insert into " . TABLE_PREFIX . "product_sides_sizes(productid,side,printsize,is_transition) values" . substr($values, 1);
                    $status = $this->executeGenericDMLQuery($sql);
                }
                if (!$status) {
                    $msg = array("status" => "failed");
                } else {
                    $msg = array("status" => "success", "productid" => $productid);
                }
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        } else {
            $msg = array("status" => "invalid");
        }
        if ($isTemplate == 1) {
            return $this->json($msg);
        } else {
            $this->response($this->json($msg), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Set print area type
     *
     *@param (String)apikey
     *@param (Int)productid
     *@param (Int)maskstatus
     *@param (Int)boundsstatus
     *@param (Int)customsizestatus
     *@param (Int)unitId
     *@param (Int)pricePerUnit
     *@param (Int)maxWidth
     *@param (Int)maxHeight
     *@return json data
     *
     */
    public function setPrintareaType()
    {
        extract($this->_request);
        try {
	    $unitId = (isset($unitId) && $unitId && $unitId)?$unitId:1;
            $sql = "SELECT COUNT( * ) AS exist FROM " . TABLE_PREFIX . "product_printarea_type WHERE productid=" . $productid;
            $res = $this->executeFetchAssocQuery($sql);
            if ($res[0]['exist']) {
                $sql = "UPDATE " . TABLE_PREFIX . "product_printarea_type SET mask = '$maskstatus' , bounds='$boundsstatus', custom_size='$customsizestatus', unit_id='$unitId', price_per_unit='$pricePerUnit', max_width='$maxWidth', max_height='$maxHeight',custom_mask='$customMask' WHERE productid = $productid";
            } else {
                $sql = "insert into " . TABLE_PREFIX . "product_printarea_type(productid,mask,bounds,custom_size,unit_id,price_per_unit,max_width,max_height,custom_mask)values('$productid','$maskstatus','$boundsstatus','$customsizestatus','$unitid','$pricePerUnit','$maxWidth','$maxHeight','$customMask')";
            }
            $status = $this->executeGenericDMLQuery($sql);
            return $status;
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Get Count details
     *
     *@param (String)apikey
     *@return json data
     *
     */
    public function getCountDetails()
    {
        $apiKey = $this->_request['apikey'];
        if ($this->isValidCall($apiKey)) {
            try {
                $countArray = array();
                $countDesign = $this->executeFetchAssocQuery("SELECT COUNT(id) as total FROM " . TABLE_PREFIX . "designs");
                $countDesignCategory = $this->executeFetchAssocQuery("SELECT COUNT(id) as total FROM " . TABLE_PREFIX . "des_cat");
                $countFontCategory = $this->executeFetchAssocQuery("SELECT COUNT(id) as total FROM " . TABLE_PREFIX . "font_category");
                $countTemplateCategory = $this->executeFetchAssocQuery("SELECT COUNT(id) as total FROM " . TABLE_PREFIX . "template_category");
                $countShapeCategory = $this->executeFetchAssocQuery("SELECT COUNT(id) as total FROM " . TABLE_PREFIX . "shape_cat");
                $countPaletteCategory = $this->executeFetchAssocQuery("SELECT COUNT(id) as total FROM " . TABLE_PREFIX . "palette_category");
                $countShape = $this->executeFetchAssocQuery("SELECT COUNT(id) as total FROM " . TABLE_PREFIX . "shapes");
                $countMaskList = $this->executeFetchAssocQuery("SELECT COUNT(id) as total FROM " . TABLE_PREFIX . "mask_paths");
                $countDistress = $this->executeFetchAssocQuery("SELECT COUNT(id) as total FROM " . TABLE_PREFIX . "distress");
                $countPalettes = $this->executeFetchAssocQuery("SELECT COUNT(value) as total FROM " . TABLE_PREFIX . "palettes");
                $countWebFonts = $this->executeFetchAssocQuery("SELECT COUNT(id) as total FROM " . TABLE_PREFIX . "fonts");
                $countTextFx = $this->executeFetchAssocQuery("SELECT COUNT(id) as total FROM " . TABLE_PREFIX . "textfx");
                $countTextOnPath = $this->executeFetchAssocQuery("SELECT COUNT(id) as total FROM " . TABLE_PREFIX . "textonpath");
                $countWordCloud = $this->executeFetchAssocQuery("SELECT COUNT(id) as total FROM " . TABLE_PREFIX . "wordcloud");
                $countPrintTypes = $this->executeFetchAssocQuery("SELECT COUNT(id) as total FROM " . TABLE_PREFIX . "printing_details");
                $countPriceSettingItems = $this->executeFetchAssocQuery("SELECT COUNT(id) as total FROM " . TABLE_PREFIX . "module_price");
                $countArray['countDesign'] = $countDesign['total'];
                $countArray['countDesignCategory'] = $countDesignCategory['total'];
                $countArray['countFontCategory'] = $countFontCategory['total'];
                $countArray['countTemplateCategory'] = $countTemplateCategory['total'];
                $countArray['countShapeCategory'] = $countShapeCategory['total'];
                $countArray['countPaletteCategory'] = $countPaletteCategory['total'];
                $countArray['countShape'] = $countShape['total'];
                $countArray['countMaskList'] = $countMaskList['total'];
                $countArray['countDistress'] = $countDistress['total'];
                $countArray['countPalettes'] = $countPalettes['total'];
                $countArray['countWebFonts'] = $countWebFonts['total'];
                $countArray['countTextFx'] = $countTextFx['total'];
                $countArray['countTextOnPath'] = $countTextOnPath['total'];
                $countArray['countWordCloud'] = $countWordCloud['total'];
                $countArray['countPrintTypes'] = $countPrintTypes['total'];
                $countArray['countPriceSettingItems'] = $countPriceSettingItems['total'];
                $this->closeConnection();
                $this->response($this->json($countArray), 200);
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        } else {
            $msg = array("status" => "invalid");
            $this->response($this->json($msg), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Get  printint details
     *
     *@param (String)apikey
     *@return json data
     *
     */
    public function getPrintingDetails()
    {
        $apiKey = $this->_request['apikey'];
        if ($this->isValidCall($apiKey)) {
            $printArray['print'] = array();
            try {
                $sql = "SELECT * FROM " . TABLE_PREFIX . "printing_details";
                $printdataFromValue = mysqli_query($this->db, $sql);
                $i = 0;
                while ($row = mysqli_fetch_array($printdataFromValue)) {
                    $printArray['print'][$i]['id'] = $row['id'];
                    $printArray['print'][$i]['name'] = $row['name'];
                    $printArray['print'][$i]['type'] = $row['print_type'];
                    $printArray['print'][$i]['description'] = $row['description'];
                    $printArray['print'][$i]['status'] = $row['status'];
                    $printArray['print'][$i]['additional_price_status'] = $row['additional_price_status'];
                    $printArray['print'][$i]['min_order_lim'] = $row['min_quantity'];
                    $printArray['print'][$i]['max_palettes'] = $row['max_palettes_limit'];
                    $printArray['print'][$i]['setup_price'] = $row['setup_price'];
                    $printArray['print'][$i]['whitebase_price'] = $row['whitebase_price'];
                    $printArray['print'][$i]['palette_setup_status'] = $row['palette_setup_status'];
                    $printArray['print'][$i]['range_price'] = array();
                    $priceTable = array();
                    $sql = "SELECT id,lower_limit,upper_limit,whitebase_price FROM " . TABLE_PREFIX . "print_order_range WHERE printtype_id = " . $row['id'] . " ORDER BY lower_limit ASC";
                    $rangedataFromValue = mysqli_query($this->db, $sql);

                    $j = 0;
                    while ($rangeData = mysqli_fetch_array($rangedataFromValue)) {
                        $priceTable[$j]['rng'][0] = intval($rangeData['lower_limit']);
                        $priceTable[$j]['rng'][1] = intval($rangeData['upper_limit']);
                        $priceTable[$j]['whitebase_price'] = floatval($rangeData['whitebase_price']);
                        if ($row['palette_setup_status'] == 1) {
                            $sql = "SELECT price FROM " . TABLE_PREFIX . "palette_range_price WHERE order_range_id = " . $rangeData['id'] . ' ORDER BY num_palettes ASC';
                            $rangepriceFromValue = mysqli_query($this->db, $sql);
                            $k = 0;
                            while ($rangePrice = mysqli_fetch_array($rangepriceFromValue)) {
                                $priceTable[$j]['price'][$k] = floatval($rangePrice['price']);
                                $k++;
                            }
                            $j++;
                        }
                    }
                    $printArray['print'][$i]['range_price'] = $priceTable;
                    $i++;
                }
                if (isset($this->_request['returns']) && $this->_request['returns'] == true) {
                    return $printArray;
                } else {
                    $sql = "SELECT is_whitebase FROM " . TABLE_PREFIX . "settings_config";
                    $row = $this->executeFetchAssocQuery($sql);
                    $printArray['isWhitebase'] = intval($row['is_whitebase']);

                    $this->closeConnection();
                    $this->response($this->json($printArray), 200);
                }
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        } else {
            $msg = array("status" => "invalid");
            if (isset($this->_request['returns']) && $this->_request['returns'] == true) {
                return $msg;
            } else {
                $this->response($this->json($msg), 200);
            }

        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Get dtg print size details
     *
     *@param (String)apikey
     *@return json data
     *
     */
    public function getDtgSizeDetails()
    {
        $apiKey = $this->_request['apikey'];
        if ($this->isValidCall($apiKey)) {
            try {
                $printArray['dtgprint'] = array();
                $sql = "SELECT * FROM " . TABLE_PREFIX . "printing_dtg_details";
                $printdataFromValue = $this->executeFetchAssocQuery($sql);
                foreach ($printdataFromValue as $k => $row) {
                    $printArray['dtgprint'][$k]['id'] = $row['id'];
                    $printArray['dtgprint'][$k]['size'] = $row['size'];
                    $printArray['dtgprint'][$k]['width'] = $row['width'];
                    $printArray['dtgprint'][$k]['height'] = $row['height'];
                    $printArray['dtgprint'][$k]['price'] = $row['price'];
                }
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
            $this->closeConnection();
            $this->response($this->json($printArray), 200);
        } else {
            $msg = array("status" => "invalid");
            $this->response($this->json($msg), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Add dtg print size
     *
     *@param (String)apikey
     *@param (String)sizeName
     *@param (int)sizeWidth
     *@param (int)sizeHeight
     *@param (Float)sizePrice
     *@param (int)removedIds
     *@return json data
     *
     */
    public function addDtgSize()
    {
        $apiKey = $this->_request['apikey'];
        if ($this->isValidCall($apiKey)) {
            try {
                $sizeName = $this->_request['sizeName'];
                $sizeWidth = $this->_request['sizeWidth'];
                $sizeHeight = $this->_request['sizeHeight'];
                $sizePrice = floatval($this->_request['sizePrice']);
                $maxId = $this->getMaxId('printing_dtg_details', 'id');
                $maxId = $maxId + 1;
                $sql = "insert into " . TABLE_PREFIX . "printing_dtg_details(id,size,width,height,price)values($maxId,'$sizeName',$sizeWidth,$sizeHeight,$sizePrice)";
                $status = $this->executeGenericDMLQuery($sql);
                $this->closeConnection();
                if ($status) {
                    $msg = array("status" => "success", "id" => $maxId);
                } else {
                    $msg = array("status" => "failed", "sql" => $sql);
                }
                $this->response($this->json($msg), 200);
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        } else {
            $msg = array("status" => "invalid");
            $this->response($this->json($msg), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Update dtg print Size
     *
     *@param (String)apikey
     *@param (int)sizeIds
     *@param (int)sizeWidth
     *@param (int)sizeHeight
     *@param (Float)printPrices
     *@param (int)removedIds
     *@return json data
     *
     */
    public function updateDtgSizeDetails()
    {
        $apiKey = $this->_request['apikey'];
        if ($this->isValidCall($apiKey)) {
            $sizeIdsArray = $this->_request['sizeIds'];
            $sizeWidth = $this->_request['sizeWidth'];
            $sizeHeight = $this->_request['sizeHeight'];
            $printPricesArray = $this->_request['printPrices'];
            $removedIds = $this->_request['removedIds'];
            $status = 0;
            try {
                if (sizeof($printPricesArray) != 0) {
                    for ($j = 0; $j < sizeof($removedIds); $j++) {
                        $sql = "delete from " . TABLE_PREFIX . "printing_dtg_details where id=$removedIds[$j]";
                        $status .= $this->executeGenericDMLQuery($sql);
                    }
                    for ($j = 0; $j < sizeof($sizeIdsArray); $j++) {
                        $sql = "UPDATE " . TABLE_PREFIX . "printing_dtg_details SET price = " . $printPricesArray[$j] . " WHERE id = '" . $sizeIdsArray[$j] . "'";
                        $status .= $this->executeGenericDMLQuery($sql);
                    }
                } else {
                    $sql = "UPDATE " . TABLE_PREFIX . "printing_dtg_details SET width = " . $sizeWidth . " ,height = " . $sizeHeight . " WHERE id = '" . $sizeIdsArray . "'";
                    //$this->log('updateDtgSizeDetails:'.$sql);
                    $status .= $this->executeGenericDMLQuery($sql);
                }
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
            $this->closeConnection();
            if ($status) {
                $msg = array("status" => "success");
                $this->response($this->json($msg), 200);
            } else {
                $msg = array("status" => "failed");
                $this->response($this->json($msg), 200);
            }
        } else {
            $msg = array("status" => "invalid");
            $this->response($this->json($msg), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *remove print Size
     *
     *@param (String)apikey
     *@param (int)printsize
     *@return json data
     *
     */
    public function removePrintSize()
    {
        $apiKey = $this->_request['apikey'];
        $printSizeName = $this->_request['printsize'];
        if ($this->isValidCall($apiKey)) {
            try {
                $sql = "Select * from  " . TABLE_PREFIX . "product_sides_sizes where printsize='$printSizeName'";
                $result = mysqli_query($this->db, $sql);

                if (mysqli_num_rows($result) < 1) {
                    $sql = "delete from  " . TABLE_PREFIX . "printing_dtg_details where size='$printSizeName'";
                    $status = $this->executeGenericDMLQuery($sql);
                    if ($status) {
                        $msg = array("status" => "success");
                    } else {
                        $msg = array("status" => "failed");
                    }

                } else {
                    $msg = array("status" => "printsize exists");
                }

                $this->closeConnection();
                $this->response($this->json($msg), 200);
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        } else {
            $msg = array("status" => "invalid");
            $this->response($this->json($msg), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Update print type
     *
     *@param (String)apikey
     *@param (int)printTypeId
     *@return json data
     *
     */
    public function updatePrintingType()
    {
        $apiKey = $this->_request['apikey'];
        if ($this->isValidCall($apiKey)) {
            $printTypeId = $this->_request['printTypeId'];
            $status = 0;
            try {
                $sql = "UPDATE " . TABLE_PREFIX . "printing_details SET status = 'false' WHERE status = 'true'";
                $this->executeGenericDMLQuery($sql);

                $sql = "UPDATE " . TABLE_PREFIX . "printing_details SET status = 'true' WHERE id = '" . $printTypeId . "'";

                $status .= $this->executeGenericDMLQuery($sql);
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
            $status = ($status) ? "success" : "nodata";
            $msg = array("status" => $status);
            $this->response($this->json($msg), 200);
        } else {
            $status = "invalid";
            $msg = array("status" => $status);
            $this->response($this->json($msg), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Update Module price status
     *
     *@param (String)apikey
     *@param (Array)itemIds
     *@param (int)itemsStatus
     *@return json data
     *
     */
    public function updateModulePriceStatus()
    {
        $apiKey = $this->_request['apikey'];
        if ($this->isValidCall($apiKey)) {
            $itemIds = $this->_request['itemIds'];
            $itemsStatus = $this->_request['itemsStatus'];
            $status = 0;
            try {
                for ($i = 0; $i < sizeof($itemIds); $i++) {
                    $itemId = $itemIds[$i];
                    $itemStatus = $itemsStatus[$i];
                    $sql = "UPDATE " . TABLE_PREFIX . "module_price SET status = '" . $itemStatus . "' WHERE id = '" . $itemId . "'";
                    $status .= $this->executeGenericDMLQuery($sql);
                }
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
            $status = ($status) ? "success" : "nodata";
            $msg = array("status" => $status);
            $this->response($this->json($msg), 200);
        } else {
            $status = "invalid";
            $msg = array("status" => $status);
            $this->response($this->json($msg), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Update paletta category
     *
     *@param (String)apikey
     *@param (Int)categoryId
     *@param (String)categoryName
     *@return json data
     *
     */
    public function editPaletteCategory()
    {
        $apiKey = $this->_request['apikey'];
        if ($this->isValidCall($apiKey)) {
            try {
                $categoryId = $this->_request['categoryId'];
                $categoryName = $this->_request['categoryName'];
                $sql = "UPDATE " . TABLE_PREFIX . "palette_category SET name = '" . $categoryName . "' WHERE id = $categoryId";
                $status = $this->executeGenericDMLQuery($sql);
                if ($status) {
                    $msg = array("status" => "success");
                } else {
                    $msg = array("status" => "failed", "sql" => $sql);
                }

                $settingsObj = Flight::setting();
                $settingsObj->allSettingsDetails(1);
                $this->closeConnection();
                $this->response($this->json($msg), 200);
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        } else {
            $msg = array("status" => "invalid");
            $this->response($this->json($msg), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Save word cloud
     *
     *@param (String)apikey
     *@param (String)wordcloudName
     *@param (Float)wordcloudPrice
     *@param (String)fileExtensions
     *@param (String)data
     *@return json data
     *
     */
    public function saveWordcloud()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('!POST', 406);
        }
        $apiKey = $this->_request['apikey'];
        $wordcloudName = $this->_request["wordcloudName"];
        $wordcloudPrice = $this->_request["wordcloudPrice"];
        $fileExtensionsArray = $this->_request['fileExtensions']; //file names
        $base64DataArray = $this->_request['data']; //base64Data for files
        if ($this->isValidCall($apiKey)) {
            try {
                $base64Data = base64_decode($data);
                $dir = $this->getWordcloudImagePath();
                if (!$dir) {
                    $this->response('Invalid Directory', 204); //204 - immediately termiante this request
                }
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }
                for ($j = 0; $j < sizeof($base64DataArray); $j++) {
                    $wordcloudId = $this->getDBUniqueId('wordcloud', 'id');
                    $fileName = $wordcloudId . '.' . $fileExtensionsArray[$j];
                    $data = $base64DataArray[$j];
                    $base64Data = base64_decode($data);
                    $filePath = $dir . $fileName;
                    $fileStatus = file_put_contents($filePath, $base64Data);
                    $msg = '';
                    $dataStatus = 0;
                    if ($fileStatus) {
                        $sql = "insert into " . TABLE_PREFIX . "wordcloud(id,name,price,file_name) values('$wordcloudId','$wordcloudName','wordcloudPrice','$fileName')";
                        $dataStatus = $this->executeGenericDMLQuery($sql);
                    }
                    $msg['status'] = ($dataStatus) ? 'success' : 'failed';
                }
                $this->closeConnection();
                $this->response($this->json($msg), 200);
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        } else {
            $msg = array("status" => "invalid");
            $this->response($this->json($msg), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)`
     *Resize print image
     *
     *@param (String)apikey
     *@param (String)src
     *@param (String)dst
     *@param (int)width
     *@param (int)height
     *@param (int)crop
     *@return true value
     *
     */
    public function resizePatternImage($src, $dst, $width, $height, $crop = 0)
    {
        try {
            if (!list($w, $h) = getimagesize($src)) {
                return "Unsupported picture type!";
            }

            $type = strtolower(substr(strrchr($src, "."), 1));
            if ($type == 'jpeg') {
                $type = 'jpg';
            }

            switch ($type) {
                case 'bmp':$img = imagecreatefromwbmp($src);
                    break;
                case 'gif':$img = imagecreatefromgif($src);
                    break;
                case 'jpg':$img = imagecreatefromjpeg($src);
                    break;
                case 'png':$img = imagecreatefrompng($src);
                    break;
                default:return "Unsupported picture type!";
            }
            // resize
            if ($crop) {
                if ($w < $width or $h < $height) {
                    return "Picture is too small!";
                }

                $ratio = max($width / $w, $height / $h);
                $h = $height / $ratio;
                $x = ($w - $width / $ratio) / 2;
                $w = $width / $ratio;
            } else {
                if ($w < $width and $h < $height) {
                    return "Picture is too small!";
                }

                $ratio = min($width / $w, $height / $h);
                $width = $w * $ratio;
                $height = $h * $ratio;
                $x = 0;
            }
            $new = imagecreatetruecolor($width, $height);
            // preserve transparency
            if ($type == "gif" or $type == "png") {
                imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
                imagealphablending($new, false);
                imagesavealpha($new, true);
            }
            imagecopyresampled($new, $img, 0, 0, $x, 0, $width, $height, $w, $h);
            switch ($type) {
                case 'bmp':imagewbmp($new, $dst);
                    break;
                case 'gif':imagegif($new, $dst);
                    break;
                case 'jpg':imagejpeg($new, $dst);
                    break;
                case 'png':imagepng($new, $dst);
                    break;
            }
            imagedestroy($img);
            return true;
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            return $result;
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get Template category list
     *
     *@param (String)apikey
     *@return json data
     *
     */
    public function getTemplateCategoryList()
    {
        try {
            $sql = "Select tc.id,tc.name,ts.template_image from " . TABLE_PREFIX . "template_category tc left join " . TABLE_PREFIX . "template_state ts on (tc.id=ts.cat_id) where ts.template_image<>'' group by ts.cat_id";
            $result = $this->executeGenericDQLQuery($sql);
            if (!empty($result)) {
                $categories = array();
                foreach ($result as $rows) {
                    $id = $rows['id'];
                    $name = $rows['name'];
                    $template_image = $rows['template_image'];
                    $savePath = $this->getTemplateImageURL();
                    $productURL = $savePath . 'products/' . $template_image;
                    $data = array("id" => $id, "name" => $name, "product_image" => $productURL);
                    array_push($categories, $data);
                }
                $this->closeConnection();
                $this->response($this->json($categories), 200);
            } else {
                $msg = array("status" => "nodata");
                $this->closeConnection();
                $this->response($this->json($msg), 200);
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
     *save Template sub category
     *
     *@param (String)apikey
     *@param (int)subCategoryId
     *@param (int)curSubCategory
     *@return json data
     *
     */
    public function saveTemplateSubCategory()
    {
        try {
            $status = 0;
            $apiKey = $this->_request['apikey'];
            //$refid = $this->_request['refid'];
            $categoryId = $this->_request['categoryId'];
            $subCategory = $this->_request['subCategory'];
            $sql = "INSERT INTO " . TABLE_PREFIX . "template_subcategory (name, cat_id) VALUES ('$subCategory', $categoryId)";
            $status = $this->executeGenericDMLQuery($sql);
            if ($status) {
                $msg = array("status" => "success", "templtsubCategory" => $subCategory);
            } else {
                $msg = array("status" => "failed", "sql" => $sql);
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
     *Update Template sub category
     *
     *@param (String)apikey
     *@param (int)subCategoryId
     *@param (int)curSubCategory
     *@return json data
     *
     */
    public function updateTemplateSubCategory()
    {
        try {
            $apiKey = $this->_request['apikey'];
            $subCategoryId = $this->_request['subCategoryId'];
            $curSubCategory = $this->_request['curSubCategory'];
            $sql = "UPDATE " . TABLE_PREFIX . "template_subcategory SET name = '" . $curSubCategory . "' WHERE id = " . $subCategoryId;
            $result = $this->executeGenericDMLQuery($sql);
            $msg['status'] = ($status) ? 'success' : 'failed';
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
     *Save Svg file
     *
     *@param (String)apikey
     *@param (int)customerId
     *@param (String)data
     *@return json data
     *
     */
    public function saveSvg()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('not post', 406);
        }
        $apiKey = $this->_request['apikey'];
        $customerId = 0;
        if (isset($this->_request['customerId'])) {
            $customerId = $this->_request['customerId'];
        }

        $data = $this->_request['data'];
        $ext = "svg";
        $base64Data = base64_decode($data);
        $count = 0;
        try {
            $sql0 = "Select max(id) from " . TABLE_PREFIX . "svg_data";
            $result0 = mysqli_query($this->db, $sql0);
            if (mysqli_num_rows($result0) > 0) {
                if ($rows0 = mysqli_fetch_array($result0)) {
                    $count = $rows0[0];
                }

            }
            $count = $count + 1;
            $fileName = $count . '.' . $ext;
            $baseImagePath = $this->getUserImagePath();
            $savePath = $baseImagePath . $customerId . '/';
            $baseImageURL = $this->getUserImageURL();
            $imageURL = $baseImageURL . $customerId . '/';
            if (!file_exists($savePath)) {
                mkdir($savePath, 0777, true);
            }
            $filePath = $savePath . $fileName;
            $status = file_put_contents($filePath, $base64Data);
            $msg = '';
            if ($status) {
                $sql = "INSERT INTO " . TABLE_PREFIX . "svg_data (customerId,svg,date_created) VALUES ($customerId,'$fileName', now())";
                $status = $this->executeGenericDMLQuery($sql);
                if ($status) {
                    $msg = array("status" => "success", "filename" => $fileName, "filepath" => $filePath);
                } else {
                    $msg = array("status" => "failed");
                }
            } else {
                $msg = array("status" => "failed");
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
     *getSvg file
     *
     *@param (String)apikey
     *@param (int)customerId
     *@return json data
     *
     */
    public function getSvg()
    {
        try {
            $apiKey = $this->_request['apikey'];
            $customerId = $this->_request['customerId'];
            $baseImageURL = $this->getUserImageURL();
            $imageURL = $baseImageURL . $customerId . '/';
            $sql = "Select svg from  " . TABLE_PREFIX . "svg_data where customerid=" . $customerId;
            $result = mysqli_query($this->db, $sql);
            if (mysqli_num_rows($result) > 0) {
                $images = array();
                while ($rows = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                    $image = $rows['svg'];
                    $data = array("filepath" => $imageURL . $image);
                    $images[] = $data;
                }
                $this->closeConnection();
                $this->response($this->json($images), 200);
            } else {
                $msg = array("status" => "nodata");
                $this->response($sql);
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
     *remove category
     *
     *@param (String)apikey
     *@param (String)removeCategory
     *@return json data
     *
     */
    public function removeCategory()
    {
        try {
            $pCategory = $this->_request['removeCategory'];
            $sql = "select id , category_name from " . TABLE_PREFIX . "des_cat where category_name = '$pCategory'";
            $row = $this->executeGenericDQLQuery($sql);
            $response = array();
            if (sizeof($row) == "0") {
                // category not present error
                $response['status'] = false;
                $response['message'] = 'ERROR cateory not present';
            } else {
                // perform delete
                $sql = "DELETE FROM " . TABLE_PREFIX . "des_cat WHERE category_name= '$pCategory'";
                $this->executeGenericDMLQuery($sql);
                // deleting fow from design_category_sub_category_rel
                $pCategoryId = $row[0]['id'];
                $sql = "DELETE FROM " . TABLE_PREFIX . "design_category_sub_category_rel  WHERE category_id= $pCategoryId";
                $this->executeGenericDMLQuery($sql);
                $response['status'] = true;
                $response['message'] = "'$pCategory' cateory delete successful !!";
            }
            $this->closeConnection();
            $this->response($this->json($response), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created 19-1-2015 (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get Screen Printing percentage value
     *
     *@param (String)apikey
     *@return json data
     *
     */
    public function getScreenPrintPercentageInfo()
    {
        $apiKey = $this->_request['apikey'];
        if ($this->isValidCall($apiKey)) {
            try {
                $sql = "SELECT * FROM " . TABLE_PREFIX . "printing_dtg_details WHERE is_default=1";
                $rows = $this->executeGenericDQLQuery($sql, $this->db);
                $printSizePercentage = array();
                for ($i = 0; $i < sizeof($rows); $i++) {
                    $printSizePercentage[$i]['id'] = $rows[$i]['id'];
                    $printSizePercentage[$i]['size'] = $rows[$i]['size'];
                    $printSizePercentage[$i]['percentage'] = floatval($rows[$i]['screenprint_percentage']);
                }
                $this->response($this->json($printSizePercentage), 200);
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        } else {
            $msg = array("status" => "invalid");
            $this->response($this->json($msg), 200);
        }
    }
    /**
     *
     *date created 19-1-2015 (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Update Screen Printing percentage value
     *
     *@param (String)apikey
     *@param (Array)sizeIds
     *@param (Array)percentages
     *@return json data
     *
     */
    public function updateScreenPrintPercentageInfo()
    {
        $apiKey = $this->_request['apikey'];
        if ($this->isValidCall($apiKey)) {
            try {
                $sizeIds = $this->_request['sizeIds'];
                $percentages = $this->_request['percentages'];
                $status = 0;
                for ($i = 0; $i < sizeof($sizeIds); $i++) {
                    $sql = "UPDATE " . TABLE_PREFIX . "printing_dtg_details SET screenprint_percentage = " . $percentages[$i] . " WHERE id = " . $sizeIds[$i];
                    $status .= $this->executeGenericDMLQuery($sql);
                }
                $msg['status'] = ($status) ? 'success' : 'failed';
            } catch (Exception $e) {
                $msg = array('Caught exception:' => $e->getMessage());
            }
        } else {
            $msg = array("status" => "invalid");
        }
        $this->response($this->json($msg), 200);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *same as array_unique function in php but it works on array of objects i.e. multi diamentional array
     *
     *@param (String)apikey
     *@param (Array)pObjArr
     *@param (String)prop1
     *@return array
     *
     */
    public function uniqueObjArray($pObjArr, $prop1)
    {
        // setting the property attributes
        $resArr = array();
        for ($i = 0; $i < sizeof($pObjArr); $i++) {
            $found = false;
            for ($j = 0; $j < sizeof($resArr) && isset($pObjArr[$i][$prop1]); $j++) {
                if ($pObjArr[$i][$prop1] == $resArr[$j][$prop1]) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                array_push($resArr, $pObjArr[$i]);
            }
        }
        return $resArr;
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *get labels
     *
     *@param (String)apikey
     *@param (Int)languageId
     *@return json data
     *
     */
    public function getLabels()
    {
        try {
            $languageId = $this->_request['languageId'];
            if ($languageId == '' || $languageId == 'undefined') {
                $sql = "select c.id content_id ,c.content_name content_name from " . TABLE_PREFIX . "content c ";
            } else {
                $sql = "select c.id content_id , c.content_name content_name , l.id lang_id , " .
                    "l.name lang_name , l.`status` lang_status ,t.content_id content_id_rel , " .
                    "t.language_id lang_id_rel , t.translate_text translate_text  from " . TABLE_PREFIX . "content c " .
                    "left join " . TABLE_PREFIX . "translate t on c.id = t.content_id left join " . TABLE_PREFIX . "app_language l " .
                    "on t.language_id = l.id where l.id = $languageId or l.id is null";
            }
            $rows = $this->executeGenericDQLQuery($sql);
            $languageInfo = array();
            $languageInfo['language_id'] = isset($rows[0]['lang_id']) == "" ? 0 : $rows[0]['lang_id'];
            $languageInfo['language_name'] = isset($rows[0]['lang_name']) == "" ? "" : $rows[0]['lang_name'];
            $translateArr = array();
            for ($i = 0; $i < sizeof($rows); $i++) {
                $translateArr[$i]['content_id'] = $rows[$i]['content_id'];
                $translateArr[$i]['content_name'] = $rows[$i]['content_name'];
                $translateArr[$i]['translate_text'] = isset($rows[$i]['translate_text']) == "" ? "" : $rows[$i]['translate_text'];
            }
            $translateData = array();
            $translateData['languageInfo'] = $languageInfo;
            $translateData['translateArr'] = $translateArr;
            $this->response($this->json($translateData), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *post translate details
     *
     *@param (String)apikey
     *@param (Object)languageObj
     *@param (Array)translateArr
     *@return json data
     *
     */
    public function postTranslateDetails()
    {
        try {
            $languageObj = $this->_request['languageObj'];
            $translateArr = $this->_request['translateArr'];
            $isNewEntry = false;
            //checking for language is new or existing
            //if languageObj[0]['id'] = 0 --> new language else existing
            $sql = '';
            if ($languageObj['id'] == 0) {
                // new language , insert to language table and get the language id for mapping
                $sql = "insert into " . TABLE_PREFIX . "app_language(name) values('" . $languageObj['name'] . "')";
                $languageId = $this->executeGenericInsertQuery($sql);
                $isNewEntry = true;
                $languageObj['id'] = $languageId;
            } else {
                // update language  and get the language id for mapping
                $sql = "update " . TABLE_PREFIX . "app_language set name='" . $languageObj['name'] . "' where id=" . $languageObj['id'];
                $languageId = $languageObj['id'];
            }
            // mapping of language id and translate text into translate table
            // deleting previous records of translation the selected language
            $sql = "delete from " . TABLE_PREFIX . "translate where language_id=$languageId";
            $this->executeGenericDMLQuery($sql);
            for ($i = 0; $i < sizeof($translateArr); $i++) {
                $sql = "insert into " . TABLE_PREFIX . "translate(content_id,translate_text,language_id) values(" . $translateArr[$i]['content_id'] . ",'" . $translateArr[$i]['translate_text'] . "'," . $languageId . ")";
                $this->executeGenericDMLQuery($sql);
            }
            $response = array();
            $response['status'] = "success";
            $response['new_entry'] = $isNewEntry;
            // ading new language info
            if ($isNewEntry) {
                $response['new_language'] = $languageObj;
                $response['message'] = "language added successfully";
            } else {
                $response['message'] = "language updated successfully";
            }
            $this->response($this->json($response), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Update paletta category Status
     *
     *@param (String)apikey
     *@param (Array)paletteCategoryId_List
     *@param (Int)paletteCategoryStatus_List
     *@return json data
     *
     */
    public function updatePaletteCategoryStatus()
    {
        $apiKey = $this->_request['apikey'];
        $status = 0;
        if ($this->isValidCall($apiKey)) {
            try {
                $paletteCategoryId_List = $this->_request['paletteCategoryId_List'];
                $paletteCategoryStatus_List = $this->_request['paletteCategoryStatus_List'];
                for ($i = 0; $i < sizeof($paletteCategoryId_List); $i++) {
                    $sql = "UPDATE " . TABLE_PREFIX . "palette_category SET is_available = '" . $paletteCategoryStatus_List[$i]['status'] . "' WHERE id = '" . $paletteCategoryId_List[$i]['id'] . "'";
                    $status = $this->executeGenericDMLQuery($sql);
                }
                $msg['status'] = ($status) ? 'success' : 'failed';
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        } else {
            $msg = array("status" => "invalid");
        }
        $this->response($this->json($msg), 200);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *fetch Size variant additional price
     *
     *@param (String)apikey
     *@param (Int)confProductId
     *@return json data
     *
     */
    public function getSizeVariantAdditionalPrice($confProductId)
    {
        if (isset($confProductId) && $confProductId) {
            try {
                $sql = "SELECT DISTINCT xe_size_id FROM " . TABLE_PREFIX . "size_variant_additional_price where product_id='" . $confProductId . "' order by pk_id";
                $resArr = array();
                $rows = $this->executeGenericDQLQuery($sql);
                for ($i = 0; $i < sizeof($rows); $i++) {
                    $resArr[$i]['xe_size_id'] = $rows[$i]['xe_size_id'];
                }
                for ($i = 0; $i < sizeof($resArr); $i++) {
                    $sql_arr = "SELECT  distinct pk_id,print_method_id,percentage
						FROM   " . TABLE_PREFIX . "size_variant_additional_price
						WHERE   xe_size_id ='" . $resArr[$i]['xe_size_id'] . "' AND product_id =" . $confProductId . ' ORDER BY pk_id';
                    $row = $this->executeGenericDQLQuery($sql_arr);
                    $sizePrice = array();
                    for ($j = 0; $j < sizeof($row); $j++) {
                        $sizePrice[$j]['pk_id'] = $row[$j]['pk_id'];
                        $sizePrice[$j]['print_method_id'] = $row[$j]['print_method_id'];
                        $sizePrice[$j]['percentage'] = $row[$j]['percentage'];
                    }
                    $resArr[$i]['sizePrice'] = $sizePrice;
                }
                return $resArr;
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        } else {
            $msg = array("status" => 'nodata');
            $this->response($this->json($msg), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *fetch product featur list
     *
     *@param (String)apikey
     *@return json data
     *
     */
    public function getProductFeatureList()
    {
        $apiKey = $this->_request['apikey'];
        $response = array();
        if ($this->isValidCall($apiKey)) {
            try {
                $sql = "select f.id,f.name,f.type from " . TABLE_PREFIX . "features f where f.status = 1 AND f.product_level_status = 1";
                $rows = $this->executeGenericDQLQuery($sql);
                $activeFeatures;
                foreach ($rows as $row) {
                    $activeFeatures[] = array('id' => $row['id'],
                        'name' => $row['name'],
                        'type' => $row['type']);
                }
                $response = $activeFeatures;
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        } else {
            $response['status'] = "error";
            $response['data'] = 'invalid Api key';
        }
        $this->response($this->json($response), 200);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 26-4-2016(dd-mm-yy)
     *fetch print size information
     *
     *@param (String)apikey
     *@return json data
     *
     */
    public function getSizeInfo()
    {
        $apiKey = $this->_request['apikey'];
        if ($this->isValidCall($apiKey)) {
            $printSize['printSize'] = array();
            try {
                $sql = "SELECT * FROM " . TABLE_PREFIX . "print_size ORDER BY pk_id DESC";
                $rows = $this->executeFetchAssocQuery($sql);
                foreach ($rows as $k => $row) {
                    $printSize['printSize'][$k]['id'] = $row['pk_id'];
                    $printSize['printSize'][$k]['name'] = $row['name']; //addslashes($row['name']);//$row['name'];
                    $printSize['printSize'][$k]['width'] = $row['width'];
                    $printSize['printSize'][$k]['height'] = $row['height'];
                    $printSize['printSize'][$k]['is_user_defined'] = $row['is_user_defined'];
                    $printSize['printSize'][$k]['is_default'] = $row['is_default'];
                }
                $this->response($this->json($printSize), 200);
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        } else {
            $msg = array("status" => "invalid");
            $this->response($this->json($msg), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *add last revision
     *
     *@param (String)apikey
     *@param (String)comment
     *@return json data
     *
     */
    public function addLatestRevision()
    {
        try {
            $comment = $this->_request['comment'];
            $setDataStatus = 0;
            $sql = "insert into " . TABLE_PREFIX . "revision (comment,date_created) values('$comment',now())";
            $id .= $this->executeGenericInsertQuery($sql);
            if ($id) {
                $msg = array("status" => "success", "comment" => $comment);
            } else {
                $msg = array("status" => "Can't save the data. ::failed");
            }
            $this->closeConnection();
            $this->response($this->json($msg), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *fetch comment from revision by maximum  id
     *
     *@param (String)apikey
     *@return json data
     *
     */
    public function getLatestRevision()
    {
        try {
            $sql = "SELECT MAX(pk_id) AS n FROM " . TABLE_PREFIX . "revision LIMIT 1";
            $res = $this->executeFetchAssocQuery($sql);
            $latestRevision = (!empty($res) && $res[0]['n']) ? $res[0]['n'] : 0;
            $this->closeConnection();
            $this->response($latestRevision, 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *To update palette/color category
     *
     *@param (String)apikey
     *@param (Array)palette_category
     *@param (int)print_method_id
     *@return json data
     *
     */
    public function updatePaletteCategory($data = array())
    {
        //TRUNCATE print_method_palette_category ;Truncate palette_category;
        $status = 0;
        try {
            if (!empty($this->_request) && isset($this->_request['print_method_id']) && $this->_request['print_method_id']) {
                extract($this->_request);
                // Delete from print_method_palette_category During Update
                $sql = "DELETE FROM " . TABLE_PREFIX . "print_method_palette_category WHERE print_method_id='" . $print_method_id . "'";
                $status = $this->executeGenericDMLQuery($sql);
                if (!empty($palette_category)) {
                    // For Multiple Insertion
                    $sql = "INSERT INTO " . TABLE_PREFIX . "print_method_palette_category(print_method_id, palette_category_id, is_enable) VALUES";
                    foreach ($palette_category as $palette_category) {
                        $sql .= "('" . $print_method_id . "', '" . $palette_category['pk_id'] . "', '" . $palette_category['is_enable'] . "'),";
                    }
                    $sql = substr($sql, 0, strlen($sql) - 1);
                    $status = $this->executeGenericDMLQuery($sql);
                }
                $settingsObj = Flight::setting();
                $settingsObj->allSettingsDetails(1);
            }
            $this->getAllPrintSettings($print_method_id);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *get Template by product id
     *
     *@param (String)apikey
     *@param (int)productId
     *@return json data
     *
     */
    public function getTemplateByProductId()
    {
        try {
            $productId = $this->_request['productId'];
            if ($productId != '') {
                $sql = "Select template_id from " . TABLE_PREFIX . "template_product_rel where product_id='" . $productId . "'";
                $row = $this->executeFetchAssocQuery($sql);

                $templateIds['templateIds'] = array();
                if (!empty($row)) {
                    foreach ($row as $rows) {
                        $templateIds['templateIds'][] = $rows['template_id'];
                    }
                }
            }
            $this->response($this->json($templateIds), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Assign Template to product
     *
     *@param (String)apikey
     *@param (int)productId
     *@param (int)templateIds
     *@return json data
     *
     */
    public function assignTemplatesToProduct()
    {
        try {
            $productId = $this->_request['productId'];
            $templateIds = explode(",", $this->_request['templateIds']);
            $sql = "delete from " . TABLE_PREFIX . "template_product_rel where product_id='" . $productId . "'";
            $this->executeGenericDMLQuery($sql);
            $setDataStatus = 0;
            for ($k = 0; $k < sizeof($templateIds); $k++) {
                $sql = "insert into " . TABLE_PREFIX . "template_product_rel(template_id,product_id) values($templateIds[$k],'$productId')";
                $setDataStatus .= $this->executeGenericDMLQuery($sql);
            }
            $msg['status'] = ($status) ? 'success' : 'failed';

        } catch (Exception $e) {
            $msg = array('Caught exception:' => $e->getMessage());
        }
        $this->response($this->json($msg), 200);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *set  paroductid and printmethodid
     *Needs to be checked: when all the print types unchecked and submited.
     *
     *@param (String)apikey
     *@param (int)productId
     *@param (int)prntmethodid
     *@return json data
     *
     */
    public function setProductPrintmethod_old()
    {
        $productId = $this->_request['productId'];
        $printmethodid = $this->_request['prntmethodid'];
        $printmethodId = explode(',', $printmethodid);
        $key = $this->_request['apikey'];
        if (!empty($productId) && !empty($printmethodId) && !empty($key) && $this->isValidCall($key)) {
            $sql = "delete from " . TABLE_PREFIX . "product_printmethod_rel where product_id='" . $productId . "'";
            $this->executeGenericDMLQuery($sql);
            $status = 0;
            for ($k = 0; $k < sizeof($printmethodId); $k++) {
                $sql = "insert into " . TABLE_PREFIX . "product_printmethod_rel(product_id,print_method_id) values('$productId','$printmethodId[$k]')";
                $status .= $this->executeGenericInsertQuery($sql);
            }
            if ($status) {
                $msg = array("status" => "success");
            } else {
                $msg = array("status" => "failed");
            }

        } else {
            $msg = array("status" => "invalidkey");
        }

        $this->closeConnection();
        $this->response($this->json($msg), 200);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *set product additional price by product id
     *
     *@param (String)apikey
     *@param (int)productid
     *@param (Array)variantDetails
     *@return json data
     *
     */
    public function setProductAdditionalPrice()
    {
        try {
            if (!empty($this->_request) && $this->_request['productid'] && $this->_request['apikey'] && $this->isValidCall($this->_request['apikey'])) {
                extract($this->_request);
                $variantDetails = isset($variantDetails) ? $this->formatJSONToArray($variantDetails) : array();
                //echo '<pre>';print_r($variantDetails);
                // deleting existing records of selected product id
                $status = 0;
                $sql = "delete from " . TABLE_PREFIX . "product_additional_prices where product_id=$productid";
                $this->executeGenericDMLQuery($sql);
                // inserting new data get from font end
                if (!empty($variantDetails)) {
                    $sql = array();
                    foreach ($variantDetails as $k => $v) {
                        if (!empty($variantDetails[$k]['price'])) {
							$v1['prntmthdid'] = (isset($v1['prntmthdid']) && $v1['prntmthdid'])?$v1['prntmthdid']:0;
							foreach ($variantDetails[$k]['price'] as $k1 => $v1) {
                                $sql[] = "('" . $productid . "' , '" . $variantDetails[$k]['variantid'] . "','" . $v1['prntmthdid'] . "', '" . $v1['prntmthdprice'] . "','" . $v1['is_whitebase'] . "')";
                            }
                        }
                    }
                    if (count($sql)) {
                        $sql = "insert into " . TABLE_PREFIX . "product_additional_prices(product_id,variant_id,print_method_id,price,is_whitebase) values" . implode(',', $sql);
                        $status = $this->executeGenericDMLQuery($sql);
                    }
                }
                $status = ($status) ? "success" : "failed";
            } else {
                $status = "invalidkey";
            }
            $msg = array("status" => $status);
            $this->closeConnection();
            $this->response($this->json($msg), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *fetch all additional price by product id
     *
     *@param (String)apikey
     *@param (int)productid
     *@return json data
     *
     */
    public function getProductAdditionalPrice()
    {
        $productId = $this->_request['productid'];
        $key = $this->_request['apikey'];
        if (!empty($productId) && !empty($key) && $this->isValidCall($key)) {
            try {
                $sql = "SELECT DISTINCT variant_id FROM " . TABLE_PREFIX . "product_additional_prices WHERE product_id='$productId' ORDER BY pk_id";
                $variantIdsArray = array();
                //$rows = $this->executeGenericDQLQuery($sql);
                $rows = $this->executeFetchAssocQuery($sql);
                // $count = sizeof($rows);
                if (!empty($rows)) {
                    foreach ($rows as $k => $v) {
                        $variantIdsArray[$k]['variantid'] = $v['variant_id'];
                    }
                }
                $countvarient = sizeof($variantIdsArray);
                for ($i = 0; $i < $countvarient; $i++) {
                    $sql = "SELECT  distinct print_method_id,price,is_whitebase
							FROM   " . TABLE_PREFIX . "product_additional_prices
							WHERE  product_id ='$productId'
							AND    variant_id =" . $variantIdsArray[$i]['variantid'] . " ORDER BY pk_id";
                    //$rows = $this->executeGenericDQLQuery($sql);
                    $rows = $this->executeFetchAssocQuery($sql);
                    $priceDetails = array();
                    //$num = sizeof($rows);
                    if (!empty($rows)) {
                        foreach ($rows as $k => $v) {
                            $priceDetails[$k]['prntmthdid'] = $v['print_method_id'];
                            $priceDetails[$k]['prntmthdprice'] = $v['price'];
                            $priceDetails[$k]['is_whitebase'] = $v['is_whitebase'];
                        }
                    }
                    $variantIdsArray[$i]['price'] = $priceDetails;
                }
                $varnDetails = array();
                $varnDetails = $variantIdsArray;
                $this->response($this->json($varnDetails), 200);
            } catch (Exception $e) {
                $msg = array('Caught exception:' => $e->getMessage());
            }
        } else {
            $msg = array("status" => "invalidkey");
        }
        $this->response($this->json($msg), 200);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Get TextFx svg file path
     *
     *@return base image url
     *
     */
    public function getTextfxSvgPath()
    {
        $baseImagePath = $this->getBasePath() . self::HTML5_TEXTFX_DIR;
        return $baseImagePath;
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *get Word cloud svg file path
     *
     *@return base image url
     *
     */
    public function getWordcloudSvgPath()
    {
        $baseImagePath = $this->getBasePath() . self::HTML5_WORDCLOUD_DIR;
        return $baseImagePath;
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *get paletta path
     *
     *@return image url
     *
     */
    public function getPalettePath()
    {
        $baseImagePath = $this->getBasePath() . self::HTML5_PALETTE_DIR;
        return $baseImagePath;
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Get Font revision Number
     *
     *@param (String)apikey
     *@return json data
     *
     */
    public function getFontRevisionNo()
    {
        try {
            $fetch_sql = "SELECT MAX(id) AS n FROM " . TABLE_PREFIX . "fonts LIMIT 1";
            $res = $this->executeFetchAssocQuery($fetch_sql);
            $heighest_id = 0;
            if (!empty($res) && $res[0]['n']) {
                $heighest_id = $res[0]['n'];
            }
            //return $heighest_id;
            $msg['heighest_id'] = $heighest_id;
            $this->response($this->json($msg), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Update WebFontsCss
     *
     *@param (String)apikey
     *@param (File)file
     *@return json data
     *
     */
    public function updateFontCss($file)
    {
        try {
            $css_content = '';
            $fsql = "SELECT font_name,orgName FROM " . TABLE_PREFIX . "fonts WHERE is_delete='0' ORDER BY id DESC";
            $fres = $this->executeFetchAssocQuery($fsql);
            if (!empty($fres)) {
                foreach ($fres as $kf => $vf) {
                    $css_content .= '@font-face {
						font-family: "' . $vf['orgName'] . '";
						src: url("' . str_replace(array("'", " "), array("", "_"), $vf['orgName']) . '.ttf")  format("truetype");
					}';
                }
            }
            @unlink($file);
            return file_put_contents($file, $css_content);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Add TextFx Charcter
     *
     *@param (String)apikey
     *@param (int)textfx_category_id
     *@param (Array)apikey files
     *@return json data
     *
     */
    public function addTextfxCharecter()
    {
        $status = 0;
        try {
            if (!empty($this->_request) && isset($this->_request['textfx_category_id']) && $this->_request['textfx_category_id'] && $this->_request['files']) {
                $dir = $this->getTextfxSvgPath();
                if (!$dir) {
                    $this->response('', 204);
                }
                //204 - immediately termiante this request
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }

                $sql = '';
                foreach ($this->_request['files'] as $k => $v) {
                    $sql .= ",('" . $this->_request['textfx_category_id'] . "','" . $v['alphabate'] . "','" . $v['alphabate'] . ".svg')";
                    if ($v['base64Data']) {
                        $thumbBase64Data = base64_decode($v['base64Data']); //file_put_contents($dir.$fname.$k.'.svg', $thumbBase64Data[$k]);
                        file_put_contents($dir . $v['alphabate'] . '.svg', $thumbBase64Data[$k]);
                    }
                }
                $sql = "INSERT INTO " . TABLE_PREFIX . "textfx_charecters (textfx_category_id, alphabate,file_name) VALUES " . substr($sql, 1);
                $status = $this->executeGenericDMLQuery($sql);
            }
            $status['status'] = ($status) ? 'Success' : 'Failed';
            $this->response($this->json($status), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Remove TextFx Charcter by textfx_style_id
     *
     *@param (String)apikey
     *@param (int)textfx_style_id
     *@return json data
     *
     */
    public function deleteTextfxCharacter()
    {
        $status = 0;
        try {
            if (isset($this->_request['textfx_style_id']) && $this->_request['textfx_style_id'] && isset($this->_request['charecter_id']) && $this->_request['charecter_id']) {
                $dir = $this->getTextfxSvgPath();
                if (!$dir) {
                    $this->response('', 204);
                }
                //204 - immediately termiante this request
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }

                $sql = "SELECT CONCAT(textfx_style_id,'_',alphabate,'.svg') AS file_name FROM " . TABLE_PREFIX . "textfx_charecters WHERE pk_id='" . $this->_request['charecter_id'] . "'";
                $rec = $this->executeFetchAssocQuery($sql);
                if (file_exists($dir . $rec[0]['file_name'])) {@unlink($dir . $rec[0]['file_name']);}
                $sql = "DELETE FROM " . TABLE_PREFIX . "textfx_charecters WHERE pk_id='" . $this->_request['charecter_id'] . "'";
                $status = $this->executeGenericDMLQuery($sql);
            }
            $status['status'] = ($status) ? 'Success' : 'Failed';
            $this->response($this->json($status), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Update Priny size
     *
     *@param (String)apikey
     *@param (Int) print_size
     *@param (Float) height
     *@param (Float) width
     *@param (Int)id
     *@return json data
     *
     */
    public function updatePrintSizeArea()
    {
        $status = 0;
        try {
            if (!empty($this->_request)) {
                if (!empty($this->_request['print_size'])) {
                    $usql = '';
                    foreach ($this->_request['print_size'] as $k => $v) {
                        $usql .= ",('" . $v['id'] . "','" . $v['name'] . "','" . $v['height'] . "','" . $v['width'] . "')";
                    }
                    if (strlen($usql)) {
                        $usql = "INSERT INTO " . TABLE_PREFIX . "print_size (pk_id,name,height,width) VALUES " . substr($usql, 1) . " ON DUPLICATE KEY UPDATE pk_id=VALUES(pk_id),name=VALUES(name),height = VALUES(height),width = VALUES(width)";
                        $status = $this->executeGenericDMLQuery($usql);
                    }
                }
            }
            if ($status) {
                $msg['status'] = 'Success';
                $msg['data'] = $this->getSizeInfo();
            } else {
                $msg = array("status" => 'Failed');
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
     *date modified 15-4-2016(dd-mm-yy)
     *Add New Priny size
     *
     *@param (String)apikey
     *@param (String) name
     *@param (Float) height
     *@param (Float) width
     *@param (Enm)is_user_defined
     *@return json data
     *
     */
    public function addNewPrintSize()
    {
        try {
            $status = 0;
            if (!empty($this->_request) && isset($this->_request['name'])) {
                extract($this->_request);
                $sql = "INSERT INTO " . TABLE_PREFIX . "print_size(name,height,width,is_user_defined)VALUES('" . $name . "','" . $height . "','" . $width . "','" . $is_user_defined . "')";
                $status = $this->executeGenericInsertQuery($sql);
            }
            $status = ($status) ? $this->getSizeInfo() : 'Failed';
            $msg = array("status" => $status);
            $this->response($this->json($msg), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Delete color price range
     *
     *@param (String)apikey
     *@param (int)print_method_id
     *@return json data
     *
     */
    public function deletePrintSize()
    {
        try {
            $status = 0;
            if (!empty($this->_request) && isset($this->_request['print_method_id']) && $this->_request['print_method_id'] && isset($this->_request['print_size_id']) && $this->_request['print_size_id']) {
                extract($this->_request);
                $sql = "DELETE FROM " . TABLE_PREFIX . "print_size_method_rel WHERE print_method_id='" . $print_method_id . "' AND print_size_id='" . $print_size_id . "'";
                $status = $this->executeGenericDMLQuery($sql);
            }
            $settingsObj = Flight::setting();
            $settingsObj->allSettingsDetails(1);
            $this->getAllPrintSettings($print_method_id);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Delete color price range
     *
     *@param (String)apikey
     *@param (int)print_method_id
     *@return json data
     *
     */
    public function deleteColorPriceRange()
    {
        try {
            if (!empty($this->_request) && isset($this->_request['print_method_id']) && isset($this->_request['quantity_range_id'])) {
                $sql = "DELETE FROM " . TABLE_PREFIX . "print_method_quantity_range_rel WHERE print_method_id='" . $this->_request['print_method_id'] . "' AND print_quantity_range_id='" . $this->_request['quantity_range_id'] . "'";
                $status = $this->executeGenericDMLQuery($sql);
            }
            $settingsObj = Flight::setting();
            $settingsObj->allSettingsDetails(1);
            $this->getAllPrintSettings($this->_request['print_method_id']);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Set Default size area
     *
     *@param (String)apikey
     *@param (int)pk_id
     *@return json data
     *
     */
    public function setDefaultSizeArea()
    {
        $status = 0; //$this->_request['pk_id']=2;
        try {
            if (!empty($this->_request) && isset($this->_request['pk_id'])) {
                $sql = "UPDATE " . TABLE_PREFIX . "print_size SET is_default='0'";
                $status = $this->executeGenericDMLQuery($sql);
                $sql = "UPDATE " . TABLE_PREFIX . "print_size SET is_default='1' WHERE pk_id='" . $this->_request['pk_id'] . "' LIMIT 1";
                $status = $this->executeGenericDMLQuery($sql);
            }
            if ($status) {
                $this->getSizeInfo();
            } else {
                $this->response($this->json(array("status" => "invalidkey")), 200);
            }

        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Remove print area
     *
     *@param (String)apikey
     *@param (int)printId
     *@return json data
     *
     */
    public function deletePrintArea()
    {
        try {
            if (!empty($this->_request) && isset($this->_request['printId'])) {
                $sql = "DELETE FROM " . TABLE_PREFIX . "print_size WHERE pk_id=" . $this->_request['printId'] . " and is_user_defined='1'";
                $status = $this->executeGenericDMLQuery($sql);
                $settingsObj = Flight::setting();
                $settingsObj->allSettingsDetails(1);
            }
            $this->getSizeInfo();
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Set Default product boundary
     *
     *@param (String)apikey
     *@param (int)height
     *@param (int)width
     *@return json data
     *
     */
    public function setDefaultProductBoundary()
    {
        try {
            $status = 0;
            if (isset($this->_request['apikey']) && $this->isValidCall($this->_request['apikey'])) {
                if (isset($this->_request['height']) && isset($this->_request['width'])) {
                    $sql = "UPDATE " . TABLE_PREFIX . "default_product_boundary SET height='" . $this->_request['height'] . "',width='" . $this->_request['width'] . "'";
                    $status = $this->executeGenericDMLQuery($sql);
                }
            } else {
                $msg = array("status" => "invalid");
                $this->response($this->json($msg), 200);
            }
            $msg['status'] = ($status) ? 'Success' : 'Failure';
            $this->response($this->json($msg), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Get Default product boundary
     *
     *@param (String)apikey
     *@return json data
     *
     */
    public function getDefaultProductBoundary()
    {
        try {
            $sql = "SELECT height,width FROM " . TABLE_PREFIX . "default_product_boundary LIMIT 1";
            $rows = $this->executeFetchAssocQuery($sql);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
        if (empty($rows)) {
            $this->response($this->json(array('status' => 'failed')), 200);
        } else {
            $this->response($this->json(array('boudary' => $rows[0])), 200);
        }

    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Get print format by oredrid
     *
     *@param (String)apikey
     *@param (in)orderId
     *@return json data
     *
     */
    public function getPrintFormat()
    {
        if (isset($_GET['orderId']) && $_GET['orderId'] != '') {
            $orderId = $_GET['orderId'];
            $msg = array();
            try {
                $sql = 'SELECT * FROM ' . TABLE_PREFIX . 'sync_order WHERE orderId="' . $orderId . '"';
                $rows = $this->executeFetchAssocQuery($sql);
                if (count($rows)) {
                    foreach ($rows as $value) {
                        $msg['filename'][] = $value['fileName'];
                    }
                } else {
                    $msg = array("status" => "order id does not match");
                }
            } catch (Exception $e) {
                $msg = array('Caught exception:' => $e->getMessage());
            }
            $this->response($this->json($msg), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Update bleed mark
     *
     *@param (String)apikey
     *@param (int)productid
     *@param (Array)data
     *@return json data
     *
     */
    public function updateBleed()
    {
        $status = 0;
        if (!empty($this->_request)) {
            extract($this->_request);
            foreach ($data as $v) {
                try {
                    $sql = "UPDATE " . TABLE_PREFIX . "mask_data SET is_safeZone = '" . $v['is_safeZone'] . "', is_cropMark = '" . $v['is_cropMark'] . "', safeValue = '" . $v['safeValue'] . "', cropValue = '" . $v['cropValue'] . "' WHERE productid='" . $productid . "' AND side='" . $v['side'] . "'";
                    $status = $this->executeGenericDMLQuery($sql);
                } catch (Exception $e) {
                    $msg = array('Caught exception:' => $e->getMessage());
                }
            }
        }
        $msg['status'] = ($status) ? 'Success' : 'Failed';
        $this->response($this->json($msg), 200);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Add bulk of TextArt
     *
     *@param (String)apikey
     *@param (String)name
     *@param (Array)textArtfontList
     *@return json data
     *
     */
    public function addBulkTextArt()
    {
        $status = 0;
        if (!empty($this->_request)) {
            extract($this->_request);
            $sql = '';
            try {
                $new_string = str_replace("'", "''", $textArtfontList);
                foreach ($new_string as $v) {
                    $sql .= ",('" . $name . "', '" . $v . "')";
                }
                $sql = 'INSERT INTO ' . TABLE_PREFIX . 'textart (name, textArtfontList) VALUES' . substr($sql, 1);
                $status = $this->executeGenericDMLQuery($sql);
            } catch (Exception $e) {
                $msg = array('Caught exception:' => $e->getMessage());
            }
        }
        if ($status) {
            $this->loadTextArt();
        } else {
            $msg = array("status" => "failed");
        }
        $this->response($this->json($msg), 200);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Fetch TextArt
     *
     *@param (String)apikey
     *@return json data
     *
     */
    public function loadTextArt()
    {
        $result = array();
        try {
            $sql = "SELECT count(name) from " . TABLE_PREFIX . "textart";
            $res = $this->executeFetchAssocQuery($sql);
            $result['total_count'] = $res[0]['count(name)'];
            $sql1 = "SELECT * from " . TABLE_PREFIX . "textart";
            $res1 = $this->executeFetchAssocQuery($sql1);
            foreach ($res1 as $k => $v) {
                $res1[$k]['textArtfontList'] = str_replace("\"", "'", $res1[$k]['textArtfontList']);
            }
            $result['textArt'] = $res1;
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
        $this->response($this->json($result), 200);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Remove TextArt by id
     *
     * @param (String)apikey
     * @param (int)fileIds
     * @return json data
     *
     */
    public function removeTextArt()
    {
        extract($this->_request);
        $status = 0;
        if (isset($fileIds)) {
            $id_str = implode(',', $fileIds);
            try {
                $sql = "delete from " . TABLE_PREFIX . "textart WHERE id IN(" . $id_str . ")";
                $status = $this->executeGenericDMLQuery($sql);
                $msg['status'] = ($status) ? "success" : "failed";
            } catch (Exception $e) {
                $msg = array('Caught exception:' => $e->getMessage());
            }
            $this->response($this->json($msg), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Update textart by id
     *
     * @param (String)apikey
     * @param (String)name
     * @param (int)id
     * @return json data
     *
     */
    public function updateTextArtData()
    {
        $status = 0;
        if (!empty($this->_request) && isset($this->_request['id']) && isset($this->_request['name'])) {
            extract($this->_request);
            $id_str = implode(',', $id);
            try {
                $sql = "UPDATE " . TABLE_PREFIX . "textart SET name = '" . $name . "' WHERE id IN(" . $id_str . ")";
                $status = $this->executeGenericDMLQuery($sql);
                if ($status) {
                    $this->loadTextArt();
                } else {
                    $msg['status'] = 'failed';
                }

            } catch (Exception $e) {
                $msg = array('Caught exception:' => $e->getMessage());
            }
        } else {
            $msg = array("status" => "nodata");
        }
        $this->response($this->json($msg), 200);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 14-4-2016(dd-mm-yy)
     *Save custom mask
     *
     * @param (String)apikey
     * @param (String)name
     * @param (String)file_name
     * @param (int)maskheight
     * @param (int)maskwidth
     * @param (Float)price
     * @return json data
     *
     */
    public function saveCustomMaskData()
    {
        if (isset($this->_request['apikey']) && $this->isValidCall($this->_request['apikey'])) {
            extract($this->_request);
            try {
                $dir = $this->getMaskImagePath();
                if (!$dir) {
                    $this->response('', 204);
                }
                //204 - immediately termiante this request
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }

                $maskBase64Data = base64_decode($maskdata);
                $maskdata = str_replace(array(' ', '"', '\\', '/', ':', '?', '*', '<', '>', '|'), '_', $name) . '_0.svg';
                $svgImageFilePath = $dir . $maskdata;
                $status = file_put_contents($svgImageFilePath, $maskBase64Data);
                $sql = "INSERT INTO " . TABLE_PREFIX . "custom_maskdata(name,file_name,maskheight, maskwidth,price) VALUES ('$name','$maskdata', '$maskheight', '$maskwidth','$price')";
                $status = $this->executeGenericDMLQuery($sql);
                $settingsObj = Flight::setting();
                $settingsObj->allSettingsDetails(1);
                $msg['status'] = ($status) ? $this->fetchCustomMaskData() : 'Failed';
            } catch (Exception $e) {
                $msg = array('Caught exception:' => $e->getMessage());
            }
            $this->response($this->json($msg), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 14-4-2016(dd-mm-yy)
     *Fetch custom mask
     *
     * @param (String)apikey
     * @return json data
     *
     */
    public function fetchCustomMaskData()
    {
        $status = 0;
        try {
            $sql = "SELECT * from " . TABLE_PREFIX . "custom_maskdata ORDER BY id DESC";
            $rows = $this->executeGenericDQLQuery($sql);
            $resultArr = array();
            $url = $this->getCurrentUrl() . '/designer-tool' . self::HTML5_MASK_IMAGE_DIR;
            for ($i = 0; $i < sizeof($rows); $i++) {
                $resultArr[$i]['id'] = $rows[$i]['id'];
                $resultArr[$i]['name'] = $rows[$i]['name'];
                $resultArr[$i]['url'] = $url . $rows[$i]['file_name'];
                $resultArr[$i]['file_name'] = $rows[$i]['file_name'];
                $resultArr[$i]['maskheight'] = $rows[$i]['maskheight'];
                $resultArr[$i]['maskwidth'] = $rows[$i]['maskwidth'];
                $resultArr[$i]['price'] = $rows[$i]['price'];
            }
            if ($rows) {
                $this->response($this->json($resultArr), 200);
            } else {
                $msg = array("status" => "nodata");
            }

        } catch (Exception $e) {
            $msg = array('Caught exception:' => $e->getMessage());
        }
        $this->response($this->json($msg), 200);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 14-4-2016(dd-mm-yy)
     *Remove custom mask by mask id
     *
     * @param (String)apikey
     * @param (int)mask_id
     * @return json data
     *
     */
    public function deleteCustomMaskData()
    {
        $status = 0;
        if (isset($this->_request['mask_id']) && $this->_request['mask_id']) {
            extract($this->_request);
            $sql = "SELECT file_name FROM " . TABLE_PREFIX . "custom_maskdata WHERE id =" . $mask_id . "";
            $rec = $this->executeFetchAssocQuery($sql);
            if (!empty($rec)) {
                $file = $this->getMaskImagePath() . $rec[0]['file_name'];
                if (file_exists($file)) {
                    @chmod($file, 0777);
                    @unlink($file);
                    $sql = "DELETE FROM " . TABLE_PREFIX . "custom_maskdata WHERE id =" . $mask_id . "";
                    $status = $this->executeGenericDMLQuery($sql);
                    $settingsObj = Flight::setting();
                    $settingsObj->allSettingsDetails(1);
                } else {
                    $this->response('', 204); //204 - immediately termiante this request
                }
            }
        }
        $msg['status'] = ($status) ? $this->fetchCustomMaskData() : 'Failed';
        $this->response($this->json($msg), 200);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *create sepated svg file from design products
     *
     * @param (String)apikey
     * @param (int)refids
     * @param (int)side
     * @param (String)index
     * @return json data
     *
     */
    public function textFxSvgChange()
    {
        $apiKey = $this->_request['apikey'];
        $refids = $this->_request['refids'];
        $side = $this->_request['side'];
        $index = $this->_request['index'];
        $return = 1;
        try {
            $cartArrs = $this->getCartPreviewImages($apiKey, $refids, $return);
            foreach ($cartArrs[$refids] as $value) {
                $url = $cartArrs[$refids][$side]['svg'];
            }
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
        }
        $this->_request['svgFile'] = $url;
        $result = array();
        $result['url'] = $this->changeSvg();
        $this->response($this->json($result), 200);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 14-4-2016(dd-mm-yy)
     *get size variant additional price
     *
     * @param (int)productid
     * @param (String)apikey
     * @param (int)printmethodid
     * @return json data
     *
     */
    public function getSizeVariantAdditionalPriceDetails()
    {
        $confProductId = $this->_request['productid'];
        $print_method_id = $this->_request['printmethodid'];
        if (isset($confProductId) && $confProductId && isset($print_method_id) && $print_method_id) {
            try {
                $sql = "SELECT svap.xe_size_id,svap.percentage FROM " . TABLE_PREFIX . "size_variant_additional_price as svap WHERE svap.product_id=" . $confProductId . " AND svap.print_method_id=" . $print_method_id . ' ORDER BY svap.xe_size_id DESC';
                $result = $this->executeFetchAssocQuery($sql);
                $this->response($this->json($result), 200);
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        } else {
            $msg = array('status' => "invalidId");

        }
        $this->response($this->json($msg), 200);
    }

    /**
     *
     *date created 4-1-2016(dd-mm-yy)
     *date modified 14-4-2016(dd-mm-yy)
     *get design state details data
     *
     * @param (String)apikey
     * @param (int)refids
     * @return json data
     *
     */
    public function getDesignStateDetails($apiKey = 0, $refids = 0, $return = 0)
    {
        $apiKey = $this->_request['apikey'];
        $refids = $this->_request['refids'];
        $refids = $this->executeEscapeStringQuery($refids);
        if ($this->isValidCall($apiKey)) {
            try {
                $jsonData = '';
                $fileName = 'designState.json';
                $baseImagePath = $this->getPreviewImagePath();
                $savePath = $baseImagePath . $refids . '/';
                $stateDesignPath = $savePath . 'svg/';
                $stateDesignPath = $stateDesignPath . $fileName;
                if (isset($refids) && $refids != '') {
                    if (!file_exists($stateDesignPath)) {
                        $sql = "Select json_data,status from " . TABLE_PREFIX . "design_state where id=" . $refids;
                        $rows = $this->executeFetchAssocQuery($sql);
                        $jsonData = $rows[0]['json_data'];
                        $status = $rows[0]['status'];
                        $jsonData = $this->formatJSONToArray($jsonData); // converting json string to array
                    } else {
                        $jsonData = $this->formatJSONToArray(file_get_contents($stateDesignPath));
                        $status = 0;
                    }
                    if ($jsonData != '') {
                        if (isset($this->_request['side_index'])) {
                            $index = $this->_request['side_index'];
                            $arr = $jsonData['sides'][$index];
                            $jsonData['sides'] = array();
                            $jsonData['sides'][$index] = $arr;
                        } else { $index = 0;}

                        $sql = "SELECT side,scale_ratio FROM " . TABLE_PREFIX . "mask_data WHERE productid=" . $jsonData['productInfo']['productId'] . ' ORDER BY side';
                        $res = $this->executeFetchAssocQuery($sql);

                        $msg = array('jsondata' => $jsonData, 'islocked' => $status);
                        $msg = $this->svgJSON($msg);
                        if (isset($this->_request['printType'])) {
                            for ($i = $index; $i < sizeof($jsonData['sides']); $i++) {
                                $jsonData['sides'][$i]['side'] = $res[$i]['side'];
                                $jsonData['sides'][$i]['scale_ratio'] = $res[$i]['scale_ratio'];
                                if ($this->_request['printType'] == 1) {
                                    $jsonData['sides'][$i]['svg'] = rawurlencode($this->parsePrintSVG($jsonData['sides'][$i]['svg']));
                                } else {
                                    $jsonData['sides'][$i]['svg'] = rawurlencode($jsonData['sides'][$i]['svg']);
                                }

                            }
                            if (isset($this->_request['side_index'])) {
                                $jsonData['sides'] = $jsonData['sides'][$this->_request['side_index']];
                            }
                            $msg = array('jsondata' => $jsonData, 'islocked' => $status);
                            $msg = $this->svgJSON($msg);
                        } else {
                            for ($i = $index; $i < sizeof($jsonData['sides']); $i++) {
                                $jsonData['sides'][$i]['svg'] = rawurlencode($jsonData['sides'][$i]['svg']);
                            }
                            $msg = array('jsondata' => $jsonData, 'islocked' => $status);
                            $msg = $this->svgJSON($msg);
                        }
                        if ($return) {
                            return $msg;
                        } else {
                            $this->response($msg, 200);
                        }

                    } else {
                        $msg = array("status" => "nodata");
                        if ($return) {
                            return $msg;
                        } else {
                            $this->response($this->json($msg), 200);
                        }

                    }
                } else {
                    $msg = array("status" => "norefid");
                    if ($return) {
                        return $msg;
                    } else {
                        $this->response($this->json($msg), 200);
                    }

                }
            } catch (Exception $e) {
                $error = array('Caught exception:' => $e->getMessage());
                if ($return) {
                    return $error;
                } else {
                    $this->response($this->json($error), 200);
                }

            }
        } else {
            $msg = array('status' => 'invaliedkey');
            $this->response($this->json($msg), 200);
        }
    }

    /**
     *
     *date created 8-2-2016(dd-mm-yy)
     *date modified 13-4-2016(dd-mm-yy)
     *save discount data map to product
     *
     * @param (String)apikey
     * @param (Array)discount
     * @param (String)name
     * @param (int)from
     * @param (int)to
     * @return JSON data
     *
     */
    public function saveDiscountData()
    {
        $status = 0;
        if (!empty($this->_request) && isset($this->_request['apikey']) && $this->isValidCall($this->_request['apikey'])) {
            extract($this->_request);
            try {
                $sql_insert = "INSERT INTO " . TABLE_PREFIX . "discount(name) values('" . $name . "')";
                $discount_id = $this->executeGenericInsertQuery($sql_insert);
                foreach ($discount as $key => $value) {
                    $sql = "INSERT INTO " . TABLE_PREFIX . "discont_range(from_range,to_range,discount_price,discount_id) VALUES('" . $value['from'] . "','" . $value['to'] . "','" . $value['percentage'] . "','" . $discount_id . "')";
                    $status = $this->executeGenericDMLQuery($sql);
                }
                $msg['status'] = ($status) ? 'success' : 'failed';
            } catch (Exception $e) {
                $msg = array('Caught exception:' => $e->getMessage());
            }
        } else {
            $msg = array('stattus' => 'invaliedkey');
        }

        $this->response($this->json($msg), 200);
    }

    /**
     *
     *date of created 8-2-2016(dd-mm-yy)
     *date of Modified 13-4-2016(dd-mm-yy)
     *fetch discount data map to product
     *
     * @param (String)apikey
     * @param (int)start
     * @param (int)count
     * @return JSON data
     *
     */
    public function fetchDiscountData()
    {
        if (!empty($this->_request) && isset($this->_request['apikey']) && $this->isValidCall($this->_request['apikey'])) {
            $start = $this->_request['start'];
            $range = $this->_request['count'];
            $discountData = array();
            try {
                if (isset($start) && isset($range)) {
                    $sql_feth = "SELECT pk_id,name FROM " . TABLE_PREFIX . "discount limit $start,$range";
                } else {
                    $sql_feth = "SELECT pk_id,name FROM " . TABLE_PREFIX . "discount";
                }

                $values = $this->executeGenericDQLQuery($sql_feth);
                $i = 0;
                foreach ($values as $key => $value) {
                    $discountData[$i]['discount_name'] = $value['name'];
                    $discountData[$i]['id'] = $value['pk_id'];
                    if (isset($start) && isset($range)) {
                        $sql = "SELECT dr.pk_id as discountId,dr.from_range,dr.to_range,dr.discount_price FROM " . TABLE_PREFIX . "discount d , " . TABLE_PREFIX . "discont_range dr WHERE d.pk_id = dr.discount_id and d.pk_id=" . $value['pk_id'] . "  limit $start,$range";
                    } else {
                        $sql = "SELECT dr.pk_id as discountId,dr.from_range,dr.to_range,dr.discount_price FROM " . TABLE_PREFIX . "discount d , " . TABLE_PREFIX . "discont_range dr WHERE d.pk_id = dr.discount_id and d.pk_id=" . $value['pk_id'] . " ";
                    }

                    $rows = $this->executeGenericDQLQuery($sql);
                    if (!empty($rows)) {
                        $countRows = sizeof($rows);
                        $resultDiscount = array();
                        for ($j = 0; $j < $countRows; $j++) {
                            $resultDiscount[$j]['id'] = $rows[$j]['discountId'];
                            $resultDiscount[$j]['from'] = $rows[$j]['from_range'];
                            $resultDiscount[$j]['to'] = $rows[$j]['to_range'];
                            $resultDiscount[$j]['percentage'] = $rows[$j]['discount_price'];
                        }
                    }
                    $discountData[$i]['discount_range'] = $resultDiscount;
                    $i++;
                }
                if ($discountData) {
                    $this->response($this->json($discountData), 200);
                } else {
                    $msg = array("status" => "nodata");
                    $this->response($this->json($msg), 200);
                }
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        } else {
            $msg = array('status' => 'invaliedkey');
            $this->response($this->json($msg), 200);
        }
    }

    /**
     *
     *date of created 8-2-2016(dd-mm-yy)
     *date of Modified 13-4-2016(dd-mm-yy)
     *remove discount data map to product by dicount id
     *
     * @param (String)apikey
     * @param (int)id
     * @return JSON data
     *
     */
    public function removeDiscountData()
    {
        $status = 0;
        if (!empty($this->_request) && isset($this->_request['apikey']) && $this->isValidCall($this->_request['apikey'])) {
            $id = $this->_request['discount_id'];
            if (isset($id) && $id != '') {
                try {
                    $sql = "DELETE FROM  " . TABLE_PREFIX . "discount WHERE pk_id='" . $id . "'";
                    $status = $this->executeGenericDMLQuery($sql);
                    if ($status) {
                        $sql_delete = "DELETE FROM " . TABLE_PREFIX . "discont_range WHERE discount_id= '" . $id . "'";
                        $status = $this->executeGenericDMLQuery($sql_delete);
                        $sql_delete_rel = "DELETE FROM " . TABLE_PREFIX . "product_print_discount_rel WHERE discount_id= '" . $id . "'";
                        $status = $this->executeGenericDMLQuery($sql_delete_rel);
                        $msg = ($status) ? 'success' : 'failed';
                    }
                } catch (Exception $e) {
                    $msg = array('Caught exception:' => $e->getMessage());
                }
            } else {
                $msg = array("status" => "nodata");
            }
        } else {
            $msg = array('stattus' => 'invaliedkey');
        }
        $this->response($this->json($msg), 200);
    }

    /**
     *
     *date of 8-2-2016(dd-mm-yy)
     *date of Modified 13-4-2016(dd-mm-yy)
     *update discount data map to product by dicount id
     *
     * @param (String)apikey
     * @param (int)id
     * @param (float)percentage
     * @param (int)from
     * @param (int)to
     * @return JSON data
     *
     */
    public function updateDiscountData()
    {
        $status = 0;
        if (!empty($this->_request) && isset($this->_request['apikey']) && $this->isValidCall($this->_request['apikey'])) {
            extract($this->_request);
            try {
                $sql_update = "UPDATE " . TABLE_PREFIX . "discount set name ='" . $discount_name . "' WHERE pk_id='" . $id . "'";
                $status = $this->executeGenericDMLQuery($sql_update);
                if ($status) {
                    $sql_delete = "DELETE FROM " . TABLE_PREFIX . "discont_range WHERE discount_id= '" . $id . "'";
                    $status = $this->executeGenericDMLQuery($sql_delete);
                    foreach ($discount_range as $value) {
                        $sql = "INSERT INTO " . TABLE_PREFIX . "discont_range(from_range,to_range,discount_price,discount_id)
						VALUES('" . $value['from'] . "','" . $value['to'] . "','" . $value['percentage'] . "','" . $id . "')";
                        $status = $this->executeGenericDMLQuery($sql);
                    }
                    $msg['status'] = ($status) ? 'success' : 'failed';
                }
            } catch (Exception $e) {
                $msg = array('Caught exception:' => $e->getMessage());
            }
        } else {
            $msg = array('status' => 'invaliedkey');
        }
        $this->response($this->json($msg), 200);
    }

    /**
     *
     *date of created 8-2-2016(dd-mm-yy)
     *date of Modified 13-4-2016(dd-mm-yy)
     *add discount data map to product by product id
     *
     * @param (String)apikey
     * @param (int)pid
     * @param (int)print_id
     * @param (int)discount_id
     * @return JSON  data
     *
     */
    public function addDiscountToProduct()
    {
        $status = 0;
        if (!empty($this->_request) && isset($this->_request['apikey']) && $this->isValidCall($this->_request['apikey'])) {
            extract($this->_request);
            try {
                $sql_select = "SELECT * FROM  " . TABLE_PREFIX . "product_print_discount_rel WHERE product_id='" . $pid . "'";
                $rows = $this->executeGenericDQLQuery($sql_select);
                if (!empty($rows)) {
                    $sql_delete = "DELETE FROM " . TABLE_PREFIX . "product_print_discount_rel WHERE product_id='" . $pid . "'";
                    $this->executeGenericDMLQuery($sql_delete);
                }
                foreach ($discount as $key => $value) {
                    $sql = "INSERT INTO " . TABLE_PREFIX . "product_print_discount_rel(product_id,print_id,discount_id)
						VALUES('" . $pid . "','" . $value['print_id'] . "','" . $value['discount_id'] . "')";
                    $status = $this->executeGenericDMLQuery($sql);
                }
                $msg['status'] = ($status) ? 'success' : 'failed';
            } catch (Exception $e) {
                $msg = array('Caught exception:' => $e->getMessage());
            }
        } else {
            $msg = array('status' => 'invaliedkey');
        }
        $this->response($this->json($msg), 200);
    }

    /**
     *
     *date of created 14-4-2016(dd-mm-yy)
     *date of Modified 18-4-2016(dd-mm-yy)
     *updateCustomMaskData by id
     *
     * @param (String)apikey
     * @param (Array)customMaskList
     * @param (String)name
     * @param (float)maskheight
     * @param (float)maskwidth
     * @param (float)price
     * @param (int)id
     * @return to update allSettingsDetails(),and return getAllPrintSettings();
     *
     */
    public function updateCustomMaskData()
    {
        if (isset($this->_request['apikey']) && $this->isValidCall($this->_request['apikey'])) {
            extract($this->_request);
            $value = '';
            try {
                foreach ($customMaskList as $v) {
                    $sql = "update " . TABLE_PREFIX . "custom_maskdata set name='" . $v['name'] . "',maskheight='" . $v['maskheight'] . "',
					 maskwidth='" . $v['maskwidth'] . "',price='" . $v['price'] . "' where id='" . $v['id'] . "'";
                    $status = $this->executeGenericDMLQuery($sql);
                }
                $settingsObj = Flight::setting();
                $settingsObj->allSettingsDetails(1);
                $msg['status'] = ($status) ? $this->fetchCustomMaskData() : 'failed';
                $this->response($this->json($msg), 200);
            } catch (Exception $e) {
                $msg = array('Caught exception:' => $e->getMessage());
            }
        } else {
            $msg = array('status' => 'invaliedkey');
        }
        $this->response($this->json($msg), 200);
    }

    /**
     *
     *date 18th_Apr-2016
     *create all svg files for an order
     *
     * @param (String)order_id (request method)
     * @return blank
     *
     */
    public function createPrintSvg()
    {
        $msg = '';
        $order_id = (isset($this->_request['order_id'])) ? $this->_request['order_id'] : 0;
        $orderPath = $this->getOrdersPath();
        if (is_numeric($order_id) && $order_id > 0) {
            $orderFolderPath = $orderPath . "/" . $order_id;
            if (file_exists($orderFolderPath) && is_dir($orderFolderPath)) {
                // scan directory to fetch all items folder //
                $scanProductDir = scandir($orderFolderPath);
                if (is_array($scanProductDir)) {
                    // check the item folders under the product folder //
                    foreach ($scanProductDir as $itemDir) {
                        if ($itemDir != '.' && $itemDir != '..' && is_dir($orderFolderPath . "/" . $itemDir)) {
                            //to fetch only item id folders//
                            // fetch all with product svg files //
                            //echo "item DIR=".$itemDir; echo "<br/>";
                            $kounter = 1;
                            for ($i = 1; $i <= 10; $i++) {
                                if (file_exists($orderFolderPath . "/" . $itemDir . "/preview_0" . $i . ".svg")) {
// with product svg file exists or not//
                                    if (!file_exists($orderFolderPath . "/" . $itemDir . "/" . $i . ".svg")) {
                                        /* check if without product svg file exists or not.
                                        if not exist, then create the file
                                         */
                                        $reqSvgFile = XEPATH . "designer-tool" . ORDER_PATH_DIR . "/" . $order_id . "/" . $itemDir . "/preview_0" . $i . ".svg";
                                        $item_id = $itemDir;
                                        $this->createWithoutProductSvg($reqSvgFile, $order_id, $item_id);
                                        $msg = 'Success';
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                $msg = 'No Orders Found to download';
            }
        } else {
            $msg = 'No Orders Found to download';
        }
        $response = array("Response" => $msg);
        $this->response($this->json($response), 200);
    }

    /**
     *
     * date 18th_Apr-2016
     * generate list of all svg,pmg and pdf files for a perticular order
     *
     * @param (String)order_id (request method)
     * @return json
     *
     */
    public function getPrintFormatAll()
    {
        $msg = '';
        $file_array = array();
        $svg_array = array();
        $pdf_array = array();
        $png_array = array();
        $item_array = array();
        $order_id = (isset($this->_request['order_id'])) ? $this->_request['order_id'] : 0;
        $orderPath = $this->getOrdersPath();
        if (is_numeric($order_id) && $order_id > 0) {
            $orderFolderPath = $orderPath . "/" . $order_id;
            if (file_exists($orderFolderPath) && is_dir($orderFolderPath)) {
                // scan directory to fetch all items folder //
                $scanProductDir = scandir($orderFolderPath);
                if (is_array($scanProductDir)) {
                    // check the item folders under the product folder //
                    foreach ($scanProductDir as $itemDir) {
                        if ($itemDir != '.' && $itemDir != '..' && is_dir($orderFolderPath . "/" . $itemDir)) {
                            //to fetch only item id folders//
                            // fetch all with product svg files //
                            //echo "item DIR=".$itemDir; echo "<br/>";
                            $kounter = 1;
                            for ($i = 1; $i <= 10; $i++) {

                                // for SVG Files //
                                if (file_exists($orderFolderPath . "/" . $itemDir . "/" . $i . ".svg")) {
                                    //svg file exists or not//
                                    if (!in_array($itemDir . "/" . $i . ".svg", $file_array)) {
                                        //array_push($file_array,$itemDir."/".$i.".svg");
                                        array_push($svg_array, $i . ".svg");
                                    }
                                }
                                // for PNG Files //
                                if (file_exists($orderFolderPath . "/" . $itemDir . "/" . $i . ".png")) {
                                    //png file exists or not//
                                    if (!in_array($itemDir . "/" . $i . ".png", $file_array)) {
                                        //array_push($file_array,$itemDir."/".$i.".png");
                                        array_push($png_array, $i . ".png");
                                    }
                                }
                                // for PDF Files //
                                if (file_exists($orderFolderPath . "/" . $itemDir . "/" . $i . ".pdf")) {
                                    //pdf file exists or not//
                                    if (!in_array($itemDir . "/" . $i . ".pdf", $file_array)) {
                                        //array_push($file_array,$itemDir."/".$i.".pdf");
                                        array_push($pdf_array, $i . ".pdf");
                                    }
                                }
                            }
                            $file_array[$itemDir] = array('svg' => $svg_array, 'png' => $png_array, 'pdf' => $pdf_array);
                            $svg_array = array();
                            $pdf_array = array();
                            $png_array = array();
                        }
                    }
                    if (count($file_array) > 0) {
                        $msg = $file_array;
                    }

                }
            }
        } else {
            $msg = 'No Orders Found to download';
        }
        $this->response($this->json($msg), 200);
    }

    /**
     *
     *date of created 5-5-2016(dd-mm-yy)
     *date of Modified (dd-mm-yy)
     *get mask price details
     *
     * @return Array  data
     *
     */
    public function getMaskPrice()
    {
        try {
            $result = array();
            $sql = "SELECT id,name,price FROM " . TABLE_PREFIX . "custom_maskdata";
            $rows = $this->executeFetchAssocQuery($sql);
            foreach ($rows as $k => $v) {
                $result[$k]['id'] = $v['id'];
                $result[$k]['name'] = $v['name'];
                $result[$k]['price'] = $v['price'];
            }
            return $result;
        } catch (Exception $e) {
            $msg = array('Caught exception:' => $e->getMessage());
            return $msg;
        }
    }
    /**
     *
     *date of created 08-12-2016(dd-mm-yy)
     *date of Modified 08-12-2016(dd-mm-yy)
     *get physical path of preview images on template preview.
     *
     * @return String
     *
     */
    public function getTemplatePreviewImagePath()
    {
        return $this->getBasePath() . TEMPLATE_PREVIEW_IMAGE_DIR;
    }

    /**
     *
     *date of created 08-12-2016(dd-mm-yy)
     *date of Modified 08-12-2016(dd-mm-yy)
     *get the URL of preview images on template preview.
     *
     * @return String
     *
     */
    public function getTemplatePreviewImageUrl()
    {
        $baseImagePath = $this->getCurrentUrl() . self::ASSETS_CONTAINER_DIR . TEMPLATE_PREVIEW_IMAGE_DIR;
        return $baseImagePath;
    }
    /**
     *
     *date of created 08-12-2016(dd-mm-yy)
     *date of Modified 08-12-2016(dd-mm-yy)
     *get physical path of preview images on user slot preview.
     *
     * @return String
     *
     */
    public function getUserSlotpreviewImagePath()
    {
        return $this->getBasePath() . USERSLOT_PREVIEW_IMAGE_DIR;
    }

    /**
     *
     *date of created 08-12-2016(dd-mm-yy)
     *date of Modified 08-12-2016(dd-mm-yy)
     *get the URL of preview images on decorated product.
     *
     * @return String
     *
     */
    public function getUserSlotpreviewImageUrl()
    {
        $baseImagePath = $this->getCurrentUrl() . self::ASSETS_CONTAINER_DIR . USERSLOT_PREVIEW_IMAGE_DIR;
        return $baseImagePath;
    }
    /**
     *
     *date of created 08-12-2016(dd-mm-yy)
     *date of Modified 08-12-2016(dd-mm-yy)
     *get physical path of preview images on decorated product.
     *
     * @return String
     *
     */
    public function getPreDecoProductImagePath()
    {
        return $this->getBasePath() . PRE_DECO_PRODUCT_IMAGE_DIR;
    }

    /**
     *
     *date of created 08-12-2016(dd-mm-yy)
     *date of Modified 08-12-2016(dd-mm-yy)
     *get the URL of preview images on decorated product.
     *
     * @return String
     *
     */
    public function getPreDecoProductImageUrl()
    {
        $baseImagePath = $this->getCurrentUrl() . self::ASSETS_CONTAINER_DIR . PRE_DECO_PRODUCT_IMAGE_DIR;
        return $baseImagePath;
    }
     /**
     *
     *date of created 08-12-2016(dd-mm-yy)
     *date of Modified 08-12-2016(dd-mm-yy)
     *get physical path of preview images on sharing.
     *
     * @return String
     *
     */
    public function getSharedImagePath()
    {
        return $this->getBasePath() . SHARED_IMAGE_DIR;
    }

    /**
     *
     *date of created 08-12-2016(dd-mm-yy)
     *date of Modified 08-12-2016(dd-mm-yy)
     *get the URL of preview images on sharing.
     *
     * @return String
     *
     */
    public function getSharedImageUrl()
    {
        $baseImagePath = $this->getCurrentUrl() . self::ASSETS_CONTAINER_DIR . SHARED_IMAGE_DIR;
        return $baseImagePath;
    }
    /**
     *
     *date of created 08-12-2016(dd-mm-yy)
     *date of Modified 08-12-2016(dd-mm-yy)
     *get physical path of preview images on add to cart.
     *
     * @return String
     *
     */
    public function getCartImagePath()
    {
        return $this->getBasePath() . CART_IMAGE_DIR;
    }

    /**
     *
     *date of created 08-12-2016(dd-mm-yy)
     *date of Modified 08-12-2016(dd-mm-yy)
     *get the URL of preview images on add to cart.
     *
     * @return String
     *
     */
    public function getCartImageUrl()
    {
        $baseImagePath = $this->getCurrentUrl() . self::ASSETS_CONTAINER_DIR . CART_IMAGE_DIR;
        return $baseImagePath;
    }

    /**
     *
     *date of created 5-5-2016(dd-mm-yy)
     *date of Modified 08-12-2016(dd-mm-yy)
     *get mask price details
     *
     * @return Array  data
     *
     */

    public function capturedCustomizedImages()
    {
        $result = array();
        $ids = array();
        if (!empty($this->_request) && !empty($this->_request['capturedData'])) {
            $source = $this->_request['type'];
            $capturedData = $this->_request['capturedData'];
            switch ($source) {
                case 'cart':
                    $dir = $this->getCartImagePath();
                    $fileUrl = $this->getCartImageUrl();
                break;
                case 'pre-deco':
                    $dir = $this->getPreDecoProductImagePath();
                    $fileUrl = $this->getPreDecoProductImageUrl();
                break;
                case 'socialShare':
                    $dir = $this->getSharedImagePath();
                    $fileUrl = $this->getSharedImageUrl();
                break;
                case 'userSlot':
                    $dir = $this->getUserSlotpreviewImagePath();
                    $fileUrl = $this->getUserSlotpreviewImageUrl();
                break;
                case 'template':
                    $dir = $this->getTemplatePreviewImagePath();
                    $fileUrl = $this->getTemplatePreviewImageUrl();
                break;
                default:
                    $dir = $this->getCapturedImagePath();
                    $fileUrl = $this->getCapturedImageUrl();
                break;
            }
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }
            $type = 'png';
            foreach ($capturedData as $k => $v) {
                if (strpos($v, ';base64') != false) {
                    if (!file_exists($dir)) {
                        mkdir($dir, 0777, true);
                    }

                    $fname = uniqid('ci_', true) . '.' . $type;
                    $file = $dir . $fname;
                    $base64 = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $v));
                    
                    $sql = "INSERT INTO " . TABLE_PREFIX . "capture_image (image, type, date_created) VALUES ('".$fname."','".$source."', now())";
                    $id = $this->executeGenericInsertQuery($sql);
                    if($id > 0){
                        $status = file_put_contents($file, $base64);
                        if ($status) {
                            $result[] = $fileUrl . $fname;
                            $ids[] = $id;
                        }
                    }else{
                        $msg['status'] = 'failed';
                        $msg['Message'] = 'SQL error';
                        $this->response($this->json($msg), 200);
                    }                    

                } else {
                    $result[] = $v;
                }
            }
        }
        $msg['status'] = ($status) ? 'success' : 'failed';
        $msg['result'] = $result;
        $msg['imageid'] = $ids;
        $this->response($this->json($msg), 200);
    }

    /**
     *
     *date of created 5-5-2016(dd-mm-yy)
     *date of Modified (dd-mm-yy)
     *get mask price details
     *
     * @return Array  data
     *
     */
    public function getCapturedImagePath()
    {
        return $this->getBasePath() . self::CAPTURED_IMAGE_DIR;
    }

    /**
     *
     *date of created 5-5-2016(dd-mm-yy)
     *date of Modified (dd-mm-yy)
     *get mask price details
     *
     * @return Array  data
     *
     */
    public function getCapturedImageUrl()
    {
        $baseImagePath = $this->getCurrentUrl() . self::ASSETS_CONTAINER_DIR . self::CAPTURED_IMAGE_DIR;
        return $baseImagePath;
    }

    /**
     *
     *date created 7-07-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *get user wordclud svg path
     *
     *
     */
    public function setUserWordCloudSvgPath()
    {
        return $this->getBasePath() . self::USER_WORD_CLOUD_SVG;
    }

    /**
     *
     *date created 7-07-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *get user wordclud svg url
     *
     *
     */
    public function getUserWordCloudSvgUrl()
    {
        $baseImagePath = $this->getCurrentUrl() . self::ASSETS_CONTAINER_DIR . self::USER_WORD_CLOUD_SVG;
        return $baseImagePath;
    }

    /**
     *
     *date created 20-06-2016(dd-mm-yy)
     *date modified 20-06-2016 (dd-mm-yy)
     *Get custom Preview Images
     *
     *@param (Int)refids
     *@return json data
     *
     */
    public function getCustomPreviewImages($refids = 0, $return = 0)
    {
        $refids = $this->_request['refids'];
        try {
            $refids = $this->executeEscapeStringQuery($refids);
            if ($refids) {
                $regidArr = explode(',', $refids);
                $finalArray = array();
                $jsonData = '';
                $fileName = 'designState.json';
                $baseImagePath = $this->getPreviewImagePath();
                foreach ($regidArr as $keys => $values) {
                    if ($values == 0 || $finalArray[$values]) {
                        continue;
                    }
                    // skip if refid=0 or repeatation of refid;
                    $savePath = $baseImagePath . $values . '/';
                    $stateDesignPath = $savePath . 'svg/';
                    $stateDesignPath = $stateDesignPath . $fileName;
                    $jsonData = $this->formatJSONToArray(file_get_contents($stateDesignPath));
                    $nameAndNumber = 0;
                    $displayEdit = 0;
                    if ($jsonData['nameNumberData']['list'] != null) {
                        $nameAndNumber = 1;
                    }
                    $sql = "SELECT parent_id FROM " . TABLE_PREFIX . "template_state_rel WHERE ref_id = " . $values . " OR temp_id = " . $jsonData[productInfo][productId];
                    $parentIds = $this->executeFetchAssocQuery($sql);

                    if ($jsonData != '') {
                        $designStatus = 1;
                        $printid = $jsonData['printTypeId'];
                        for ($i = 0; $i < sizeof($jsonData['sides']); $i++) {
                            $productUrl = $jsonData['sides'][$i]['url'];
                            $customImageUrl = $jsonData['sides'][$i]['customizeImage'];
                            $svgData = $jsonData['sides'][$i]['svg'];
                            $displayEdit = ($nameAndNumber == 1 || !empty($parentIds)) ? 0 : 1;
                            if ($svgData != '') {$designStatus = 1;} else { $designStatus = 0;}
                            $images[$values][] = array('design_status' => $designStatus, 'customImageUrl' => $customImageUrl, 'productImageUrl' => $productUrl, 'printid' => $printid, 'nameAndNumber' => $nameAndNumber, 'display_edit' => $displayEdit);
                        }
                    } else {
                        $msg = array("status" => "nodata");
                        $this->response($this->json($msg), 200);
                    }
                    if (array_key_exists($values, $images)) {
                        $finalArray[$values] = $images[$values];
                    } else {
                        $finalArray[$values] = array();
                    }

                }
                if ($return) {
                    return $finalArray;
                } else {
                    $this->response($this->json($finalArray), 200);
                }
            } else {
                $this->log('getCartPreviewImages :: invalid refids : ' . $refids);
            }
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /*
     *
     *date created 18-7-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *addNewCustomBoundaryUnit
     *purpose:adding custom boundary units
     *
     */
    public function addNewCustomBoundaryUnit()
    {
        $status = 0;
        if (!empty($this->_request) && isset($this->_request['name'])) {
            extract($this->_request);
            $sql = "INSERT INTO " . TABLE_PREFIX . "custom_boundary_unit(name,price)VALUES('" . $this->_request['name'] . "','" . $this->_request['price'] . "')";
            $status = $this->executeGenericInsertQuery($sql);
        }
        $status = ($status) ? $this->getCustomBoundaryUnit() : 'Failed';
        $msg = array("status" => $status);
        $this->response($this->json($msg), 200);
    }

    /*
     *
     *date created 18-7-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *deleteCustomBoundaryUnit
     *@param (int)id
     *purpose:deleting custom boundary units
     *
     */
    public function deleteCustomBoundaryUnit()
    {
        if (!empty($this->_request) && isset($this->_request['id'])) {
            $sql = "DELETE FROM " . TABLE_PREFIX . "custom_boundary_unit WHERE id='" . $this->_request['id'] . "'";
            $status = $this->executeGenericDMLQuery($sql);
        }
        $this->getCustomBoundaryUnit();
    }

    /*
     *
     *date created 18-7-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *updateCustomBoundaryUnit
     *@return json data
     *purpose:updating custom boundary units
     *
     */
    public function updateCustomBoundaryUnit()
    {
        if (!empty($this->_request)) {
            if (!empty($this->_request['unitList'])) {
                $id_str = implode(',', $this->_request['id']);
                foreach ($this->_request['unitList'] as $k => $v) {
                    $sql = "UPDATE " . TABLE_PREFIX . "custom_boundary_unit SET name = '" . $v['name'] . "',price = '" . $v['price'] . "' WHERE id =" . $v['id'] . "";
                    $status = $this->executeGenericDMLQuery($sql);
                }
            }
        }
        if ($status) {
            $msg['status'] = 'Success';
            $msg['data'] = $this->getCustomBoundaryUnit();
        } else {
            $msg = array("status" => 'Failed');
        }
        $this->response($this->json($msg), 200);
    }

    /*
     *
     *date created 18-7-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *getCustomBoundaryUnit
     *@return json data
     *purpose:fetching custom boundary units
     *
     */
    public function getCustomBoundaryUnit()
    {
        try {
            $sql = "SELECT * FROM " . TABLE_PREFIX . "custom_boundary_unit ORDER BY id DESC";
            $rows = $this->executeFetchAssocQuery($sql);
            $unitList = array();
            foreach ($rows as $k => $row) {
                $unitList[$k]['id'] = $row['id'];
                $unitList[$k]['name'] = $row['name'];
                $unitList[$k]['price'] = $row['price'];
            }
            $this->response($this->json($unitList), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /*
     *
     *date created 27-9-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *addMaxNumberForSelectColor
     *purpose:adding maximum number of selected color in the database
     *
     */
    public function addMaxNumberForSelectColor()
    {
        $max_number_of_color = $this->_request['max_number_of_color'];
        if (isset($max_number_of_color)) {
            $sql_insert = "INSERT INTO " . TABLE_PREFIX . "image_edit_select_color(max_number_of_color) values('" . $max_number_of_color . "')";
            $status = $this->executeGenericDMLQuery($sql_insert);
            $msg['status'] = ($status) ? 'success' : 'failed';
            $this->response($this->json($msg), 200);
        }
    }

    /////////////////////*****************COMMON METHODS*********************/////////////////////////

    /**
     *
     * @param type $haystack Text to be searched
     * @param type $needle Search string
     * @return type Boolean
     */
    public function endsWith($haystack, $needle)
    {
        // search forward starting from end minus needle length characters
        return $needle === "" || strpos($haystack, $needle, strlen($haystack) - strlen($needle)) !== false;
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Xor decode
     *
     *@param (String)apikey
     *@param (String)key
     *@param (String)str
     *@return string data
     *
     */
    protected function xorDec($key, $str)
    {
        try {
            $decoded = "";
            for ($i = 0; $i < strlen($str); $i++) {
                $b = ord($str[$i]);
                $a = $b ^ $key; // <-- must be same number used to encode the character
                $decoded .= chr($a);
            }
            return $decoded;
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            return $result;
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *mb converter for charcter
     *
     *@param (String)apikey
     *@param (String)char
     *@return string data
     *
     */
    protected function mb_chr($char)
    {
        return mb_convert_encoding('&#' . intval($char) . ';', 'UTF-8', 'HTML-ENTITIES');
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *mb converter for mb_ord
     *
     *@param (String)apikey
     *@param (String)char
     *@return string data
     *
     */
    protected function mb_ord($char)
    {
        try {
            $result = unpack('N', mb_convert_encoding($char, 'UCS-4BE', 'UTF-8'));
            if (is_array($result) === true) {
                return $result[1];
            }
            return ord($char);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Encrypted data
     *
     *@param (String)apikey
     *@param (Int)key
     *@param (String)str
     *@return string data
     *
     */
    public function rcEncDec($key, $str)
    {
        try {
            if (extension_loaded('mbstring') === true) {
                mb_language('Neutral');
                mb_internal_encoding('UTF-8');
                mb_detect_order(array('UTF-8', 'ISO-8859-15', 'ISO-8859-1', 'ASCII'));
            }
            $s = array();
            for ($i = 0; $i < 256; $i++) {
                $s[$i] = $i;
            }
            $j = 0;
            for ($i = 0; $i < 256; $i++) {
                $j = ($j + $s[$i] + $this->mb_ord(mb_substr($key, $i % mb_strlen($key), 1))) % 256;
                $x = $s[$i];
                $s[$i] = $s[$j];
                $s[$j] = $x;
            }
            $i = 0;
            $j = 0;
            $res = '';
            for ($y = 0; $y < mb_strlen($str); $y++) {
                $i = ($i + 1) % 256;
                $j = ($j + $s[$i]) % 256;
                $x = $s[$i];
                $s[$i] = $s[$j];
                $s[$j] = $x;
                $res .= $this->mb_chr($this->mb_ord(mb_substr($str, $y, 1)) ^ $s[($s[$i] + $s[$j]) % 256]);
            }
            return $res;
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     * @param type $src Source Image
     * @param type $dst Destination Image
     * @param type $width   Width of the Destination Image
     * @param type $height  Height of the Destination Image
     * @param type $crop    Crop Value in number
     * @return string|boolean
     */
    protected function resizeImage($src, $dst, $width, $height, $crop = 0)
    {
        try {
            $original_mem = ini_get('memory_limit');
            ini_set('memory_limit', '256M');
            if (!list($w, $h) = getimagesize($src)) {
                return "Unsupported picture type!";
            }

            $type = strtolower(substr(strrchr($src, "."), 1));
            if ($type == 'jpeg') {
                $type = 'jpg';
            }

            $image_info = getimagesize($src);
            switch ($image_info['mime']) {
                case 'image/bmp':$img = @imagecreatefromwbmp($src);
                    break;
                case 'image/gif':$img = @imagecreatefromgif($src);
                    break;
                case 'image/jpeg':$img = @imagecreatefromjpeg($src);
                    break;
                case 'image/png':$img = @imagecreatefrompng($src);
                    break;
                default:return "Unsupported picture type!";
            }
            // resize
            if ($crop) {
                if ($w < $width or $h < $height) {
                    return "Picture is too small!";
                }

                $ratio = max($width / $w, $height / $h);
                $h = $height / $ratio;
                $x = ($w - $width / $ratio) / 2;
                $w = $width / $ratio;
            } else {
                if ($w < $width and $h < $height) {
                    return "Picture is too small!";
                }

                $ratio = min($width / $w, $height / $h);
                $width = $w * $ratio;
                $height = $h * $ratio;
                $x = 0;
            }
            $new = imagecreatetruecolor($width, $height);
            // preserve transparency
            if ($image_info['mime'] == "image/gif" or $image_info['mime'] == "image/png") {
                @imagecolortransparent($new, @imagecolorallocatealpha($new, 255, 255, 255, 127));
                @imagealphablending($new, false);
                @imagesavealpha($new, true);
            }
            @imagecopyresampled($new, $img, 0, 0, $x, 0, $width, $height, $w, $h);
            switch ($image_info['mime']) {
                case 'image/bmp':imagewbmp($new, $dst);
                    break;
                case 'image/gif':imagegif($new, $dst);
                    break;
                case 'image/jpg':imagejpeg($new, $dst);
                    break;
                case 'image/png':imagepng($new, $dst);
                    break;
            }
            @imagedestroy($img);
            // at the end of the script set it to it's original value
            // (if you forget this PHP will do it for you when performing garbage collection)
            ini_set('memory_limit', $original_mem);
            return true;
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    ####################################################
    ############## syncOrdersZip ###################
    ####################################################
    protected function getMaxId($table, $col)
    {
        $maxId = 0;
        $sql = "Select max($col)  from  " . $table;
        $result = mysqli_query($this->db, $sql);
        if ($result) {
            if ($rows = mysqli_fetch_array($result)) {
                $maxId = $rows[0];
            }

        }
        return $maxId;
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get result data
     *
     *@param (String)apikey
     *@param (String)sql
     *@return array
     *
     */
    protected function getResult($sql)
    {
        try {
            $rows = $this->executeGenericDQLQuery($sql);
            if ($rows) {
                return $rows;
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
     *get template image path
     *
     *@param (String)apikey
     *@return base image path
     *
     */
    protected function svgJSON($data)
    {
        if (is_array($data)) {
            $formatted = json_encode($data);
            return $this->svgFormatJson($formatted);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get svg Format json
     *
     *@param (String)apikey
     *@return json format
     *
     */
    protected function svgFormatJson($jsonData)
    {
        $formatted = $jsonData;
        $formatted = str_replace('"{', '{', $formatted);
        $formatted = str_replace('}"', '}', $formatted);
        return $formatted;
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *extractNumber
     *
     *@param (String)apikey
     *@param (String)string
     *@return json data
     *
     */
    public function extractNumber($string)
    {
        try {
            preg_match_all('!\d+!', $string, $match);
            $val = 0;
            $arr = $match[0];
            if (is_array($arr)) {
                $arr = array_filter($arr);
                if (!empty($arr)) {
                    $val = $arr[0];
                } else {
                    $val = 0;
                }

            }
            return $val;
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *getSqlSeparator
     *
     *@param (String)apikey
     *@return json data
     *
     */
    public function getSqlSeparator($flag)
    {
        if ($flag) {
            return ",";
        } else {
            return "";
        }

    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *compress image
     *
     *@param (String)apikey
     *@param (String)source
     *@param (String)destination
     *@param (String)quality
     *@return json data
     *
     */
    public function compress($source, $destination, $quality)
    {
        try {
            $info = getimagesize($source);
            if ($info['mime'] == 'image/jpeg') {
                $image = imagecreatefromjpeg($source);
            } elseif ($info['mime'] == 'image/gif') {
                $image = imagecreatefromgif($source);
            } elseif ($info['mime'] == 'image/png') {
                $image = imagecreatefrompng($source);
            }

            imagejpeg($image, $destination, $quality);
            return $destination;
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Get resize All image
     *
     *@param (String)apikey
     *@param (String)src
     *@param (String)dst
     *@param (in)width
     *@param (in)height
     *@return json data
     *
     */
    public function resize($src, $dst, $width, $height)
    {
        try {
            $original_mem = ini_get('memory_limit');
            ini_set('memory_limit', '256M');
            if (!is_file($src)) {
                return true;
            }

            if (!is_file($dst) || (filectime($src) > filectime($dst))) {
                $path = '';

                list($width_orig, $height_orig) = getimagesize($src);

                if ($width_orig != $width || $height_orig != $height) {
                    $image = new ImageResize($src);
                    $image->resize($width, $height);
                    $image->save($dst);
                } else {
                    copy($src, $dst);
                }
            }

            ini_set('memory_limit', $original_mem);

            return true;
        } catch (Exception $e) {
            $msg = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($msg), 200);
        }
    }

    /**
     *
     *date created 3-6-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *get template product path
     *
     *
     */
    public function getProductTemplatePath()
    {
        $baseImagePath = $this->getCurrentUrl() . self::ASSETS_CONTAINER_DIR . self::HTML5_PRODUCTTEMPLATE_DIR;
        return $baseImagePath;
    }

    protected function getBackgroundDesignImagePath()
    {
        $baseImagePath = $this->getBasePath() . self::HTML5_DESIGN_BACKGROUND_DIR;
        return $baseImagePath;
    }

    protected function getBackgroundDesignImageURL()
    {
        $baseImagePath = $this->getCurrentUrl() . self::ASSETS_CONTAINER_DIR . self::HTML5_DESIGN_BACKGROUND_DIR;
        return $baseImagePath;
    }

    protected function customRequest($array = array())
    {
        foreach ($array as $key => $val) {
            $_GET[$key] = $val;
        }
        $this->getInputs();
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *getShape svg file path
     *
     *@return base image url
     *
     */
    protected function getShapeSvgPath()
    {
        $baseImagePath = $this->getBasePath() . self::HTML5_SHAPE_DIR;
        return $baseImagePath;
    }

    /**
     *
     *date created 3-6-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *Set template produst path
     *
     *@return string
     *
     */
    public function setProductTemplatePath()
    {
        return $this->getBasePath() . self::HTML5_PRODUCTTEMPLATE_DIR;
    }

    /**
     *
     *date of created 2-2-2016(dd-mm-yy)
     *date of Modified 13-4-2016(dd-mm-yy)
     *Get color Swateches Path
     *
     *@param (String)apikey
     *@param (File)Filedata
     *@return string
     *
     */
    public function getSwatchesPath()
    {
        $baseImagePath = $this->getBasePath() . self::SWATCH_PATH_DIR;
        return $baseImagePath;
    }
    /**
     *
     *date of created 2-2-2016(dd-mm-yy)
     *date of Modified 13-4-2016(dd-mm-yy)
     *Get color Swateches Url
     *
     *@param (String)apikey
     *@param (File)Filedata
     *@return string
     *
     */
    public function getSwatchURL()
    {
        $baseImagePath = $this->getCurrentUrl() . self::ASSETS_CONTAINER_DIR . self::SWATCH_PATH_DIR . '/';
        return $baseImagePath;
    }

    /**
     *
     *date created 3-6-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *Add template to product
     *
     *@param (String)apikey
     *@param (int)pid
     *@param (int)productTempId
     *@return json data
     *
     */
    public function addTemplateToProduct()
    {
        $status = 0;
        extract($this->_request);
        if (isset($this->_request['isTemplate'])) {
            $isTemplate = $this->_request['isTemplate'];
        }
        if ($productTempId == 0 || $pid != '') {
            $sql = "DELETE FROM " . TABLE_PREFIX . "product_temp_rel WHERE product_id = '" . $pid . "'";
            $status = $this->executeGenericDMLQuery($sql);
        }
        if (!empty($this->_request) && $this->_request['pid'] && $this->_request['productTempId']) {
            $insertSql = "INSERT INTO " . TABLE_PREFIX . "product_temp_rel(product_id,temp_id) VALUES('" . $pid . "','" . $productTempId . "')";
            $status = $this->executeGenericDMLQuery($insertSql);
        }
        $this->customRequest(array('id' => $this->_request['pid'], 'apikey' => $this->_request['apiKey']));
        if ($isTemplate == 1) {
            //$msg = array("status" => "success");
            //return $this->json($msg);
        } else {
            $pObj = Flight::products();
            if ($status) {
                $pObj->getSimpleProduct();
            } else {
                $msg['status'] = 'failed';
            }

            $this->response($this->json($msg), 200);
        }
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Remove App image
     *
     *@param (String)apikey
     *@param (String)category
     *@return Integer value
     *
     */
    public function removeAppImage()
    {
        $apiKey = $this->_request['apikey'];
        $category = $this->_request['category'];
        $name = $this->_request['name']; //file name or directory name in case of category=design

        $name = explode(",", $name);
        if ($this->isValidCall($apiKey)) {
            try {
                $dir = '';
                if ($category == 'design') {
                    $dir = $this->getDesignImagePath();
                }

                if ($category == 'shape') {
                    $dir = $this->getShapeImagePath();
                }

                if ($category == 'background_design') {
                    $dir = $this->getBackgroundDesignImagePath();
                }

                if ($category == 'background_pattern') {
                    $dir = $this->getBackgroundPatternImagePath();
                } else if ($category == 'font') {
                    $dir = $this->getWebfontsPath();
                }

                if (!$dir) {
                    $this->response('', 204);
                }
                //204 - immediately termiante this request
                $filePath = $dir . $name[0]; // as name receives in array
                $msg = '';
                if ($category == 'design') {
                    $designIdArr = $this->_request['designIdArr'];
                    $designIdArr = explode(",", $designIdArr);
                    for ($i = 0; $i < sizeof($name); $i++) {
                        $this->_request['design_id'] = $designIdArr[$i];
                        $this->deleteDesignById();
                        $filePath = $dir . $name[$i];
                        if (file_exists($filePath)) {
                            unlink($filePath);
                            if ($category == 'design' && file_exists($dir . 'thumb_' . $name[0])) {
                                unlink($dir . 'thumb_' . $name[0]);
                            }

                        }
                        if (file_exists($filePath)) //file couldn't be deleted
                        {
                            $msg = array("status" => "failed");
                        } else {
                            $msg = array("status" => "success", "success_count" => sizeof($name), "filePath" => $filePath);
                        }

                    }
                } else if ($category == 'background_design') {
                    $designIdArr = $this->_request['design_background_id'];
                    $designIdArr = explode(",", $designIdArr);
                    $name = $this->_request['name'];
                    $name = explode(",", $name);
                    for ($i = 0; $i < sizeof($name); $i++) {
                        $bgObj = Flight::background();
                        $bgObj->deleteBackgroundDesignById($designIdArr[$i]);
                        $filePath = $dir . $name[$i];
                        if (file_exists($filePath)) {
                            unlink($filePath);
                            if ($category == 'background_design' && file_exists($dir . 'thumb_' . $name[0])) {
                                unlink($dir . 'thumb_' . $name[0]);
                            }

                        }
                        if (file_exists($filePath)) //file couldn't be deleted
                        {
                            $msg = array("status" => "failed");
                        } else {
                            $msg = array("status" => "success", "success_count" => sizeof($name), "filePath" => $filePath);
                        }
                    }
                } else if ($category == 'background_pattern') {
                    $designIdArr = $this->_request['designIdArr'];
                    $designIdArr = explode(",", $designIdArr);
                    for ($i = 0; $i < sizeof($name); $i++) {
                        $this->_request['pBackgroundPatternId'] = $designIdArr[$i];
                        $backgroundObj = Flight::backgroundPattern();
                        $backgroundObj->deleteBackgroundPatternById();
                        $filePath = $dir . $name[$i];
                        if (file_exists($filePath)) {
                            unlink($filePath);
                            if ($category == 'background_pattern' && file_exists($dir . 'thumb_' . $name[0])) {
                                unlink($dir . 'thumb_' . $name[0]);
                            }

                        }
                        if (file_exists($filePath)) //file couldn't be deleted
                        {
                            $msg = array("status" => "failed");
                        } else {
                            $msg = array("status" => "success", "success_count" => sizeof($name), "filePath" => $filePath);
                        }

                    }
                } else if ($category == 'shape') {
                    $shapeIdArr = $this->_request['shapeIdArr'];
                    $shapeIdArr = explode(",", $shapeIdArr);
                    for ($i = 0; $i < sizeof($name); $i++) {
                        $shapeObj = Flight::shape();
                        $shapeObj->customRequest(array('shape_id' => $shapeIdArr[$i]));
                        $shapeObj->deleteShapeById();
                        $filePath = $dir . $name[$i];
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }

                        if (file_exists($filePath)) //file couldn't be deleted
                        {
                            $msg = array("status" => "failed");
                        } else {
                            $msg = array("status" => "success", "success_count" => sizeof($name), "filePath" => $filePath);
                        }

                    }
                } else if ($category == 'font') {
                    $webFontIdArr = $this->_request['id'];
                    $webFontIdArr = explode(",", $webFontIdArr);
                    for ($i = 0; $i < sizeof($name); $i++) {
                        $fontObj = Flight::font();
                        $fontObj->customRequest(array('WebFont_id' => $webFontIdArr[$i]));
                        $fontObj->deleteWebFontById();

                        $filePath = $dir . $name[$i] . '.ttf';
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }

                        if (file_exists($filePath)) //file couldn't be deleted
                        {
                            $msg = array("status" => "failed");
                        } else {
                            $msg = array("status" => "success", "success_count" => sizeof($name), "filePath" => $filePath);
                        }

                    }
                }
                $this->closeConnection();
                $this->response($this->json($msg), 200);
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        }
    }
    protected function getBackgroundPatternImagePath()
    {
        $baseImagePath = $this->getBasePath() . self::HTML5_BACKGROUND_PATTERN_DIR;
        return $baseImagePath;
    }
    protected function getBackgroundPatternImageURL()
    {
        $baseImagePath = $this->getCurrentUrl() . self::ASSETS_CONTAINER_DIR . self::HTML5_BACKGROUND_PATTERN_DIR;
        return $baseImagePath;
    }

    /**
     * Returns a unique 32 char alphanumeric value against the given table
     *
     * @param $table Table name
     * @param $column Column name
     * @return alphanumeric : 32 char alphanumeric unique value
     */
    protected function getDBUniqueId($table, $column)
    {
        $uniqueId = $this->generateUniqueId();
        if ($this->isValueExists($table, $column, $uniqueId)) {
            return $this->getDBUniqueId($table, $column);
        } else {
            return $uniqueId;
        }

    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *generateUniqueId
     *
     *@param (String)apikey
     *@param (String)start
     *@return 32 char value
     *
     */
    protected function generateUniqueId($start = 0)
    {
        $dmt = date("d") + date("m") + date("Y") + time();
        $ran = rand(0, 10000000);
        $dmtran = $dmt + $ran;
        $un = uniqid();
        $dmtun = $dmt . $un;
        $mdun = md5($dmtran . $un); //32 char
        if ($start) {
            $mdun = substr($mdun, $start);
        }
        // if you want sort length code.
        return $mdun;
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *to check exists table,column
     *
     *@param (String)apikey
     *@param (String)table
     *@param (String)column
     *@param (String)value
     *@return true or false
     *
     */
    protected function isValueExists($table, $column, $value)
    {
        $sql = "Select " . $column . " from  " . $table . " where " . $column . "='$value'";
        $result = $this->executeGenericCountQuery($sql);
        if (!empty($result)) {
            return true;
        } else {
            return false;
        }

    }
    /**
     *
     * @Purpose: Recursively deletes all the directories, files and sub-direcories
     * @param string $dir
     *
     */
    protected function rrmdir($dir)
    {
        if (file_exists($dir) && is_dir($dir)) {
            $objects = array_diff(scandir($dir), array('.', '..'));
            foreach ($objects as $object) {
                if (is_dir($dir . "/" . $object)) {
                    $this->rrmdir($dir . "/" . $object);
                } else {
                    @chmod($dir . "/" . $object, 0777);
                    @unlink($dir . "/" . $object);
                }
            }
            @chmod($dir, 0777);
            @rmdir($dir);
        }
    }
	
	/**
	* Get the file content
	* @param url path of the file
	* @return string data
	*/
	public function getFileContents($url){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		$data = curl_exec($curl);
		curl_close($curl);
		return $data;
	}

    /**
    * Sent mail to customer while order placed
    * @param (String)toMail
    * @param (String)html
    * @return status successful and failure messages.
    */
    public function sentCustomerMail($toMail, $html)
    {

        $boundary = str_replace(" ", "", date('l jS \of F Y h i s A'));
        $fromMail = SENDER_EMAIL;
        $msg = array();

        $subjectMail = "Thank you for Order";
        $headersMail .= 'From: ' . $fromMail . "\r\n" . 'Reply-To: ' . $fromMail . "\r\n";
        $headersMail .= "MIME-Version: 1.0\r\n";
        $headersMail .= "Content-Type: multipart/alternative; boundary = \"" . $boundary . "\"\r\n\r\n";
        $headersMail .= "--" . $boundary . "\r\n";
        $headersMail .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        $headersMail .= "Content-Transfer-Encoding: base64\r\n\r\n";

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
     * @Purpose: Encrypt/decrypt the data by using a key
     * @param string $string
     * @param string $key
     * @param boolean $type
     *
     */
	protected function xorIt($string, $key, $type = 0)
	{
		$sLength = strlen($string);
		$xLength = strlen($key);
		for ($i = 0; $i < $sLength; $i++) {
			for ($j = 0; $j < $xLength; $j++) {
				if ($type == 1) {
					//decrypt
					$string[$i] = $key[$j]^$string[$i];
						 
				} else {
					//crypt
					$string[$i] = $string[$i]^$key[$j];
				}
			}
		}
		return $string;
	}
}
