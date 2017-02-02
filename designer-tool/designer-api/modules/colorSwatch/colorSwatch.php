<?php
/* Check Un-authorize Access */
if (!defined('accessUser')) {
    die("Error");
}

class ColorSwatch extends ColorSwatchStore
{
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *save color swatches
     *
     * @param (String)apikey
     * @param (int)id
     * @param (int)value
     * @param (String)imgData
     * @param (int)swatchWidth
     * @param (int)swatchHeight
     * @return json data
     *
     */
    public function saveColorSwatch($param = null)
    {
        if (!empty($this->_request)) {
            $value = isset($this->_request['value'])?$this->_request['value']:0;
            $imgData = $this->_request['imgData'];
            $base64Data = base64_decode($imgData);
            $swatchWidth = $this->_request['swatchWidth'];
            $swatchHeight = $this->_request['swatchHeight'];
            $imageType = $this->_request['imagetype'];
            $swatchPath = $this->getSwatchesPath();
            $swatchDir = $this->getSwatchURL();
            $swatchFile = $value . '.png';
            $swatchFilePath = $swatchPath . '/' . $value . '.png';
            $swatchFileDir = $swatchDir . $value . '.png';
            $swatchFileDstPath = $swatchPath . '/' . $swatchWidth . 'x' . $swatchWidth . '/' . $value . '.png';
            $swatchFileSestDir = $swatchDir . $swatchWidth . 'x' . $swatchWidth . '/' . $value . '.png';

            try {
                if (!file_exists($swatchPath)) {
                    mkdir($swatchPath, 0777, true);
                    chmod($swatchPath, 0777);
                }
                if (!file_exists($swatchPath . '/' . $swatchWidth . 'x' . $swatchWidth)) {
                    mkdir($swatchPath . '/' . $swatchWidth . 'x' . $swatchWidth, 0777, true);
                    chmod($swatchPath . '/' . $swatchWidth . 'x' . $swatchWidth, 0777);
                }
                if (strlen($imgData) > '7' && $imageType == 'image') {
                    $imageName = $value . '.png';
                    $status = file_put_contents($swatchFilePath, $base64Data);
                    $msg['optionId'] = $value;
                    if ($status) {
                        if (file_exists($swatchFileDstPath)) {
                            if (is_file($swatchFileDstPath)) {
                                unlink($swatchFileDstPath);
                            }

                        }
                        $resizeImage = $this->resize($swatchFilePath, $swatchFileDstPath, $swatchWidth, $swatchHeight);
                        $sql_check = 'SELECT * FROM ' . TABLE_PREFIX . 'swatches WHERE attribute_id = "' . $value . '"';
                        $rows = $this->executeFetchAssocQuery($sql_check);
                        if ($rows[0]['hex_code'] || $rows[0]['image_name']) {
                            $delete_sql = "DELETE FROM " . TABLE_PREFIX . "swatches where attribute_id='" . $value . "'";
                            $status = $this->executeGenericDMLQuery($delete_sql);
                        }
                        $sql = "INSERT INTO " . TABLE_PREFIX . "swatches (attribute_id,image_name) VALUES ('$value','$imageName')";
                        $status = $this->executeGenericDMLQuery($sql);
                        $msg['swatchImage'] = $swatchFileSestDir;
                        $msg['hexCode'] = '';
                        $msg['status'] = 'success';
                    } else {
                        $msg['status'] = 'failed';
                    }
                } else {
                    $sql_check = 'SELECT * FROM ' . TABLE_PREFIX . 'swatches WHERE attribute_id = "' . $value . '"';
                    $rows = $this->executeFetchAssocQuery($sql_check);
                    if ($rows[0]['hex_code'] || $rows[0]['image_name']) {
                        $delete_sql = "DELETE FROM " . TABLE_PREFIX . "swatches where attribute_id='" . $value . "'";
                        $status = $this->executeGenericDMLQuery($delete_sql);
                        if (file_exists($swatchFileDstPath)) {
                            if (is_file($swatchFileDstPath)) {
                                unlink($swatchFileDstPath);
                            }

                        }
                    }
                    $sql = "INSERT INTO " . TABLE_PREFIX . "swatches (attribute_id,hex_code) VALUES ('$value','$imgData')";
                    $status = $this->executeGenericDMLQuery($sql);
                    $msg['hexCode'] = $imgData;
                    $msg['swatchImage'] = '';

                }
            } catch (Exception $e) {
                $msg = array('Caught exception:' => $e->getMessage());
            }
            if ($param == 'add') {
                return $msg;exit();
            } else {
                $this->response($this->json($msg), 200);
            }
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Fetch color swatches
     *
     * @param (String)apikey
     * @return json data
     *
     */
    public function fetchColorSwatch()
    {
        $isSameClass = true;
        $colArrObj = Flight::products();
        $colorOptions = $colArrObj->getColorArr($isSameClass);
        if (!is_array($colorOptions)) {
            $colorOptions = $this->formatJSONToArray($colorOptions);
        }
        $dir = $this->getSwatchURL();
        $filePath = $this->getSwatchesPath();
        try {
            foreach ($colorOptions as $key => $value) {
                $swatchFilePath = $filePath . '/45x45/' . $value['value'] . '.png';

                if (file_exists($swatchFilePath)) {
                    $swatchFileDir = $dir . '45x45/' . $value['value'] . '.png';
                    $colorOptions[$key]['swatchImage'] = $swatchFileDir;
                    $colorOptions[$key]['hexCode'] = '';
                } else {
                    $colorOptions[$key]['swatchImage'] = '';
                    $sql = "select hex_code from " . TABLE_PREFIX . "swatches where attribute_id = '" . $value['value'] . "'";
                    $row = $this->executeGenericDQLQuery($sql);
                    $colorOptions[$key]['hexCode'] = $row[0]['hex_code'];
                }
                $colorOptions[$key]['width'] = 45;
                $colorOptions[$key]['height'] = 45;

            }
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
        $this->response(json_encode($colorOptions), 200);
    }

}
