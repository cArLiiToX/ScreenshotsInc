<?php
/* Check Un-authorize Access */
if (!defined('accessUser')) {
    die("Error");
}

class ColorSwatchStore extends UTIL
{
    public function __construct()
    {
        parent::__construct();
        $this->datalayer = new Datalayer();
        $this->helper = new Helper();
    }

    /**
     * Add color swatch to the color
     *
     * @param   color name, image
     * @return  boolean true/false
     */
    public function addColorSwatch()
    {
        $error = '';
        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];

            if (isset($this->_request['colorname']) && trim($this->_request['colorname']) != '') {
                $colorname = $this->_request['colorname'];
            }
            if (isset($this->_request['imagename']) && trim($this->_request['imagename']) != '') {
                $imagename = trim($this->_request['imagename']);
            }
            if (isset($this->_request['imagetype']) && trim($this->_request['imagetype']) != '') {
                $imagetype = trim($this->_request['imagetype']);
            }
            try {
                $result = $this->datalayer->addColor($colorname);
                $rsultrsponse = json_decode($result, true);
                if (isset($imagename) && $imagename != '') {
                    $swatchObj = Flight::colorSwatch();
                    $swatchObj->customRequest(array('value' => $rsultrsponse['attribute_id'], 'imgData' => $imagename, 'imagetype' => $imagetype, 'swatchWidth' => 45, 'swatchHeight' => 45, 'base64Data' => base64_decode($imagename)));
                    $saveSucss = $swatchObj->saveColorSwatch('add');
                    if ($saveSucss['status'] == 'success') {
                        $file = explode("45x45/", $saveSucss['swatchImage']);
                        $filename = $file[1];
                        $path = dirname(__FILE__) . '/../../../image/catalog/swatchImage';
                        $filedst = $path . '/' . $filename;
                        if (!file_exists($path)) {
                            mkdir($path, 0777, true);
                            chmod($path, 0777);
                        }
                        $swatchPath = $this->getSwatchesPath();
                        $swatchFileDstPath = $swatchPath . '/45x45/' . $filename;
                        @copy($swatchFileDstPath, $filedst);
                        $res = $this->datalayer->addColorImage($rsultrsponse['attribute_id'], $filename);
                    }
                    $rsultrsponse['swatchImage'] = $saveSucss['swatchImage'];
                    $rsultrsponse['hexCode'] = $saveSucss['hexCode'];

                }
                $result = json_encode($rsultrsponse);

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
     * Edit color swatch of a color
     *
     * @param   option_id, color name, image
     * @return  boolean true/false
     */
    public function editSwachColor()
    {
        $error = '';
        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];

            if (isset($this->_request['option_id']) && trim($this->_request['option_id']) != '') {
                $option_id = $this->_request['option_id'];
            }
            if (isset($this->_request['colorname']) && trim($this->_request['colorname']) != '') {
                $colorname = $this->_request['colorname'];
            }
            if (isset($this->_request['imagename']) && trim($this->_request['imagename']) != '') {
                $imagename = trim($this->_request['imagename']);
            }
            if (isset($this->_request['imagetype']) && trim($this->_request['imagetype']) != '') {
                $imagetype = trim($this->_request['imagetype']);
            }
            try {
                $filters = array(
                    'option_id' => $option_id,
                    'colorname' => $colorname,
                );
                //$result = $this->datalayer->editColor($filters);
                //$rsultrsponse = json_decode($result,true);
                $rsultrsponse = array();
                $rsultrsponse['attribute_id'] = $option_id;
                $rsultrsponse['attribute_value'] = $colorname;
                $rsultrsponse['status'] = 'success';
                $rsultrsponse['swatchImage'] = '';
                if (isset($imagename) && $imagename != '') {
                    $swatchObj = Flight::colorSwatch();
                    $swatchObj->customRequest(array('value' => $option_id, 'imgData' => $imagename, 'imagetype' => $imagetype, 'swatchWidth' => 45, 'swatchHeight' => 45, 'base64Data' => base64_decode($imagename)));
                    $saveSucss = $swatchObj->saveColorSwatch('add');
                    if ($saveSucss['status'] == 'success') {
                        $file = explode("45x45/", $saveSucss['swatchImage']);
                        $filename = $file[1];
                        $path = dirname(__FILE__) . '/../../../image/catalog/swatchImage';
                        $filedst = $path . '/' . $filename;
                        if (!file_exists($path)) {
                            mkdir($path, 0777, true);
                            chmod($path, 0777);
                        }
                        $swatchPath = $this->getSwatchesPath();
                        $swatchFileDstPath = $swatchPath . '/45x45/' . $filename;
                        @copy($swatchFileDstPath, $filedst);
                        $res = $this->datalayer->addColorImage($option_id, $filename);
                    }
                    $rsultrsponse['swatchImage'] = $saveSucss['swatchImage'];
                    $rsultrsponse['hexCode'] = $saveSucss['hexCode'];
                }
                $result = json_encode($rsultrsponse);

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
}
