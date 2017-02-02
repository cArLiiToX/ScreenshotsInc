<?php
/* Check Un-authorize Access */
if (!defined('accessUser')) {
    die("Error");
}

class ImageEdit extends UTIL
{
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Get  mask image
     *
     *@param (String)apikey
     *@param (Int)srtIndex
     *@param (Int)range
     *@return json data
     *
     */
    public function getMaskImageList()
    {
        $apiKey = (isset($this->_request['apikey']) && $this->_request['apikey'])?$this->_request['apikey']:'';
        if ($this->isValidCall($apiKey)) {
            try {
                $srtIndex = (isset($this->_request['srtIndex'])) ? $this->_request['srtIndex'] : 0;
                $range = (isset($this->_request['range'])) ? $this->_request['range'] : 30;
                $sql = "Select name,mask_id,thumb_image,svg_image from  " . TABLE_PREFIX . "mask_paths ORDER BY id DESC LIMIT $srtIndex,$range";
                $result = $this->executeGenericDQLQuery($sql);
                if (!empty($result)) {
                    $maskedImageData = array();
                    foreach ($result as $rows) {
                        $mask_id = $rows['mask_id'];
                        $name = $rows['name'];
                        $thumb_image = $rows['thumb_image'];
                        $svg_image = $rows['svg_image'];
                        $thumb_imageURL = $this->getMaskImageURL() . $thumb_image;
                        $svg_imageURL = $this->getMaskImageURL() . $svg_image;
                        $data = array("name" => $name, "mask_id" => $mask_id, "thumb_image" => $thumb_image, "thumb_imageURL" => $thumb_imageURL, "svg_image" => $svg_image, "svg_imageURL" => $svg_imageURL);
                        $maskedImageData[] = $data;
                    }
                    $this->closeConnection();
                    $this->response($this->json($maskedImageData), 200);
                } else {
                    $msg['status'] = array('nodata');
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
     *date created 9-9-2015 (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get effect list
     *
     *@param (String)apikey
     *@return json data
     *
     */
    public function getEffectList()
    {
        header('HTTP/1.1 200 OK');
        try {
            $effectName = array();
            $sql = "SELECT file_name FROM " . TABLE_PREFIX . "effect_list";
            $result = $this->executeGenericDQLQuery($sql);
            foreach ($result as $k => $row) {
                $effectName[$k] = $row['file_name'];
            }
            echo json_encode($effectName);exit;
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
        $this->closeConnection();
    }
    /*
     *
     *date created 28-9-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *getRangeFromAdmin
     *purpose:for fetching the value from database to be displayed in the drop-down
     *
     */
    public function getRangeFromAdmin()
    {
        try {
            $sql = "SELECT max_number_of_color FROM " . TABLE_PREFIX . "image_edit_select_color ORDER by id DESC LIMIT 1";
            $rows = $this->executeGenericDQLQuery($sql);
            $range = array();
            for ($i = 0; $i < sizeof($rows); $i++) {
                $range[$i]['max_number_of_color'] = $rows[$i]['max_number_of_color'];
            }
            //echo $rows[$i]['max_number_of_color'];
            $this->response($this->json($range), 200);
            //break;
            $msg['status'] = ($rows) ? 'success' : 'failed';
            //$this->response($this->json($msg), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }
    /*
    Fetch percentage of file uploaded.
    @return integer
     */
    public function imageUploadedPercentage()
    {
        try {
            session_start();
            $randomString = $this->_request['randomString'];

            $key = ini_get("session.upload_progress.prefix") . $randomString;

            if (!empty($_SESSION[$key])) {
                $current = $_SESSION[$key]["bytes_processed"];
                $total = $_SESSION[$key]["content_length"];
                echo $current < $total ? ceil($current / $total * 100) : 100;exit();
            } elseif ($_SESSION[$randomString] == 'true') {
                unset($_SESSION[$randomString]);
                echo 100;exit();
            }
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
        exit();
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *save  mask data
     *
     *@param (String)apikey
     *@param (String)name
     *@param (String)maskdata
     *@param (String)thumbdata
     *@return json data
     *
     */
    public function saveMaskPaths()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }
        $apiKey = $this->_request['apikey'];
        $name = $this->_request['name'];
        $maskdata = $this->_request['maskdata'];
        $thumbdata = $this->_request['thumbdata']; // design/pattern/textfx
        $id = $this->_request['id']; //file name

        if ($this->isValidCall($apiKey)) {
            try {
                $maskBase64Data = base64_decode($maskdata);
                $thumbBase64Data = base64_decode($thumbdata);
                $dir = $this->getMaskImagePath();

                if (!$dir) {
                    $this->response('', 204); //204 - immediately termiante this request
                }
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }
                $imageName = $id . '_0.png';
                $svgName = $id . '_0.svg';

                $thumbImageFilePath = $dir . $imageName;
                $svgImageFilePath = $dir . $svgName;

                $msg = '';
                // $status='';
                if ($thumbdata != '') {
                    $status = file_put_contents($thumbImageFilePath, $thumbBase64Data);
                }
                $status = file_put_contents($svgImageFilePath, $maskBase64Data);
                $sql = "INSERT INTO " . TABLE_PREFIX . "mask_paths (name,svg_image, thumb_image, mask_id, date_created) VALUES ('$name','$svgName', '$imageName', '$id', now())";
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
     *date modified 15-4-2016 (dd-mm-yy)
     *update mask path by mask id
     *
     *@param (String)apikey
     *@param (Int)maskId
     *@return json data
     *
     */
    public function updateMaskPaths()
    {
        if (!empty($this->_request) && isset($this->_request['apikey']) && $this->isValidCall($this->_request['apikey']) && isset($this->_request['maskId']) && $this->_request['maskId']) {
            extract($this->_request);
            try {
                $sql = "SELECT name,mask_id,thumb_image,svg_image FROM " . TABLE_PREFIX . "mask_paths WHERE mask_id=" . $maskId . " LIMIT 1";
                $rec = $this->executeFetchAssocQuery($sql);
                if (!empty($rec[0])) {
                    $sql = "UPDATE " . TABLE_PREFIX . "mask_paths SET";
                    $msg = array("status" => "success");
                    if ($rec[0]['maskName'] !== $maskName) {
                        $sql .= " name='$maskName',";
                        $msg['name'] = $maskName;
                    }
                    if ($rec[0]['mask_id'] == $maskId) {
                        $msg['mask_id'] = $maskId;
                        $dir = $this->getMaskImagePath();
                        if (!$dir) {
                            $this->response('', 204);
                        }
                        //204 - immediately termiante this request
                        if (!file_exists($dir)) {
                            mkdir($dir, 0777, true);
                        }

                        if ($imgbase64) {
                            if (file_exists($dir . $rec[0]['thumb_image'])) {
                                unlink($dir . $rec[0]['thumb_image']);
                            }

                            $thumbBase64Data = base64_decode($imgbase64); //$thumbImage = $mask_id.'.png';
                            file_put_contents($dir . $imgFileName, $thumbBase64Data);
                            $sql .= " thumb_image='$imgFileName',";
                            $msg['thumb_imageURL'] = $dir . $imgFileName;
                        }
                        if ($svgBase64) {
                            if (file_exists($dir . $rec[0]['svg_image'])) {
                                unlink($dir . $rec[0]['svg_image']);
                            }

                            $maskBase64Data = base64_decode($svgBase64);
                            file_put_contents($dir . $svgFileName, $maskBase64Data);

                            $sql .= " svg_image='$svgFileName',";
                            $msg['svg_imageURL'] = $dir . $svgFileName;
                        }
                    }
                    $sql .= " date_modified = NOW() WHERE mask_id=" . $maskId;
                    $status .= $this->executeGenericDMLQuery($sql);

                    if (!$status) {
                        $msg['status'] = "failed";
                    }

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
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Remove Design mask by design mask id
     *
     *@param (String)apikey
     *@param (Int)designmaskIds
     *@return json data
     *
     */
    public function removeDesignMasks()
    {
        $apiKey = $this->_request['apikey'];
        if ($this->isValidCall($apiKey)) {
            try {
                $designmaskIdsArray = $this->_request['designmaskIds'];
                $svgPathArray = array();
                $thumbPathArray = array();
                for ($j = 0; $j < sizeof($designmaskIdsArray); $j++) {
                    $sql = "Select thumb_image,svg_image from  " . TABLE_PREFIX . "mask_paths where mask_id=" . $designmaskIdsArray[$j];
                    $result = $this->executeGenericDQLQuery($sql);
                    if (!empty($result)) {
                        foreach ($result as $rows) {
                            $svg_image = $rows['svg_image'];
                            $thumb_image = $rows['thumb_image'];

                            $svgPathArray[$j] = $this->getMaskImagePath() . $svg_image;
                            $thumbPathArray[$j] = $this->getMaskImagePath() . $thumb_image;
                        }
                    }
                }
                $status = 0;
                $ids = implode(',', $designmaskIdsArray);
                $sql = "DELETE FROM " . TABLE_PREFIX . "mask_paths WHERE  mask_id in ($ids)";
                $this->log('removeDesignMasks:' . $sql);
                $status .= $this->executeGenericDMLQuery($sql);
                if ($status) {
                    for ($i = 0; $i < sizeof($designmaskIdsArray); $i++) {
                        if (file_exists($svgPathArray[$i])) {
                            @chmod($svgPathArray[$i], 0777);
                            @unlink($svgPathArray[$i]);
                        }
                        if (file_exists($thumbPathArray[$i])) {
                            @chmod($thumbPathArray[$i], 0777);
                            @unlink($thumbPathArray[$i]);
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
        }
    }

}
