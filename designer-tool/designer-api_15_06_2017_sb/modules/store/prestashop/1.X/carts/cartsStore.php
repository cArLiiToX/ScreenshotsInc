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
    }
    /**
     * Add customized product to cart
     *
     * @param   product information
     * @return  boolean true/false
     *
     */
    public function addToCart()
    {
        // Set Memory limit //
        $original_mem = ini_get('memory_limit');
        $mem = substr($original_mem, 0, -1);
        if ($original_mem <= $mem) {
            $mem = $mem + 256;
            ini_set('memory_limit', $mem . 'M');
            set_time_limit(0);
        }
        $error = false;
        $result = $this->storeApiLogin();
        // check ApiLogin //
        if ($this->storeApiLogin == true) {
            $isTemplate = 0;
            if (isset($this->_request['isTemplate'])) {
                $refid = $this->_request['refid'];
                $isTemplate = $this->_request['isTemplate'];
            }
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
            if ($isTemplate == 0) {
                $refid = $this->saveDesignStateCart($apikey, $refid, $designData);
                if ($refid > 0) {
                    $dbstat = $this->saveProductPreviewSvgImagesOnAddToCart($apikey, $refid, $designData);
                }
            }
            if ($dbstat == 'insertSuccess' || $isTemplate) {
                // If success //
                $result = array();
                try {
                    session_start();
                    $productdata = array();
                    $i = 0;
                    $j = 0;
                    // loop the cart cartArr //
                    foreach ($cartArr as $cartArrs) {
                        // Initialization of variables //
                        $addToCart_params = array();
                        $addToCart_params['id'] = $cartArrs['id'];
                        $addToCart_params['custom_fields'] = "";
                        $addToCart_params['id_product_attribute'] = $cartArrs['simple_product']['simpleProductId'];
                        $addToCart_params['quantity'] = $cartArrs['qty'];
                        $addToCart_params['ref_id'] = $refid;
                        $addToCart_params['addedprice'] = $cartArrs['addedprice'];

                        //print_r($addToCart_params);
                        // Add to Cart for storing data to DB //
                        if ($addToCart_params['quantity'] > 0) {
                            $status = $this->datalayer->addProductToCart($addToCart_params);
                        }
                    }
                    ini_set('memory_limit', $original_mem);
                    if ($status['status'] == 'success') {
                        $url = $this->getCurrentUrl();
                        $str = substr($url, -1);
                        if ($str == '/') {
                            $url = $this->getCurrentUrl() . 'order';
                        } else {
                            $url = $this->getCurrentUrl() . '/order';
                        }
                        $result = array('status' => 'success', 'url' => $status['url'], 'quoteId' => 0, 'refid' => $refid);
                    } else {
                        $result = array('error' => 'Cart is empty please check your input parameters.', 'error_no' => $status['status']);
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
     * Add predeco product in to store
     *
     * @param   (Int)vid
     * @param   (Int)pid
     * @param   (Int)refid
     * @param   (Int)orderQty
     * @return  String/Array
     */
    public function addTemplateToCart()
    {
        $error = false;
        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            try {
                $productInfo = array(
                    'configId' => $this->_request['vid'],
                    'smplProdID' => $this->_request['pid'],
                    'refid' => $this->_request['refid'],
                    'qty' => $this->_request['orderQty'],
                );
                $result = $this->datalayer->getProductInfo($productInfo);
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
            echo 'Add to cart failed';
        }
    }
    /**
     * Get no of cart item from store
     *
     * @param   nothing
     * @return  Array
     *
     */
    public function getTotalCartItem()
    {
        $store_api_result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            try {
                $cart_result = $this->datalayer->getTotalCartItem();
                $this->response($this->json($cart_result), 200);
            } catch (Exception $e) {
                $cart_result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $this->response($cart_result, 200);
            }
        }
    }
}
