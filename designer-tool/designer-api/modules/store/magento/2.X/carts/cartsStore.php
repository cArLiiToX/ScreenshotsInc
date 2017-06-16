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
            $mem = $mem + 1024;
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
            $designData = urldecode($designData);
            $productDataJSON = urldecode($productDataJSON);
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
            if ($refid > 0) //$action = 'update';
            {
                if (!$error) {
                    try {
                        $filters = array(
                            'quoteId' => $quoteId,
                            'store' => $this->getDefaultStoreId(),
                            'productsData' => json_encode($arrProducts),
                            'action' => $action,
                        );
                        $result = $this->apiCall('Cart', 'addToCart', $filters);
                        $result = $result->result;

                    } catch (Exception $e) {
                        $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                        $error = true;
                    }
                }
            }

            ini_set('memory_limit', $original_mem);
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
            $cutom_design_refId = $refid;
            $quantity = $cartArr['qty'];
            $totalQty = $cartArr['totalQty'];
            $simpleProductId = $cartArr['simple_product']['simpleProductId'];
            if(!empty($cartArr['simple_product']['xe_color'])){
                $xeColor = $this->getStoreAttributes("xe_color");
                if($xeColor != 'xe_color'){
                    $cartArr['simple_product'][$xeColor] = $cartArr['simple_product']['xe_color'];
                    unset($cartArr['simple_product']['xe_color']);
                }
            }
            if(!empty($cartArr['simple_product']['xe_size'])){
                $xeSize = $this->getStoreAttributes("xe_size");
                if($xeSize != 'xe_size'){
                    $cartArr['simple_product'][$xeSize] = $cartArr['simple_product']['xe_size'];
                    unset($cartArr['simple_product']['xe_size']);
                }
            }
            $product = array(
                "product_id" => $configProductId,
                "qty" => $quantity,
                "totalQty" => $totalQty,
                "simpleproduct_id" => $simpleProductId,
                "options" => array(),
                "custom_price" => $custom_price,
                "custom_design" => $cutom_design_refId,
            );
            foreach ($cartArr['simple_product'] as $key => $value) {
                if ($cartArr['simple_product'][$key]) {
                    $product['options'][$key] = $cartArr['simple_product'][$key];
                }

            }
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

    /**
     *
     *date created 07-06-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *fetch data before add to cart
     *
     * @param (int)confProductId
     * @param (int)xe_size
     * @param (int)xe_color
     * @param (int)qty
     * @param (int)qty
     *
     */
    public function addTemplateToCart($confProductId, $xe_size, $xe_color, $qty, $refId)
    {
        $error = false;
        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];
            try {
                $productInfo = array(
                    'configId' => $confProductId,
                    'colorId' => $xe_color,
                    'sizeId' => $xe_size,
                    'qty' => $qty,
                    'color' => $this->getStoreAttributes("xe_color"),
                    'size' => $this->getStoreAttributes("xe_size")
                );
                $result = $this->apiCall('Product', 'getProductInfo', $productInfo);
                $result = $result->result;
                // $result    = $this->proxy->call($key,'cedapi_product.getProductInfo', $productInfo);
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
            $this->_request['refid'] = $refId;
            return $result = $this->addToCart();
        } else {
            echo 'Add to cart failed';
        }
    }
    /**
     *
     *date created 27-12-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *get item present in cart
     *@param (int)customerId
     */
    public function getTotalCartItem()
    {
        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];
            $customer = $this->_request['customerId'];
            if (isset($customer)) {
                $customerId = $customer;
            } else {
                $customerId = 0;
            }
            if (isset($_COOKIE['quoteId'])) {
                $quoteId = intval($_COOKIE['quoteId']);
            } else {
                $quoteId = 0;
            }
            try {
                $param = array(
                    'quoteId' => $quoteId,
                    'store' => $this->getDefaultStoreId(),
                    'customerId' => $customerId,
                );
                $result = $this->apiCall('Cart', 'getTotalCartItem', $param);
                $result = $result->result;
                $this->response($result, 200);
            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $this->response($result, 200);
            }
        }
    }
}
