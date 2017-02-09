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
        $this->wcApi = new WC_API_Client(C_KEY, C_SECRET, XEPATH);
    }

    /**
     * Used to get all the xe_size inside magento
     *
     * @param   nothing
     * @return  array contains all the xe_size inside store
     */
    public function getSizeArr()
    {
        header('HTTP/1.1 200 OK');
        $error = '';
        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];
            try {
                $sizes = get_terms('pa_xe_size', 'hide_empty = 0');
                $size_array = array();
                $i = 0;
                foreach ($sizes as $size) {
                    $size = (array) $size;
                    $size_array[$i]['value'] = $size['slug'];
                    $size_array[$i]['label'] = $size['name'];
                    $i++;
                }
            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $error = true;
            }
            if (!$error) {
                $this->response(json_encode($size_array), 200);
            } else {
                $this->response($this->formatJSONToArray($result), 200);
            }
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => $this->formatJSONToArray($result));
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
        header('HTTP/1.1 200 OK');
        global $wpdb;
        $error = '';
        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            $lastLoaded = ($this->_request['lastLoaded']) ? $this->_request['lastLoaded'] : 0;
            $loadCount = ($this->_request['loadCount']) ? $this->_request['loadCount'] : 0;
            $product_id = ($this->_request['productId']) ? $this->_request['productId'] : 0;
            $productColors = array();
            if ($product_id != 0) {
                $result = $this->wcApi->get_product($product_id);
                foreach ($result->product->attributes as $attribute) {
                    if (isset($attribute->name) && $attribute->name == 'xe_color') {
                        $productColors = $attribute->options;
                    }
                }
            }
            $num = ($lastLoaded == 0) ? $loadCount : $loadCount + $lastLoaded;
            $limit = '';
            if ($loadCount != 0) {
                $limit = "LIMIT " . $lastLoaded . "," . $loadCount;
            }

            $table_term = $wpdb->prefix . "terms";
            $table_taxo = $wpdb->prefix . "term_taxonomy";

            $key = $GLOBALS['params']['apisessId'];
            try {
                $sql = "SELECT t.term_id, t.name, t.slug FROM $table_term t LEFT JOIN $table_taxo tt ON (t.term_id = tt.term_id)  WHERE tt.taxonomy='pa_xe_color' ORDER BY t.term_id ASC $limit";
                $colors = $wpdb->get_results($sql) or die(mysql_error());
                $color_array = array();
                $i = 0;
                foreach ($colors as $color) {
                    $color = (array) $color;
                    if ($product_id != 0 && !empty($productColors)) {
                        if (in_array($color['slug'], $productColors) || in_array($color['name'], $productColors)) {
                            $color_array[$i]['value'] = $color['term_id'];
                            $color_array[$i]['label'] = $color['name'];
                            $color_array[$i]['swatchImage'] = $color['slug'] . '.png';
                            $i++;
                        }
                    } else {
                        $color_array[$i]['value'] = $color['term_id'];
                        $color_array[$i]['label'] = $color['name'];
                        $color_array[$i]['swatchImage'] = $color['slug'] . '.png';
                        $i++;
                    }

                }
            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $error = true;
            }
            if (!$error) {
                if ($isSameClass) {
                    return $color_array;
                } else {
                    $this->response(json_encode($color_array), 200);
                }

            } else {
                $this->response($this->formatJSONToArray($result), 200);
            }
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => $this->formatJSONToArray($result));
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
        global $wpdb;
        global $woocommerce;
        $start = $this->_request['start'];
        $range = $this->_request['range'];
        /* if($start!=0 && $start!='')
        $page = ($start/$range)+1;
        else
        $page = 1; */
        $page = $this->_request['offset'];
        $filter = array();
        if ($range != '' && $range != 0) {
            $filter['filter[limit]'] = $range;
        }

        $filter['filter[page]'] = $page;
        $filter['per_page'] = $range;
        $filter['page'] = $page;
        $filter['attribute'] = 'xe_is_designer';
        $filter['attribute_term'] = 1;
        if ($this->_request['categoryid'] != '' && $this->_request['categoryid'] != 0) {
            $cat = $this->_request['categoryid'];
            $table_name = $wpdb->prefix . "terms";
            $slug = $wpdb->get_var("SELECT slug FROM $table_name WHERE term_id='$cat'");
            $filter['filter[category]'] = $slug;
        }
        $error = false;

        $result = $this->wcApi->get_products($filter);

        $count = 0;

        if (!isset($result->errors)) {
            try {

                $productsArr = array();

                foreach ($result->products as $key => $value) {
                    $refid = get_post_meta($value->id, 'refid', true);
                    $variant = array();
                    $catArr = $value->categories;
                    if ((isset($this->_request['preDecorated']) && $this->_request['preDecorated'] == 'false')) {
                        if ($refid == '') {
                            if ($this->_request['searchstring'] != '') {
                                $search = $this->_request['searchstring'];
                                if (($value->title == $search) || strlen(stristr($value->title, $search)) > 0 || in_array($search, $value->tags)) {
                                    $productsArr[] = array('id' => $value->id, 'name' => $value->title, 'description' => wp_strip_all_tags($value->description), 'price' => $value->price, 'thumbnail' => $value->images[0]->src, 'image' => $value->images[0]->src, 'category' => $catArr);
                                    $count++;
                                }
                            } else {
                                $productsArr[] = array('id' => $value->id, 'name' => $value->title, 'description' => wp_strip_all_tags($value->description), 'price' => $value->price, 'thumbnail' => $value->images[0]->src, 'image' => $value->images[0]->src, 'category' => $catArr);
                                $count++;
                            }
                        }
                    } else {
                        if ($this->_request['searchstring'] != '') {
                            $search = $this->_request['searchstring'];
                            if (($value->title == $search) || strlen(stristr($value->title, $search)) > 0 || in_array($search, $value->tags)) {
                                $productsArr[] = array('id' => $value->id, 'name' => $value->title, 'description' => wp_strip_all_tags($value->description), 'price' => $value->price, 'thumbnail' => $value->images[0]->src, 'image' => $value->images[0]->src, 'category' => $catArr);
                                $count++;
                            }
                        } else {
                            $productsArr[] = array('id' => $value->id, 'name' => $value->title, 'description' => wp_strip_all_tags($value->description), 'price' => $value->price, 'thumbnail' => $value->images[0]->src, 'image' => $value->images[0]->src, 'category' => $catArr);
                            $count++;
                        }
                    }
                }
                $result = array('product' => $productsArr);
                $sql = "SELECT distinct pm.pk_id as printid,pm.name as printName
                            FROM " . TABLE_PREFIX . "print_method pm
                            JOIN " . TABLE_PREFIX . "print_setting  pst ON pm.pk_id = pst.pk_id
                            LEFT JOIN " . TABLE_PREFIX . "print_method_setting_rel pmsr ON pst.pk_id = pmsr.print_setting_id where pst.is_default = 1";
                $default_id = $this->executeFetchAssocQuery($sql);

                foreach ($result['product'] as $k => $product) {
                    $productPrintTypeSql = "SELECT distinct pm.pk_id, pm.name FROM " . TABLE_PREFIX . "print_method pm
                        INNER JOIN " . TABLE_PREFIX . "product_printmethod_rel ppr ON ppr.print_method_id = pm.pk_id
                        WHERE ppr.product_id=" . $product['id'];
                    $productPrintType = $this->executeGenericDQLQuery($productPrintTypeSql);

                    if (!empty($productPrintType)) {
                        $this->log('productPrintTypeSql: ' . $productPrintTypeSql, true, 'Zsql.log');
                        foreach ($productPrintType as $k2 => $v2) {
                            $product['print_details'][$k2]['prntMthdId'] = $v2['pk_id'];
                            $product['print_details'][$k2]['prntMthdName'] = $v2['name'];
                        }
                    } else {
                        $catIdArr = wp_get_post_terms($product['id'], 'product_cat', array('fields' => 'ids'));
                        $catIds = !empty($catIdArr) ? implode(',', (array) $catIdArr) : 0;
                        $catSql = 'SELECT DISTINCT pm.pk_id, pm.name
                                    FROM ' . TABLE_PREFIX . 'product_category_printmethod_rel AS pcpml
                                    JOIN ' . TABLE_PREFIX . 'print_method AS pm ON pm.pk_id = pcpml.print_method_id WHERE pcpml.product_category_id IN(' . $catIds . ')';

                        //$this->log('sql '.$sql, true, 'Zsql.log');
                        $rows = $this->executeFetchAssocQuery($catSql);

                        if (empty($rows)) {
                            //$this->log('if ', true, 'Zsql.log');
                            $default_print_type = "SELECT pmsr.print_method_id,pm.name FROM " . TABLE_PREFIX . "print_method_setting_rel AS pmsr JOIN " . TABLE_PREFIX . "print_setting ps ON pmsr.print_setting_id = ps.pk_id JOIN " . TABLE_PREFIX . "print_method AS pm ON pmsr.print_method_id = pm.pk_id WHERE ps.is_default='1' AND pm.is_enable='1' LIMIT 1";
                            $res = $this->executeFetchAssocQuery($default_print_type);
                            $product['print_details'][0]['prntMthdId'] = $res[0]['print_method_id'];
                            $product['print_details'][0]['prntMthdName'] = $res[0]['name'];
                        } else {
                            //$this->log('else ', true, 'Zsql.log');
                            foreach ($rows as $k1 => $v1) {
                                $product['print_details'][$k1]['prntMthdId'] = $v1['pk_id'];
                                $product['print_details'][$k1]['prntMthdName'] = $v1['name'];
                            }
                        }
                    }
                    $result['product'][$k] = $product;
                }

                $result['count'] = $count;
            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $error = true;
            }
            if (!$error) {
                $this->response($this->json($result), 200);
            } else {
                $msg = array('status' => 'failed', 'error' => $this->formatJSONToArray($result));
                $this->response($this->json($msg), 200);
            }
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => $result);
            $this->response($this->json($params), 200);
        }
        $this->response($this->json($params), 200);

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
        header('HTTP/1.1 200 OK');
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
            $msg = array('status' => 'apiLoginFailed', 'error' => $this->formatJSONToArray($result));
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
        require_once dirname(__FILE__) . '/../../../../../../../wp-admin/includes/plugin.php';
        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];
            try {
                $current_plugin = get_option("active_plugins");
                $myplugin = 'jck_woothumbs/jck_woothumbs.php';
                if (!in_array($myplugin, $current_plugin)) {
                    $result = 'Disabled';
                } else {
                    $result = 'Enabled';
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
            $msg = array('status' => 'apiLoginFailed', 'error' => $this->formatJSONToArray($result));
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
                $product_info = $this->wcApi->get_product($confId);
                $product = (array) $product_info->product;
                $attr_array = array();
                $colorArr = array();
                $j = 0;
                foreach ($product['variations'] as $variation) {
                    $variation = (array) $variation;
                    foreach ($variation['attributes'] as $attributes) {

                        $attributes = (array) $attributes;
                        if ($attributes['name'] == 'xe_color') {
                            $attr_array[$j]['color_id'] = $attributes['option'];
                            $colorArr[] = $attributes['option'];
                        } else if ($attributes['name'] == 'xe_size') {
                            $attr_array[$j]['size'] = $attributes['option'];
                        }

                    }
                    $j++;
                }
                $pvariants = array();
                $k = 0;
                $resultArr = array();
                $resultArr['conf_id'] = $confId;
                $colorArr = array_unique($colorArr);
                $colArr = array();
                foreach ($attr_array as $attribute) {
                    if (empty($colArr) || !in_array($attribute['color_id'], $colArr)) {
                        $colArr[] = $attribute['color_id'];
                        $pvariants[$k]['sizeid'] = array();
                        $pvariants[$k]['color_id'] = $attribute['color_id'];
                        $pvariants[$k]['sizeid'][] = $attribute['size'];
                        $k++;
                    } else {
                        $key = array_search($attribute['color_id'], $colArr);
                        $pvariants[$key]['sizeid'][] = $attribute['size'];
                    }

                }
                $resultArr['variants'] = $pvariants;
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
        global $wpdb;
        header('HTTP/1.1 200 OK');
        $error = false;
        $limit = 1; // default values
        $start = 0; // default values
        if (isset($this->_request['start']) && trim($this->_request['start']) != '') {
            $start = trim($this->_request['start']);
        }
        if (isset($this->_request['range']) && trim($this->_request['range']) != '') {
            $limit = trim($this->_request['range']);
        }
        $startIndex = (isset($this->_request['offset'])) ? (int) $this->_request['range'] * ((int) $this->_request['offset'] - 1) : 0;
        $result = $this->storeApiLogin();
        $confId = $this->_request['conf_pid'];
        if ($this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];
            try {
                $filters = array(
                    'confId' => $confId,
                    'range' => array('start' => $start, 'range' => $limit),
                );
                $result = $this->wcApi->get_product($confId);
                if (!isset($result->errors)) {
                    $i = 0;
                    $variant = array();
                    $color = array();
                    foreach ($result->product->attributes as $attribute) {
                        if ($attribute->name == 'xe_color') {
                            $color = $attribute->option;
                        }

                    }
                    $color_array = array();
                    foreach ($result->product->variations as $variations) {
                        foreach ($variations->attributes as $attribute) {
                            if ($attribute->name == 'xe_color') {
                                if (empty($variant) || (!empty($variant) && !in_array($attribute->option, $color_array))) {

                                    if (!empty($color) && in_array('#' . $attribute->option, $color)) {
                                        $variant[$i]['xeColor'] = '#' . $attribute->option;
                                    } else {
                                        $variant[$i]['xeColor'] = $attribute->option;
                                    }

                                    $color_array[] = $attribute->option;

                                    $table_name = $wpdb->prefix . "terms";
                                    $color_option = $attribute->option;
                                    $term_id = $wpdb->get_var("SELECT term_id FROM $table_name WHERE slug='" . $color_option . "' OR lower(name) = '" . strtolower($color_option) . "'");
                                    $variant[$i]['colorUrl'] = $term_id . '.png';
                                    $variant[$i]['id'] = $variations->id;
                                    $variant[$i]['name'] = $result->product->title;
                                    $variant[$i]['price'] = $variations->price;
                                    $variant[$i]['image'] = $variations->image[0]->src;
                                    $variant[$i]['xe_color_id'] = $term_id;
                                    $i++;
                                }
                            }
                        }
                    }
                    $variants = array();
                    $j = 0;
                    $length = sizeof($variant);
                    $range = (isset($this->_request['range'])) ? $this->_request['range'] : $length;
                    while ($startIndex < $length) {
                        if ($j < $range) {
                            $variants[$j]['id'] = $variant[$startIndex]['id'];
                            $variants[$j]['name'] = $variant[$startIndex]['name'];
                            $variants[$j]['price'] = $variant[$startIndex]['price'];
                            $variants[$j]['xeColor'] = $variant[$startIndex]['xeColor'];
                            $variants[$j]['xe_color_id'] = $variant[$startIndex]['xe_color_id'];
                            $variants[$j]['colorUrl'] = $variant[$startIndex]['colorUrl'];
                            $variants[$j]['thumbnail'] = $variant[$startIndex]['image'];
                            $j++;
                            $startIndex++;
                        } else {
                            break;
                        }
                    }

                    $resultArr['variants'] = $variants;
                    $resultArr['count'] = $length;
                    $productId = $confId;
                    foreach ($resultArr['variants'] as $key => $value) {
                        $surplusPrice = $resultArr['variants'][$key]['price'];
                        $sql = "SELECT ref_id,parent_id FROM " . TABLE_PREFIX . "template_state_rel WHERE temp_id = " . $confId;
                        $parentId = $this->executeFetchAssocQuery($sql);
                        if (!empty($parentId)) {
                            $sql = "SELECT custom_price FROM " . TABLE_PREFIX . "decorated_product WHERE product_id = " . $parentId[0]['parent_id'] . " and refid = " . $parentId[0]['ref_id'];
                            $res = $this->executeFetchAssocQuery($sql);
                            $customPrice = $res[0]['custom_price'];
                            $resultArr['variants'][$key]['price'] = $surplusPrice - $customPrice;
                            $resultArr['variants'][$key]['finalPrice'] = $surplusPrice;
                        }
                        $colorId = $resultArr['variants'][$key]['xe_color_id'];
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

                        $resultArr['variants'][$key]['colorUrl'] = $colorSwatch;
                    }
                }
            } catch (Exception $e) {
                $resultArr = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $error = true;
            }

            if (!$error) {
                $this->response($this->json($resultArr), 200);
            } else {
                $msg = array('status' => 'failed', 'error' => $this->formatJSONToArray($resultArr));
                $this->response($this->json($msg), 200);
            }
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => $this->formatJSONToArray($result));
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
        $error = false;
        global $wpdb;
        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];

            if (!isset($this->_request['productId']) || trim($this->_request['productId']) == '') {
                $msg = array('status' => 'invalid productId', 'productId' => $this->_request['productId']);
                $this->response($this->json($msg), 204);
            } else {
                $product_id = trim($this->_request['productId']);
            }

            if (!isset($this->_request['simplePdctId']) || trim($this->_request['simplePdctId']) == '') {
                $msg = array('status' => 'invalid simplePdctId', 'simplePdctId' => $this->_request['simplePdctId']);
                $this->response($this->json($msg), 204);
            } else {
                $varient_id = trim($this->_request['simplePdctId']);
            }

            $result = $this->wcApi->get_product($product_id);
            if (!isset($result->errors)) {
                try {
                    $variantsArr = array();
                    $i = 0;
                    $variant = array();
                    $colorArr = array();
                    foreach ($result->product->attributes as $value) {
                        if ($value->name == "xe_color") {
                            $colorArr = $value->options;
                        }

                    }
                    foreach ($result->product->variations as $variations) {
                        $variant[$i]['id'] = $variations->id;
                        foreach ($variations->attributes as $attribute) {
                            if ($attribute->name == 'xe_color') {
                                if ($variations->id == $varient_id) {
                                    if (!empty($colorArr) && in_array('#' . $attribute->option, $colorArr)) {
                                        $color = '#' . $attribute->option;
                                    } else {
                                        $color = $attribute->option;
                                    }

                                }
                                if (!empty($colorArr) && in_array('#' . $attribute->option, $colorArr)) {
                                    $variant[$i][$attribute->name] = '#' . $attribute->option;
                                } else {
                                    $variant[$i][$attribute->name] = $attribute->option;
                                }
                            } else {
                                $variant[$i][$attribute->name] = ucfirst($attribute->option);
                            }
                            $table_name = $wpdb->prefix . "terms";
                            $attr_val = $variant[$i][$attribute->name];

                            if ($attr_val != "") {
                                $variant[$i]['attributes'][$attribute->name] = $attr_val;
                            }
                        }

                        $variant[$i]['price'] = $variations->price;
                        $variant[$i]['quantity'] = $variations->stock_quantity;
                        $i++;
                    }
                    $j = 0;
                    $size_array = array();
                    foreach ($variant as $var) {
                        if (isset($this->_request['byAdmin'])) {
                            if (empty($size_array) || !in_array(strtoupper($var['attributes']['xe_size']), $size_array)) {
                                $size = $var['xe_size'];
                                $xecolor = $var['xe_color'];
                                $table_name = $wpdb->prefix . "terms";
                                $color_id = $wpdb->get_var("SELECT term_id FROM $table_name WHERE slug = '$xecolor'");
                                $size_id = $wpdb->get_var("SELECT term_id FROM $table_name WHERE slug = '$size'");
                                $variantsArr[$j]['simpleProductId'] = $var['id'];
                                $variantsArr[$j]['xe_size_id'] = $size_id;
                                $variantsArr[$j]['xe_color_id'] = $color_id;
                                $variantsArr[$j]['xe_color'] = $var['xe_color'];
                                $variantsArr[$j]['xe_size'] = strtoupper($var['xe_size']);
                                foreach ($var['attributes'] as $key => $value) {
                                    $attrId = $wpdb->get_var("SELECT term_id FROM $table_name WHERE slug = '$value'");
                                    $variantsArr[$j]['attributes'][$key] = $value;
                                    $variantsArr[$j]['attributes'][$key . '_id'] = $attrId;
                                }
                                $variantsArr[$j]['quantity'] = $var['quantity'];
                                $variantsArr[$j]['price'] = $var['price'];
                                $variantsArr[$j]['minQuantity'] = 1;
                                $size_array[] = $var['attributes']['xe_size'];
                                $j++;
                            }
                        } else {
                            if ($var['xe_color'] == $color) {
                                $size = $var['xe_size'];
                                $table_name = $wpdb->prefix . "terms";
                                $color_id = $wpdb->get_var("SELECT term_id FROM $table_name WHERE slug = '$color'");
                                $size_id = $wpdb->get_var("SELECT term_id FROM $table_name WHERE slug = '$size'");
                                $variantsArr[$j]['simpleProductId'] = $var['id'];
                                $variantsArr[$j]['xe_size_id'] = $size_id;
                                $variantsArr[$j]['xe_color_id'] = $color_id;
                                $variantsArr[$j]['xe_color'] = $var['xe_color'];
                                $variantsArr[$j]['xe_size'] = strtoupper($var['xe_size']);
                                foreach ($var['attributes'] as $key => $value) {
                                    $attrId = $wpdb->get_var("SELECT term_id FROM $table_name WHERE slug = '$value'");
                                    $variantsArr[$j]['attributes'][$key] = $value;
                                    $variantsArr[$j]['attributes'][$key . '_id'] = $attrId;
                                }
                                $variantsArr[$j]['quantity'] = $var['quantity'];
                                $variantsArr[$j]['price'] = $var['price'];
                                $variantsArr[$j]['minQuantity'] = 1;
                                $j++;
                            }
                        }
                        foreach ($variantsArr as $key => $value) {
                            $surplusPrice = $variantsArr[$key]['price'];
                            $sql = "SELECT ref_id,parent_id FROM " . TABLE_PREFIX . "template_state_rel WHERE temp_id = " . $product_id;
                            $parentId = $this->executeFetchAssocQuery($sql);
                            if (!empty($parentId)) {
                                $sql = "SELECT custom_price FROM " . TABLE_PREFIX . "decorated_product WHERE product_id = " . $parentId[0]['parent_id'] . " and refid = " . $parentId[0]['ref_id'];
                                $res = $this->executeFetchAssocQuery($sql);
                                $customPrice = $res[0]['custom_price'];
                                $variantsArr[$key]['price'] = $surplusPrice - $customPrice;
                                $variantsArr[$key]['finalPrice'] = $surplusPrice;
                            }
                        }
                    }
                    $result = array('quantities' => $variantsArr);
                } catch (Exception $e) {
                    $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                    $error = true;
                }
                $this->closeConnection();
                $this->response($this->json($result), 200);
            } else {
                $msg = array('status' => 'apiLoginFailed', 'error' => $result);
                $this->response($this->json($msg), 200);
            }

        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => $this->formatJSONToArray($result));
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
            $key = $GLOBALS['params']['apisessId'];
            try {
                $orderID = $this->_request['orderID'];
                $productID = $this->_request['productID'];
                $itemID = trim($this->_request['orderItemId']);
                $ref_id = trim($this->_request['refid']);

                $data['order']['id'] = $orderID;
                $data['order']['line_items'][] = array(
                    'id' => $itemID,
                    'product_id' => $productID,
                    'variations' => array(
                        'print_status' => 1,
                    ),
                );

                $data['order']['order_meta'][] = array(
                    'key' => "print_status",
                    'label' => "Print_status",
                    'value' => 1,

                );
                $result = $this->wcApi->update_order($orderID, $data);
                if (!isset($result->errors)) {
                    $sql = "update order_list set item_printed = 1 where refid = $ref_id and  itemid = '" . $itemID . "' ";
                    $stat = mysqli_query($this->db, $sql);
                }
            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $error = true;
            }
            if (!$error) {
                $this->response($result, 200);
            } else {
                $this->response($this->formatJSONToArray($result), 200);
            }

        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => $this->formatJSONToArray($result));
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
        header('HTTP/1.1 200 OK');
        $error = false;
        $result = $this->storeApiLogin();
        $t = time();
        if (!empty($this->_request) && $this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];
            $product_array = array();
            global $wpdb;
            $upload_dir = wp_upload_dir();
            if (isset($_FILES['configFile']['tmp_name'])) {
                $filename = str_replace(" ", "_", $_FILES['configFile']['name']);
                $filename = $t . "_" . $filename;
                $file = $upload_dir['path'] . "/" . trim($filename);
                if (file_exists($file)) {
                    $status = 1;
                } else {
                    $status = move_uploaded_file($_FILES['configFile']['tmp_name'], $file);
                }

                if ($status);
                {
                    $imageFile = $upload_dir['url'] . "/" . $filename;
                }
            }
            $image_array = array();
            $image_file = array();
            if (isset($_FILES['simpleFile'])) {
                foreach ($_FILES['simpleFile']['tmp_name'] as $key => $product_image) {
                    $t = time();
                    $filename = str_replace(" ", "_", $_FILES['simpleFile']['name'][$key]);
                    $filename = $t . "_" . $filename;
                    $file = $upload_dir['path'] . "/" . trim($filename);
                    if (file_exists($file)) {
                        $status = 1;
                    } else {
                        $status = move_uploaded_file($product_image, $file);
                    }

                    if ($status);
                    {
                        $image_array[] = $upload_dir['url'] . "/" . trim($filename);
                        $image_file[] = trim($filename);
                    }
                }
            }
            $data = $this->formatJSONToArray($this->_request['data']);
            $productData = $data->productData;
            $productVariants = $productData['variants'];
            $var_ids = array();
            $attr_array = array();
            $colorArr = array();
            $varCount = 0;
            if ($productData['conf_id'] == 0) {
                $var_color = $productVariants[0]->color_id;
                $table_name = $wpdb->prefix . "terms";
                $color = $wpdb->get_var("SELECT slug FROM $table_name WHERE term_id = '$var_color'");

                $product_array['product']['title'] = $productData['product_name'];
                $product_array['product']['type'] = 'variable';
                $product_array['product']['sku'] = $productData['sku'];
                $product_array['product']['virtual'] = false;
                $product_array['product']['price'] = $productData['price'];
                $product_array['product']['regular_price'] = $productData['price'];
                $product_array['product']['managing_stock'] = true;
                $product_array['product']['stock_quantity'] = $productData['qty'];
                $product_array['product']['in_stock'] = true;
                $product_array['product']['visible'] = true;
                $product_array['product']['catalog_visibility'] = "visible";
                $product_array['product']['weight'] = $productData['weight'];
                $product_array['product']['description'] = $productData['description'];
                $product_array['product']['short_description'] = $productData['short_description'];
                $product_array['product']['categories'] = $productData['cat_id'];
                $product_array['product']['images'][0]['src'] = $imageFile;
                $product_array['product']['images'][0]['position'] = 0;
                $product_array['product']['featured_src'] = $imageFile;
                $product_array['product']['attributes'][0]['name'] = 'xe_color';
                $product_array['product']['attributes'][0]['slug'] = 'xe_color';
                $product_array['product']['attributes'][0]['position'] = 0;
                $product_array['product']['attributes'][0]['visible'] = true;
                $product_array['product']['attributes'][0]['variation'] = true;
                $product_array['product']['attributes'][0]['options'] = array($color);
                $product_array['product']['attributes'][1]['name'] = 'xe_size';
                $product_array['product']['attributes'][1]['slug'] = 'xe_size';
                $product_array['product']['attributes'][1]['position'] = 0;
                $product_array['product']['attributes'][1]['visible'] = true;
                $product_array['product']['attributes'][1]['variation'] = true;
                $product_array['product']['attributes'][2]['name'] = 'xe_is_designer';
                $product_array['product']['attributes'][2]['slug'] = 'xe_is_designer';
                $product_array['product']['attributes'][2]['position'] = 0;
                $product_array['product']['attributes'][2]['visible'] = false;
                $product_array['product']['attributes'][2]['variation'] = false;
                $product_array['product']['attributes'][2]['options'] = array("1");
                $size_array = array();
                foreach ($productVariants as $variants) {
                    $i = 0;
                    $simpleProduct = (array) $variants->simpleProducts;
                    foreach ($simpleProduct as $variant) {
                        $variant = (array) $variant;
                        $product_array['product']['variations'][$i]['sku'] = $variant['sku'];
                        $product_array['product']['variations'][$i]['regular_price'] = $variant['price'];
                        $product_array['product']['variations'][$i]['managing_stock'] = true;
                        $product_array['product']['variations'][$i]['stock_quantity'] = $variant['qty'];
                        $product_array['product']['variations'][$i]['in_stock'] = true;
                        $product_array['product']['variations'][$i]['weight'] = $variant['weight'];
                        if (!empty($image_array)) {
                            $j = 0;
                            foreach ($image_array as $image) {
                                if ($imageFile != $image) {
                                    $product_array['product']['images'][$j]['src'] = $image;
                                    $product_array['product']['images'][$j]['position'] = $j;
                                }
                                $product_array['product']['variations'][$i]['image'][$j]['src'] = $image;
                                $product_array['product']['variations'][$i]['image'][$j]['position'] = $j;
                                $j++;
                            }
                        }
                        $product_array['product']['variations'][$i]['attributes'][0]['name'] = 'xe_color';
                        $product_array['product']['variations'][$i]['attributes'][0]['slug'] = 'xe_color';
                        $product_array['product']['variations'][$i]['attributes'][0]['option'] = $color;
                        $product_array['product']['variations'][$i]['attributes'][1]['name'] = 'xe_size';
                        $product_array['product']['variations'][$i]['attributes'][1]['slug'] = 'xe_size';
                        $product_array['product']['variations'][$i]['attributes'][1]['option'] = $variant['sizeId'];
                        $size_array[] = $variant['sizeId'];
                        $i++;
                    }
                }
                $product_array['product']['attributes'][1]['options'] = $size_array;

            } else {
                $product_info = $this->wcApi->get_product($productData['conf_id']);
                $colorArray = array();
                $sizeArr = array();

                foreach ($product_info->product->attributes as $attribute) {
                    if ($attribute->name == 'xe_color') {
                        foreach ($attribute->options as $option) {
                            $colorArray[] = $option;
                        }
                    } else if ($attribute->name == 'xe_size') {
                        foreach ($attribute->options as $option) {
                            $sizeArr[] = $option;
                        }
                    }

                }
                $a = 0;
                foreach ($product_info->product->variations as $variations) {
                    foreach ($variations->attributes as $attributes) {
                        $attributes = (array) $attributes;
                        if ($attributes['name'] == 'xe_color') {
                            $attr_array[$a]['color_id'] = $attributes['option'];
                            $colorArr[] = $attributes['option'];
                        } else if ($attributes['name'] == 'xe_size') {
                            $attr_array[$a]['size'] = $attributes['option'];
                        }

                    }
                    $a++;
                }
                $varCount = count($product_info->product->variations);
                $var_color = $productVariants[0]->color_id;
                $table_name = $wpdb->prefix . "terms";
                $color = $wpdb->get_var("SELECT slug FROM $table_name WHERE term_id='$var_color'");
                $colorArray[] = $color;
                $product_array = array();
                $product_array['product']['type'] = 'variable';
                $product_array['product']['attributes'][0]['name'] = 'xe_color';
                $product_array['product']['attributes'][0]['slug'] = 'xe_color';
                $product_array['product']['attributes'][0]['position'] = 0;
                $product_array['product']['attributes'][0]['visible'] = true;
                $product_array['product']['attributes'][0]['variation'] = true;
                $product_array['product']['attributes'][0]['options'] = $colorArray;
                $product_array['product']['attributes'][1]['name'] = 'xe_size';
                $product_array['product']['attributes'][1]['slug'] = 'xe_size';
                $product_array['product']['attributes'][1]['position'] = 0;
                $product_array['product']['attributes'][1]['visible'] = true;
                $product_array['product']['attributes'][1]['variation'] = true;
                $product_array['product']['attributes'][2]['name'] = 'xe_is_designer';
                $product_array['product']['attributes'][2]['slug'] = 'xe_is_designer';
                $product_array['product']['attributes'][2]['position'] = 0;
                $product_array['product']['attributes'][2]['visible'] = false;
                $product_array['product']['attributes'][2]['variation'] = false;
                $product_array['product']['attributes'][2]['options'] = array("1");
                $img_count = count($product_info->product->images);

                foreach ($product_info->product->variations as $pvar) {
                    $var_ids[] = $pvar->id;
                }
                foreach ($productVariants as $variants) {
                    $i = 0;
                    $simpleProduct = (array) $variants->simpleProducts;
                    foreach ($simpleProduct as $variant) {
                        $variant = (array) $variant;
                        $product_array['product']['variations'][$i]['sku'] = $variant['sku'];
                        $product_array['product']['variations'][$i]['regular_price'] = $variant['price'];
                        $product_array['product']['variations'][$i]['managing_stock'] = true;
                        $product_array['product']['variations'][$i]['stock_quantity'] = $variant['qty'];
                        $product_array['product']['variations'][$i]['in_stock'] = true;
                        $product_array['product']['variations'][$i]['weight'] = $variant['weight'];
                        if (!empty($image_array)) {
                            $j = 0;
                            $m = 0;
                            foreach ($image_array as $image) {
                                $product_array['product']['images'][$j]['src'] = $image;
                                $product_array['product']['images'][$j]['position'] = $j + $img_count;
                                $product_array['product']['variations'][$i]['image'][$m]['src'] = $image;
                                $product_array['product']['variations'][$i]['image'][$m]['position'] = $m;
                                $j++;
                                $m++;
                            }
                        }
                        $product_array['product']['variations'][$i]['attributes'][0]['name'] = 'xe_color';
                        $product_array['product']['variations'][$i]['attributes'][0]['slug'] = 'xe_color';
                        $product_array['product']['variations'][$i]['attributes'][0]['option'] = $color;
                        $product_array['product']['variations'][$i]['attributes'][1]['name'] = 'xe_size';
                        $product_array['product']['variations'][$i]['attributes'][1]['slug'] = 'xe_size';
                        $product_array['product']['variations'][$i]['attributes'][1]['option'] = $variant['sizeId'];
                        $sizeArr[] = $variant['sizeId'];
                        $i++;
                    }
                }
                $product_array['product']['attributes'][1]['options'] = $sizeArr;
            }

            if (!$error) {
                try {
                    if ($productData['conf_id'] == 0) {
                        $result = $this->wcApi->create_product($product_array);
                    } else {
                        $result = $this->wcApi->edit_product($productData['conf_id'], $product_array);
                    }
                    if (!empty($result)) {
                        $product = (array) $result->product;
                        $j = $varCount;

                        foreach ($product['variations'] as $variation) {
                            $variation = (array) $variation;
                            $i = 0;
                            $attach_id = array();
                            if (empty($var_ids) || !in_array($variation['id'], $var_ids)) {
                                foreach ($image_file as $filename) {
                                    if ($i > 0) {
                                        $finfo = getimagesize($upload_dir['path'] . '/' . basename($filename));
                                        $type = $finfo['mime'];
                                        $attachment = array(
                                            'guid' => $upload_dir['url'] . '/' . basename($filename),
                                            'post_mime_type' => $type,
                                            'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                                            'post_content' => '',
                                            'post_status' => 'inherit',
                                        );
                                        $attach_id[] = wp_insert_attachment($attachment, $upload_dir['subdir'] . '/' . basename($filename), $product['id']);
                                    }
                                    $i++;

                                }
                                if (!empty($attach_id)) {
                                    $var_image = implode(",", $attach_id);
                                    update_post_meta($variation['id'], 'variation_image_gallery', $var_image);
                                }

                                foreach ($variation['attributes'] as $attributes) {

                                    $attributes = (array) $attributes;
                                    if ($attributes['name'] == 'xe_color') {
                                        $attr_array[$j]['color_id'] = $attributes['option'];
                                        $colorArr[] = $attributes['option'];
                                    } else if ($attributes['name'] == 'xe_size') {
                                        $attr_array[$j]['size'] = $attributes['option'];
                                    }

                                }
                                $j++;

                            }

                        }
                    }
                    $product_id = ($productData['conf_id'] == 0) ? $result->product->id : $productData['conf_id'];

                    $pvariants = array();
                    $k = 0;
                    $productArr = array();
                    $productArr['conf_id'] = $product_id;
                    $colorArr = array_unique($colorArr);
                    $colArr = array();
                    foreach ($attr_array as $attribute) {
                        if (empty($colArr) || !in_array($attribute['color_id'], $colArr)) {
                            $colArr[] = $attribute['color_id'];
                            $pvariants[$k]['sizeid'] = array();
                            $pvariants[$k]['color_id'] = $attribute['color_id'];
                            $pvariants[$k]['sizeid'][] = $attribute['size'];
                            $k++;
                        } else {
                            $key = array_search($attribute['color_id'], $colArr);
                            $pvariants[$key]['sizeid'][] = $attribute['size'];
                        }

                    }
                    $productArr['variants'] = $pvariants;

                } catch (Exception $e) {
                    $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                    $error = true;
                }
            }
            if (!$error) {
                $this->response($this->json($productArr), 200);
            } else {
                $this->response($this->formatJSONToArray($result), 200);
            }
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => $this->formatJSONToArray($result));
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
        header('HTTP/1.1 200 OK');
        $error = false;
        global $wpdb;
        if (!empty($this->_request['data'])) {
            $data = $this->_request['data'];
            $apikey = $this->_request['apikey'];
            $result = $this->storeApiLogin();
            if ($this->storeApiLogin == true) {
                $key = $GLOBALS['params']['apisessId'];
                $var_color = $data['color_id'];
                $table_name = $wpdb->prefix . "terms";
                $colorData = $wpdb->get_row("SELECT * FROM $table_name WHERE term_id='$var_color'", ARRAY_A);
                $color = $colorData['slug'];
                $colorName = $colorData['name'];
                $oldProductData = $this->wcApi->get_product($data['simpleproduct_id']);
                $oldVariantId = 0;
                foreach ($oldProductData->product->variations as $variants) {
                    foreach ($variants->attributes as $attribute) {
                        if ($attribute->name == 'xe_color' && ($colorName == $attribute->option || $color == $attribute->option)) {
                            $oldVariantId = $variants->id;
                            foreach ($variants->image as $img) {
                                $image_array[] = $img->src;
                            }
                        }
                    }
                    if ($oldVariantId != 0) {
                        break;
                    }

                }
                $attachments = get_post_meta($oldVariantId, 'variation_image_gallery', true);
                $attachmentsExp = array_filter(explode(',', $attachments));
                foreach ($attachmentsExp as $id) {
                    $imageSrc = wp_get_attachment_image_src($id, 'full');
                    $image_array[] = $imageSrc[0];
                }
                if ($data['conf_id'] == 0) {
                    $product_array['product']['title'] = $data['product_name'];
                    $product_array['product']['type'] = 'variable';
                    $product_array['product']['sku'] = $data['sku'];
                    $product_array['product']['virtual'] = false;
                    $product_array['product']['price'] = $data['price'];
                    $product_array['product']['regular_price'] = $data['price'];
                    $product_array['product']['managing_stock'] = true;
                    $product_array['product']['stock_quantity'] = $data['qty'];
                    $product_array['product']['in_stock'] = true;
                    $product_array['product']['visible'] = true;
                    $product_array['product']['catalog_visibility'] = "visible";
                    $product_array['product']['weight'] = '';
                    $product_array['product']['description'] = $data['description'];
                    $product_array['product']['short_description'] = $data['short_description'];
                    $product_array['product']['categories'] = $data['cat_id'];
                    $product_array['product']['attributes'][0]['name'] = 'xe_color';
                    $product_array['product']['attributes'][0]['slug'] = 'xe_color';
                    $product_array['product']['attributes'][0]['position'] = 0;
                    $product_array['product']['attributes'][0]['visible'] = true;
                    $product_array['product']['attributes'][0]['variation'] = true;
                    $product_array['product']['attributes'][0]['options'] = array($color);
                    $product_array['product']['attributes'][1]['name'] = 'xe_size';
                    $product_array['product']['attributes'][1]['slug'] = 'xe_size';
                    $product_array['product']['attributes'][1]['position'] = 0;
                    $product_array['product']['attributes'][1]['visible'] = true;
                    $product_array['product']['attributes'][1]['variation'] = true;
                    if ($data['is_customized'] == 1) {
                        $product_array['product']['attributes'][2]['name'] = 'xe_is_designer';
                        $product_array['product']['attributes'][2]['slug'] = 'xe_is_designer';
                        $product_array['product']['attributes'][2]['position'] = 0;
                        $product_array['product']['attributes'][2]['visible'] = false;
                        $product_array['product']['attributes'][2]['variation'] = false;
                        $product_array['product']['attributes'][2]['options'] = array($data['is_customized']);
                    }
                    $count = 0;
                    foreach ($data['images'] as $productImage) {
                        $product_array['product']['images'][$count]['src'] = $productImage;
                        $product_array['product']['images'][$count]['position'] = $count;
                        if ($count == 0) {
                            $product_array['product']['featured_src'] = $productImage;
                        }

                        $count++;
                    }
                } else {
                    $product_info = $this->wcApi->get_product($data['conf_id']);
                    $colorArray = array();
                    $sizeArr = array();
                    foreach ($product_info->product->attributes as $attribute) {
                        if ($attribute->name == 'xe_color') {
                            foreach ($attribute->options as $option) {
                                $colorArray[] = $option;
                            }
                        } else if ($attribute->name == 'xe_size') {
                            foreach ($attribute->options as $option) {
                                $sizeArr[] = $option;
                            }
                        }

                    }
                    $a = 0;
                    foreach ($product_info->product->variations as $variations) {
                        foreach ($variations->attributes as $attributes) {
                            $attributes = (array) $attributes;
                            if ($attributes['name'] == 'xe_color') {
                                $attr_array[$a]['color_id'] = $attributes['option'];
                                $colorArr[] = $attributes['option'];
                            } else if ($attributes['name'] == 'xe_size') {
                                $attr_array[$a]['size'] = $attributes['option'];
                            }

                        }
                        $a++;
                    }
                    $varCount = count($product_info->product->variations);
                    $colorArray[] = $color;
                    $product_array = array();
                    $product_array['product']['type'] = 'variable';
                    $product_array['product']['attributes'][0]['name'] = 'xe_color';
                    $product_array['product']['attributes'][0]['slug'] = 'xe_color';
                    $product_array['product']['attributes'][0]['position'] = 0;
                    $product_array['product']['attributes'][0]['visible'] = true;
                    $product_array['product']['attributes'][0]['variation'] = true;
                    $product_array['product']['attributes'][0]['options'] = $colorArray;
                    $product_array['product']['attributes'][1]['name'] = 'xe_size';
                    $product_array['product']['attributes'][1]['slug'] = 'xe_size';
                    $product_array['product']['attributes'][1]['position'] = 0;
                    $product_array['product']['attributes'][1]['visible'] = true;
                    $product_array['product']['attributes'][1]['variation'] = true;
                    if ($data['is_customized'] == 1) {
                        $product_array['product']['attributes'][2]['name'] = 'xe_is_designer';
                        $product_array['product']['attributes'][2]['slug'] = 'xe_is_designer';
                        $product_array['product']['attributes'][2]['position'] = 0;
                        $product_array['product']['attributes'][2]['visible'] = false;
                        $product_array['product']['attributes'][2]['variation'] = false;
                        $product_array['product']['attributes'][2]['options'] = array($data['is_customized']);
                    }
                    $img_count = count($product_info->product->images);
                    foreach ($product_info->product->variations as $pvar) {
                        $var_ids[] = $pvar->id;
                    }
                }
                $i = 0;
                foreach ($data['sizes'] as $size) {
                    $product_array['product']['variations'][$i]['sku'] = '';
                    $product_array['product']['variations'][$i]['regular_price'] = $data['price'];
                    $product_array['product']['variations'][$i]['managing_stock'] = true;
                    $product_array['product']['variations'][$i]['stock_quantity'] = $data['qty'];
                    $product_array['product']['variations'][$i]['in_stock'] = true;
                    $product_array['product']['variations'][$i]['weight'] = '';
                    if (!empty($image_array)) {
                        $j = 0;
                        foreach ($image_array as $image) {
                            $product_array['product']['variations'][$i]['image'][$j]['src'] = $image;
                            $product_array['product']['variations'][$i]['image'][$j]['position'] = $j;
                            $j++;
                        }
                    }
                    $product_array['product']['variations'][$i]['attributes'][0]['name'] = 'xe_color';
                    $product_array['product']['variations'][$i]['attributes'][0]['slug'] = 'xe_color';
                    $product_array['product']['variations'][$i]['attributes'][0]['option'] = $color;
                    $product_array['product']['variations'][$i]['attributes'][1]['name'] = 'xe_size';
                    $product_array['product']['variations'][$i]['attributes'][1]['slug'] = 'xe_size';
                    $product_array['product']['variations'][$i]['attributes'][1]['option'] = $size;
                    $size_array[] = $size;
                    $i++;

                }
                $product_array['product']['attributes'][1]['options'] = $size_array;
                try {
                    if ($data['conf_id'] == 0) {
                        $result = $this->wcApi->create_product($product_array);
                        add_post_meta($result->product->id, 'refid', $data['ref_id']);
                    } else {
                        $result = $this->wcApi->edit_product($data['conf_id'], $product_array);
                    }
                    if (!empty($result)) {
                        $product = (array) $result->product;
                        $j = $varCount;

                        foreach ($product['variations'] as $variation) {
                            $variation = (array) $variation;
                            $i = 0;
                            $attach_id = array();
                            if (empty($var_ids) || !in_array($variation['id'], $var_ids)) {
                                foreach ($image_array as $image) {
                                    if ($i > 0) {
                                        $finfo = getimagesize($image);
                                        $type = $finfo['mime'];
                                        $filename = basename($image);
                                        $dirPath = explode("wp-content/uploads", $image);
                                        $subDir = explode($filename, $dirPath[1]);
                                        $attachment = array(
                                            'guid' => $image,
                                            'post_mime_type' => $type,
                                            'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                                            'post_content' => '',
                                            'post_status' => 'inherit',
                                        );
                                        $attach_id[] = wp_insert_attachment($attachment, $subDir[0] . basename($filename), $product['id']);
                                    }
                                    $i++;

                                }
                                if (!empty($attach_id)) {
                                    $var_image = implode(",", $attach_id);
                                    update_post_meta($variation['id'], 'variation_image_gallery', $var_image);
                                }

                                foreach ($variation['attributes'] as $attributes) {
                                    $attributes = (array) $attributes;
                                    if ($attributes['name'] == 'xe_color') {
                                        $attr_array[$j]['color_id'] = $attributes['option'];
                                        $colorArr[] = $attributes['option'];
                                    } else if ($attributes['name'] == 'xe_size') {
                                        $attr_array[$j]['size'] = $attributes['option'];
                                    }

                                }
                                $j++;
                            }
                        }
                    }
                    $product_id = ($productData['conf_id'] == 0) ? $result->product->id : $productData['conf_id'];
                    $pvariants = array();
                    $k = 0;
                    $productArr = array();
                    $productArr['conf_id'] = $product_id;
                    $colorArr = array_unique($colorArr);
                    $colArr = array();
                    foreach ($attr_array as $attribute) {
                        if (empty($colArr) || !in_array($attribute['color_id'], $colArr)) {
                            $color_id = $wpdb->get_var("SELECT term_id FROM $table_name WHERE slug='" . $attribute['color_id'] . "'");
                            $colArr[] = $attribute['color_id'];
                            $pvariants[$k]['sizeid'] = array();
                            $pvariants[$k]['color_id'] = $color_id;
                            $pvariants[$k]['sizeid'][] = $attribute['size'];
                            $k++;
                        } else {
                            $key = array_search($attribute['color_id'], $colArr);
                            $pvariants[$key]['sizeid'][] = $attribute['size'];
                        }

                    }
                    $productArr['variants'] = $pvariants;
                    $this->customRequest(array('productid' => $data['simpleproduct_id'], 'isTemplate' => 1));
                    $sides = sizeof($data['images']);
                    $productTemplate = $this->getProductTemplateByProductId($data['simpleproduct_id']);
                    $maskData = $this->getMaskData($sides);
                    $maskData = json_decode($maskData, true);
                    $printArea = array();
                    $printArea = $this->getPrintareaType($data['simpleproduct_id']);
                    $this->customRequest(array('maskScalewidth' => $maskData[0]['mask_width'], 'maskScaleHeight' => $maskData[0]['mask_height'], 'maskPrice' => $maskData[0]['mask_price'], 'scaleRatio' => $maskData[0]['scale_ratio'], 'scaleRatio_unit' => $maskData[0]['scaleRatio_unit'], 'maskstatus' => $printArea['mask'], 'unitid' => $printArea['unit_id'], 'pricePerUnit' => $printArea['pricePerUnit'], 'maxWidth' => $printArea['maxWidth'], 'maxHeight' => $printArea['maxHeight'], 'boundsstatus' => $printArea['bounds'], 'customsizestatus' => $printArea['custom_size'], 'customMask' => $printArea['customMask']));
                    $printSizes = $this->getDtgPrintSizesOfProductSides($data['simpleproduct_id']);
                    $this->customRequest(array('productid' => $productArr['conf_id'], 'jsondata' => json_encode($maskData), 'printsizes' => $printSizes));
                    $this->saveMaskData();
                    if ($printSizes['status'] != 'nodata') {
                        $this->setDtgPrintSizesOfProductSides();
                    }
                    $this->saveProductTemplateData($data['print_method_id'], $data['ref_id'], $data['simpleproduct_id'], $productArr['conf_id']);
                    if (!empty($productTemplate['tepmlate_id'])) {
                        $this->customRequest(array('pid' => $productArr['conf_id'], 'productTempId' => $productTemplate['tepmlate_id']));
                        $this->addTemplateToProduct();
                    }
                } catch (Exception $e) {
                    $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                    $error = true;
                }
                if (!$error) {
                    $this->response($this->json($productArr), 200);
                } else {
                    $this->response($this->formatJSONToArray($result), 200);
                }
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
        header('HTTP/1.1 200 OK');
        //$error='';
        $printProfile = Flight::printProfile();
        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true && isset($this->_request['pid']) && $this->_request['pid']) {
            $key = $GLOBALS['params']['apisessId'];
            $res = array();
            try {
                $catIdArr = wp_get_post_terms($productId, 'product_cat', array('fields' => 'ids'));

                if (empty($catIdArr)) {
                    $res = $printProfile->getDefaultPrintMethodId();
                } else {
                    $catIdStr = implode(',', $catIdArr);
                    $sql = 'SELECT DISTINCT pm.pk_id AS print_method_id,pm.name FROM ' . TABLE_PREFIX . 'print_method AS pm INNER JOIN ' . TABLE_PREFIX . 'product_category_printmethod_rel AS pcpmr ON pm.pk_id = pcpmr.print_method_id WHERE pcpmr.product_category_id IN(' . $catIdStr . ')';
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
        $catArr = array();
        $print_id = $this->_request['printId'];
        if (!isset($result->errors)) {
            try {
                $taxonomy = 'product_cat';
                $orderby = 'name';
                $show_count = 0; // 1 for yes, 0 for no
                $pad_counts = 0; // 1 for yes, 0 for no
                $hierarchical = 1; // 1 for yes, 0 for no
                $title = '';
                $empty = 0;

                $args = array(
                    'taxonomy' => $taxonomy,
                    'orderby' => $orderby,
                    'parent' => 0,
                    'show_count' => $show_count,
                    'pad_counts' => $pad_counts,
                    'hierarchical' => $hierarchical,
                    'title_li' => $title,
                    'hide_empty' => $empty,
                );
                $all_categories = get_categories($args);
                $all_categories = (array) $all_categories;
                if (isset($print_id) && $print_id != 0) {
                    $category_result = array();
                    $sql = "SELECT product_category_id FROM " . TABLE_PREFIX . "product_category_printmethod_rel WHERE print_method_id='$print_id'";
                    $category = array();
                    $rows = $this->executeGenericDQLQuery($sql);
                    $category = $rows;
                    foreach ($all_categories as $categories) {
                        $categories = (array) $categories;
                        for ($j = 0; $j < sizeof($category); $j++) {
                            if ($categories['term_id'] == $category[$j]['product_category_id']) {
                                $category_result[$j]['id'] = $categories['term_id'];
                                $category_result[$j]['name'] = $categories['name'];
                            }
                        }
                    }
                    $result_arr = array();
                    $result_arr['categories'] = array_values($category_result);
                    $this->response($this->json($result_arr), 200);
                } else {
                    $catArr = array();
                    foreach ($all_categories as $cat) {
                        $cat = (array) $cat;
                        $catArr[] = array('id' => $cat['term_id'], 'name' => $cat['name']);
                    }
                    $result = array('categories' => $catArr);
                    $this->response($this->json($result), 200);
                }

            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $error = true;
            }
            $this->response($this->json($result), 200);
        } else {
            $msg = array('status' => 'failed', 'error' => $result);
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
        if ($this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];
            try {
                $taxonomy = 'product_cat';
                $orderby = 'name';
                $show_count = 0; // 1 for yes, 0 for no
                $pad_counts = 0; // 1 for yes, 0 for no
                $hierarchical = 1; // 1 for yes, 0 for no
                $title = '';
                $empty = 0;

                $args = array(
                    'taxonomy' => $taxonomy,
                    'child_of' => 0,
                    'parent' => $this->_request['selectedCategory'],
                    'orderby' => $orderby,
                    'show_count' => $show_count,
                    'pad_counts' => $pad_counts,
                    'hierarchical' => $hierarchical,
                    'title_li' => $title,
                    'hide_empty' => $empty,
                );
                $sub_cats = get_categories($args);
                foreach ($sub_cats as $cat) {
                    $catArr[] = array('id' => "" . $cat->term_id . "", 'name' => $cat->name);
                }
                $result = array('subcategories' => $catArr);
            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $error = true;
            }

            if (!$error) {
                $categories = array();
                $this->response($this->json($result), 200);
            } else {
                $this->response($this->formatJSONToArray($result), 200);
            }
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => $this->formatJSONToArray($result));
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
                $msg = array('status' => 'failed', 'error' => $this->formatJSONToArray($result));
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
        global $wpdb;
        $_tax = new WC_Tax();
        if (!isset($this->_request['id']) || trim($this->_request['id']) == '') {
            $msg = array('status' => 'invalid id', 'id' => $this->_request['id']);
            $this->response($this->json($msg), 204); //terminate
        } else {
            $pid = trim($this->_request['id']);
        }
        if (!isset($this->_request['size']) || trim($this->_request['size']) == '') {
            $size = '';
        } else {
            $size = trim($this->_request['size']);
        }
        if (!isset($this->_request['confId']) || trim($this->_request['confId']) == '') {
            $configProduct_id = 0;
        } else {
            $configProduct_id = trim($this->_request['confId']);
        }
        $configId = ($this->_request['confId']) ? $this->_request['confId'] : '';
        $pro_id = ($configId != '' && $configId != $product_id) ? $configId : $pid;
        $attributes = array();
        $simpleProductId = '';
        if ($size != '') {
            $attributes['size'] = $size;
        }
        $result = $this->wcApi->get_product($pro_id);
        if (!isset($result->errors)) {

            try {
                $product_id = $result->product->id;
                $catArr = $result->product->categories;
                $catArray = array();
                $table_name = $wpdb->prefix . "terms";
                $table_name1 = $wpdb->prefix . "term_taxonomy";
                foreach ($catArr as $cat) {
                    $ID = $wpdb->get_var("SELECT term_id FROM $table_name WHERE name='$cat'");
                    $parent = $wpdb->get_var("SELECT parent FROM $table_name1 WHERE term_id=$ID");
                    $catArray[] = ($parent) ? $parent : $ID;
                }
                $typesArr = array();
                $colorArr = array();
                foreach ($result->product->attributes as $key => $value) {

                    if ($value->name == "size") {
                        $typesArr = $value->options;
                    }
                    if ($value->name == "xe_color") {
                        $colorArr = $value->options;
                    }

                }

                $productsArr = array('pid' => $result->product->id, 'pidtype' => 'configurable', 'pname' => $result->product->title, 'shortdescription' => wp_strip_all_tags($result->product->short_description), 'category' => $catArray);
                $table_name = $wpdb->prefix . "terms";
                $tax_table = $wpdb->prefix . "options";
                $tax_enabled = $wpdb->get_var("SELECT option_value FROM $tax_table WHERE option_name='woocommerce_calc_taxes'");
                if ($configId != '' && $configId != $pid) {
                    foreach ($result->product->variations as $variants) {
                        if ($variants->id == $pid) {
                            $pvariant = $variants;
                            $tax_rate = $_tax->get_rates($pvariant->tax_class);
                            $tax = 0;
                            if ($tax_enabled == 'yes') {
                                foreach ($tax_rate as $value) {
                                    $tax += $value['rate'];
                                }
                            }
                            $productsArr['pvid'] = $pvariant->id;
                            $productsArr['pvname'] = $result->product->title;
                            $productsArr['quanntity'] = $pvariant->stock_quantity;
                            $productsArr['price'] = $pvariant->price;
                            $productsArr['taxrate'] = $tax;
                            foreach ($pvariant->attributes as $attribute) {

                                if ($attribute->name == 'xe_color') {
                                    if (!empty($colorArr) && in_array('#' . $attribute->option, $colorArr)) {
                                        $productsArr['xecolor'] = '#' . $attribute->option;
                                    } else {
                                        $productsArr['xecolor'] = $attribute->option;
                                    }
                                } else {
                                    if ($attribute->name == 'xe_size') {
                                        $productsArr['xesize'] = ucfirst($attribute->option);
                                    } else {
                                        $productsArr[$attribute->name] = ucfirst($attribute->option);
                                    }

                                }
                                $attrId = $wpdb->get_var("SELECT term_id FROM $table_name WHERE slug='$attribute->option'");
                                $productsArr[$attribute->name . '_id'] = $attrId;
                                if ($attribute->option != "") {
                                    $productsArr['attributes'][$attribute->name] = $attribute->option;
                                    $productsArr['attributes'][$attribute->name . '_id'] = $attrId;
                                }
                            }
                            $attachments = get_post_meta($pvariant->id, 'variation_image_gallery', true);
                            $attachmentsExp = array_filter(explode(',', $attachments));
                            $variantImg = get_post_meta($pvariant->id, '_thumbnail_id', true);
                            if (!empty($pvariant->image) && $variantImg != 0) {
                                foreach ($pvariant->image as $img) {
                                    $image[] = $img->src;
                                }
                            }
                            foreach ($attachmentsExp as $id) {
                                $imageSrc = wp_get_attachment_image_src($id, 'full');
                                $image[] = $imageSrc[0];
                            }
                        }
                    }
                } else {
                    $pvariant = $result->product->variations[0];
                    $tax_rate = $_tax->get_rates($pvariant->tax_class);
                    $tax = 0;
                    if ($tax_enabled == 'yes') {
                        foreach ($tax_rate as $value) {
                            $tax += $value['rate'];
                        }
                    }
                    $productsArr['pvid'] = $pvariant->id;
                    $productsArr['pvname'] = $result->product->title;
                    $productsArr['quanntity'] = $pvariant->stock_quantity;
                    $productsArr['price'] = $pvariant->price;
                    $productsArr['taxrate'] = $tax;
                    foreach ($pvariant->attributes as $attribute) {
                        if ($attribute->name == 'xe_color') {
                            if (!empty($colorArr) && in_array('#' . $attribute->option, $colorArr)) {
                                $productsArr['xecolor'] = '#' . $attribute->option;
                            } else {
                                $productsArr['xecolor'] = $attribute->option;
                            }
                        } else {
                            if ($attribute->name == 'xe_size') {
                                $productsArr['xesize'] = ucfirst($attribute->option);
                            } else {
                                $productsArr[$attribute->name] = ucfirst($attribute->option);
                            }

                        }
                        $attrId = $wpdb->get_var("SELECT term_id FROM $table_name WHERE slug='$attribute->option'");
                        $productsArr[$attribute->name . '_id'] = $attrId;
                        if ($attribute->option != "") {
                            $productsArr['attributes'][$attribute->name] = $attribute->option;
                            $productsArr['attributes'][$attribute->name . '_id'] = $attrId;
                        }
                    }
                    $attachments = get_post_meta($pvariant->id, 'variation_image_gallery', true);
                    $attachmentsExp = array_filter(explode(',', $attachments));
                    $variantImg = get_post_meta($pvariant->id, '_thumbnail_id', true);
                    if (!empty($pvariant->image) && $variantImg != 0) {
                        foreach ($pvariant->image as $img) {
                            $image[] = $img->src;
                        }
                    }
                    foreach ($attachmentsExp as $id) {
                        $imageSrc = wp_get_attachment_image_src($id, 'full');
                        $image[] = $imageSrc[0];
                    }
                }
                $productsArr['thumbsides'] = $image;
                $productsArr['sides'] = $image;
                $productsArr['labels'] = array();
                $colorId = $productsArr['xe_color_id'];
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

                $productsArr['colorSwatch'] = $colorSwatch;
                $this->_request['productid'] = $pro_id; //Mask Info
                $this->_request['returns'] = true; //Mask Info
                $maskInfo = $this->getMaskData(sizeof($productsArr['sides']));

                $productsArr['maskInfo'] = $this->formatJSONToArray($maskInfo);

                $printsize = $this->getDtgPrintSizesOfProductSides($pro_id);
                $productsArr['printsize'] = $printsize;

                $printareatype = $this->getPrintareaType($pro_id);
                $productsArr['printareatype'] = $printareatype;
                $surplusPrice = $productsArr['price'];
                if (isset($product_id) && $product_id) {
                    $sql = "SELECT ref_id,parent_id FROM " . TABLE_PREFIX . "template_state_rel WHERE temp_id = " . $configProduct_id;
                    $parentId = $this->executeFetchAssocQuery($sql);
                    if (!empty($parentId)) {
                        $sql = "SELECT custom_price FROM " . TABLE_PREFIX . "decorated_product WHERE product_id = " . $parentId[0]['parent_id'] . " and refid = " . $parentId[0]['ref_id'];
                        $res = $this->executeFetchAssocQuery($sql);
                        $customPrice = $res[0]['custom_price'];
                        $productsArr['price'] = $surplusPrice - $customPrice;
                        $productsArr['finalPrice'] = $surplusPrice;
                    }
                }
                $productsArr['sizeAdditionalprices'] = $this->getSizeVariantAdditionalPriceClient($product_id, $this->_request['print_method_id']);

                $pCategories = $catArray;
                $pCategoryIds = array();
                for ($i = 0; $i < sizeof($pCategories); $i++) {
                    array_push($pCategoryIds, $pCategories[$i]);
                }
                $features = array();
                $productsArr['features'] = $features;
                $templates = array();
                if (isset($product_id) && $product_id) {
                    $sql = "SELECT template_id FROM template_product_rel WHERE product_id = " . $product_id;
                    $res = $this->executeFetchAssocQuery($sql);
                    foreach ($res as $k => $v) {
                        $templates[$k] = $v['template_id'];
                    }
                }
                $simpleProductId = $productsArr['pvid'];
                $productsArr['templates'] = $templates;
                $sql = "SELECT  distinct print_method_id,price,is_whitebase
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
                $productsArr['additionalprices'] = $priceDetails;
                $productsArr['is_product_template'] = false;
                $templateArr = $this->getProductTemplateByProductId($pro_id, $pCategories);
                if (!empty($templateArr) && $templateArr['tepmlate_id'] != '') {
                    $productsArr['is_product_template'] = true;
                    $productsArr['tepmlate_id'] = $templateArr['tepmlate_id'];
                    if (!empty($templateArr['thumbsides']) && !empty($templateArr['sides'])) {
                        $productsArr['thumbsides'] = $templateArr['thumbsides'];
                        $productsArr['sides'] = $templateArr['sides'];
                    } else {
                        $productsArr['thumbsides'] = [];
                        $productsArr['sides'] = [];
                    }
                }
                if (empty($productsArr['maskInfo'])) {
                    $maskInfo = $this->getMaskData(sizeof($templateArr['side_id']));
                    $productsArr['maskInfo'] = $this->formatJSONToArray($maskInfo);

                }
                $result = $productsArr;
            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $error = true;
            }
            $this->closeConnection();
            $this->response($this->json($result), 200);
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => $result);
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
        global $wpdb;
        $_tax = new WC_Tax();
        if (!isset($this->_request['id']) || trim($this->_request['id']) == '') {
            $msg = array('status' => 'invalid id', 'id' => $this->_request['id']);
            $this->response($this->json($msg), 204); //terminate
        } else {
            $pid = trim($this->_request['id']);
        }
        if (!isset($this->_request['size']) || trim($this->_request['size']) == '') {
            $size = '';
        } else {
            $size = trim($this->_request['size']);
        }
        $configId = ($this->_request['confId']) ? $this->_request['confId'] : '';
        $pro_id = ($configId != '' && $configId != $pid) ? $configId : $pid;
        $attributes = array();
        $simpleProductId = '';
        if ($size != '') {
            $attributes['size'] = $size;
        }

        $result = $this->wcApi->get_product($pro_id);
        if (!isset($result->errors)) {
            try {
                $product_id = $result->product->id;
                $catArr = $result->product->categories;
                $catArray = array();
                $table_name = $wpdb->prefix . "terms";
                $table_name1 = $wpdb->prefix . "term_taxonomy";
                foreach ($catArr as $cat) {
                    $ID = $wpdb->get_var("SELECT term_id FROM $table_name WHERE name='$cat'");
                    $parent = $wpdb->get_var("SELECT parent FROM $table_name1 WHERE term_id=$ID");
                    $catArray[] = ($parent) ? $parent : $ID;
                }
                $typesArr = array();
                $colorArr = array();
                foreach ($result->product->attributes as $key => $value) {

                    if ($value->name == "size") {
                        $typesArr = $value->options;
                    }
                    if ($value->name == "xe_color") {
                        $colorArr = $value->options;
                    }

                }
                $productsArr = array('pid' => $result->product->id, 'pidtype' => 'configurable', 'pname' => $result->product->title, 'category' => $catArray);
                $table_name = $wpdb->prefix . "terms";
                $image = array();
                if ($configId != '' && $configId != $pid) {
                    foreach ($result->product->variations as $variants) {
                        if ($variants->id == $pid) {
                            $pvariant = $variants;
                            $tax_rate = $_tax->get_rates($pvariant->tax_class);
                            $tax = 0;
                            foreach ($tax_rate as $value) {
                                $tax += $value['rate'];
                            }
                            $productsArr['pvid'] = $pvariant->id;
                            $productsArr['pvname'] = $result->product->title;
                            $productsArr['quanntity'] = $pvariant->stock_quantity;
                            $productsArr['price'] = $pvariant->price;
                            $productsArr['taxrate'] = $tax;
                            foreach ($pvariant->attributes as $attribute) {
                                if ($attribute->name == 'xe_size') {
                                    $productsArr['xesize'] = ucfirst($attribute->option);
                                    $size_id = $wpdb->get_var("SELECT term_id FROM $table_name WHERE slug='$attribute->option'");
                                    $productsArr['xe_size_id'] = $size_id;
                                }
                                if ($attribute->name == 'xe_color') {
                                    if (!empty($colorArr) && in_array('#' . $attribute->option, $colorArr)) {
                                        $productsArr['xecolor'] = '#' . $attribute->option;
                                    } else {
                                        $productsArr['xecolor'] = $attribute->option;
                                    }

                                    $color_id = $wpdb->get_var("SELECT term_id FROM $table_name WHERE slug='$attribute->option'");
                                    $productsArr['xe_color_id'] = $color_id;

                                }
                            }
                            $attachments = get_post_meta($pvariant->id, 'variation_image_gallery', true);
                            $attachmentsExp = array_filter(explode(',', $attachments));
                            $variantImg = get_post_meta($pvariant->id, '_thumbnail_id', true);
                            if (!empty($pvariant->image) && $variantImg != 0) {
                                foreach ($pvariant->image as $img) {
                                    $image[] = $img->src;
                                }
                            }
                            foreach ($attachmentsExp as $id) {
                                $imageSrc = wp_get_attachment_image_src($id, 'full');
                                $image[] = $imageSrc[0];
                            }
                        }
                    }
                } else {
                    $pvariant = $result->product->variations[0];
                    $tax_rate = $_tax->get_rates($pvariant->tax_class);
                    $tax = 0;
                    foreach ($tax_rate as $value) {
                        $tax += $value['rate'];
                    }
                    $productsArr['pvid'] = $pvariant->id;
                    $productsArr['pvname'] = $result->product->title;
                    $productsArr['quanntity'] = $pvariant->stock_quantity;
                    $productsArr['price'] = $pvariant->price;
                    $productsArr['taxrate'] = $tax;
                    foreach ($pvariant->attributes as $attribute) {
                        if ($attribute->name == 'xe_size') {
                            $productsArr['xesize'] = ucfirst($attribute->option);
                            $size_id = $wpdb->get_var("SELECT term_id FROM $table_name WHERE slug='$attribute->option'");
                            $productsArr['xe_size_id'] = $size_id;
                        }
                        if ($attribute->name == 'xe_color') {
                            if (!empty($colorArr) && in_array('#' . $attribute->option, $colorArr)) {
                                $productsArr['xecolor'] = '#' . $attribute->option;
                            } else {
                                $productsArr['xecolor'] = $attribute->option;
                            }

                            $color_id = $wpdb->get_var("SELECT term_id FROM $table_name WHERE slug='$attribute->option'");
                            $productsArr['xe_color_id'] = $color_id;
                        }
                    }
                    $attachments = get_post_meta($pvariant->id, 'variation_image_gallery', true);
                    $attachmentsExp = array_filter(explode(',', $attachments));
                    $variantImg = get_post_meta($pvariant->id, '_thumbnail_id', true);
                    if (!empty($pvariant->image) && $variantImg != 0) {
                        foreach ($pvariant->image as $img) {
                            $image[] = $img->src;
                        }
                    }
                    foreach ($attachmentsExp as $id) {
                        $imageSrc = wp_get_attachment_image_src($id, 'full');
                        $image[] = $imageSrc[0];
                    }
                }
                $productsArr['thumbsides'] = $image;
                $productsArr['sides'] = $image;
                $refid = get_post_meta($product_id, 'refid', true);
                $productsArr['isPreDecorated'] = ($refid != '') ? true : false;
                $this->customRequest(array('productid' => $pro_id, 'returns' => true));
                $maskInfo = $this->getMaskData(sizeof($productsArr['sides']));
                $productsArr['maskInfo'] = $this->formatJSONToArray($maskInfo);
                $printsize = $this->getDtgPrintSizesOfProductSides($pro_id);
                $productsArr['printsize'] = $printsize;
                $printareatype = $this->getPrintareaType($pro_id);
                $productsArr['printareatype'] = $printareatype;
                $cVariants = $variant;
                $cVariantsIds = array();
                for ($i = 0; $i < sizeof($cVariants); $i++) {
                    array_push($cVariantsIds, $cVariants[$i]['data']['id']);
                }
                $productsArr['sizeAdditionalprices'] = $this->getSizeVariantAdditionalPrice($pro_id);

                $pCategories = $catArray;
                $pCategoryIds = array();
                for ($i = 0; $i < sizeof($pCategories); $i++) {
                    array_push($pCategoryIds, $pCategories[$i]);
                }
                $features = array();
                $productsArr['features'] = $features;
                $templates = array();
                if (isset($pro_id) && $pro_id) {
                    $sql = "SELECT template_id FROM " . TABLE_PREFIX . "template_product_rel WHERE product_id = " . $pro_id;
                    $res = $this->executeFetchAssocQuery($sql);
                    foreach ($res as $k => $v) {
                        $templates[$k] = $v['template_id'];
                    }
                }
                $productsArr['templates'] = $templates;
                $productsArr['is_product_template'] = false;
                $templateArr = $this->getProductTemplateByProductId($pro_id, $pCategories);
                if (!empty($templateArr) && $templateArr['tepmlate_id'] != '') {
                    $productsArr['is_product_template'] = true;
                    $productsArr['tepmlate_id'] = $templateArr['tepmlate_id'];
                    if (!empty($templateArr['thumbsides']) && !empty($templateArr['sides'])) {
                        $productsArr['thumbsides'] = $templateArr['thumbsides'];
                        $productsArr['sides'] = $templateArr['sides'];
                    } else {
                        $productsArr['thumbsides'] = [];
                        $productsArr['sides'] = [];
                    }
                }
                if (empty($productsArr['maskInfo'])) {
                    $maskInfo = $this->getMaskData(sizeof($templateArr['side_id']));
                    $productsArr['maskInfo'] = $this->formatJSONToArray($maskInfo);

                }
                $result = $productsArr;
            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $error = true;
            }
            $this->closeConnection();
            $this->response($this->json($result), 200);
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => $result);
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
            INNER JOIN " . TABLE_PREFIX . "product_printmethod_rel ppr ON ppr.print_method_id = pm.pk_id
            JOIN " . TABLE_PREFIX . "print_setting AS pst ON pm.pk_id = pst.pk_id
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
                        $catIds = wp_get_post_terms($productId, 'product_cat', array('fields' => 'ids'));
                        $catIds = implode(',', (array) $catIds);
                        $catSql = 'SELECT DISTINCT pm.pk_id, pm.name
                            FROM ' . TABLE_PREFIX . 'product_category_printmethod_rel AS pcpml
                            JOIN ' . TABLE_PREFIX . 'print_method AS pm ON pm.pk_id = pcpml.print_method_id
                            JOIN ' . TABLE_PREFIX . 'print_setting AS pst ON pm.pk_id = pst.pk_id
                            LEFT JOIN ' . TABLE_PREFIX . 'print_method_setting_rel pmsr ON pst.pk_id = pmsr.print_setting_id
                            WHERE pcpml.product_category_id IN(' . $catIds . ')';
                        $rows = $this->executeFetchAssocQuery($catSql);

                        $printDetails = array();
                        if (empty($rows)) {
                            $default_print_type = "SELECT pm.pk_id,pm.name
                            from " . TABLE_PREFIX . "print_method AS pm
                            JOIN " . TABLE_PREFIX . "print_setting ps ON pm.pk_id = ps.pk_id
                            LEFT JOIN " . TABLE_PREFIX . "print_method_setting_rel pmsr ON ps.pk_id = pmsr.print_setting_id
                            WHERE ps.is_default='1' AND pm.is_enable ='1' AND ps.is_default='1'";

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

            $refid = get_post_meta($confProductId, 'refid', true);

            $fieldSql = 'SELECT distinct pm.pk_id AS print_method_id, pm.name';
            if ($additional_price) {
                $fieldSql .= ', pst.additional_price';
            }

            // Check whether product has specific print method assigned //
            $productPrintTypeSql = $fieldSql . ' FROM ' . TABLE_PREFIX . "print_method pm
            INNER JOIN " . TABLE_PREFIX . "product_printmethod_rel ppr ON ppr.print_method_id = pm.pk_id
            JOIN " . TABLE_PREFIX . "print_setting AS pst ON pm.pk_id = pst.pk_id
            WHERE ppr.product_id=" . $confProductId . " order by pm.pk_id ASC";
            $res = $this->executeFetchAssocQuery($productPrintTypeSql);
            $result_arr = array();
            if (empty($res)) {
                try {
                    $catIds = $catIds = wp_get_post_terms($confProductId, 'product_cat', array('fields' => 'ids'));

                    if (!empty($catIds)) {
                        $catIds = implode(',', $catIds);
                        $catSql = $fieldSql . ' FROM ' . TABLE_PREFIX . 'product_category_printmethod_rel AS pcpml
                                JOIN ' . TABLE_PREFIX . 'print_method AS pm ON pm.pk_id = pcpml.print_method_id
                                JOIN ' . TABLE_PREFIX . 'print_setting AS pst ON pm.pk_id = pst.pk_id
                                LEFT JOIN ' . TABLE_PREFIX . 'print_method_setting_rel pmsr ON pst.pk_id = pmsr.print_setting_id
                                WHERE pcpml.product_category_id IN(' . $catIds . ') order by pm.pk_id ASC';
                        $res = $this->executeFetchAssocQuery($catSql);
                        foreach ($res as $k => $v) {
                            $result_arr[$k]['print_method_id'] = $v['print_method_id'];
                            $result_arr[$k]['name'] = $v['name'];
                            $result_arr[$k]['fetched_from'] = 'category';
                            if ($refid != '') {
                                $result_arr[$k]['refid'] = $refid;
                            }

                        }
                        if (empty($res)) {
                            $res = $printProfile->getDefaultPrintMethodId();
                            foreach ($res as $k => $v) {
                                $result_arr[$k]['print_method_id'] = $v['print_method_id'];
                                $result_arr[$k]['name'] = $v['name'];
                                $result_arr[$k]['fetched_from'] = 'default';
                                if ($refid != '') {
                                    $result_arr[$k]['refid'] = $refid;
                                }

                            }
                        }
                    } else {
                        $res = $printProfile->getDefaultPrintMethodId();
                        foreach ($res as $k => $v) {
                            $result_arr[$k]['print_method_id'] = $v['print_method_id'];
                            $result_arr[$k]['name'] = $v['name'];
                            $result_arr[$k]['fetched_from'] = 'default';
                            if ($refid != '') {
                                $result_arr[$k]['refid'] = $refid;
                            }

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
                    if ($refid != '') {
                        $result_arr[$k]['refid'] = $refid;
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
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *product is custmizable or not by product id
     *
     *@param (int)pid
     *@return json data
     *
     */
    public function isCustomizable()
    {
        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true && isset($this->_request['pid']) && $this->_request['pid']) {
            $confProductId = $this->_request['pid'];
            $result = $this->wcApi->get_product($confProductId);
            $colorArr = array();
            foreach ($result->product->attributes as $key => $value) {
                if ($value->name == "xe_is_designer") {
                    $colorArr = $value->options;
                }

            }
            if (!empty($colorArr) && in_array(1, $colorArr)) {
                $customizable = 1;
            } else {
                $customizable = 0;
            }

            $this->response($customizable, 200);
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
            try {
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
