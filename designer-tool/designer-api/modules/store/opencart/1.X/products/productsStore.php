<?php
/* Check Un-authorize Access */
if (!defined('accessUser')) {
    die("Error");
}

class ProductsStore extends UTIL
{
    public function __construct()
    {
        parent::__construct();
        $this->datalayer = new Datalayer();
        $this->helper = new Helper();
    }

    /**
     * Used to get all the xe_size inside magento
     *
     * @param   nothing
     * @return  array contains all the xe_size inside store
     */

    public function getSizeArr()
    {
        $error = '';
        $result = $this->storeApiLogin();
		$size = $this->getStoreAttributes("xe_size");
        $filter = array("filter_name" => $size);
        if ($this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];
            try {
                $result = $this->datalayer->getOptions($filter);
                //$result = $proxy->call($key, 'catalog_category.tree');
            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $error = true;
            }
            if (!$error) {
                $categories = array();
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
     * Used to get all the xe_color inside magento
     *
     * @param   nothing
     * @return  array contains all the xe_color inside store
     */
    public function getColorArr($isSameClass = false)
    {
        $error = '';
        $result = $this->storeApiLogin();
        $productId = 0;
        if (!empty($this->_request['productId'])) {
            $productId = $this->_request['productId'];
        }
		$color = $this->getStoreAttributes("xe_color");
        $lastLoaded = ($this->_request['lastLoaded']) ? $this->_request['lastLoaded'] : 0;
        $loadCount = ($this->_request['loadCount']) ? $this->_request['loadCount'] : 0;
        $filter = array("filter_name" => $color, "lastLoaded" => $lastLoaded, "loadCount" => $loadCount, 'oldConfId' => $productId);
        if ($this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];
            try {
                $result = $this->datalayer->getOptions($filter);
            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $error = true;
            }
            if (!$error) {
                if ($isSameClass) {
                    return $result;
                } else {
                    $this->response($result, 200);
                }

            } else {
                $this->response(json_decode($result), 200);
            }
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
            $this->response($this->json($msg), 200);
        }
    }
    /**
     * Used to get all products which are eligible to customize
     *
     * @param   $categoryid, $searchstring, $start, $limit, $loadVariants (To filter the product list)
     * @return  list of products which are eligible to customize
     */

    public function getAllProducts()
    {
        $limit = 10;
        $start = 0;
        $categoryid = 0;
        $searchstring = '';
        $productsCat = '';
        $loadVariants = false;
        $startvar = 0;
        $limitvar = 50;
        if (isset($this->_request['categoryid']) && trim($this->_request['categoryid']) != '') {
            $categoryid = trim($this->_request['categoryid']);
        }
        if (isset($this->_request['searchstring']) && trim($this->_request['searchstring']) != '') {
            $searchstring = trim($this->_request['searchstring']);
        }

        if (isset($this->_request['start']) && trim($this->_request['start']) != '') {
            $start = trim($this->_request['start']);
        }
        if (isset($this->_request['range']) && trim($this->_request['range']) != '') {
            $limit = trim($this->_request['range']);
        }
        if (isset($this->_request['loadVariants']) && trim($this->_request['loadVariants']) == true) {
            $loadVariants = true;
        }
        $error = false;
        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            try {
                $result = $this->datalayer->getAllProducts((object) $this->_request, $categoryid);
                $sql = "SELECT distinct pm.pk_id as printid,pm.name as printName
                        FROM " . TABLE_PREFIX . "print_method pm
                        JOIN " . TABLE_PREFIX . "print_setting  pst ON pm.pk_id=pst.pk_id
                        LEFT JOIN " . TABLE_PREFIX . "print_method_setting_rel pmsr ON pst.pk_id=pmsr.print_setting_id where pst.is_default=1";
                $default_id = $this->executeFetchAssocQuery($sql);
                if (!empty($result['product'])) {
                    foreach ($result['product'] as $k => $product) {
                        $productPrintTypeSql = "SELECT distinct pm.pk_id, pm.name FROM " . TABLE_PREFIX . "print_method pm
                        INNER JOIN " . TABLE_PREFIX . "product_printmethod_rel ppr ON ppr.print_method_id=pm.pk_id
                        WHERE ppr.product_id=" . $product['id'];
                        $productPrintType = $this->executeGenericDQLQuery($productPrintTypeSql);

                        if (!empty($productPrintType)) {
                            //$this->log('productPrintTypeSql: '.$productPrintTypeSql, true, 'Zsql.log');
                            foreach ($productPrintType as $k2 => $v2) {
                                $product['print_details'][$k2]['prntMthdId'] = $v2['pk_id'];
                                $product['print_details'][$k2]['prntMthdName'] = $v2['name'];
                            }
                        } else {
                            $catIds = $product['category'];
                            $catIds = !empty($catIds) ? implode(',', (array) $catIds) : 0;
                            $catSql = 'SELECT DISTINCT pm.pk_id, pm.name
                                    FROM ' . TABLE_PREFIX . 'product_category_printmethod_rel AS pcpml
                                    JOIN ' . TABLE_PREFIX . 'print_method AS pm ON pm.pk_id = pcpml.print_method_id WHERE pcpml.product_category_id IN(' . $catIds . ')';
                            $rows = $this->executeFetchAssocQuery($catSql);
                            if (empty($rows)) {
                                $default_print_type = "SELECT pmsr.print_method_id,pm.name FROM " . TABLE_PREFIX . "print_method_setting_rel AS pmsr JOIN " . TABLE_PREFIX . "print_setting ps ON pmsr.print_setting_id=ps.pk_id JOIN " . TABLE_PREFIX . "print_method AS pm ON pmsr.print_method_id=pm.pk_id WHERE ps.is_default='1' AND pm.is_enable='1' LIMIT 1";
                                $res = $this->executeFetchAssocQuery($default_print_type);
                                $product['print_details'][0]['prntMthdId'] = $res[0]['print_method_id'];
                                $product['print_details'][0]['prntMthdName'] = $res[0]['name'];
                            } else {
                                foreach ($rows as $k1 => $v1) {
                                    $product['print_details'][$k1]['prntMthdId'] = $v1['pk_id'];
                                    $product['print_details'][$k1]['prntMthdName'] = $v1['name'];
                                }
                            }
                        }
                        $result['product'][$k] = $product;
                    }
                }
            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $error = true;
            }
            if (!$error) {
                $this->response($this->json($result,1), 200);
            } else {
                $msg = array('status' => 'failed', 'error' => json_decode($result));
                $this->response($this->json($msg), 200);
            }
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
            $this->response($this->json($msg), 200);
        }
    }

    /**
     * Check whether the given sku exists or doesn't
     *
     * @param   $sku_arr
     * @return  true/false
     */
    public function checkDuplicateSku()
    {
// chk for storeid
        $error = false;
        $result = $this->storeApiLogin();
        if (!empty($this->_request) && $this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];
            if (!$error) {
                $filters = array(
                    'sku_arr' => $this->_request['sku_arr'],
                );

                try {
                    $result = $this->json(array()); //array("status"=>"failed");//$this->proxy->call($key, 'cedapi_product.checkDuplicateSku', $filters);
                } catch (Exception $e) {
                    $result = json_encode(array('isFault inside apiv4: ' => 1, 'faultMessage' => $e->getMessage()));
                    $error = true;
                }
            }
            $this->closeConnection();
            $this->response($result, 200);
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
            $this->response($this->json($msg), 200);
        }
    }

    /**
     * Check whether xetool is enabled or disabled
     *
     * @param   nothing
     * @return  true/false
     */
    public function checkDesignerTool($t = 0)
    {
        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];
            try {
                $url = $this->getCurrentUrl() . '/qvcheck.php';
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                //  curl_setopt($ch,CURLOPT_HEADER, false);

                $version = curl_exec($ch);
                curl_close($ch);
                $path = $this->getCurrentUrl() . '/vqmod/xml/Riaxe_Product_Designer.xml';
                $status = is_array(@get_headers($path));
                if ($version == 'VQMOD ALREADY INSTALLED!' && $status) {
                    $result = 'Enabled';
                } else {
                    $result = 'Disabled';
                }

            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
            }
            if ($t) {
                return $result;
            } else {
                $this->response($result, 200);
            }

        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
            $this->response($this->json($msg), 200);
        }
    }

    /**
     * Get the list of variants available for a product
     *
     * @param   nothing
     * @return  json list of variants
     */
    public function getVariantList()
    {
        $error = false;
        $resultArr = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];

            try {
                $confId = $this->_request['conf_pid'];
                $filters = array('confId' => $confId);
                //$resultArr = $this->proxy->call($key, 'cedapi_product.getVariantList',$filters);
                $pvariant = $this->datalayer->getProductInfo($confId);
                $resultArr = array("conf_id" => $confId, "variants" => $pvariant);
            } catch (Exception $e) {
                $resultArr = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $error = true;
            }

            if (!$error) {
                $this->response($this->json($resultArr), 200);
            } else {
                $this->response(json_decode($resultArr), 200);
            }
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($resultArr));
            $this->response($this->json($msg), 200);
        }
    }

    /**
     * Get the list of variants available for a product
     *
     * @param   nothing
     * @return  json list of variants
     */
    public function getVariants()
    {
        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            try {
                $confId = $this->_request['conf_pid'];
                $result = $this->datalayer->getVariants((object) $this->_request);
                foreach ($result['variants'] as $key => $value) {
                    $surplusPrice = $result['variants'][$key]['price'];
                    $sql = "SELECT ref_id,parent_id FROM " . TABLE_PREFIX . "template_state_rel WHERE temp_id = " . $confId;
                    $parentId = $this->executeFetchAssocQuery($sql);
                    if (!empty($parentId)) {
                        $sql = "SELECT custom_price FROM " . TABLE_PREFIX . "decorated_product WHERE product_id = " . $parentId[0]['parent_id'] . " and refid = " . $parentId[0]['ref_id'];
                        $res = $this->executeFetchAssocQuery($sql);
                        $customPrice = $res[0]['custom_price'];
                        $result['variants'][$key]['price'] = $surplusPrice - $customPrice;
                        $result['variants'][$key]['finalPrice'] = $surplusPrice;
                    }
                    $colorId = $result['variants'][$key]['xe_color_id'];
                    $sqlSwatch = "SELECT  hex_code,image_name FROM " . TABLE_PREFIX . "swatches WHERE attribute_id='" . $colorId . "'";
                    $res = $this->executeFetchAssocQuery($sqlSwatch);
                    if ($res) {
                        if ($res[0]['hex_code']) {
                            $colorSwatch = $res[0]['hex_code'];
                        } else {
                            $imageName = $res[0]['image_name'];
                            $swatchWidth = '45';
                            $swatchDir = $this->getSwatchURL();
                            $colorSwatch = $swatchDir . $swatchWidth . 'x' . $swatchWidth . '/' . $imageName;
                        }
                    } else {
                        $colorSwatch = '';
                    }

                    $result['variants'][$key]['colorUrl'] = $colorSwatch;
                }
            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $error = true;
            }
            if (!$error) {
                $this->response($this->json($result), 200);
            } else {
                $msg = array('status' => 'failed', 'error' => json_decode($result));
                $this->response($this->json($msg), 200);
            }
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
            $this->response($this->json($msg), 200);
        }
    }

    /**
     * Get the list of available size and their quantity of a product
     *
     * @param   nothing
     * @return  json list of size and their quantity
     */

    public function getSizeAndQuantity()
    {
        $error = '';
        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            if (!isset($this->_request['productId']) || trim($this->_request['productId']) == '') {
                //$product_id = '';
                $msg = array('status' => 'invalid productId', 'productId' => $this->_request['productId']);
                $this->response($this->json($msg), 204);
            } else {
                $product_id = trim($this->_request['productId']);
            }
            if (isset($this->_request['byAdmin'])) {
                $byAdmin = true;
            } else {
                $byAdmin = false;
            }
            try {
               /*  $resultArr = (!$byAdmin) ? $this->datalayer->getSizeAndQuantity((object) $this->_request) : $this->datalayer->getSizeVariants((object) $this->_request); */
				$resultArr = $this->datalayer->getSizeAndQuantity((object) $this->_request);
                foreach ($resultArr['quantities'] as $key => $value) {
                    $surplusPrice = $resultArr['quantities'][$key]['price'];
                    $sql = "SELECT ref_id,parent_id FROM " . TABLE_PREFIX . "template_state_rel WHERE temp_id = " . $product_id;
                    $parentId = $this->executeFetchAssocQuery($sql);
                    if (!empty($parentId)) {
                        $sql = "SELECT custom_price FROM " . TABLE_PREFIX . "decorated_product WHERE product_id = " . $parentId[0]['parent_id'] . " and refid = " . $parentId[0]['ref_id'];
                        $res = $this->executeFetchAssocQuery($sql);
                        $customPrice = $res[0]['custom_price'];
                        $resultArr['quantities'][$key]['price'] = $surplusPrice - $customPrice;
                        $resultArr['quantities'][$key]['finalPrice'] = $surplusPrice;
                    }
                    $tier = array();
                    $tierPrice = $this->datalayer->getTierPrice($resultArr['quantities'][$key]['simpleProductId']);
                    if (!empty($tierPrice)) {
                        foreach ($tierPrice as $k => $value) {
                            $tier[$k]['tierQty'] = (int) $value['quantity'];
                            $tier[$k]['percentage'] = round(100 - $value['price'] / $surplusPrice * 100);
                            $tier[$k]['tierPrice'] = number_format($value['price'], 2);
                        }
                    }
                    $resultArr['quantities'][$key]['tierPrices'] = $tier;
                }
                $result = $this->json($resultArr);
            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $error = true;
            }
            $this->closeConnection();
            $this->response($result, 200);
        } else {
            $msg = array('status' => 'failed', 'error' => json_decode($result));
            $this->response($this->json($msg), 200);
        }
    }

    /**
     * Update item print status
     *
     * @param   orderID, productID, orderItemId, refid
     * @return  true/false
     */

    public function updateItemPrintStatus()
    {
        $error = false;

        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            $order_product_id = trim($this->_request['order_product_id']);
            $url = $this->getCurrentUrl() . '/?route=' . $this->extensionPath() . 'feed/web_api/updatePrintStatus&order_product_id=' . $order_product_id;
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $output = curl_exec($ch);

            curl_close($ch);
            $this->response($output, 200);

        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
            $this->response($this->json($msg), 200);
        }
    }

    /**
     * Add Product to store
     *
     * @param   product information
     * @return  product id,name in json format
     */
    public function addProducts()
    {
        $error = false;
        $result = $this->storeApiLogin();
        if (!empty($this->_request) && $this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];
            if (!$error) {
                try {
                    $result = $this->datalayer->addProduct(json_encode($this->_request, true));
                } catch (Exception $e) {
                    $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                    $error = true;
                }
            }
            if ($result['status'] == 'success') {
                $this->response($this->json($result), 200);
            } else {
                $this->response(json_decode($result), 200);
            }
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
            $this->response($this->json($msg), 200);
        }
    }

    /**
     *
     *date created 31-05-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *Add template as product
     *
     *
     */
    public function addTemplateProducts()
    {
        $error = false;
        if (!empty($this->_request['data'])) {
            $data = json_decode(urldecode($this->_request['data']), true);
            $apikey = $this->_request['apikey'];
            $result = $this->storeApiLogin();
            if ($this->storeApiLogin == true) {
                $key = $GLOBALS['params']['apisessId'];
                if (!$error) {
                    try {
                        $arr = array('store' => $this->getDefaultStoreId(), 'data' => $data, 'configFile' => $data['images'], 'oldConfId' => $data['simpleproduct_id'], 'varColor' => $data['color_id'], 'varSize' => $data['sizes']);
                        $result = $this->datalayer->addTemplateProducts($arr);
                        $resultData = json_decode($result, true);
                        $this->_request['productid'] = $data['simpleproduct_id'];
                        $this->_request['isTemplate'] = 1;
                        $sides = sizeof($data['images']);
                        $productTemplate = $this->getProductTemplateByProductId($data['simpleproduct_id']);
                        $maskData = $this->getMaskData($sides);
                        $maskData = json_decode($maskData, true);
                        $printArea = array();
                        $printArea = $this->getPrintareaType($data['simpleproduct_id']);
                        $this->customRequest(array('maskScalewidth' => $maskData[0]['mask_width'], 'maskScaleHeight' => $maskData[0]['mask_height'], 'maskPrice' => $maskData[0]['mask_price'], 'scaleRatio' => $maskData[0]['scale_ratio'], 'scaleRatio_unit' => $maskData[0]['scaleRatio_unit'], 'maskstatus' => $printArea['mask'], 'unitid' => $printArea['unit_id'], 'pricePerUnit' => $printArea['pricePerUnit'], 'maxWidth' => $printArea['maxWidth'], 'maxHeight' => $printArea['maxHeight'], 'boundsstatus' => $printArea['bounds'], 'customsizestatus' => $printArea['custom_size'], 'customMask' => $printArea['customMask']));
                        $printSizes = $this->getDtgPrintSizesOfProductSides($data['simpleproduct_id']);
                        $this->customRequest(array('productid' => $resultData['conf_id'], 'jsondata' => json_encode($maskData), 'printsizes' => $printSizes));
                        $this->saveMaskData();
                        if ($printSizes['status'] != 'nodata') {
                            $this->setDtgPrintSizesOfProductSides();
                        }
                        $this->saveProductTemplateData($data['print_method_id'], $data['ref_id'], $data['simpleproduct_id'], $resultData['conf_id']);
                        if (!empty($productTemplate['tepmlate_id'])) {
                            $this->customRequest(array('pid' => $resultData['conf_id'], 'productTempId' => $productTemplate['tepmlate_id']));
                            $this->addTemplateToProduct();
                        }
                    } catch (Exception $e) {
                        $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                        $error = true;
                    }
                }
                $this->response($result, 200);
            } else {
                $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
                $this->response($this->json($msg), 200);
            }
        }
    }

    /**
     * Get Category list by product id
     *
     * @param   pid
     * @return  category list in json format
     */
    public function getCategoriesByProduct()
    {
        //$error='';
        $result = $this->storeApiLogin();
        $printProfile = Flight::printProfile();
        if ($this->storeApiLogin == true && isset($this->_request['pid']) && $this->_request['pid']) {
            $key = $GLOBALS['params']['apisessId'];
            $res = array();
            try {
                $catIdArr = $this->datalayer->getProductCategoryList($this->_request['pid']);
                if (empty($catIdArr)) {
                    $res = $printProfile->getDefaultPrintMethodId();
                } else {
                    $catIdStr = implode(',', $catIdArr);
                    $sql = 'SELECT DISTINCT pm.pk_id AS print_method_id,pm.name FROM ' . TABLE_PREFIX . 'print_method AS pm INNER JOIN ' . TABLE_PREFIX . 'product_category_printmethod_rel AS pcpmr ON pm.pk_id=pcpmr.print_method_id WHERE pcpmr.product_category_id IN(' . $catIdStr . ')';
                    $res = $this->executeFetchAssocQuery($sql);
                    if (empty($res)) {
                        $res = $printProfile->getDefaultPrintMethodId();
                    }
                }
            } catch (Exception $e) {
                $res = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
            }
            $this->response(json_encode($res), 200);
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
            $this->response($this->json($msg), 200);
        }

    }

    /**
     * Get Category list
     *
     * @param   nothing
     * @return  category list in json format
     */
    public function getCategories()
    {
        $error = '';
        $result = $this->storeApiLogin();
        $print_id = $this->_request['printId'];
        if ($this->storeApiLogin == true) {
            try {
                $categories = $this->datalayer->getCategories();
                $category_result = array();
                if (isset($print_id) && $print_id != 0) {
                    $sql = "SELECT product_category_id FROM " . TABLE_PREFIX . "product_category_printmethod_rel WHERE print_method_id='$print_id'";
                    $category = array();
                    $rows = $this->executeGenericDQLQuery($sql);
                    $category = $rows;
                    foreach ($categories as $categories) {
                        for ($j = 0; $j < sizeof($category); $j++) {
                            if ($categories['id'] == $category[$j]['product_category_id']) {
                                $category_result[$j]['id'] = $categories['id'];
                                $category_result[$j]['name'] = $categories['name'];
                            }
                        }
                    }
                    $result_arr = array();
                    $result_arr['categories'] = array_values($category_result);
                    $this->response($this->json($result_arr), 200);
                } else {
                    foreach ($categories as $categories) {
                        $category_result[] = array('id' => "" . $categories['id'] . "", 'name' => $categories['name']);
                    }
                    $result_arr = array();
                    $result_arr['categories'] = array_values($category_result);
                    $this->response($this->json($result_arr), 200);
                }
            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $error = true;
            }
            if (!$error) {
                $result = array('categories' => $result);
                $this->response($this->json($result), 200);
            } else {
                $this->response(json_decode($result), 200);
            }
            $this->response($this->json($result), 200);
        } else {
            $msg = array('status' => 'failed', 'error' => json_decode($result));
            $this->response($this->json($msg), 200);
        }
    }

    /**
     * Get Sub-Category list
     *
     * @param   selectedCategory
     * @return  sub-category list in json format
     */
    public function getsubCategories()
    {
        $error = '';
        $result = $this->storeApiLogin();
        $cat_id = $this->_request['selectedCategory'];
        if ($this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];
            try {
                //$filters=array('catid'=>$this->_request['selectedCategory'], 'store'=>1 );
                $result = array('subcategories' => $this->datalayer->getSubCategory($cat_id));
            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $error = true;
            }

            if (!$error) {
                $categories = array();
                $this->response($this->json($result), 200);
            } else {
                $this->response(json_decode($result), 200);
            }
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
            $this->response($this->json($msg), 200);
        }

    }

    /**
     * Get product count
     *
     * @param   orderIncrementId
     * @return  integer number of product
     */
    public function getProductCount()
    {
        $error = false;
        $result = $this->wcApi->get_products_count();
        if (!isset($result->errors)) {
            try {
                $result = array('size' => $result->count);
            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $error = true;
            }
            if (!$error) {
                $this->response($this->json($result), 200);
            } else {
                $msg = array('status' => 'failed', 'error' => $this->de_json($result));
                $this->response($this->json($msg), 200);
            }

        } else {

            $msg = array('status' => 'apiLoginFailed', 'error' => $result);
            $this->response($this->json($msg), 200);
        }
    }

    /**
     * Get detail of a product for client app
     *
     * @param   product_id
     * @return  product detail in json format
     */

    public function getSimpleProductClient()
    {
        $error = false;

        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {

            try {
                if (!isset($this->_request['confId']) || trim($this->_request['confId']) == '') {
                    $configProduct_id = 0;
                } else {
                    $configProduct_id = trim($this->_request['confId']);
                }
                $result = $this->datalayer->getProductById((object) $this->_request);
                $product_id = trim($result['pid']);
                $simpleProductId = trim($result['pvid']);
                $colorId = trim($result['xe_color_id']);
                $result = json_encode($result);
                $resultArr = json_decode($result);
                $sqlSwatch = "SELECT  hex_code,image_name FROM " . TABLE_PREFIX . "swatches WHERE attribute_id='" . $colorId . "'";
                $res = $this->executeFetchAssocQuery($sqlSwatch);
                if ($res) {
                    if ($res[0]['hex_code']) {
                        $colorSwatch = $res[0]['hex_code'];
                    } else {
                        $imageName = $res[0]['image_name'];
                        $swatchWidth = '45';
                        $swatchDir = $this->getSwatchURL();
                        $colorSwatch = $swatchDir . $swatchWidth . 'x' . $swatchWidth . '/' . $imageName;
                    }
                } else {
                    $colorSwatch = '';
                }

                $resultArr->colorSwatch = $colorSwatch;
                $this->_request['productid'] = $product_id; //Mask Info
                $this->_request['returns'] = true; //Mask Info
                $maskInfo = $this->getMaskData(sizeof($resultArr->sides));
                $resultArr->labels = array();
                $resultArr->maskInfo = json_decode($maskInfo);

                $printsize = $this->getDtgPrintSizesOfProductSides($product_id);
                $resultArr->printsize = $printsize;
                $printareatype = $this->getPrintareaType($product_id);
                $resultArr->printareatype = $printareatype;
                // insert multiple boundary data; if available
                $settingsObj = Flight::multipleBoundary();
                $multiBoundData = $settingsObj->getMultiBoundMaskData($product_id);
                if (!empty($multiBoundData)) {
                    $resultArr->printareatype['multipleBoundary'] = "true";
                    $resultArr->multiple_boundary = $multiBoundData;
                }
                $resultArr->sizeAdditionalprices = $this->getSizeVariantAdditionalPriceClient($product_id, $this->_request['print_method_id']);
                $surplusPrice = $resultArr->price;
                if (isset($product_id) && $product_id) {
                    $sql = "SELECT ref_id,parent_id FROM " . TABLE_PREFIX . "template_state_rel WHERE temp_id = " . $configProduct_id;
                    $parentId = $this->executeFetchAssocQuery($sql);
                    if (!empty($parentId)) {
                        $sql = "SELECT custom_price FROM " . TABLE_PREFIX . "decorated_product WHERE product_id = " . $parentId[0]['parent_id'] . " and refid = " . $parentId[0]['ref_id'];
                        $res = $this->executeFetchAssocQuery($sql);
                        $customPrice = $res[0]['custom_price'];
                        $resultArr->price = $surplusPrice - $customPrice;
                        $resultArr->finalPrice = $surplusPrice;
                    }
                }
                $tier = array();
                $tierPrice = $this->datalayer->getTierPrice($simpleProductId);
                if (!empty($tierPrice)) {
                    foreach ($tierPrice as $k => $value) {
                        $tier[$k]['tierQty'] = (int) $value['quantity'];
                        $tier[$k]['percentage'] = round(100 - $value['price'] / $surplusPrice * 100);
                        $tier[$k]['tierPrice'] = number_format($value['price'], 2);
                    }
                }
                $resultArr->tierPrices = $tier;
                $pCategories = $resultArr->category;
                $pCategoryIds = array();
                for ($i = 0; $i < sizeof($pCategories); $i++) {
                    $cat_id = $this->datalayer->getParentCategory($pCategories[$i]);
                    $cat_id = ($cat_id != '') ? $cat_id : $pCategories[$i];
                    array_push($pCategoryIds, $cat_id);
                }
                $features = $this->fetchProductFeatures($product_id, $pCategoryIds);
                $resultArr->features = $features;
                $templates = array();
                if (isset($product_id) && $product_id) {
                    $sql = "SELECT template_id FROM template_product_rel WHERE product_id = " . $product_id;
                    $res = $this->executeFetchAssocQuery($sql);
                    foreach ($res as $k => $v) {
                        $templates[$k] = $v['template_id'];
                    }
                }
                $resultArr->templates = $templates;

                    $sql = "SELECT distinct pk_id, print_method_id,price,is_whitebase
                        FROM   " . TABLE_PREFIX . "product_additional_prices
                        WHERE  product_id =" . $product_id . "
                        AND variant_id =" . $simpleProductId . " ORDER BY pk_id";
                $rows = $this->executeFetchAssocQuery($sql);
                $priceDetails = array();
                //$num = sizeof($rows);
                if (!empty($rows)) {
                    foreach ($rows as $k => $v) {
                        $priceDetails[$k]['prntMthdId'] = $v['print_method_id'];
                        $priceDetails[$k]['prntMthdPrice'] = $v['price'];
                        $priceDetails[$k]['is_whitebase'] = intval($v['is_whitebase']);
                    }
                }
                $resultArr->additionalprices = $priceDetails;
                $resultArr->is_product_template = false;
                $templateArr = $this->getProductTemplateByProductId($product_id, $pCategoryIds);
                if (!empty($templateArr) && $templateArr['tepmlate_id'] != '') {
                    $resultArr->is_product_template = true;
                    $resultArr->tepmlate_id = $templateArr['tepmlate_id'];
                    if (!empty($templateArr['thumbsides']) && !empty($templateArr['sides'])) {
                        $resultArr->thumbsides = $templateArr['thumbsides'];
                        $resultArr->sides = $templateArr['sides'];
                    } else {
                        $resultArr->thumbsides = [];
                        $resultArr->sides = [];
                    }
                }
                if (empty($resultArr->maskInfo)) {
                    $maskInfo = $this->getMaskData(sizeof($templateArr['side_id']));
                    $resultArr->maskInfo = json_decode($maskInfo);

                }
                $result = json_encode($resultArr, JSON_UNESCAPED_UNICODE);

            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $error = true;
            }

            $this->closeConnection();
            $this->response($result, 200);
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
            $this->response($this->json($msg), 200);
        }
    }

    /**
     * Get detail of a product for admin app
     *
     * @param   product_id
     * @return  product detail in json format
     */
    public function getSimpleProduct()
    {
        $error = false;

        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {

            try {
                $result = $this->datalayer->getProductById((object) $this->_request);
                $product_id = trim($result['pid']);
                $simpleProductId = $resultArr['pvid'];
                $result = json_encode($result);
                $resultArr = json_decode($result);
                $this->_request['productid'] = $product_id; //Mask Info
                $this->_request['returns'] = true; //Mask Info
                $maskInfo = $this->getMaskData(sizeof($resultArr->sides));
                $resultArr->maskInfo = json_decode($maskInfo);
                $printsize = $this->getDtgPrintSizesOfProductSides($product_id);
                $resultArr->printsize = $printsize;
                $printareatype = $this->getPrintareaType($product_id);
                $resultArr->printareatype = $printareatype;
                // insert multiple boundary data; if available
                $settingsObj = Flight::multipleBoundary();
                $multiBoundData = $settingsObj->getMultiBoundMaskData($product_id);
                if (!empty($multiBoundData)) {
                    $resultArr->printareatype['multipleBoundary'] = "true";
                    $resultArr->multiple_boundary = $multiBoundData;
                }
                $cVariants = $resultArr->product->variants;
                $cVariantsIds = array();
                $resultArr->sizeAdditionalprices = $this->getSizeVariantAdditionalPrice($product_id);
                $pCategories = $resultArr->product->category;
                $pCategoryIds = array();
                for ($i = 0; $i < sizeof($pCategories); $i++) {
                    $cat_id = $this->datalayer->getParentCategory($pCategories[$i]);
                    $cat_id = ($cat_id != '') ? $cat_id : $pCategories[$i];
                    array_push($pCategoryIds, $cat_id);
                }
                $features = $this->fetchProductFeatures($product_id, $pCategoryIds);
                $resultArr->features = $features;
                $templates = array();
                if (isset($product_id) && $product_id) {
                    $sql = "SELECT template_id FROM template_product_rel WHERE product_id = " . $product_id;
                    $res = $this->executeFetchAssocQuery($sql);
                    foreach ($res as $k => $v) {
                        $templates[$k] = $v['template_id'];
                    }
                }
                $resultArr->templates = $templates;
                $templateArr = $this->getProductTemplateByProductId($product_id, $pCategoryIds);
                if (!empty($templateArr) && $templateArr['tepmlate_id'] != '') {
                    $resultArr->is_product_template = true;
                    $resultArr->tepmlate_id = $templateArr['tepmlate_id'];
                    if (!empty($templateArr['thumbsides']) && !empty($templateArr['sides'])) {
                        $resultArr->thumbsides = $templateArr['thumbsides'];
                        $resultArr->sides = $templateArr['sides'];
                    } else {
                        $resultArr->thumbsides = [];
                        $resultArr->sides = [];
                    }
                }
                if (empty($resultArr->maskInfo)) {
                    $maskInfo = $this->getMaskData(sizeof($templateArr['side_id']));
                    $resultArr->maskInfo = json_decode($maskInfo);

                }

                $result = json_encode($resultArr, JSON_UNESCAPED_UNICODE);

            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $error = true;
            }
            $this->closeConnection();
            $this->response($result, 200);
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
            $this->response($this->json($msg), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *fetch print method id and name
     *
     *@param (String)apikey
     *@param (int)productid
     *@return json data
     *
     */
    public function getProductPrintMethod()
    {
        $productId = $this->_request['productid'];
        $key = $this->_request['apikey'];
        if (!empty($productId)) {
            // &&  !empty($key) && $this->isValidCall($key)){
            $error = false;
            $productPrintTypeSql = "SELECT distinct pm.pk_id, pm.name FROM " . TABLE_PREFIX . "print_method pm
            INNER JOIN " . TABLE_PREFIX . "product_printmethod_rel ppr ON ppr.print_method_id=pm.pk_id
            JOIN " . TABLE_PREFIX . "print_setting AS pst ON pm.pk_id=pst.pk_id
            WHERE ppr.product_id=" . $productId;
            $productPrintType = $this->executeGenericDQLQuery($productPrintTypeSql);

            if (!empty($productPrintType)) {
                //$this->log('productPrintTypeSql: '.$productPrintTypeSql, true, 'Zsql.log');
                foreach ($productPrintType as $k2 => $v2) {
                    $printDetails[$k2]['print_method_id'] = $v2['pk_id'];
                    $printDetails[$k2]['name'] = $v2['name'];
                }
            } else {
                $result = $this->storeApiLogin();
                if ($this->storeApiLogin == true) {
                    try {
                        $catIds = $this->datalayer->getProductCategoryList($productId);
                        //$lencatid = count(trim($catIds));
                        $catIds = implode(',', (array) $catIds);
                        $catSql = 'SELECT DISTINCT pm.pk_id, pm.name
                            FROM ' . TABLE_PREFIX . 'product_category_printmethod_rel AS pcpml
                            JOIN ' . TABLE_PREFIX . 'print_method AS pm ON pm.pk_id = pcpml.print_method_id
                            JOIN ' . TABLE_PREFIX . 'print_setting AS pst ON pm.pk_id=pst.pk_id
                            LEFT JOIN ' . TABLE_PREFIX . 'print_method_setting_rel pmsr ON pst.pk_id=pmsr.print_setting_id
                            WHERE pcpml.product_category_id IN(' . $catIds . ')';
                        $rows = $this->executeFetchAssocQuery($catSql);

                        $printDetails = array();
                        if (empty($rows)) {
                            $default_print_type = "SELECT pm.pk_id,pm.name
                            from " . TABLE_PREFIX . "print_method AS pm
                            JOIN " . TABLE_PREFIX . "print_setting ps ON pm.pk_id=ps.pk_id
                            LEFT JOIN " . TABLE_PREFIX . "print_method_setting_rel pmsr ON ps.pk_id=pmsr.print_setting_id
                            WHERE ps.is_default='1' AND pm.is_enable='1' AND ps.is_default='1'";

                            $res = $this->executeFetchAssocQuery($default_print_type);
                            $printDetails[0]['print_method_id'] = $res[0]['pk_id'];
                            $printDetails[0]['name'] = $res[0]['name'];
                        } else {

                            foreach ($rows as $k1 => $v1) {
                                $printDetails[$k1]['print_method_id'] = $v1['pk_id'];
                                $printDetails[$k1]['name'] = $v1['name'];
                            }
                        }

                    } catch (Exception $e) {
                        $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                        $error = true;
                    }

                } else {
                    $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
                    $this->response($this->json($msg), 200);
                }
            }
            if (!$error) {

                $resultArr = $printDetails;
                $result = json_encode($resultArr);
                $this->response($this->json($resultArr), 200);
            } else {
                $this->response($result, 200);
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
     *fetch print method id and name
     *
     *@param (String)apikey
     *@param (int)productid
     *@return json data
     *
     */
    public function getPrintMethodByProduct($additional_price = false)
    {
        $result = $this->storeApiLogin();
        $printProfile = Flight::printProfile();
        if ($this->storeApiLogin == true && isset($this->_request['pid']) && $this->_request['pid']) {
            $key = $GLOBALS['params']['apisessId'];
            $result_arr = array();
            $filters = array('store' => $this->getDefaultStoreId());
            $confProductId = $this->_request['pid'];
            $isAdmin = (isset($this->_request['isAdmin']) && trim($this->_request['isAdmin']) == true) ? true : false;

            //  Do not send any print method ID for multiple boundary product
            $MultiBoundQry = "SELECT * FROM " . TABLE_PREFIX . "multi_bound_print_profile_rel WHERE product_id = '" . $confProductId . "'";
            $records = $this->executeFetchAssocQuery($MultiBoundQry);
            if (!empty($records) && !$isAdmin) {
                $result_arr[0]['print_method_id'] = 0;
                $result_arr[0]['name'] = "multiple";
                $result_arr[0]['fetched_from'] = 'DB';
            } else {
                $fieldSql = 'SELECT distinct pm.pk_id AS print_method_id, pm.name';
                if ($additional_price) {
                    $fieldSql .= ', pst.additional_price';
                }

                // Check whether product has specific print method assigned //
                $productPrintTypeSql = $fieldSql . ' FROM ' . TABLE_PREFIX . "print_method pm
                INNER JOIN " . TABLE_PREFIX . "product_printmethod_rel ppr ON ppr.print_method_id=pm.pk_id
                JOIN " . TABLE_PREFIX . "print_setting AS pst ON pm.pk_id=pst.pk_id
                WHERE ppr.product_id=" . $confProductId . " order by pm.pk_id ASC";
                $res = $this->executeFetchAssocQuery($productPrintTypeSql);
                $result_arr = array();
                if (empty($res)) {
                    try {
                        $catIds = $this->datalayer->getProductCategoryList($confProductId);

                        if (!empty($catIds)) {
                            $catIds = implode(',', $catIds);
                            $catSql = $fieldSql . ' FROM ' . TABLE_PREFIX . 'product_category_printmethod_rel AS pcpml
                                    JOIN ' . TABLE_PREFIX . 'print_method AS pm ON pm.pk_id = pcpml.print_method_id
                                    JOIN ' . TABLE_PREFIX . 'print_setting AS pst ON pm.pk_id=pst.pk_id
                                    LEFT JOIN ' . TABLE_PREFIX . 'print_method_setting_rel pmsr ON pst.pk_id=pmsr.print_setting_id
                                    WHERE pcpml.product_category_id IN(' . $catIds . ') order by pm.pk_id ASC';
                            $res = $this->executeFetchAssocQuery($catSql);
                            foreach ($res as $k => $v) {
                                $result_arr[$k]['print_method_id'] = $v['print_method_id'];
                                $result_arr[$k]['name'] = $v['name'];
                                $result_arr[$k]['fetched_from'] = 'category';
                            }
                            if (empty($res)) {
                                $res = $printProfile->getDefaultPrintMethodId();
                                foreach ($res as $k => $v) {
                                    $result_arr[$k]['print_method_id'] = $v['print_method_id'];
                                    $result_arr[$k]['name'] = $v['name'];
                                    $result_arr[$k]['fetched_from'] = 'default';
                                }
                            }
                        } else {
                            $res = $printProfile->getDefaultPrintMethodId();
                            foreach ($res as $k => $v) {
                                $result_arr[$k]['print_method_id'] = $v['print_method_id'];
                                $result_arr[$k]['name'] = $v['name'];
                                $result_arr[$k]['fetched_from'] = 'default';
                            }
                        }
                    } catch (Exception $e) {
                        $result_arr = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                    }
                } else {
                    foreach ($res as $k => $v) {
                        $result_arr[$k]['print_method_id'] = $v['print_method_id'];
                        $result_arr[$k]['name'] = $v['name'];
                        $result_arr[$k]['fetched_from'] = 'product';
                    }
                }
            }
            $this->response($this->json($result_arr), 200);
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
            $this->response($this->json($msg), 200);
        }
    }

    /**
     * Check magento version
     *
     * @param   nothing
     * @return  string $version
     */
    public function storeVersion()
    {
        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            //$key = $GLOBALS['params']['apisessId'];

            try {
                //$result = $this->proxy->call($key, 'cedapi_product.storeVersion');
                //return $version = (!empty($result))?strchr($result,'.',true):1;
                return $version = 1;
            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $error = true;
            }
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
            $this->response($this->json($msg), 200);
        }
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
     *date created 07-06-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *Save product template data
     *
     *@param (Int)old productid
     *@param (Int)new productid
     *@param (Int)refId
     *
     */
    public function saveProductTemplateData($printMethodId, $refId, $oldId, $newId)
    {
        $apiKey = $this->_request['apikey'];
        if ($this->isValidCall($apiKey)) {
            try {
                $sql = "delete from " . TABLE_PREFIX . "template_state_rel where temp_id=" . $newId . "";
                $result = $this->executeGenericDMLQuery($sql);
                $sql = "delete from " . TABLE_PREFIX . "product_printmethod_rel where product_id=" . $newId . "";
                $result = $this->executeGenericDMLQuery($sql);
                $values = '';
                $pValues = '';
                $status = 0;
                $values .= ",(" . $refId . "," . $newId . "," . $oldId . ")";
                $pValues .= ",(" . $newId . "," . $printMethodId . ")";
                if (strlen($values)) {
                    $sql = "INSERT INTO " . TABLE_PREFIX . "template_state_rel (ref_id,temp_id,parent_id) VALUES" . substr($values, 1);
                    $status = $this->executeGenericDMLQuery($sql);
                }
                if (strlen($pValues)) {
                    $sql = "INSERT INTO " . TABLE_PREFIX . "product_printmethod_rel (product_id,print_method_id) VALUES" . substr($pValues, 1);
                    $status = $this->executeGenericDMLQuery($sql);
                }
                if ($status) {
                    $msg = array("status" => "success");
                } else {
                    $msg = array("status" => "failed");
                }
                return $this->json($msg);
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        }
    }
}
