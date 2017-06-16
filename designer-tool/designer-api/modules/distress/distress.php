<?php
/* Check Un-authorize Access */
if (!defined('accessUser')) {
    die("Error");
}

class Distress extends UTIL
{
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Get Distress data
     *
     *@param (String)apikey
     *@param (String)name
     *@param (Float)price
     *@param (Int)id
     *@return json data
     *
     */
    public function getDistressDetails()
    {
        $apiKey = $this->_request['apikey'];
        if ($this->isValidCall($apiKey)) {
            try {
                $srtIndex = $this->_request['srtIndex'];
                $range = $this->_request['range'];
                $sql = "SELECT * FROM " . TABLE_PREFIX . "distress ORDER BY distress_id DESC LIMIT $srtIndex,$range";
                $distressFromValue = $this->executeGenericDQLQuery($sql);
                $distressArray['distress'] = array();
                foreach ($distressFromValue as $i => $row) {
                    $distressArray['distress'][$i]['id'] = $row['id'];
                    $distressArray['distress'][$i]['name'] = $row['name'];
                    $distressArray['distress'][$i]['url'] = $this->getDistressImageURL() . $row['file_name'];
                    $distressArray['distress'][$i]['filename'] = $row['file_name'];
                    $distressArray['distress'][$i]['price'] = $row['price'];
                }
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
            $this->closeConnection();
            $this->response($this->json($distressArray,1), 200);
        } else {
            $msg = array("status" => "invalid" . $apiKey);
            $this->response($this->json($msg), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Add Bulk Distress
     *
     *@param (String)apikey
     *@param (String)name
     *@param (Array)files
     *@param (int)id
     *@return json data
     *
     */
    public function addBulkDistress()
    {
        try {
            $status = 0;
            if (!empty($this->_request) && isset($this->_request['name']) && isset($this->_request['files'])) {
                extract($this->_request);
                $sql = '';
                if (!empty($files)) {
                    $dir = $this->getDistressImagePath();
                    $usql1 = '';
                    $usql2 = '';
                    foreach ($files as $k => $v) {
                        $name = addslashes($name);
                        $distressId[$k] = $this->getDBUniqueId('' . TABLE_PREFIX . 'distress', 'id');
                        $fname[$k] = $distressId[$k] . '.' . $v['type'];

                        $sql[$k] = "INSERT INTO " . TABLE_PREFIX . "distress(name,id,file_name) values('" . $name . "','" . $distressId[$k] . "','" . $fname[$k] . "')";
                        $distress_id[$k] = $this->executeGenericInsertQuery($sql[$k]);
                        //$fname[$k] = $distress_id[$k].'.'.$v['type'];
                        $thumbBase64Data[$k] = base64_decode($v['base64']);
                        $status = file_put_contents($dir . $fname[$k], $thumbBase64Data[$k]);
                    }
                    $msg['status'] = ($status) ? "success" : "failed";
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
     *Update Distress data
     *
     *@param (String)apikey
     *@param (String)name
     *@param (Float)price
     *@param (Int)id
     *@return json data
     *
     */
    public function updateDistressData()
    {
        $status = 0;
        $apiKey = $this->_request['apikey'];
        if ($this->isValidCall($apiKey)) {
            try {
                $id = $this->_request['id'];
                $name = addslashes($this->_request['name']);
                $price = $this->_request['price'];
                $status = 0;
                $sql = "UPDATE " . TABLE_PREFIX . "distress SET name = '" . $name . "', price = '" . $price . "' WHERE id = '" . $id . "'";
                $status = $this->executeGenericDMLQuery($sql);
                $msg['status'] = ($status) ? 'success' : 'failed';
                $this->closeConnection();
                $this->response($this->json($msg), 200);
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        } else {
            $msg = array("status" => "invalid " . $apiKey);
            $this->response($this->json($msg), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Remove Distress
     *
     *@param (String)apikey
     *@param (int)fileIds
     *@param (String)fileNames
     *@return json data
     *
     */
    public function removeDistress()
    {
        $apiKey = $this->_request['apikey'];
        $fileNamesArray = $this->_request['fileNames'];
        $fileIdsArray = $this->_request['fileIds'];
        if ($this->isValidCall($apiKey)) {
            //$fileIdsArray = explode("," , $fileIds);
            $ids = implode("','", $fileIdsArray);
            $sql = "DELETE FROM " . TABLE_PREFIX . "distress WHERE id in ('" . $ids . "')";
            $status = $this->executeGenericDMLQuery($sql);
            if ($status) {
                $dir = '';
                $dir = $this->getDistressImagePath();
                if (!$dir) {
                    $this->response('', 204);
                }
                //204 - immediately termiante this request
                for ($j = 0; $j < sizeof($fileNamesArray); $j++) {
                    $filePath = $dir . $fileNamesArray[$j];
                    $msg = '';
                    try {
                        if (file_exists($filePath)) {
                            if (is_file($filePath)) {
                                @chmod($filePath, 0777);
                                @unlink($filePath);
                            }
                            // removing file from  thumbs folder
                            $thumbPath = $dir . "thumbs/";
                            $filePath = $thumbPath . $fileNamesArray[$j];
                            if (is_file($filePath)) {
                                @chmod($filePath, 0777);
                                @unlink($filePath);
                            }
                        }
                    } catch (Exception $e) {
                        $result = array('Caught exception:' => $e->getMessage());
                        $this->response($this->json($result), 200);
                    }
                }
                $msg = array("status" => "success");
            } else {
                $msg = array("status" => "failed", "sql" => $sql);
            }

            $this->closeConnection();
            $this->response($this->json($msg), 200);
        } else {
            $msg = array("status" => "invalid");
            $this->response($this->json($msg), 200);
        }
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Save Distress
     *
     *@param (String)apikey
     *@param (String)distressName
     *@param (Float)distressPrice
     *@param (String)fileExtensions
     *@param (String)data
     *@return true value
     *
     */
    public function saveDistress()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('!POST', 406);
        }
        $apiKey = $this->_request['apikey'];
        $distressName = $this->_request["distressName"];
        $distressPrice = floatval($this->_request["distressPrice"]);
        $fileExtensionsArray = $this->_request['fileExtensions']; //file names
        $base64DataArray = $this->_request['data']; //base64Data for files
        if ($this->isValidCall($apiKey)) {
            try {
                $base64Data = base64_decode($data);
                $dir = $this->getDistressImagePath();
                if (!$dir) {
                    $this->response('Invalid Directory', 204); //204 - immediately termiante this request
                }
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }
                for ($j = 0; $j < sizeof($base64DataArray); $j++) {
                    $distressId = $this->getDBUniqueId('distress', 'id');

                    $fileName = $distressId . '.' . $fileExtensionsArray[$j];
                    $data = $base64DataArray[$j];
                    $base64Data = base64_decode($data);

                    $filePath = $dir . $fileName;

                    $thumbDirPath = $dir . 'thumbs/';
                    if (!file_exists($thumbDirPath)) {
                        mkdir($thumbDirPath, 0777, true);
                        chmod($thumbDirPath, 0777);
                    }
                    $thumbFilePath = $thumbDirPath . $fileName;

                    $fileStatus = file_put_contents($filePath, $base64Data);
                    $msg = '';
                    if ($fileStatus) {
                        $resizeImage = $this->resizeImage($filePath, $thumbFilePath, 70, 70, 0);
                        $sql = "insert into " . TABLE_PREFIX . "distress(id,name,file_name,price) values('$distressId','$distressName','$fileName',$distressPrice)";
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
}
