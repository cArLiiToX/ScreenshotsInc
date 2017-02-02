<?php
/* Check Un-authorize Access */
if (!defined('accessUser')) {
    die("Error");
}

class ColorSwatchStore extends UTIL
{
    ####################################################
    ############## syncOrdersZip ###################
    ####################################################
    public function addColorSwatch()
    {
        $error = '';
        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];

            $colorname = (isset($this->_request['colorname']) && trim($this->_request['colorname']) != '')?$this->_request['colorname']:'';
            $imagename = (isset($this->_request['imagename']) && trim($this->_request['imagename']) != '')?trim($this->_request['imagename']):'';
            $imagetype = (isset($this->_request['imagetype']) && trim($this->_request['imagetype']) != '')?trim($this->_request['imagetype']):'';
            try {
                $filters = array(
                    'colorname' => $colorname,
                    'store' => $this->getDefaultStoreId(),
                );
                $result = $this->proxy->call($key, 'cedapi_product.addAttributeColorOptionValue', $filters);

                $rsultrsponse = json_decode($result, true);
                if (isset($imagename) && $imagename != '') {
                    $this->customRequest(array('value' => $option_id, 'imgData' => $imagename, 'imagetype' => $imagetype, 'swatchWidth' => 45, 'swatchHeight' => 45, 'base64Data' => base64_decode($imagename)));
                    $saveSucss = $this->saveColorSwatch('add');
                    $rsultrsponse['swatchImage'] = $saveSucss['swatchImage'];
                    $rsultrsponse['hexCode'] = $saveSucss['hexCode'];
                }
                $result = json_encode($rsultrsponse);
            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $error = true;
            }
            if (!$error) {
                print_r($result);exit();
            } else {
                print_r(json_decode($result));exit();
            }
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
            $this->response($this->json($msg), 200);
        }
    }
    ####################################################
    ############## syncOrdersZip ###################
    ####################################################
    public function editSwachColor()
    {
        $error = '';
        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];

            $imagename = (isset($this->_request['imagename']) && trim($this->_request['imagename']) != '')?trim($this->_request['imagename']):'';
            $colorname = (isset($this->_request['colorname']) && trim($this->_request['colorname']) != '')?$this->_request['colorname']:'';
            if (isset($this->_request['option_id']) && trim($this->_request['option_id']) != '') {
                $option_id = $this->_request['option_id'];
            }
            if (isset($this->_request['imagetype']) && trim($this->_request['imagetype']) != '') {
                $imagetype = trim($this->_request['imagetype']);
            }
            try {
                $filters = array(
                    'option_id' => $option_id,
                    'colorname' => $colorname,
                    'store' => $this->getDefaultStoreId(),
                );
                $result = $this->proxy->call($key, 'cedapi_product.editAttributeColorOptionValue', $filters);
                $rsultrsponse = json_decode($result, true);
                if (isset($imagename) && $imagename != '') {
                    $this->customRequest(array('value' => $option_id, 'imgData' => $imagename, 'imagetype' => $imagetype, 'swatchWidth' => 45, 'swatchHeight' => 45, 'base64Data' => base64_decode($imagename)));
                    $saveSucss = $this->saveColorSwatch('add');
                    $rsultrsponse['swatchImage'] = $saveSucss['swatchImage'];
                    $rsultrsponse['hexCode'] = $saveSucss['hexCode'];
                }
                $result = json_encode($rsultrsponse);
            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $error = true;
            }
            if (!$error) {
                print_r($result);exit();
            } else {
                print_r(json_decode($result));exit();
            }
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
            $this->response($this->json($msg), 200);
        }
    }
}
