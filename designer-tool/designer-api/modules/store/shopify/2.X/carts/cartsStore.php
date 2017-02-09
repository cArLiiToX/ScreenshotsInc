<?php
/* Check Un-authorize Access */
if (!defined('accessUser')) {
    die("Error");
}

class CartsStore extends UTIL
{
    ####################################################
    ############## syncOrdersZip ###################
    ####################################################
    public function addToCart()
    {
        $original_mem = ini_get('memory_limit');
        $mem = substr($original_mem, 0, -1);
        if ($original_mem <= $mem) {
            $mem = $mem + 256;
            ini_set('memory_limit', $mem . 'M');
            set_time_limit(0);
        }
        $error = false;
        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];
            $isTemplate = 0;
            if (isset($this->_request['isTemplate'])) {
                $refid = $this->_request['refid'];
                $isTemplate = $this->_request['isTemplate'];
            }
            if (isset($this->_request['cartEncData'])) {
                echo "string";
                $cartData = json_decode(stripslashes($this->rcEncDec5(self::POST_KEY, $this->_request['cartEncData'])));
                $msg = array('status' => 'checking', 'cartData' => $this->_request['cartEncData']);
                $this->response($this->json($msg), 200);
                $apikey = $cartData['apikey'];
                $designData = $cartData['designData'];
                $productDataJSON = $cartData['productData'];
            } else {
                $apikey = $this->_request['apikey'];
                $designData = $this->_request['designData'];
                $productDataJSON = $this->_request['productData'];
            }
            $cartArr = json_decode($productDataJSON, true);
            if ($isTemplate == 0) {
                $refid = $this->saveDesignStateCart($apikey, $refid, $designData); // private
                if ($refid > 0) {
                    $dbstat = $this->saveProductPreviewSvgImagesOnAddToCart($apikey, $refid, $designData);
                }
            }
            $arrProducts = array();
            foreach ($cartArr as $arkey => $value) {
                $product = $this->buildProductArray($value, $refid);
                if ($product) {
                    array_push($arrProducts, $product);
                }

            }
            $quoteId = 0;
            if (isset($this->_request['quoteId'])) {
                $quoteId = intval($this->_request['quoteId']);
            } else if (isset($_COOKIE['quoteId'])) {
                $quoteId = intval($_COOKIE['quoteId']);
            }
            if ($quoteId == '' || $quoteId <= 0) {
                $quoteId = 0;
            }
            $customer = 0;
            if (isset($this->_request['customer'])) {
                $customer = intval($this->_request['customer']);
                if ($customer == 0 || $customer <= 0) {
                    $customer = 0;
                }
            }
            $action = 'add';
            ini_set('memory_limit', $original_mem);
            if (!$error) {
                foreach ($arrProducts as $ap) {
                    $ref_parts[] = $refid . '-' . $ap['simpleproduct_id'] . '-' . $ap['qty'];
                }

                //$url = 'http://' . SHOPIFY_SHOP . '/cart?view=refitem&ref=' . implode('--',$ref_parts);
                $url = 'http://' . COOKIE_DOMAIN . '/cart?view=refitem&ref=' . implode('--', $ref_parts);
                $msg = array('status' => 'success', 'url' => $url, 'refid' => $refid, 'productData' => $arrProducts);
                $url = 'http://' . SHOPIFY_SHOP . '/cart?view=refitem&ref=' . implode('--', $ref_parts);
                $this->response($this->json($msg), 200);
            } else {
                $msg = array('status' => 'failed', 'error' => json_decode($result));
                $this->response($this->json($msg), 200);
            }
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
            $this->response($this->json($msg), 200);
        }
    }
    ####################################################
    ############## addTemplateToCart ###################
    ####################################################
    public function addTemplateToCart()
    {
        $error = false;
        $contents = "";
        if (!isset($this->_request['pid']) || trim($this->_request['pid']) == '') {
            $msg = array('status' => 'invalid id', 'id' => $this->_request['pid']);
            $this->response($this->json($msg), 204); //terminate
        } else {
            $product_id = trim($this->_request['pid']);
        }
        $smplProdID = (isset($this->_request['vid']) && trim($this->_request['vid']) != '') ? trim($this->_request['vid']) : 0;
        $refid = (isset($this->_request['refid']) && trim($this->_request['refid']) != '') ? trim($this->_request['refid']) : 0;
        $quantity = (isset($this->_request['orderQty']) && trim($this->_request['orderQty']) != '') ? trim($this->_request['orderQty']) : 1;
        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];
            try {
                $productInfo = array(
                    'configId' => $product_id,
                    'smplProdID' => $smplProdID,
                    'refid' => $refid,
                    'qty' => $quantity,
                );
                $result = $this->proxy->call($key, 'cedapi_product.getProductInfo', $productInfo);
            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $error = true;
            }
            $tarray = array(" ", "\n", "\r");
            if (file_exists($this->getBasePath() . "/localsettings.js")) {
                $contents = file_get_contents($this->getBasePath() . "/localsettings.js");
                $contents = trim(str_replace($tarray, "", $contents));
                $contents = substr($contents, 0, -1);
                $contents = explode("localSettings=", $contents);
                $contents = json_decode($contents['1'], true);
            }
            $this->_request['productData'] = $result;
            $this->_request['apikey'] = $contents['api_key'];
            $this->_request['isTemplate'] = 1;
            $this->_request['refid'] = $this->_request['refid'];
            return $result = $this->addToCart();
        } else {
            echo 'Add to cart failed';exit();
        }
    }
    ####################################################
    ############## syncOrdersZip ###################
    ####################################################
    public function addToCartByRefId()
    {
        $error = false;
        if (!empty($this->_request) && $this->_request['apikey'] && $this->_request['refid'] && $this->_request['productData']) {
            $result = $this->storeApiLogin();
            if ($this->storeApiLogin == true) {
                $key = $GLOBALS['params']['apisessId'];
                $apikey = $this->_request['apikey'];
                $refid = $this->_request['refid'];
                $productDataJSON = $this->_request['productData'];
                $cartArr = json_decode($productDataJSON, true);
                if ($refid > 0) {
                    $dbstat = $this->saveProductPreviewSvgImagesOnAddToCart($apikey, $refid, $designData);
                }
                $arrProducts = array();
                foreach ($cartArr as $arkey => $value) {
                    $product = $this->buildProductArray($value, $refid);
                    if ($product) {
                        array_push($arrProducts, $product);
                    }

                }
                $quoteId = 0;
                if (isset($this->_request['quoteId'])) {
                    $quoteId = intval($this->_request['quoteId']);
                } else if (isset($_COOKIE['quoteId'])) {
                    $quoteId = intval($_COOKIE['quoteId']);
                }
                if ($quoteId == '' || $quoteId <= 0) {
                    $quoteId = 0;
                }
                $customer = 0;
                if (isset($this->_request['customer'])) {
                    $customer = intval($this->_request['customer']);
                    if ($customer == 0 || $customer <= 0) {
                        $customer = 0;
                    }
                }
                $action = 'add';
                if ($refid > 0) {
                    //$action = 'update';
                }
                if (!$error) {
                    try {
                        $filters = array(
                            'quoteId' => $quoteId,
                            'store' => 1,
                            'productsData' => $arrProducts,
                            'action' => $action, //add or update or remove
                            'customer' => $customer,
                        );
                        $result = $this->proxy->call($key, 'cedapi_cart.addToCart', $filters);
                    } catch (Exception $e) {
                        $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                        $error = true;
                    }
                }
                if (!$error) {
                    $cartInfo = array();
                    $cartInfo = json_decode($result);
                    $checkoutURL = $cartInfo->checkoutURL;
                    $url = $checkoutURL . '?quoteId=' . $cartInfo->quoteId;
                    $msg = array('status' => 'success', 'url' => $url, 'quoteId' => $cartInfo->quoteId, 'refid' => $refid);
                    if ($cartInfo->is_Fault == 1) {
                        $msg = array('status' => 'failed', 'url' => $url, 'quoteId' => $cartInfo->quoteId, 'error' => $cartInfo);
                    } else if ($cartInfo->quoteId != $quoteId) {
                        $expire = time() + 60 * 60 * 24 * 30; //30 days
                        setcookie("quoteId", $cartInfo->quoteId, $expire, "/");
                    }
                } else {
                    $msg = array('status' => 'failed', 'error' => json_decode($result));
                }
            } else {
                $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
            }
        } else {
            $msg = array('status' => 'Incomplete request');
        }
        $this->response($this->json($msg), 200);
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Build Product Array
     *
     *@param (String)apikey
     *@param (Array)cartArr
     *@param (Int)refid
     *@return Array or boolean value
     *
     */
    public function buildProductArray($cartArr, $refid)
    {
        try {
            $configProductId = $cartArr['id'];
            $custom_price = $cartArr['addedprice'];
            //$cutom_design_refId = $cartArr['refid'];
            $cutom_design_refId = $refid;
            $quantity = $cartArr['qty'];
            $simpleProductId = $cartArr['simple_product']['simpleProductId'];
            //$color1 = $cartArr['simple_product']['color1'];
            $xeColor = $cartArr['simple_product']['xe_color'];
            $xeSize = $cartArr['simple_product']['xe_size'];
            $product_data = array(
                "product_id" => $configProductId,
                "simpleproduct_id" => $simpleProductId,
                "options" => array('xe_color' => $xeColor, 'xe_size' => $xeSize),
                "custom_price" => $custom_price,
                "ref_id" => $refid,
            );
            // if ($custom_price > 0) {
            $result = $this->proxy->call($key, 'cedapi_product.addCustomProduct', $product_data);
            $product = array(
                "product_id" => $result['pid'],
                "qty" => $quantity,
                "simpleproduct_id" => $result['simpleprodID'],
                "options" => array('xe_color' => $xeColor, 'xe_size' => $xeSize),
                "custom_price" => $custom_price,
                "custom_design" => $cutom_design_refId,
            );
            // }else{
            //  $product = array(
            //                "product_id" => $configProductId,
            //                "qty" => $quantity,
            //                "simpleproduct_id" => $simpleProductId,
            //                "options"=>array('xe_color'=>$xeColor, 'xe_size'=>$xeSize),
            //                "custom_price" => $custom_price,
            //                "custom_design" => $cutom_design_refId,
            //        );
            // }
            if ($quantity > 0) {
                return $product;
            } else {
                return false;
            }

        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            return $result;
        }
    }
}
