<?php
/* Check Un-authorize Access */
if (!defined('accessUser')) {
    die("Error");
}

class UserSlot extends UTIL
{

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Save User Images
     *
     *@param (String)apikey
     *@param (Array)data
     *@return json data
     *
     */
    public function saveUserImage()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('not post', 406);
        }
        $postData = $this->formatJSONToArray($this->_request['data'], false);

        $randomString = $postData->randomString;
        session_start();
        $_SESSION[$randomString] = 'true';

        $apiKey = $postData->apikey;
        $customerId = 0;
        $uid = 0;
        if (isset($postData->customerId) && !empty($postData->customerId)) {
            $customerId = $postData->customerId;
        }

        if (isset($postData->uid)) {
            $uid = $postData->uid;
        }

        if (!$uid && $uid == 0) {
            $uid = $this->getDBUniqueId(TABLE_PREFIX . 'image_data', 'uid');
        }
        if ($this->isValidCall($apiKey)) {
            try {
                $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                $count = 0;
                $sql0 = "Select max(id) from  " . TABLE_PREFIX . "image_data where uid='" . $uid . "'";
                if ($customerId && $customerId > 0) {
                    $sql0 = "Select max(id) from  " . TABLE_PREFIX . "image_data where customer_id=" . $customerId;
                }
                $rows0 = $this->executeGenericDQLQuery($sql0);
                if (!empty($rows0)) {
                    $count = $rows0[0][0];
                }
                $count = $count + 1;
                $fileName = $count . '.' . $ext;
                $thumbFileName = 'thumb_' . $count . '.' . $ext;
                $directory = $uid;
                if ($customerId && $customerId > 0) {
                    $directory = $customerId;
                }

                $baseImagePath = $this->getUserImagePath();
                $savePath = $baseImagePath . $directory . '/';
                $baseImageURL = $this->getUserImageURL();
                $imageURL = $baseImageURL . $directory . '/';
                if (!file_exists($savePath)) {
                    mkdir($savePath, 0777, true);
                    chmod($savePath, 0777);
                }
                $filePath = $savePath . $fileName;
                $thumbFilePath = $savePath . $thumbFileName;
                $imageFullUrl = $imageURL . "/" . $fileName;
                $status = move_uploaded_file($_FILES['file']['tmp_name'], $filePath);
                $msg = '';
                if ($status) {
                    $resizeImage = $this->resize($filePath, $thumbFilePath, 70, 70);
                    if ($resizeImage != true) {
                        $msg = array("status" => "Thumbnail generation failed");
                    } else {
                        $sql = "INSERT INTO " . TABLE_PREFIX . "image_data (customer_id, image, thumbnail, type, uid, date_created) VALUES ($customerId, '$fileName', '$thumbFileName', '$ext', '$uid', now())";
                        $id = $this->executeGenericInsertQuery($sql);
                        if ($id) {
                            $msg = array("status" => "success", "filename" => $fileName, "filepath" => $imageURL, "uid" => $uid, "mainImage" => $filePath, "thumb" => $thumbFilePath);
                        } else {
                            $msg = array("status" => "failed", "type" => "SQL Exception");
                        }
                    }
                } else {
                    $msg = array("status" => "failed", "type" => "Couldn't store file");
                }
                $this->response($this->json($msg), 200);
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
     *load quotes
     *
     *@param (String)apikey
     *@param (String)condition
     *@param (Int)startIndex
     *@param (Int)rangeIndex
     *@return json data
     *
     */
    public function deleteUserImages()
    {
        $customerId = ($this->_request['customerId'] != '') ? $this->_request['customerId'] : 0;
        $uid = $this->_request['uid'];
        $id = ($customerId == 0) ? $uid : $customerId;
        $fileNames = $this->_request['fileNames'];
        $imgNameList = explode(",", $fileNames);
        $imgNames = implode("','", $imgNameList);

        try {
            $sql = "DELETE FROM " . TABLE_PREFIX . "image_data WHERE customer_id = " . $customerId . " AND image in('" . $imgNames . "')";
            $status = $this->executeGenericDMLQuery($sql);
            if ($status) {
                foreach ($imgNameList as $img) {
                    $fileName = $this->getUserImagePath() . $id . '/' . $img;
                    if (file_exists($fileName)) {
                        @chmod($fileName, 0777);
                        @unlink($fileName);
                    }
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
     *date modified 15-4-2016(dd-mm-yy)
     *get user slot
     *
     *@param (String)apikey
     *@param (Int)slotid
     *@param (Int)uid
     *@param (Int)userId
     *@return json data
     *
     */
    public function getUserSlot()
    {
        $apiKey = $this->_request['apikey'];
        $slotId = $this->_request['slotid'];
        $user_id = (isset($this->_request['userid'])) ? $this->_request['userid'] : 0;
        $uid = (isset($this->_request['uid'])) ? $this->_request['uid'] : 0;
        try {
            $sql = "Select json_data,status from " . TABLE_PREFIX . "user_slot where user_id=" . $user_id . " and slot_id=" . $slotId;
            if ($uid != "" && $uid != '0') {
                $sql = "Select json_data,status from " . TABLE_PREFIX . "user_slot where uid='" . $uid . "' and slot_id=" . $slotId;
            }

            if ($user_id && $user_id > 0) {
                $sql = "Select json_data,status from " . TABLE_PREFIX . "user_slot where user_id=" . $user_id . " and slot_id=" . $slotId;
            }

            $result = $this->executeFetchAssocQuery($sql);
            $json_data = (!strpos($result[0]['json_data'], '\\\"') ? $result[0]['json_data'] : stripslashes($result[0]['json_data']));
            if (!empty($result)) {
                $msg = '{"jsondata":' . $json_data . ',"islocked":' . $result[0]['status'] . '}';
            } else {
                $msg = array("status" => "nodata", "sql" => $sql);
            }
        } catch (Exception $e) {
            $msg = array('Caught exception:' => $e->getMessage());
        }
        $this->response($msg, 200);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *save user slot
     *
     *@param (String)apikey
     *@param (Int)productSide
     *@param (Int)userid
     *@param (Int)slotStatus
     *@param (Json)data
     *@param (Int)uid
     *@return json data
     *
     */
    public function saveUserSlot()
    {
        //getUserSlotList // deleteUserSlot
        if ($this->get_request_method() != "POST") {
            $this->response('!POST', 406);
        }

        $index = 0;
        $limit = 10;
        $slotUId = 0;
        $userid = 0;

        if (isset($this->_request['productSide'])) {
            $index = $this->_request['productSide'];
        }

        if (isset($this->_request['userid'])) {
            $userid = $this->_request['userid'];
        }
        //magento userid
        if (isset($this->_request['uid'])) {
            $slotUId = $this->_request['uid'];
        }
        // unique id for local storage
        if (!$slotUId && $slotUId == 0) {
            $slotUId = $this->getDBUniqueId(TABLE_PREFIX . 'user_slot', 'uid');
        }

        $directory = $slotUId;
        if ($userid && $userid > 0) {
            $directory = $userid;
        }

        $apiKey = $this->_request['apikey'];
        $jsonData = $this->_request['data'];
        $slotStatus = $this->_request['slotStatus'];
        $dataArray1 = '';
        $dataArray = (is_array($jsonData)) ? $jsonData : $this->formatJSONToArray($jsonData, false);

        $jsonData = mysqli_real_escape_string($this->db, $jsonData);
        $productUrl = $dataArray->sides[$index]->url;
        $svgcontents = $dataArray->sides[$index]->svg;
        $imgContent = file_get_contents($productUrl);
        $base64ImgData = base64_encode($imgContent);
        $previewImage = "<svg xmlns='http://www.w3.org/2000/svg' id='svgroot' xlinkns='http://www.w3.org/1999/xlink' width='500' height='500' x='0' y='0' overflow='visible'><image x='0' y='0' width='500' height='500' id='svg_1' xmlns:xlink='http://www.w3.org/1999/xlink' xlink:href='data:image/png;
        base64," . $base64ImgData . "'>
        </image>" . $svgcontents . "</svg>";

        if ($this->isValidCall($apiKey)) {
            try {
                $query = "SELECT COUNT(id) as count FROM " . TABLE_PREFIX . "user_slot WHERE uid='$slotUId'";
                $row = $this->getResult($query);
                if ($row[0]['count'] < $limit) {
                    $sql0 = "Select max(id) from " . TABLE_PREFIX . "user_slot";
                    $result0 = $this->getResult($sql0);
                    $newSlotId = $result0[0][0] + 1;
                    $ext = 'svg';
                    $fileName = $newSlotId . '.' . $ext;
                    $baseSlotsPath = $this->getSlotsPreviewPath();
                    $savePath = $baseSlotsPath . $directory . '/';
                    $baseSlotsURL = $this->getSlotsPreviewURL();
                    $imageURL = $baseSlotsURL . $directory . '/';
                    $slotPreviewImageUrl = $imageURL . $fileName;
                    if (!file_exists($savePath)) {
                        mkdir($savePath, 0777, true);
                    }

                    $filePath = $savePath . $fileName;
                    $previewStatus = file_put_contents($filePath, $previewImage);

                    if ($previewStatus) {
                        $sql = "INSERT INTO " . TABLE_PREFIX . "user_slot (slot_id,user_id,json_data,status,date_created,slot_image,uid) VALUES ($newSlotId,'$userid','$jsonData',$slotStatus,now(),'$fileName','$slotUId')";
                        $id = $this->executeGenericInsertQuery($sql);
                        if ($id) {
                            $msg = array("status" => "success", "user_id" => $userid, "slot_id" => $newSlotId, "id" => $id, "filePath" => $slotPreviewImageUrl, "islucked" => $slotStatus, "uid" => $slotUId);
                        } else {
                            $msg = array("status" => "Can't save the data. ::failed");
                        }
                        $this->response($this->json($msg), 200);
                    } else {
                        $msg = array("status" => "image saving failed");
                        $this->response($this->json($msg), 200);
                    }
                } else {
                    $msg = array("status" => "limit exceeds");
                    $this->response($this->json($msg), 200);
                }
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        } else {
            $msg = array("status" => "invalidkey");
            $this->response($this->json($msg), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Delete user slot
     *
     *@param (String)apikey
     *@param (Int)slotid
     *@param (Int)userid
     *@return json data
     *
     */
    public function deleteUserSlot()
    {
        $status = 0;
        if (!empty($this->_request) && isset($this->_request['apikey']) && $this->isValidCall($this->_request['apikey'])) {
            extract($this->_request);
            if ($slotid && ($userid || $uid)) {
                try {
                    $getSql = "SELECT user_id,uid,slot_image FROM " . TABLE_PREFIX . "user_slot WHERE slot_id='" . $slotid . "'";
                    $deleteSql = '';
                    if ($userid != 0 && $uid == 0) {
                        $getSql .= " AND user_id='" . $userid . "'";
                        $deleteSql = " AND user_id='" . $userid . "'";
                    }
                    if ($uid != 0 && $userid == 0) {
                        $getSql .= " AND uid='" . $uid . "'";
                        $deleteSql = " AND uid='" . $uid . "'";
                    }
                    $res = $this->executeFetchAssocQuery($getSql);

                    if (!empty($res)) {
                        $path = $this->getSlotsPreviewPath();
                        $ds = DIRECTORY_SEPARATOR;
                        $dir = ($res[0]['user_id'] == 0) ? $res[0]['uid'] : $res[0]['user_id'];
                        $file = $path . $dir . $ds . $res[0]['slot_image'];
                        if (file_exists($file)) {
                            @chmod($file, 0777);
                            @unlink($file);
                            $sql = "DELETE FROM " . TABLE_PREFIX . "user_slot WHERE slot_id='" . $slotid . "'" . $deleteSql;
                            $status = $this->executeGenericDMLQuery($sql);
                        }
                    }
                } catch (Exception $e) {
                    $result = array('Caught exception:' => $e->getMessage());
                    $this->response($this->json($result), 200);
                }
            }
            $msg['status'] = ($status) ? 'Success' : 'Failure';
        } else {
            $msg['status'] = "invalid api key";
        }
        $this->response($this->json($msg), 200);
    }
}
