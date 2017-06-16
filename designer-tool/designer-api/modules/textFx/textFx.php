<?php
/* Check Un-authorize Access */
if (!defined('accessUser')) {
    die("Error");
}

class TextFx extends UTIL
{

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Get textfx style
     *
     *@param (String)apikey
     *@param (int)charecter_nos
     *@param (int)style_nos
     *@return json data
     *
     */
    public function getTextfxStyle($style_nos = 0, $charecter_nos = 0)
    {
        try {
            $sql = "SELECT s.pk_id as character_id,s.textfx_style_id,s.alphabate FROM " . TABLE_PREFIX . "textfx_charecters s INNER JOIN " . TABLE_PREFIX . "textfx_style t WHERE t.pk_id=s.textfx_style_id";
            if (isset($this->_request['style_nos']) && $this->_request['style_nos']) {
                $sql .= " AND (SELECT  COUNT(*) FROM " . TABLE_PREFIX . "textfx_style ts WHERE ts.pk_id >= t.pk_id) <= " . $this->_request['style_nos'];
            }
            if (isset($this->_request['charecter_nos']) && $this->_request['charecter_nos']) {
                $sql .= " AND (SELECT  COUNT(*) FROM " . TABLE_PREFIX . "textfx_charecters f WHERE f.textfx_style_id = s.textfx_style_id AND f.pk_id >= s.pk_id) <= " . $this->_request['charecter_nos'];
            }

            $rec = $this->executeFetchAssocQuery($sql);
            $sql1 = "SELECT * FROM " . TABLE_PREFIX . "textfx_style ORDER BY pk_id";
            $rec1 = $this->executeFetchAssocQuery($sql1);
            $res = array();
            $alph = array();

            foreach ($rec1 as $k1 => $v1) {
                $i = 0;
                $res[$k1]['textfx_style_id'] = $v1['pk_id'];
                $res[$k1]['name'] = $v1['name'];

                foreach ($rec as $k => $v) {
                    if ($v['textfx_style_id'] == $v1['pk_id']) {
                        $res[$k1]['alphabate'][$i]['charecter'] = $v['alphabate'];
                        $res[$k1]['alphabate'][$i]['id'] = $v['character_id'];
                        $i++;
                    }
                }
            }
            $this->response($this->json($res,1), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Update Textfx data
     *
     *@param (String)apikey
     *@param (String)name
     *@param (int)id
     *@param (Float)price
     *@return json data
     *
     */
    public function updateTextfxData()
    {
        $apiKey = $this->_request['apikey'];
        if ($this->isValidCall($apiKey)) {
            $id = $this->_request['id'];
            $name = $this->_request['name'];
            $price = $this->_request['price'];
            $status = 0;
            try {
                $sql = "UPDATE " . TABLE_PREFIX . "textfx SET name = '" . $name . "', price = '" . $price . "' WHERE id = '" . $id . "'";
                $status = $this->executeGenericDMLQuery($sql);
            } catch (Exception $e) {
                $msg = array('Caught exception:' => $e->getMessage());
            }
            $msg['status'] = ($status) ? 'success' : 'failed';
        } else {
            $msg = array("status" => "invalid");
        }
        $this->response($this->json($msg), 200);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Update TextFx
     *
     *@param (String)apikey
     *@param (int)textfx_style_id
     *@param (String)name
     *@return json data
     *
     */
    public function updateTextfx()
    {
        $status = 0;
        if (!empty($this->_request) && isset($this->_request['name']) && $this->_request['textfx_style_id']) {
            extract($this->_request);
            try {
                $name = addslashes($name);
                $sql = "UPDATE " . TABLE_PREFIX . "textfx_style SET name='" . $name . "' WHERE pk_id='" . $textfx_style_id . "'";
                $status = $this->executeGenericDMLQuery($sql);
                if (!empty($alphabets)) {
                    $usql1 = '';
                    $usql2 = '';
                    foreach ($alphabets as $v) {
                        $usql1 .= 'WHEN ' . $v['id'] . " THEN '" . $v['charecter'] . "'";
                        $usql2 .= ',' . $v['id'];
                    }

                    $usql = 'UPDATE ' . TABLE_PREFIX . 'textfx_charecters SET alphabate = CASE pk_id ' . $usql1 . ' END WHERE pk_id IN(' . substr($usql2, 1) . ')';
                    $status = $this->executeGenericDMLQuery($usql);
                }
            } catch (Exception $e) {
                $msg = array('Caught exception:' => $e->getMessage());
            }
        }
        $msg['status'] = ($status) ? 'Success' : 'Failed';
        $this->response($this->json($msg), 200);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *delete TextFx Style
     *
     *@param (String)apikey
     *@param (int)textfx_style_id
     *@return json data
     *
     */
    public function deleteTextfxStyle()
    {
        try {
            $status = 0;
            if (isset($this->_request['textfx_style_id']) && $this->_request['textfx_style_id']) {
                $dir = $this->getTextfxSvgPath();
                if (!$dir) {
                    $this->response('', 204);
                }
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }

                $sql = "SELECT CONCAT(textfx_style_id,'_',alphabate,'.svg') AS file_name FROM " . TABLE_PREFIX . "textfx_charecters WHERE textfx_style_id IN(" . $this->_request['textfx_style_id'] . ")";
                $rec = $this->executeFetchAssocQuery($sql);
                foreach ($rec as $v) {
                    if (file_exists($dir . $v['file_name'])) {
                        unlink($dir . $v['file_name']);
                    }
                }
                $sql = "DELETE FROM " . TABLE_PREFIX . "textfx_charecters WHERE textfx_style_id IN(" . $this->_request['textfx_style_id'] . ")";
                $status = $this->executeGenericDMLQuery($sql);
                $sql = "DELETE FROM " . TABLE_PREFIX . "textfx_style WHERE pk_id IN(" . $this->_request['textfx_style_id'] . ")";
                $this->executeGenericDMLQuery($sql);
            }
        } catch (Exception $e) {
            $msg = array('Caught exception:' => $e->getMessage());
        }
	$msg['status'] = ($status) ? 'Success' : 'Failed';
        $this->response($this->json($msg), 200);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Add bulk TextFx
     *
     *@param (String)apikey
     *@param (Array)files
     *@param (String)name
     *@return json data
     *
     */
    public function addBulkTextfx()
    {
        $status = 0;
        if (!empty($this->_request) && isset($this->_request['name']) && $this->_request['name']) {
            $styl_sql = "INSERT INTO " . TABLE_PREFIX . "textfx_style (name) VALUES ('" . addslashes($this->_request['name']) . "')";
            $textfx_style_id = $this->executeGenericInsertQuery($styl_sql);
            try {
                if (!empty($this->_request['files'])) {
                    $dir = $this->getTextfxSvgPath();
                    if (!$dir) {
                        $this->response('', 204);
                    }
                    if (!file_exists($dir)) {
                        mkdir($dir, 0777, true);
                    }

                    $sql = '';
                    foreach ($this->_request['files'] as $k => $v) {
                        $sql .= ",('" . $textfx_style_id . "','" . $v['alphabate'] . "')";
                        if ($v['base64Data']) {
                            $type = 'svg';
                            $fname[$k] = $textfx_style_id . '_' . $v['alphabate'] . '.' . $type;

                            $thumbBase64Data[$k] = base64_decode($v['base64Data']);
                            file_put_contents($dir . $fname[$k], $thumbBase64Data[$k]);
                            list($width[$k], $height[$k]) = getimagesize($dir . $fname[$k]);
                            $resizeImage = $this->resize($dir . $fname[$k], $dir . 'thumb_' . $fname[$k], 80, 80);
                            if ($resizeImage != true) {
                                $msg = array("status" => "ThumbnailForTextfx generation failed");
                                $this->response($this->json($msg), 200);
                            }
                        }
                    }
                    $sql = "INSERT INTO " . TABLE_PREFIX . "textfx_charecters (textfx_style_id, alphabate) VALUES " . substr($sql, 1);
                    $status = $this->executeGenericDMLQuery($sql);
                }
            } catch (Exception $e) {
                $msg = array('Caught exception:' => $e->getMessage());
            }
        }
        $msg['status'] = ($status) ? 'Success' : 'Failed';
        $this->response($this->json($msg), 200);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Save Textfx data
     *
     *@param (String)apikey
     *@param (int)range
     *@param (int)srtIndex
     *@param (int)char_nos
     *@return json data
     *
     */
    public function getTextfxDetails()
    {
        try {
            $apiKey = $this->_request['apikey'];
            $range = $this->_request['range'];
            $startIndex = $this->_request['srtIndex'];
            if ($this->isValidCall($apiKey)) {
                $sql = "SELECT s.pk_id as character_id,s.textfx_style_id,s.alphabate FROM " . TABLE_PREFIX . "textfx_charecters s INNER JOIN " . TABLE_PREFIX . "textfx_style t WHERE t.pk_id=s.textfx_style_id";
                if (isset($this->_request['char_nos']) && $this->_request['char_nos']) {
                    $sql .= ' AND (SELECT  COUNT(*) FROM ' . TABLE_PREFIX . 'textfx_charecters f WHERE f.textfx_style_id = s.textfx_style_id AND f.pk_id >= s.pk_id) <= ' . $this->_request['char_nos'];
                }
                $rec = $this->executeFetchAssocQuery($sql);
                $sql1 = "SELECT * FROM " . TABLE_PREFIX . "textfx_style ORDER BY pk_id limit $startIndex,$range";
                $rec1 = $this->executeFetchAssocQuery($sql1);
                $res = array();
                $alph = array();
                foreach ($rec1 as $k1 => $v1) {
                    $i = 0;
                    $res[$k1]['id'] = $v1['pk_id'];
                    $res[$k1]['name'] = $v1['name'];

                    foreach ($rec as $k => $v) {
                        if ($v['textfx_style_id'] == $v1['pk_id']) {
                            $res[$k1]['displaychar'][] = $v['alphabate'];
                            $i++;
                        }
                    }
                }
                $msg['fxfonts'] = $res;
            } else {
                $msg = array("status" => "invalid");
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
     *Save Textfx data
     *
     *@param (String)apikey
     *@param (int)textFxId
     *@param (String)textFxName
     *@param (String)textFxDisplaychar
     *@param (Float)textFxPrice
     *@return json data
     *
     */
    public function saveTextFxDetails()
    {
        $apiKey = $this->_request['apikey'];
        if ($this->isValidCall($apiKey)) {
            try {
                $textFxId = $this->_request["textFxId"];
                $textFxName = $this->_request["textFxName"];
                $textFxDisplaychar = $this->_request["textFxDisplaychar"];
                $textFxPrice = floatval($this->_request["textFxPrice"]);
                $sql = "insert into " . TABLE_PREFIX . "textfx(id,name,displaychar,price) values('$textFxId','$textFxName','$textFxDisplaychar',$textFxPrice)";
                $status = $this->executeGenericDMLQuery($sql);
                if ($status) {
                    $msg = array("status" => "success");
                } else {
                    $msg = array("status" => "failed", "sql" => $sql);
                }

                $this->closeConnection();
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
     *date modified 15-4-2016 (dd-mm-yy)
     *Update Textfx data
     *
     *@param (String)apikey
     *@param (int)textfxIds
     *@return json data
     *
     */
    public function removeTextfxFonts()
    {
        $apiKey = $this->_request['apikey'];
        $textfxIdsArray = $this->_request['textfxIds'];
        if ($this->isValidCall($apiKey)) {
            $ids = implode(',', $textfxIdsArray);
            try {
                $sql = "DELETE FROM " . TABLE_PREFIX . "textfx WHERE id in ($ids)";
                $status = $this->executeGenericDMLQuery($sql);
                if ($status) {
                    $dir = '';
                    $dir = $this->getTextFXImagePath();

                    if (!$dir) {
                        $this->response('', 204);
                    }
                    //204 - immediately termiante this request

                    for ($j = 0; $j < sizeof($textfxIdsArray); $j++) {
                        $filePath = $dir . $textfxIdsArray[$j];
                        $msg = '';
                        if (file_exists($filePath)) {
                            if (is_file($filePath)) {
                                unlink($filePath);
                            }

                            $dirPath = $filePath . '/';
                            $files = glob($dirPath . '*', GLOB_MARK);
                            foreach ($files as $file) {
                                unlink($file);
                            }
                            rmdir($dirPath);
                        }
                    }
                    $msg = array("status" => "success");
                } else {
                    $msg = array("status" => "failed", "sql" => $sql);
                }

                $this->closeConnection();
            } catch (Exception $e) {
                $msg = array('Caught exception:' => $e->getMessage());
            }
        } else {
            $msg = array("status" => "invalid");
        }
	$this->response($this->json($msg), 200);
    }
}
