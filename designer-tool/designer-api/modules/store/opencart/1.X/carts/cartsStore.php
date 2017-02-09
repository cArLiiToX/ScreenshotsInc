<?php
/* Check Un-authorize Access */
if (!defined('accessUser')) {
    die("Error");
}

class CartsStore extends UTIL
{
    public function __construct()
    {
        parent::__construct();
        $this->datalayer = new Datalayer();
        $this->helper = new Helper();
    }

    /**
     * Add customized product to cart
     *
     * @param   product information
     * @return  boolean true/false
     */

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
            if (isset($this->_request['cartEncData'])) {
                $cartData = json_decode($this->rcEncDec5(self::POST_KEY, $this->_request['cartEncData']));
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
            $refid = $this->saveDesignStateCart($apikey, $refid, $designData);
            if ($refid > 0) {
                $dbstat = $this->saveProductPreviewSvgImagesOnAddToCart($apikey, $refid, $designData);
            }
            if ($dbstat == 'insertSuccess') {
                $result = array();
                try {
                    session_start();
                    if (isset($_SESSION)) {
                        $session_id = '';
                        foreach ($_SESSION as $key => $value) {
                            if ($session_id == '' && $key != 'default') {
                                $session_id = $key;
                            } else {
                                $session_id = session_id();
                            }

                        }
                    }
                    $productdata = array();
                    $i = 0;
                    $j = 0;
                    foreach ($cartArr as $cart) {
                        if ($cart['qty'] > 0) {
                            $id = (isset($cart['simple_product']['simpleProductId']) && $cart['simple_product']['simpleProductId'] != $cart['id']) ? $cart['simple_product']['simpleProductId'] : $cart['id'];
                            $options = $this->datalayer->getProductRelatedOptions($id);
                            $productOptions = array();
                            foreach ($options as $option) {
                                $optionName = $option['name'];
                                if ($optionName == 'refid') {
                                    $productOptions[$option['id']] = $refid;
                                } elseif ($optionName != 'xe_is_design') {
                                    $productOptions[$option['id']] = $this->datalayer->getProductOptionValue($cart['simple_product'][$optionName], $option['id']);
                                }

                            }

                            $productdata[$j]['id'] = $id;
                            $productdata[$j]['qty'] = $cart['qty'];
                            $productdata[$j]['options'] = $productOptions;
                            $productdata[$j]['refid'] = $refid;
                            $productdata[$j]['extra_price'] = $cart['addedprice'];
                            $productdata[$j]['session_id'] = $session_id;
                            $j++;
                        }
                        $i++;
                    }
                    $status = $this->helper->addToCart((object) $productdata);
                    ini_set('memory_limit', $original_mem);
                    if ($status) {
                        $url = HTTP_SERVER . '?route=checkout/cart';
                        $result = array('status' => 'success', 'url' => $url, 'quoteId' => 0, 'refid' => $refid);
                    } else {
                        $result = array('error' => 'Cart is empty please check your input parameters.', 'error_no' => $status);
                    }

                } catch (Exception $e) {
                    $this->log('Exception ::' . $e->getMessage(), true, 'logc.log');
                    $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                    $error = true;
                }
            } else {
                $error = true;
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $dbstat));
            }
            if ($error) {
                $result = array('status' => 'failed', 'error' => json_decode($result));
            }
            $this->response($this->json($result), 200);
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
            $this->response($this->json($msg), 200);
        }
    }

    /**
     * Add customized product to cart
     *
     * @param   product information
     * @return  boolean true/false
     */

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
                    $magentURL = $GLOBALS['params']['magento_url'];
                    if (!$this->endsWith($magentURL, "/")) {
                        $magentURL .= '/';
                    }

                    $url = $magentURL . 'checkout/cart?quoteId=' . $cartInfo->quoteId;

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
            $product = array(
                "product_id" => $configProductId,
                "qty" => $quantity,
                "simpleproduct_id" => $simpleProductId,
                "options" => array('xe_color' => $xeColor, 'xe_size' => $xeSize),
                "custom_price" => $custom_price,
                "custom_design" => $cutom_design_refId,
            );
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
     *Method to get the total cart item's
     *
     *@param none
     *@return json string
     *
     */
    public function getTotalCartItem()
    {
        $cartCount = $this->datalayer->getTotalCartItem();
        $msg = array("is_Fault" => 0, "totalCartItem" => $cartCount, "checkoutURL" => HTTP_SERVER . "?route=checkout/cart");
        $this->response($this->json($msg), 200);
    }
}
