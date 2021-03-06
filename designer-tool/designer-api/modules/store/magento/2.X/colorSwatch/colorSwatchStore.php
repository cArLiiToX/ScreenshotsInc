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
                    'colorname' => $colorname,
                    'store' => $this->getDefaultStoreId(),
                    'color' => $this->getStoreAttributes("xe_color"),
                );
                $result = $this->apiCall('Product', 'addAttributeColorOptionValue', $filters);
                $result = $result->result;
                $rsultrsponse = json_decode($result, true);
				$option_id = $rsultrsponse['attribute_id'];
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
        $swatches = array();
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
                if (isset($imagename) && $imagename != '') {
                    $this->customRequest(array('value' => str_replace(' ', '_', $option_id), 'imgData' => $imagename, 'imagetype' => $imagetype, 'swatchWidth' => 45, 'swatchHeight' => 45, 'base64Data' => base64_decode($imagename)));
                    $saveSucss = $this->saveColorSwatch('add');
                    $rsultrsponse['attribute_id'] = str_replace(' ', '_', $option_id);
                    $rsultrsponse['attribute_value'] = str_replace(' ', '_', $colorname);
                    $rsultrsponse['status'] = 'success';
                    $rsultrsponse['swatchImage'] = $saveSucss['swatchImage'];
                    $rsultrsponse['hexCode'] = $saveSucss['hexCode'];
                    // update swatch image details
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
    /**
     * Used to get all the xe_color inside shopify
     *
     * @param   nothing
     * @return  array contains all the xe_color inside store
     */
    public function getColorArr($isSameClass = false)
    {
        $error = '';
        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];
            try {
                $filter = array('lastLoaded' => $this->_request['lastLoaded'], 'loadCount' => $this->_request['loadCount'], 'store' => $this->getDefaultStoreId(), 'productId' => $this->_request['productId']);
                $result = $this->apiCall('Product', 'getColorArr', $filter);
                $result = $result->result;
            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $error = true;
            }
            if (!$error) {
                if ($isSameClass) {
                    return $result;
                } else {
                    print_r($result);
                }
            } else {
                print_r(json_decode($result));
            }
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
            $this->response($this->json($msg), 200);
        }
    }
}
