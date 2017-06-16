<?php
/* Check Un-authorize Access */
if (!defined('accessUser')) {
    die("Error");
}

class ProductsStore extends UTIL
{

    public function getSimpleProductClient()
    {
        $error = false;
        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];

            if (!isset($this->_request['id']) || trim($this->_request['id']) == '') {
                $msg = array('status' => 'invalid id', 'id' => $this->_request['id']);
                $this->response($this->json($msg), 204); //terminate
            } else {
                $product_id = trim($this->_request['id']);
            }
            if (!isset($this->_request['confId']) || trim($this->_request['confId']) == '') {
                $configProduct_id = 0;
            } else {
                $configProduct_id = trim($this->_request['confId']);
            }
            if (!isset($this->_request['size']) || trim($this->_request['size']) == '') {
                $size = '';
            } else {
                $size = trim($this->_request['size']);
            }
            $attributes = array();
            if ($size != '') {
                $attributes['size'] = $size;
            }
            $productInfo = array(
                'productId' => $product_id,
                'store' => $this->getDefaultStoreId(),
                'attributes' => $attributes,
                'configPid' => $configProduct_id,
                'color' => $this->getStoreAttributes("xe_color"),
                'size' => $this->getStoreAttributes("xe_size")
            );

            if (!$error) {
                try {
                    //$result    = $this->proxy->call($key, 'cedapi_product.getSimpleProduct', $productInfo);
                    $result = $this->apiCall('Product', 'getSimpleProduct', $productInfo);
                    $result = $result->result;

                    if (empty($result)) {
                        $result = json_encode(array('No Records Found'));
                        $error = true;
                    } else {
                        $resultArr = json_decode($result);
                        $confProductId = $resultArr->pid;
                        $simpleProductId = $resultArr->pvid;
                        $colorId = $resultArr->xe_color_id;
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

                        //$this->_request['productid'] = $product_id; //Mask Info
                        $this->_request['productid'] = $confProductId; //Mask Info
                        $this->_request['returns'] = true; //Mask Info
                        $maskInfo = $this->getMaskData(sizeof($resultArr->sides));
                        $resultArr->maskInfo = json_decode($maskInfo);

                        $printsize = $this->getDtgPrintSizesOfProductSides($confProductId);
                        $resultArr->printsize = $printsize;

                        $printareatype = $this->getPrintareaType($confProductId);
                        $resultArr->printareatype = $printareatype;
                        // insert multiple boundary data; if available
                        $settingsObj = Flight::multipleBoundary();
                        $multiBoundData = $settingsObj->getMultiBoundMaskData($confProductId);
                        if (!empty($multiBoundData)) {
                            $resultArr->printareatype['multipleBoundary'] = "true";
                            $resultArr->multiple_boundary = $multiBoundData;
                        }

                        /* $cVariants = $resultArr->variants;
                        $cVariantsIds = array();
                        for($i=0; $i<sizeof($cVariants); $i++)
                        {
                        array_push($cVariantsIds, $cVariants[$i]->data->id);
                        } */

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

                        $pCategories = $resultArr->category;
                        $features = $this->fetchProductFeatures($confProductId, $pCategories);
                        $resultArr->features = $features;

                        $templates = array();
                        if (isset($product_id) && $product_id) {
                            $sql = "SELECT template_id FROM " . TABLE_PREFIX . "template_product_rel WHERE product_id = " . $product_id;
                            $res = $this->executeFetchAssocQuery($sql);
                            foreach ($res as $k => $v) {
                                $templates[$k] = $v['template_id'];
                            }
                        }
                        $resultArr->templates = $templates;
                        $resultArr->sizeAdditionalprices = $this->getSizeVariantAdditionalPriceClient($confProductId, $this->_request['print_method_id']);
                        //$resultArr->additionalprices = $this->getAdditionalPrintingPriceOfVariants($confProductId, $simpleProductId);
                        $sql = "SELECT distinct pk_id, print_method_id,price,is_whitebase
                            FROM   " . TABLE_PREFIX . "product_additional_prices
                            WHERE  product_id =" . $confProductId . "
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
                        $templateArr = $this->getProductTemplateByProductId($confProductId);
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
                        $result = json_encode($resultArr);
                    }
                } catch (Exception $e) {
                    $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
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

    ####################################################
    ############## syncOrdersZip ###################
    ####################################################
    public function getVariants()
    {
        $error = false;
        $start = 0; // default values
        if (isset($this->_request['start']) && trim($this->_request['start']) != '') {
            $start = trim($this->_request['start']);
        }
        if (isset($this->_request['range']) && trim($this->_request['range']) != '' && trim($this->_request['range']) != 0) {
            $limit = trim($this->_request['range']);
        } else {
            $limit = 0;
        }
        $offset = (isset($this->_request['offset']) && trim($this->_request['offset']) != '') ? trim($this->_request['offset']) : 1;
        $store = (isset($this->_request['store']) && trim($this->_request['store']) != '') ? trim($this->_request['store']) : $this->getDefaultStoreId();

        $result = $this->storeApiLogin();
        $confId = $this->_request['conf_pid'];
        if ($this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];
            try {
                $filters = array(
                    'confId' => $confId,
                    'start' => $start,
                    'limit' => $limit,
                    'store' => $store,
                    'offset' => $offset,
                    'color' => $this->getStoreAttributes("xe_color"),
                    'size' => $this->getStoreAttributes("xe_size")
                );
                $result = $this->apiCall('Product', 'getVariants', $filters);
                $result = $result->result;

                $catIds = '';
                $resultArr = json_decode($result);
                foreach ($resultArr->variants as $key => $value) {
                    $catIds = $resultArr->variants[$key]->ConfcatIds;
                    $colorId = $resultArr->variants[$key]->xe_color_id;
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
                    $resultArr->variants[$key]->colorUrl = $colorSwatch;
                }
                $productId = $confId;
                $resultArr = json_encode($resultArr);
            } catch (Exception $e) {
                $resultArr = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $error = true;
            }
            if (!$error) {
                $this->response($resultArr, 200);
            } else {
                $this->response($resultArr, 200);
            }
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
            $this->response($this->json($msg), 200);
        }
    }

    ####################################################
    ############## syncOrdersZip ###################
    ####################################################
    public function getSizeAndQuantity()
    {
        $error = false;
        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];

            if (!isset($this->_request['productId']) || trim($this->_request['productId']) == '') {
                //$product_id = '';
                $msg = array('status' => 'invalid productId', 'productId' => $this->_request['productId']);
                $this->response($this->json($msg), 204);
            } else {
                $product_id = trim($this->_request['productId']);
            }
            if (!isset($this->_request['simplePdctId']) || trim($this->_request['simplePdctId']) == '') {
                //$varient_id = '';
                $msg = array('status' => 'invalid simplePdctId', 'simplePdctId' => $this->_request['simplePdctId']);
                $this->response($this->json($msg), 204);
            } else {
                $varient_id = trim($this->_request['simplePdctId']);
            }
            if (isset($this->_request['byAdmin'])) {
                $byAdmin = true;
            } else {
                $byAdmin = false;
            }
            $productInfo = array(
                'productId' => $product_id,
                'store' => $this->getDefaultStoreId(),
                'simpleProductId' => $varient_id,
                'color' => $this->getStoreAttributes("xe_color"),
                'size' => $this->getStoreAttributes("xe_size")
            );

            if (!$error) {
                try {
                    if (!$byAdmin) {
                        $result = $this->apiCall('Product', 'getSizeAndQuantity', $productInfo);
                    } else {
                        $result = $this->apiCall('Product', 'getSizeVariants', $productInfo);
                    }
                    //$result    = $this->proxy->call($key, 'cedapi_product.getSizeAndQuantity', $productInfo);
                    $result = $result->result; //var_dump($result);exit;

                } catch (Exception $e) {
                    $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
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
     * Used to get all products which are eligible to customize
     *
     * @param   $categoryid, $searchstring, $start, $limit, $loadVariants (To filter the product list)
     * @return  list of products which are eligible to customize
     */
    public function getAllProducts()
    {
        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];

            $categoryid = (isset($this->_request['categoryid']) && trim($this->_request['categoryid']) != '') ? trim($this->_request['categoryid']) : 0;
            $searchstring = (isset($this->_request['searchstring']) && trim($this->_request['searchstring']) != '') ? trim($this->_request['searchstring']) : '';
            $start = (isset($this->_request['start']) && trim($this->_request['start']) != '') ? trim($this->_request['start']) : 0;
            $limit = (isset($this->_request['range']) && trim($this->_request['range']) != '') ? trim($this->_request['range']) : 10;
            $offset = (isset($this->_request['offset']) && trim($this->_request['offset']) != '') ? trim($this->_request['offset']) : 1;
            $store = (isset($this->_request['store']) && trim($this->_request['store']) != '') ? trim($this->_request['store']) : $this->getDefaultStoreId();
            $loadVariants = (isset($this->_request['loadVariants']) && trim($this->_request['loadVariants']) == true) ? true : false;
            $preDecorated = (isset($this->_request['preDecorated']) && trim($this->_request['preDecorated']) == 'true') ? true : false;
            $filterArray = array('type' => array('eq' => 'configurable'));
            try {
                $filters = array(
                    'filters' => $filterArray,
                    'categoryid' => $categoryid,
                    'searchstring' => $searchstring,
                    'store' => $store,
                    'range' => array('start' => $start, 'range' => $limit),
                    'loadVariants' => $loadVariants,
                    'offset' => $offset,
                    'limit' => $limit,
                    'preDecorated' => $preDecorated,
                    'color' => $this->getStoreAttributes("xe_color"),
                    'size' => $this->getStoreAttributes("xe_size")
                );
                $result = $this->apiCall('Product', 'getAllProducts', $filters);
                $result = $result->result;
                $result = json_decode($result, true);
                $finalResult = array();
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
                            if (!empty($catIds)) {
                                $catIds = implode(',', (array) $catIds);
                                $catSql = 'SELECT DISTINCT pm.pk_id, pm.name
                                    FROM ' . TABLE_PREFIX . 'product_category_printmethod_rel AS pcpml
                                    JOIN ' . TABLE_PREFIX . 'print_method AS pm ON pm.pk_id = pcpml.print_method_id WHERE pcpml.product_category_id IN(' . $catIds . ')';
                                $rows = $this->executeFetchAssocQuery($catSql);
                            }
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
            }
            $this->response($this->json($result,1), 200);
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_encode($finalResult));
            $this->response($this->json($msg), 200);
        }
    }
    ####################################################
    ############## syncOrdersZip ###################
    ####################################################
    public function getCategories()
    {
        $error = '';
        $result = $this->storeApiLogin();
        $print_id = $this->_request['printId'];
        if ($this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];
            $filters = array(
                'store' => $this->getDefaultStoreId(),
            );
            try {
                $result = $this->apiCall('Product', 'getCategories', $filters);
                $result = $result->result;
                if (isset($print_id) && $print_id != 0) {
                    $categories = json_decode($result, true);
                    $category_result = array();
                    $sql = "SELECT product_category_id FROM " . TABLE_PREFIX . "product_category_printmethod_rel WHERE print_method_id='$print_id'";
                    $category = array();
                    $rows = $this->executeGenericDQLQuery($sql);
                    $category = $rows;
                    foreach ($categories['categories'] as $categories) {
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
                }
            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $error = true;
            }
            if (!$error) {
                //print_r($result);
                $this->response($result, 200);
            } else {
                //print_r(json_decode($result));
                $this->response($result, 200);
            }
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
            $this->response($this->json($msg), 200);
        }
    }

    ####################################################
    ############## syncOrdersZip ###################
    ####################################################
    public function getsubCategories()
    {
        $error = '';
        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];
            try {
                $filters = array('catId' => $this->_request['selectedCategory']);
                $result = $this->apiCall('Product', 'getsubCategories', $filters);
                $result = $result->result;
            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $error = true;
            }
            if (!$error) {
                $categories = array();
                $this->response($result, 200);
            } else {
                $this->response($result, 200);
            }
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
            $this->response($this->json($msg), 200);
        }
    }

    ####################################################
    ############## syncOrdersZip ###################
    ####################################################
    public function getPrintMethodByProduct($additional_price = false)
    {
        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true && isset($this->_request['pid']) && $this->_request['pid']) {
            $key = $GLOBALS['params']['apisessId'];
            $result_arr = array();
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
                    $filters = array(
                        'productId' => $confProductId,
                        'store' => $this->getDefaultStoreId(),
                    );
                    try {
                        $result = $this->apiCall('Product', 'getCategoriesByProduct', $filters);
                        $result = $result->result;
                        $catIds = json_decode($result, true);
                           $printPObj = Flight::printProfile();                        
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
                                $res = $printPObj->getDefaultPrintMethodId();
                                foreach ($res as $k => $v) {
                                    $result_arr[$k]['print_method_id'] = $v['print_method_id'];
                                    $result_arr[$k]['name'] = $v['name'];
                                    $result_arr[$k]['fetched_from'] = 'default';
                                }
                            }
                        } else {
                            $res = $printPObj->getDefaultPrintMethodId();
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

    ####################################################
    ############## syncOrdersZip ###################
    ####################################################
    public function getProductCount()
    {
        $error = false;
        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];
            $filterArray = array('type' => array('eq' => 'configurable'));
            try {
                $filters = array(
                    'filters' => $filterArray,
                    'store' => $this->getDefaultStoreId(),
                );
                $result = $this->proxy->call($key, 'cedapi_product.getProductCount', $filters);
            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $error = true;
            }
            if (!$error) {
                $this->response($this->json($result), 200);
            } else {
                $this->response($this->json($result), 200);
            }
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
            $this->response($this->json($msg), 200);
        }
    }

    ####################################################
    ############## syncOrdersZip ###################
    ####################################################
    public function getSimpleProduct()
    {
        $error = false;
        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];
            if (!isset($this->_request['id']) || trim($this->_request['id']) == '') {
                $msg = array('status' => 'invalid id', 'id' => $this->_request['id']);
                $this->response($this->json($msg), 204); //terminate
            } else {
                $product_id = trim($this->_request['id']);
            }
            if (!isset($this->_request['confId']) || trim($this->_request['confId']) == '') {
                $configProduct_id = 0;
            } else {
                $configProduct_id = trim($this->_request['confId']);
            }
            if (!isset($this->_request['size']) || trim($this->_request['size']) == '') {
                $size = '';
            } else {
                $size = trim($this->_request['size']);
            }
            $attributes = array();
            if ($size != '') {
                $attributes['size'] = $size;
            }
            $productInfo = array(
                'productId' => $product_id,
                'store' => $this->getDefaultStoreId(),
                'attributes' => $attributes,
                'configPid' => $configProduct_id,
                'color' => $this->getStoreAttributes("xe_color"),
                'size' => $this->getStoreAttributes("xe_size")
            );
            if (!$error) {
                try {
                    $result = $this->apiCall('Product', 'getSimpleProduct', $productInfo);
                    $result = $result->result;
                    if (empty($result)) {
                        $result = json_encode(array('No Records Found'));
                        $error = true;
                    } else {
                        $resultArr = json_decode($result);
                        $confProductId = $resultArr->pid;
                        $simpleProductId = $resultArr->pvid;
                        $colorId = $resultArr->xe_color_id;
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
                        $this->_request['productid'] = $confProductId; //Mask Info
                        $this->_request['returns'] = true; //Mask Info
                        $maskInfo = $this->getMaskData(sizeof($resultArr->sides));
                        $resultArr->maskInfo = json_decode($maskInfo);
                        $printsize = $this->getDtgPrintSizesOfProductSides($confProductId);
                        $resultArr->printsize = $printsize;
                        $printareatype = $this->getPrintareaType($confProductId);
                        $resultArr->printareatype = $printareatype;
                        // insert multiple boundary data; if available
                        $settingsObj = Flight::multipleBoundary();
                        $multiBoundData = $settingsObj->getMultiBoundMaskData($confProductId);
                        if (!empty($multiBoundData)) {
                            $resultArr->printareatype['multipleBoundary'] = "true";
                            $resultArr->multiple_boundary = $multiBoundData;
                        }
                        $additionalprices = $this->getAdditionalPrintingPriceOfVariants($confProductId, $simpleProductId);
                        $resultArr->additionalprices = $additionalprices;
                        $resultArr->sizeAdditionalprices = $this->getSizeVariantAdditionalPrice($confProductId);
                        $pCategories = $resultArr->category;
                        $features = $this->fetchProductFeatures($confProductId, $pCategories);
                        $resultArr->features = $features;
                        $templates = array();
                        $resultArr->is_product_template = false;
                        if (isset($product_id) && $product_id) {
                            $sql = "SELECT template_id FROM " . TABLE_PREFIX . "template_product_rel WHERE product_id = " . $product_id;
                            $res = $this->executeFetchAssocQuery($sql);
                            foreach ($res as $k => $v) {
                                $templates[$k] = $v['template_id'];
                            }
                        }
                        $resultArr->templates = $templates;
                        $resultArr->discountData = $this->getDiscountToProduct($product_id);
                        $templateArr = $this->getProductTemplateByProductId($confProductId);
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
                        $result = json_encode($resultArr);
                    }
                } catch (Exception $e) {
                    $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
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
     * Used to get all the xe_size inside magento
     *
     * @param   nothing
     * @return  array contains all the xe_size inside store
     */
    public function getSizeArr()
    {
        $error = '';
        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];
            $filters = array(
                'store' => $this->getDefaultStoreId(),
                'size' => $this->getStoreAttributes("xe_size")
            );
            try {
                $result = $this->apiCall('Product', 'getSizeArr', $filters);
                $result = $result->result;
            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $error = true;
            }
            if (!$error) {
                $categories = array();
                $this->response($result, 200);
            } else {
                $this->response($result, 200);
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
        $lastLoaded = '';
        if (!empty($this->_request['lastLoaded'])) {
            $lastLoaded = $this->_request['lastLoaded'];
        }
        $loadCount = '';
        if (!empty($this->_request['loadCount'])) {
            $loadCount = $this->_request['loadCount'];
        }
        if ($this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];
            try {
                $filters = array(
                    'lastLoaded' => $lastLoaded,
                    'loadCount' => $loadCount,
                    'oldConfId' => $productId,
                    'color' => $this->getStoreAttributes("xe_color"),
                );
                $result = $this->apiCall('Product', 'getColorArr', $filters);
                $result = $result->result;

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
                $this->response($result, 200);
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
        $error = false;
        $result = $this->storeApiLogin();
        if (!empty($this->_request) && $this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];
            if (!$error) {
                $filters = array(
                    'sku_arr' => $this->_request['sku_arr'],
                    'store' => $this->getDefaultStoreId(),
                );
                try {
                    $result = $this->apiCall('Product', 'checkDuplicateSku', $filters);
                    $result = $result->result;
                } catch (Exception $e) {
                    $result = json_encode(array('isFault: ' => 1, 'faultMessage' => $e->getMessage()));
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
                        $arr = array('store' => $this->getDefaultStoreId(), 'data' => json_encode($data), 'configFile' => json_encode($data['images']), 'oldConfId' => $data['simpleproduct_id'], 'varColor' => $data['color_id'], 'varSize' => json_encode($data['sizes']), 'color' => $this->getStoreAttributes("xe_color"), 'size' => $this->getStoreAttributes("xe_size"), 'attrSet' => $this->getStoreAttributes("inkXE"));
                        $result = $this->apiCall('Product', 'addTemplateProducts', $arr);
                        $result = $result->result;
                        $resultData = json_decode($result, true);
                        $this->customRequest(array('productid' => $data['simpleproduct_id'], 'isTemplate' => 1));
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
                            $test = $this->addTemplateToProduct();
                        }
                    } catch (Exception $e) {
                        $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                        $error = true;
                    }
                }
                echo $result;exit;
            } else {
                $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
                $this->response($this->json($msg), 200);
            }
        }
    }

    /**
     *
     *date created 07-06-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *Save product template data
     *
     *@param (Int)printMethodId
     *@param (Int)refId
     *@param (Int)oldId
     *@param (Int)newId
     *
     */
    public function saveProductTemplateData($printMethodId, $refId, $oldId, $newId)
    {
        $apiKey = $this->_request['apikey'];
        if ($this->isValidCall($apiKey)) {
            try {
                $sql = "delete from " . TABLE_PREFIX . "template_state_rel where temp_id=" . $newId;
                $result = $this->executeGenericDMLQuery($sql);
                $sql = "delete from " . TABLE_PREFIX . "product_printmethod_rel where product_id=" . $newId;
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

    ####################################################
    ############## syncOrdersZip ###################
    ####################################################
    public function getVariantList()
    {
        $error = false;
        $resultArr = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];
            try {
                $confId = $this->_request['conf_pid'];
                $filters = array(
                    'confId' => $confId,
                    'store' => $this->getDefaultStoreId(),
                );
                $resultArr = $this->proxy->call($key, 'cedapi_product.getVariantList', $filters);
            } catch (Exception $e) {
                $resultArr = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $error = true;
            }
            if (!$error) {
                $this->response($resultArr, 200);
            } else {
                $this->response($resultArr, 200);
            }
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($resultArr));
            $this->response($this->json($msg), 200);
        }
    }

    ####################################################
    ############## syncOrdersZip ###################
    ####################################################
    public function addProducts()
    {
        $error = false;
        if (!empty($this->_request['data']) && !empty($_FILES['simpleFile'])) {
            $data = json_decode($this->_request['data'], true);
            $this->_request['apikey'] = $data['apikey'];
            $result = $this->storeApiLogin();
            if ($this->storeApiLogin == true) {
                $key = $GLOBALS['params']['apisessId'];
                if (!$error) {
                    try {
                        $arr = array('store' => $this->getDefaultStoreId(), 'data' => $data, 'configFile' => $_FILES['configFile'], 'simpleFile' => $_FILES['simpleFile']);
                        $result = $this->proxy->call($key, 'cedapi_product.addProducts', $arr);
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
            $error = false;
            $productPrintTypeSql = "SELECT distinct pm.pk_id, pm.name FROM " . TABLE_PREFIX . "print_method pm
            INNER JOIN " . TABLE_PREFIX . "product_printmethod_rel ppr ON ppr.print_method_id=pm.pk_id
            JOIN " . TABLE_PREFIX . "print_setting AS pst ON pm.pk_id=pst.pk_id
            WHERE ppr.product_id=" . $productId;
            $productPrintType = $this->executeGenericDQLQuery($productPrintTypeSql);

            if (!empty($productPrintType)) {
                foreach ($productPrintType as $k2 => $v2) {
                    $printDetails[$k2]['print_method_id'] = $v2['pk_id'];
                    $printDetails[$k2]['name'] = $v2['name'];
                }
            } else {
                $result = $this->storeApiLogin();
                if ($this->storeApiLogin == true) {
                    $key = $GLOBALS['params']['apisessId'];
                    try {
                        $filters = array(
                            'productId' => $productId,
                            'store' => $this->getDefaultStoreId(),
                        );
                        $result = $this->apiCall('Product', 'getCategoriesByProduct', $filters);
                        $result = $result->result;
                        $catIds = json_decode($result);
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
            $msg = array("status" => "invalid Product Id");
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
            $filters = array(
                'store' => $this->getDefaultStoreId(),
            );
            try {
                $result = $this->apiCall('Product', 'checkDesignerTool', $filters);
            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
            }
            if ($t) {
                return $result;
            } else {
                return $result;
            }
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
            $this->response($this->json($msg), 200);
        }
    }

    /**
    *
    *date created 08-12-2016(dd-mm-yy)
    *date modified (dd-mm-yy)
    *fetch simple productid 
    *
    * @param (int)confProductId 
    * @param (int)xe_size  
    * @param (int)xe_color
    * 
    */
    public function getSimpleProductId(){
        $error = false;
        $result = $this->storeApiLogin();
        if($this->storeApiLogin == true){
            try {
                $productInfo = array(
                    'configId' => $this->_request['pid'],
                    'sizeId' => $this->_request['xe_size'],
                    'colorId' => $this->_request['xe_color'],
                    'color' => $this->getStoreAttributes("xe_color"),
                    'size' => $this->getStoreAttributes("xe_size")
                );
                $result = $this->apiCall('Product','getSimpleProductId',$productInfo);
                $result = $result->result;
                print_r($result);exit();
                // $this->response($result, 200);
            }catch(Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage'=>$e->getMessage()));
                $error = true;
            }
        }else{
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
            $this->response($this->json($msg), 200);
        }
    }
}
