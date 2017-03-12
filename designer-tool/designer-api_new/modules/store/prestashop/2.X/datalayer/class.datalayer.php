<?php
ini_set('display_errors', 'on');
require_once dirname(__FILE__) . '/../../../../../../../config/config.inc.php';
require_once dirname(__FILE__) . '/../../../../../../../init.php';

class Datalayer
{
    /** @var int Module ID */
    public $id = null;
    /** @var float Version */
    public $version;
    /** @var array filled with known compliant PS versions */
    public $ps_versions_compliancy = array();
    /** @var string Unique name */
    public $name;
    /** @var string A little description of the module */
    public $description;
    /** @var int need_instance */
    public $need_instance = 1;
    /** @var bool Status */
    public $active = false;
    /** @var bool Is the module certified by addons.prestashop.com */
    public $enable_device = 7;
    /** @var array to store the limited country */
    public $limited_countries = array();
    /** for add group*/
    public $groupBox;
    public $available_date;
    /** @var array names of the controllers */
    public $controllers = array();
    /** @var array current language translations */
    protected $_lang = array();
    /** @var string Module web path (eg. '/shop/modules/modulename/')  */
    protected $_path = null;
    /**
     * @since 1.5.0.1
     * @var string Module local path (eg. '/home/prestashop/modules/modulename/')
     */
    protected $local_path = null;
    /** @var array Array filled with module errors */
    protected $_errors = array();
    /** @var array Array  array filled with module success */
    protected $_confirmations = array();
    /** @var string Main table used for modules installed */
    protected $table = 'module';
    /** @var string Identifier of the main table */
    protected $identifier = 'id_module';
    /** @var array Array cache filled with modules informations */
    protected static $modules_cache;
    /** @var array Array filled with cache translations */
    protected static $l_cache = array();
    /** @var Context */
    protected $context;
    protected static $update_translations_after_install = true;
    public $push_time_limit = 180;
    /** @var bool Random session for modules perfs logs*/
    const CACHE_FILE_TAB_MODULES_LIST = '/config/xml/tab_modules_list.xml';
    /**
     * Get all available products
     *
     * @param (int)id_lang Language id
     * @param (int)start Start number
     * @param (int) limit Number of products to return
     * @param (string) order_by Field for ordering
     * @param (string)order_way Way for ordering (ASC or DESC)
     * @return array Products details
     */
    public function getAllProducts($start, $range, $searchstring, $categoryid, $loadVariants, $preDecorated)
    {
        try {
            $id_lang = Context::getContext()->language->id;
            $custom_ssl_var = 0;
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
                $custom_ssl_var = 1;
            }

            if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
                $baseUrl = _PS_BASE_URL_SSL_;
                $xeStoreUrl = _PS_BASE_URL_SSL_ . __PS_BASE_URI__;
            } else {
                $baseUrl = _PS_BASE_URL_;
                $xeStoreUrl = _PS_BASE_URL_ . __PS_BASE_URI__;
            }
            $sql .= "SELECT DISTINCT p.id_product,p.price,pl.name,pl.description FROM " . _DB_PREFIX_ . "product AS p JOIN " . _DB_PREFIX_ . "product_lang AS pl  ON p.id_product = pl.id_product WHERE p.customize='1' and p.active=1 and p.available_for_order=1 and pl.id_lang ='$id_lang' ";
            $sql .= $preDecorated ? '' : " and p.xe_is_temp ='0'";
            $limit = '';
            if (isset($start) && isset($range)) {
                $limit = ' limit ' . (int) $start . ' ,' . (int) $range;
            }
            if ((isset($categoryid) && $categoryid != "") && (isset($subcategoryid) && $subcategoryid != "")) {
                $cat_array = array($categoryid, $subcategoryid);
                $sql .= " AND p.id_product IN(SELECT cp.id_product FROM " . _DB_PREFIX_ . "category_product cp WHERE cp.id_category IN (" . implode(',', $cat_array) . "))";
            }
            if (isset($categoryid) && $categoryid != "" && (!isset($subcategoryid))) {
                $cat_array = array($categoryid);
                $sql .= " AND p.id_product IN(SELECT cp.id_product FROM " . _DB_PREFIX_ . "category_product cp WHERE cp.id_category IN (" . implode(',', $cat_array) . "))";
                //$sql .= " AND p.id_category_default  =".$categoryid."";
            }
            $matchString = preg_match("/'/u", $searchstring);
            if (isset($searchstring) && $searchstring != "") {
                $searchstring = $matchString ? mysql_real_escape_string($searchstring) : $searchstring;
                $sql .= " AND pl.name like '%" . ($searchstring) . "%' ";
            }
            $sql .= " order by p.id_product DESC ";
            $sql .= $limit;
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            $resultArr = array();
            foreach ($result as $k => $v) {
                //get product thumbnail
                //$image = Image::getCover($v['id_product']);
                // get Product cover image (all images is possible retrieve by
                $id_image = $this->getProductCoverImageId($v['id_product']);
                // get Image by id
                if (sizeof($id_image) > 0) {
                    $image = new Image($id_image['id_image']);
                    // get image full URL
                    $thumbnail = $baseUrl . _THEME_PROD_DIR_ . $image->getExistingImgPath() . "-small_default.jpg";
                }
                $resultArr[$k]['id'] = $v['id_product'];
                $resultArr[$k]['name'] = $v['name'];
                $resultArr[$k]['description'] = trim(strip_tags($v['description']));
                $resultArr[$k]['price'] = $v['price'];
                $resultArr[$k]['thumbnail'] = $thumbnail;
                //fetch all category by product id
                $sql_fetch = "SELECT DISTINCT id_category FROM " . _DB_PREFIX_ . "category_product WHERE id_product =" . (int) $v['id_product'] . "";
                $categoryArr = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_fetch);
                foreach ($categoryArr as $v2) {
                    $resultArr[$k]['category'][] = $v2['id_category'];
                }
                $resultArr[$k]['store'][] = (int) Context::getContext()->shop->id;
            }
            $resultData = array();
            $resultData['product'] = $resultArr;
            $resultData['count'] = count($resultArr);
            return json_encode($resultData);
        } catch (PrestaShopDatabaseException $ex) {
            echo 'Other error: <br />' . $ex->getMessage();
        }
    }
    /**
     *Get all available category
     *
     *@param nothing
     *@return array category details
     */
    public function getCategories()
    {
        try {
            $id_lang = Context::getContext()->language->id;
            $shop_id = Context::getContext()->shop->id;
            $sql = "SELECT DISTINCT c.id_category,cl.name FROM " . _DB_PREFIX_ . "category AS c," . _DB_PREFIX_ . "category_lang AS cl
			WHERE c.id_category = cl.id_category
			AND cl.id_lang=".$id_lang." AND cl.id_shop=".$shop_id." AND cl.name !='ROOT' ORDER BY cl.name";
            $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            $result = array();
            if (!empty($rows)) {
                foreach ($rows as $key => $value) {
                    $result[$key]['id'] = $value['id_category'];
                    $result[$key]['name'] = $value['name'];
                }
            }
            return json_encode(array('categories' => array_values($result)));
        } catch (PrestaShopDatabaseException $ex) {
            echo 'Other error: <br />' . $ex->getMessage();
        }

    }
    /**
     * Get all subcategory by category id
     *
     * @param (int)categoryId
     * @return array category details
     */
    public function getsubCategories($categoryId)
    {
        try {
            $id_lang = Context::getContext()->language->id;
            $shop_id = Context::getContext()->shop->id;
            $sql = "SELECT c.id_category,cl.name FROM " . _DB_PREFIX_ . "category AS c," . _DB_PREFIX_ . "category_lang AS cl
			WHERE c.id_category = cl.id_category and c.id_parent = '$categoryId'
			AND cl.id_lang='$id_lang' AND cl.id_shop='$shop_id' AND cl.name !='ROOT' ORDER BY cl.name";
            $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            $result = array();
            if (!empty($rows)) {
                foreach ($rows as $key => $value) {
                    $result[$key]['id'] = $value['id_category'];
                    $result[$key]['name'] = $value['name'];
                }
            }
            return json_encode(array('subcategories' => $result));
        } catch (PrestaShopDatabaseException $ex) {
            echo 'Other error: <br />' . $ex->getMessage();
        }
    }
    /**
     * Get simple product by produt id from store
     *
     * @param (int)pvid
     * @param (int)configId
     * @return array category details
     */
    public function getSimpleProducts($pvid, $configId)
    {
        try {
            $context = \Context::getContext();
            $lang_id = (int) Context::getContext()->cookie->id_lang;
            $id_shop = (int) Context::getContext()->shop->id;
            $custom_ssl_var = 0;
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
                $custom_ssl_var = 1;
            }

            if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
                $baseUrl = _PS_BASE_URL_SSL_;
                $basepath = _PS_BASE_URL_SSL_ . __PS_BASE_URI__;
            } else {
                $baseUrl = _PS_BASE_URL_;
                $basepath = _PS_BASE_URL_ . __PS_BASE_URI__;
            }
            $result = array();
            $tiers_price = array();
            $result['isPreDecorated'] = false;
            if ($configId == $pvid) {
                $sql = "SELECT count(*) count FROM " . _DB_PREFIX_ . "product WHERE 	id_product='$configId' AND xe_is_temp='1'";
                $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                if ($row[0]['count']) {
                    $result['isPreDecorated'] = true;
                    $sql_exit = "SELECT id_product_attribute FROM " . _DB_PREFIX_ . "product_attribute WHERE id_product='" . $configId . "' AND id_product_attribute='" . $pvid . "' AND xe_is_temp='0'";
                    $exist = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_exit);
                    if (!empty($exist)) {
                        $combinationsId = $exist[0]['id_product_attribute'];
                    } else {
                        $sql_fetch = "SELECT id_product_attribute FROM " . _DB_PREFIX_ . "product_attribute WHERE id_product='" . $configId . "' AND xe_is_temp='0'";
                        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_fetch);
                        $combinationsId = $row[0]['id_product_attribute'];
                    }
                } else {
                    $sql_exit = "SELECT id_product_attribute FROM " . _DB_PREFIX_ . "product_attribute WHERE id_product='" . $configId . "' AND id_product_attribute='" . $pvid . "'";
                    $exist = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_exit);
                    if (!empty($exist)) {
                        $combinationsId = $pvid;
                    } else {
                        $sql_fetch = "SELECT id_product_attribute FROM " . _DB_PREFIX_ . "product_attribute WHERE id_product='" . $configId . "'";
                        $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_fetch);
                        $combinationsId = $rows[0]['id_product_attribute'];
                    }
                }
            } else {
                $sql = "SELECT count(*) count FROM " . _DB_PREFIX_ . "product WHERE 	id_product='$configId' AND xe_is_temp='1'";
                $exist = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                if ($exist[0]['count']) {
                    $result['isPreDecorated'] = true;
                    $sql_exit = "SELECT id_product_attribute FROM " . _DB_PREFIX_ . "product_attribute WHERE id_product='" . $configId . "' AND id_product_attribute='" . $pvid . "' AND xe_is_temp='1'";
                    $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_exit);
                    $context = \Context::getContext();
                    $product = new Product($configId, false, $context->language->id);
                    $combinations = $product->getAttributeCombinations((int) ($context->cookie->id_lang));
                    foreach ($combinations as $v1) {
                        if ($v1['id_product_attribute'] == $row[0]['id_product_attribute']) {
                            if ($v1['is_color_group'] == 0 && $v1['group_name'] != 'Pdp') {
                                $size_id = $v1['id_attribute'];
                            }
                            if (($v1['is_color_group'] == 1) && ($v1['group_name'] == 'Color')) {
                                $color_id = $v1['id_attribute'];
                            }
                        }
                    }
                    $sql1 = "SELECT id_product_attribute FROM " . _DB_PREFIX_ . "product_attribute_combination WHERE id_attribute ='$color_id' AND xe_is_temp='1' ";
                    $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql1);
                    foreach ($rows as $v) {
                        $sql2 = "SELECT id_product_attribute FROM " . _DB_PREFIX_ . "product_attribute_combination WHERE id_attribute ='$size_id' AND id_product_attribute='" . $v['id_product_attribute'] . "' AND xe_is_temp='1' ";
                        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql2);
                        if (!empty($row)) {
                            $combinationsId = $row[0]['id_product_attribute'];
                        }
                    }
                } else {
                    $combinationsId = $pvid;
                }
            }
            $product_sql = "SELECT p.id_product,p.id_tax_rules_group,p.price,pl.name,pl.description_short,cp.id_category,pa.minimal_quantity FROM " . _DB_PREFIX_ . "product as p,
			" . _DB_PREFIX_ . "product_lang as pl," . _DB_PREFIX_ . "product_attribute as pa," . _DB_PREFIX_ . "category_product as cp WHERE p.id_product ='$configId' AND p.id_product = pa.id_product AND
			p.id_product = pl.id_product AND p.id_product = cp.id_product AND pl.id_lang ='$lang_id' AND pl.id_shop ='$id_shop'";
            $rowsData = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($product_sql);

            $result['pid'] = $rowsData[0]['id_product'];
            $result['pidtype'] = 'simple';
            $result['pname'] = $rowsData[0]['name'];
			$result['minQuantity'] = $rowsData[0]['minimal_quantity'];
            $result['shortdescription'] = trim(strip_tags($rowsData[0]['description_short']));
            foreach ($rowsData as $v) {
                $result['category'][] = $v['id_category'];
            }

            $result['pvid'] = $combinationsId;
            $result['pvname'] = $rowsData[0]['name'];
            //fetch size name and id by combination id//
            $sql_size = "select sa.quantity,al.id_attribute,al.name from " . _DB_PREFIX_ . "attribute_lang as al
				left join " . _DB_PREFIX_ . "product_attribute_combination as pac on al.id_attribute = pac.id_attribute
				left join " . _DB_PREFIX_ . "attribute atr on al.id_attribute = atr.id_attribute
				join " . _DB_PREFIX_ . "stock_available sa on  pac.id_product_attribute = sa.id_product_attribute
				where atr.color =''
				and atr.id_attribute_group=(select ag.id_attribute_group from " . _DB_PREFIX_ . "attribute_group as ag, " . _DB_PREFIX_ . "attribute_group_lang agl where ag.is_color_group=0 and agl.name='Size' and agl.id_attribute_group = ag.id_attribute_group and id_lang=".$lang_id." limit 1)
				and pac.id_product_attribute = '" . $combinationsId . "' ";
            $result_size = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_size);
            //fetch color name and id by combination id//
            $sql_color = "select atr.color,atr.id_attribute as color_id,al.name from " . _DB_PREFIX_ . "attribute_lang as al,
			" . _DB_PREFIX_ . "product_attribute_combination as pac,
			" . _DB_PREFIX_ . "attribute as atr
			where al.id_attribute = pac.id_attribute
			and atr.id_attribute = al.id_attribute
			and atr.id_attribute_group=(select ag.id_attribute_group from " . _DB_PREFIX_ . "attribute_group as ag, " . _DB_PREFIX_ . "attribute_group_lang agl where ag.is_color_group=1 and agl.name='Color' and agl.id_attribute_group = ag.id_attribute_group and id_lang=".$lang_id." limit 1)
			and pac.id_product_attribute = '" . $combinationsId . "' ";
            $result_color = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_color);
            $imageUrlExit = file_exists(_PS_IMG_DIR_ . 'co' . '/' . (int) $result_color[0]['color_id'] . '.jpg');
            $imageUrl = $basepath . 'img/' . 'co' . '/' . (int) $result_color[0]['color_id'] . '.jpg';
            $result['xecolor'] = $result_color ? $result_color[0]['name'] : '';
            $result['colorSwatch'] = $imageUrlExit ? $imageUrl : ($result_color[0]['color'] ? $result_color[0]['color'] : '');
            $result['xesize'] = $result_size ? $result_size[0]['name'] : '';
            $result['xe_color_id'] = $result_color ? $result_color[0]['color_id'] : '';
            $result['xe_size_id'] = $result_size ? $result_size[0]['id_attribute'] : '';
            $result['quanntity'] = $result_size ? intval($result_size[0]['quantity']) : '';
            /*fetch extra tax */
            if ($rowsData[0]['id_tax_rules_group']) {
                $sql = "SELECT price_display_method from " . _DB_PREFIX_ . "group WHERE id_group='" . $context->customer->id_default_group . "'";
                $rq = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                if ($rq['0']['price_display_method'] == 0) {
                    $tax_sql = "SELECT t.rate FROM " . _DB_PREFIX_ . "tax AS t," . _DB_PREFIX_ . "tax_rule AS tr WHERE id_tax_rules_group=" . $rowsData[0]['id_tax_rules_group'] . "
					AND tr.id_country = " . $context->country->id . " AND tr.id_tax = t.id_tax";
                    $result_tax = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($tax_sql);
                    $result['taxrate'] = $result_tax ? $result_tax[0]['rate'] : 0;
                } else {
                    $result['taxrate'] = 0;
                }
            } else {
                $result['taxrate'] = 0;
            }
            $result['price'] = $rowsData[0]['price'];
            $country_id = (int) Context::getContext()->country->id;
            //tier price get by spacified country id
            $tire_sql = "SELECT reduction,from_quantity FROM " . _DB_PREFIX_ . "specific_price_rule WHERE id_country = " . $country_id . "";
            $result_tier_price = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($tire_sql);
            if (!empty($result_tier_price)) {
                foreach ($result_tier_price as $k => $v) {
                    $tiers_price[$k]['tierQty'] = intval($v['from_quantity']);
                    $tiers_price[$k]['percentage'] = floatval($v['reduction']);
                    $tiers_price[$k]['tierPrice'] = number_format($result['price'] - $v['reduction'], 5);
                }
            } else {
                $tire_price_country_sql = "SELECT reduction,from_quantity FROM " . _DB_PREFIX_ . "specific_price_rule "; //Tier price for all country
                $result_tire_price_country = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($tire_price_country_sql);
                foreach ($result_tire_price_country as $k1 => $v1) {
                    $tiers_price[$k1]['tierQty'] = intval($v1['from_quantity']);
                    $tiers_price[$k1]['percentage'] = floatval($v1['reduction']);
                    $tiers_price[$k1]['tierPrice'] = number_format($result['price'] - $v1['reduction'], 5);
                }
            }
            $result['tierPrices'] = $tiers_price;
            //fetch product thumbnail by combination id
            $sql = "Select pai.id_image from " . _DB_PREFIX_ . "product_attribute_image as pai
			join " . _DB_PREFIX_ . "image as im on im.id_image = pai.id_image
			where pai.id_product_attribute = " . $combinationsId . " order by im.position asc";
            $rq = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            if (!empty($rq)) {
                foreach ($rq as $k2 => $v2) {
                    if (sizeof($v2) > 0) {
                        $image = new Image($v2['id_image']);
                        // get image full URL
                        $sideIamgeUrl = $baseUrl . _THEME_PROD_DIR_ . $image->getExistingImgPath() . ".jpg"; //for product thumbnail
                        $thumbnail = $baseUrl . _THEME_PROD_DIR_ . $image->getExistingImgPath() . "-small_default.jpg"; //for prduct side image
                    }
                    $result['thumbsides'][] = $thumbnail;
                    $result['sides'][] = $sideIamgeUrl;
                    //fetch all labels by imageid
                    $sql_data = "select legend from " . _DB_PREFIX_ . "image_lang where id_image='" . $v2['id_image'] . "' and id_lang='$lang_id'";
                    $result_sql = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_data);
                    foreach ($result_sql as $v3) {
                        $result['labels'][] = $v3['legend'];
                    }
                }
            } else {
                $result['thumbsides'] = [];
                $result['sides'] = [];
                $result['labels'] = [];
            }
            //Start extra attribute for product
            $products = new Product($result['pid'], false, $context->language->id);
            $combinationsIds = $products->getAttributeCombinations((int) ($context->cookie->id_lang));
            $attribe = array();
            foreach ($combinationsIds as $k => $v) {
                if ($v['id_product_attribute'] == $combinationsId) {
                    if ($v['group_name'] == 'Color') {
                        $attribe['xe_color'] = $v['attribute_name'];
                        $attribe['xe_color_id'] = $v['id_attribute'];
                    }
                    if ($v['group_name'] == 'Size') {
                        $attribe['xe_size'] = $v['attribute_name'];
                        $attribe['xe_size_id'] = $v['id_attribute'];
                    }
                    if (($v['group_name'] != 'Size') && ($v['group_name'] != 'Color') && ($v['group_name'] != 'Pdp')) {
                        $attribe[$v['group_name']] = $v['attribute_name'];
                    }
                }
            }
            $result['attributes'] = $attribe;
            //End extra attribute for product
            return json_encode($result);
        } catch (PrestaShopDatabaseException $ex) {
            echo 'Other error: <br />' . $ex->getMessage();
        }
    }
    /**
     * Get all orders (sales,date)
     *
     * @param nothing
     * @return json array
     */
    public function getOrdersGraph()
    {
        try {
            $sql_ref = "SELECT distinct o.id_order,o.date_add FROM " . _DB_PREFIX_ . "orders as o,
			" . _DB_PREFIX_ . "cart_product as cp
			where  o.id_cart = cp.id_cart and cp.ref_id>0";
            $rq_ref = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_ref);
            $i = 0;
            foreach ($rq_ref as $k => $order) {
                $order_data[$i] = array(
                    'created_date' => date('Y-m-d', strtotime($order['date_add'])),
                    'order_id' => $order['id'],
                );
                $i++;
            }
            $result = array();
            $tempId = 0;
            $count = -1;
            foreach ($order_data as $k => $v) {
                if ($tempId != $v['created_date']) {
                    $i = 0;
                    $count++;
                    $tempId = $v['created_date'];
                    $result[$count]['date'] = $v['created_date'];
                    $result[$count]['sales'] = $i + 1;
                } else {
                    $i++;
                    $result[$count]['date'] = $v['created_date'];
                    $result[$count]['sales'] = $i + 1;
                }
            }
            return json_encode($result);
        } catch (PrestaShopDatabaseException $ex) {
            echo 'Other error: <br />' . $ex->getMessage();
        }
    }
    /**
     * Get all size and quanties by product id and combination id from store
     *
     * @param (int)productId
     * @param (int)combinationsId
     * @return json array
     */
    public function getSizeAndQuantity($productId, $combinationsId)
    {
        try {
            $context = \Context::getContext();
            $lang_id = (int) Context::getContext()->cookie->id_lang;
            $id_shop = (int) Context::getContext()->shop->id;
            $product_sql = "SELECT p.id_product,p.price,pa.id_product_attribute,pa.minimal_quantity FROM " . _DB_PREFIX_ . "product as p,
			" . _DB_PREFIX_ . "product_lang as pl," . _DB_PREFIX_ . "product_attribute as pa WHERE p.id_product = '$productId' AND
			p.id_product = pl.id_product AND p.id_product = pa.id_product AND pl.id_lang = '$lang_id' AND pl.id_shop = '$id_shop'";
            $rows_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($product_sql);
            $result = array();
            $tiers_price = array();
			$attribes = array();
			$attribe = array();
            $sql_color = "select al.name as colorName,atr.id_attribute as color_id from " . _DB_PREFIX_ . "attribute_lang as al,
				" . _DB_PREFIX_ . "product_attribute_combination as pac," . _DB_PREFIX_ . "attribute as atr
				where al.id_attribute = pac.id_attribute
				and atr.id_attribute = al.id_attribute
				and atr.id_attribute_group = (select ag.id_attribute_group from " . _DB_PREFIX_ . "attribute_group as ag, " . _DB_PREFIX_ . "attribute_group_lang agl where ag.is_color_group=1 and agl.name='Color' and agl.id_attribute_group = ag.id_attribute_group and agl.id_lang='$lang_id' limit 1)
				and al.id_lang = '$lang_id'
				and pac.id_product_attribute = '" . $combinationsId . "' ";
            $result_color = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_color);
            $sql_chk .= "SELECT id_product_attribute FROM " . _DB_PREFIX_ . "product_attribute_combination WHERE id_attribute='" . $result_color[0]['color_id'] . "'";
            $sql_chk .= $result_check[0]['no'] ? " and xe_is_temp='1'" : " and xe_is_temp='0'";
            $result_chk = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_chk);
            $country_id = (int) Context::getContext()->country->id;
            //tier price get by spacified country id
            $tire_sql = "SELECT reduction,from_quantity FROM " . _DB_PREFIX_ . "specific_price_rule WHERE id_country = " . $country_id . "";
            $result_tier_price = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($tire_sql);
            if (!empty($result_tier_price)) {
                foreach ($result_tierprice as $k => $v) {
                    $tiers_price[$k]['tierQty'] = intval($v['from_quantity']);
                    $tiers_price[$k]['percentage'] = floatval($v['reduction']);
                    $tiers_price[$k]['tierPrice'] = number_format($rows_data[0]['price'] - $v['reduction'], 5);
                }
            } else {
                $tire_price_country_sql = "SELECT reduction,from_quantity FROM " . _DB_PREFIX_ . "specific_price_rule "; //Tier price for all country
                $result_tire_price_country = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($tire_price_country_sql);
                foreach ($result_tire_price_country as $k1 => $v1) {
                    $tiers_price[$k1]['tierQty'] = intval($v1['from_quantity']);
                    $tiers_price[$k1]['percentage'] = floatval($v1['reduction']);
                    $tiers_price[$k1]['tierPrice'] = number_format($rows_data[0]['price'] - $v1['reduction'], 5);
                }
            }
            //Fetch all combination id by product id
            $products = new Product($productId, false, $context->language->id);
            $combinationsIds = $products->getAttributeCombinations((int) ($context->cookie->id_lang));
            foreach ($rows_data as $v) {
                $id_product_attribute = $v['id_product_attribute'];
                foreach ($result_chk as $k1 => $v1) {
                    if ($id_product_attribute == $v1['id_product_attribute']) {
                        $result[$k1]['simpleProductId'] = $v1['id_product_attribute'];
                        //Start product extra atrribute
                        foreach ($combinationsIds as $k2 => $v2) {
                            if ($v2['id_product_attribute'] == $result[$k1]['simpleProductId']) {
                                if ($v2['group_name'] == 'Color') {
                                    $attribe['xe_color'] = $v2['attribute_name'];
                                    $attribe['xe_color_id'] = $v2['id_attribute'];
                                }
                                if ($v2['group_name'] == 'Size') {
                                    $attribe['xe_size'] = $v2['attribute_name'];
                                    $attribe['xe_size_id'] = $v2['id_attribute'];
                                }
                                if (($v2['group_name'] != 'Size') && ($v2['group_name'] != 'Color') && ($v2['group_name'] != 'Pdp')) {
                                    $attribe[$v2['group_name']] = $v2['attribute_name'];
									//get size array
									$sql_sizes = "select sa.quantity,al.id_attribute,al.name from " . _DB_PREFIX_ . "attribute_lang as al
									left join " . _DB_PREFIX_ . "product_attribute_combination as pac on al.id_attribute = pac.id_attribute
									left join " . _DB_PREFIX_ . "attribute atr on al.id_attribute = atr.id_attribute
									join " . _DB_PREFIX_ . "stock_available sa on pac.id_product_attribute = sa.id_product_attribute
									where atr.color = '' 
									and atr.id_attribute_group=(select ag.id_attribute_group from " . _DB_PREFIX_ . "attribute_group as ag, " . _DB_PREFIX_ . "attribute_group_lang agl where ag.is_color_group=0 and agl.name='Size' and agl.id_attribute_group = ag.id_attribute_group and agl.id_lang = ".$lang_id." limit 1)
									and al.name !='active' and al.name!='inactive'
									and al.id_lang = ".$lang_id."
									and pac.id_product_attribute = '" . $v1['id_product_attribute'] . "' ";
									$result_sizes = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_sizes);
									$result[$k1]['xe_size'] = $result_sizes[0]['name'];
									$result[$k1]['xe_size_id'] = $result_sizes[0]['id_attribute'];
									$result[$k1]['quantity'] = intval($result_sizes[0]['quantity']);
									$result[$k1]['minQuantity'] = intval($v['minimal_quantity']);
									//get color array
									$sql_colors = "select al.name as colorName,atr.id_attribute as color_id from " . _DB_PREFIX_ . "attribute_lang as al,
										" . _DB_PREFIX_ . "product_attribute_combination as pac," . _DB_PREFIX_ . "attribute as atr
										where al.id_attribute = pac.id_attribute
										and atr.id_attribute = al.id_attribute
										and al.id_lang = ".$lang_id."
										and atr.id_attribute_group=(select ag.id_attribute_group from " . _DB_PREFIX_ . "attribute_group as ag, " . _DB_PREFIX_ . "attribute_group_lang agl where ag.is_color_group=1 and agl.name='Color' and agl.id_attribute_group = ag.id_attribute_group and agl.id_lang = ".$lang_id." limit 1)
										and pac.id_product_attribute = '" . $v1['id_product_attribute'] . "' ";
									$result_colors = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_colors);
									$result[$k1]['xe_color'] = $result_colors[0]['colorName'];
									$result[$k1]['xe_color_id'] = $result_colors[0]['color_id'];
									$result[$k1]['price'] = $rows_data[0]['price'];
									$result[$k1]['tierPrices'] = $tiers_price;
									$result[$k1]['attributes'] = $attribe;
                                }else{
									//get size array
									$sql_sizes = "select sa.quantity,al.id_attribute,al.name from " . _DB_PREFIX_ . "attribute_lang as al
									left join " . _DB_PREFIX_ . "product_attribute_combination as pac on al.id_attribute = pac.id_attribute
									left join " . _DB_PREFIX_ . "attribute atr on al.id_attribute = atr.id_attribute
									join " . _DB_PREFIX_ . "stock_available sa on pac.id_product_attribute = sa.id_product_attribute
									where atr.color = '' 
									and atr.id_attribute_group=(select ag.id_attribute_group from " . _DB_PREFIX_ . "attribute_group as ag, " . _DB_PREFIX_ . "attribute_group_lang agl where ag.is_color_group=0 and agl.name='Size' and agl.id_attribute_group = ag.id_attribute_group and agl.id_lang = ".$lang_id." limit 1)
									and al.name !='active' and al.name!='inactive'
									and al.id_lang = ".$lang_id."
									and pac.id_product_attribute = '" . $v1['id_product_attribute'] . "' ";
									$result_sizes = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_sizes);
									$result[$k1]['xe_size'] = $result_sizes[0]['name'];
									$result[$k1]['xe_size_id'] = $result_sizes[0]['id_attribute'];
									$result[$k1]['quantity'] = intval($result_sizes[0]['quantity']);
									$result[$k1]['minQuantity'] = intval($v['minimal_quantity']);
									//get color array
									$sql_colors = "select al.name as colorName,atr.id_attribute as color_id from " . _DB_PREFIX_ . "attribute_lang as al,
										" . _DB_PREFIX_ . "product_attribute_combination as pac," . _DB_PREFIX_ . "attribute as atr
										where al.id_attribute = pac.id_attribute
										and atr.id_attribute = al.id_attribute
										and al.id_lang = ".$lang_id."
										and atr.id_attribute_group =(select ag.id_attribute_group from " . _DB_PREFIX_ . "attribute_group as ag, " . _DB_PREFIX_ . "attribute_group_lang agl where ag.is_color_group = 1 and agl.id_attribute_group = ag.id_attribute_group and agl.id_lang=".$lang_id." limit 1)
										and pac.id_product_attribute = '" . $v1['id_product_attribute'] . "' ";
									$result_colors = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_colors);
									$result[$k1]['xe_color'] = $result_colors[0]['colorName'];
									$result[$k1]['xe_color_id'] = $result_colors[0]['color_id'];
									$result[$k1]['price'] = $rows_data[0]['price'];
									$result[$k1]['tierPrices'] = $tiers_price;
									if ($v2['group_name'] == 'Color') {
										$attribes['xe_color'] = $v2['attribute_name'];
										$attribes['xe_color_id'] = $v2['id_attribute'];
									}
									if ($v2['group_name'] == 'Size') {
										$attribes['xe_size'] = $v2['attribute_name'];
										$attribes['xe_size_id'] = $v2['id_attribute'];
									}
									$result[$k1]['attributes'] = $attribes;
								}
                            }
                        }
                    }
                }
            }
            $resultArr = array();
            $resultArr['quantities'] = array_values($result);
            return json_encode($resultArr);
        } catch (PrestaShopDatabaseException $e) {
            echo 'Database error: <br />' . $e->displayMessage();
        }
    }
    /**
     * Get all orders from store
     *
     * @param (int)start
     * @param (int)range
     * @param (int)lastOrderId
     * @return json array
     */
    public function getAllOrders($start, $range, $lastOrderId)
    {
        if ($start == $range) {
            $start = 0;
        }
        $sql = 'SELECT distinct o.id_order,o.date_add,c.firstname,c.lastname FROM ' . _DB_PREFIX_ . 'orders as o
		join ' . _DB_PREFIX_ . 'customer c on o.id_customer = c.id_customer
		left join ' . _DB_PREFIX_ . 'cart_product as cp on o.id_cart = cp.id_cart
		left join ' . _DB_PREFIX_ . 'order_history oh on o.id_order = oh.id_order where cp.ref_id >0 and ';
        $sql .= ($lastOrderId) ? ' o.id_order<' . $lastOrderId . '' : ' o.id_order>' . $lastOrderId . '';
        $sql .= ' order by o.id_order desc limit ' . $start . ',' . $range . '';
        $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        $resutl = array();
        $resultArr['is_Fault'] = 0;
        foreach ($rows as $k => $order) {
            $resutl[$k]['order_id'] = $order['id_order'];
            $sql_status = 'SELECT distinct osl.name from ' . _DB_PREFIX_ . 'order_history oh,
			' . _DB_PREFIX_ . 'order_state_lang osl where oh.id_order_state = osl.id_order_state and oh.id_order=' . $order['id_order'] . ' order by oh.id_order_history desc limit 1';
            $rows_status = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_status);
            $resutl[$k]['order_incremental_id'] = $order['id_order'];
            $resutl[$k]['order_status'] = $rows_status[0]['name'];
            $resutl[$k]['order_date'] = $order['date_add'];
            $resutl[$k]['customer_name'] = $order['firstname'] . ' ' . $order['lastname'];
        }
        $resultArr['order_list'] = $resutl;
        return json_encode($resultArr);
    }
    /**
     * Get all orders details by order id from store
     *
     * @param (int)orderId
     * @return json array
     */
    public function getOrderDetails($orderId)
    {
        global $cookie;
        $context = \Context::getContext();
        $webService = new PrestaShopWebservice(PS_SHOP_PATH, PS_WS_AUTH_KEY, DEBUG);
        $optOrder['resource'] = 'orders';
        $optOrder['display'] = 'full';
        $optOrder['filter']['id'] = $orderId; //filter by order id
        $xml = $webService->get($optOrder);
        $orders = $xml->orders->children();
        $orderArr = json_decode(json_encode((array) $orders), true);
        $address = new Address(intval($orderArr['order']['id_address_delivery'])); //get address of customer
        $country = new Country(intval($address->id_country)); //get country of customer
        $result = array();
        $result['address'] = $address;
        $result['country'] = array_values($country->name);
        //get shipping address of customer
        $shipingArr['resource'] = 'addresses';
        $shipingArr['display'] = 'full';
        $shipingArr['filter']['id'] = $orderArr['order']['id_address_delivery'];
        $xml = $webService->get($shipingArr);
        $addresses = $xml->addresses->children();
        $array = json_decode(json_encode((array) $addresses), true);
        $orderDetails['shipping_address']['first_name'] = $array['address']['firstname'];
        $orderDetails['shipping_address']['last_name'] = $array['address']['lastname'];
        $orderDetails['shipping_address']['fax'] = '';
        $orderDetails['shipping_address']['region'] = '';
        $orderDetails['shipping_address']['postcode'] = $array['address']['postcode'];
        $orderDetails['shipping_address']['telephone'] = $array['address']['phone'];
        $orderDetails['shipping_address']['city'] = $array['address']['city'];
        $orderDetails['shipping_address']['address_1'] = '';
        $orderDetails['shipping_address']['address_2'] = '';
        if (isset($array['address']['address1'])) {
            $orderDetails['shipping_address']['address_1'] = $array['address']['address1'];
        }

        if (isset($array['address']['address2'])) {
            $orderDetails['shipping_address']['address_2'] = $array['address']['address2'];
        }

        $stateArr['resource'] = 'states';
        $stateArr['display'] = '[id,name]';
        $stateArr['filter']['id'] = $array['address']['id_state'];
        $stateArr = $webService->get($stateArr);
        $states = $stateArr->states->children();
        $states = json_decode(json_encode((array) $states), true);
        $orderDetails['shipping_address']['state'] = $states['state']['name'];
        $orderDetails['shipping_address']['company'] = $array['address']['company'];
        $emailArr['resource'] = 'customers';
        $emailArr['display'] = 'full';
        $emailArr['filter']['id'] = $array['address']['id_customer'];
        $emailArr = $webService->get($emailArr);
        $email = $emailArr->customers->children();
        $email = json_decode(json_encode((array) $email), true);
        $orderDetails['shipping_address']['email'] = $email['customer']['email'];
        $countryArr['resource'] = 'countries';
        $countryArr['display'] = '[id,name]';
        $countryArr['filter']['id'] = $array['address']['id_country'];
        $countryArr = $webService->get($countryArr);
        $countries = $countryArr->countries->children();
        $countries = json_decode(json_encode((array) $countries), true);
        $orderDetails['shipping_address']['country'] = $countries['country']['name']['language'];
        //get billing address of customer
        $billingArr['resource'] = 'addresses';
        $billingArr['display'] = 'full';
        $billingArr['filter']['id'] = $orderArr['order']['id_address_invoice'];
        $xml = $webService->get($billingArr);
        $billingArr = $xml->addresses->children();
        $billingArr = json_decode(json_encode((array) $billingArr), true);
        $orderDetails['billing_address']['first_name'] = $billingArr['address']['firstname'];
        $orderDetails['billing_address']['last_name'] = $billingArr['address']['lastname'];
        $orderDetails['billing_address']['fax'] = '';
        $orderDetails['billing_address']['region'] = '';
        $orderDetails['billing_address']['postcode'] = $billingArr['address']['postcode'];
        $orderDetails['billing_address']['telephone'] = $billingArr['address']['phone'];
        $billingStateArr['resource'] = 'states';
        $billingStateArr['display'] = '[id,name]';
        $billingStateArr['filter']['id'] = $array['address']['id_state'];
        $billingStateArr = $webService->get($billingStateArr);
        $billingStateArr = $billingStateArr->states->children();
        $billingStateArr = json_decode(json_encode((array) $billingStateArr), true);
        $orderDetails['billing_address']['state'] = $billingStateArr['state']['name'];
        $orderDetails['billing_address']['city'] = $billingArr['address']['city'];
        $orderDetails['billing_address']['address_1'] = "";
        $orderDetails['billing_address']['address_2'] = "";
        if (isset($billingArr['address']['address1'])) {
            $orderDetails['billing_address']['address_1'] = $billingArr['address']['address1'];
        }

        if (isset($billingArr['address']['address2'])) {
            $orderDetails['billing_address']['address_2'] = $billingArr['address']['address2'];
        }

        $orderDetails['billing_address']['company'] = $billingArr['address']['company'];
        $orderDetails['billing_address']['email'] = $email['customer']['email'];
        $shpingCountryArr['resource'] = 'countries';
        $shpingCountryArr['display'] = 'full';
        $shpingCountryArr['filter']['id'] = $array['address']['id_country'];
        $shpingCountryArr = $webService->get($shpingCountryArr);
        $shpingCountryArr = $countryArr->countries->children();
        $shpingCountryArr = json_decode(json_encode((array) $shpingCountryArr), true);
        $orderDetails['billing_address']['country'] = $shpingCountryArr['country']['name']['language'];

        $orderDetails['order_id'] = $orderId;
        $orderDetails['order_incremental_id'] = $orderId;
        $sql = "SELECT distinct osl.name FROM " . _DB_PREFIX_ . "order_history as oh ," . _DB_PREFIX_ . "order_state_lang as osl
		where oh.id_order_state = osl.id_order_state and oh.id_order =" . $orderId . " ";
        $resul = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        $orderDetails['order_status'] = $resul['0']['name'];
        $orderDetails['order_date'] = $orderArr['order']['date_add'];
        $orderDetails['customer_name'] = $email['customer']['firstname'] . ' ' . $email['customer']['lastname'];
        $orderDetails['customer_email'] = $email['customer']['email'];
        $shipping_id = $orderArr['order']['id_carrier'];
        $shipping_details = new Carrier($shipping_id);
        $orderDetails['shipping_method'] = $shipping_details->name;
        $order = new Order($orderArr['order']['id']);
        $order_state = $order->getCurrentStateFull($context->cookie->id_lang);
        $order_item['itemStatus'] = $order_state['name'];
        foreach ($orderArr['order']['associations']['order_rows'] as $v3) {
            $order_item['product_price'] = $v3['product_price'];
            $order_item['product_sku'] = $v3['product_reference'];
            $order_item['config_product_id'] = $v3['product_id'];
            $order_item['product_id'] = $v3['product_attribute_id'];
            $order_item['product_name'] = $v3['product_name'];
            $order_item['quantity'] = $v3['product_quantity'];
        }
        $sql_ref = "SELECT ref_id FROM " . _DB_PREFIX_ . "cart_product WHERE id_product = '" . $v3['product_id'] . "' AND id_cart = (SELECT id_cart FROM " . _DB_PREFIX_ . "orders WHERE id_order = '" . $orderArr['order']['id'] . "')";
        $rq_ref = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_ref);
        $order_item['ref_id'] = $rq_ref['0']['ref_id'];
        $sql = "Select id_order_detail from " . _DB_PREFIX_ . "order_detail WHERE id_order='" . $orderArr['order']['id'] . "' AND product_id = '" . $v3['product_id'] . "'";
        $rq = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        $order_item['item_id'] = $rq['0']['id_order_detail'];
        $order_item['print_status'] = '';
        $myproduct = new Product($v3['product_id']);
        $xe_attributes = $myproduct->getAttributeCombinationsById($v3['product_attribute_id'], $context->cookie->id_lang);
        $xe_size = '';
        $xe_color = '';
        foreach ($xe_attributes as $xe_attribute) {
            // check for Color //
            if ($xe_attribute['group_name'] == 'Color') {
                $xe_color = $xe_attribute['attribute_name'];
            }
            // check for Size //
            if ($xe_attribute['group_name'] == 'Size') {
                $xe_size = $xe_attribute['attribute_name'];
            }
        }
        $order_item['xe_color'] = $xe_color;
        $order_item['xe_size'] = $xe_size;
        $orderDetails['order_items'] = $order_item;
        return json_encode(array('is_Fault' => 0, 'orderIncrementId' => $orderId, 'order_details' => $orderDetails));

    }
    /**
     * Get category id by product id id from store
     *
     * @param (int)orderId
     * @return json array
     */
    public function getCategoriesByProduct($productId)
    {
        try {
            $lang_id = (int) Context::getContext()->cookie->id_lang;
            $id_shop = (int) Context::getContext()->shop->id;
            $product_sql = "SELECT cp.id_category FROM " . _DB_PREFIX_ . "product_lang as pl," . _DB_PREFIX_ . "category_product as cp WHERE pl.id_product ='$productId' AND
			 pl.id_product = cp.id_product AND pl.id_lang ='$lang_id' AND pl.id_shop ='$id_shop'";
            $rowsData = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($product_sql);
            foreach ($rowsData as $v) {
                $result[] = $v['id_category'];
            }
            return json_encode($result);
        } catch (PrestaShopDatabaseException $e) {
            $msg['status'] = 'Database error: <br />' . $e->displayMessage();
            return json_encode($msg);
        }
    }

    /**
     * Get all variants in a product
     *
     * @param array different paramaters
     * @return array of Product variants
     *
     */
    public function getVariants($params)
    {
        $custom_ssl_var = 0;
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $custom_ssl_var = 1;
        }

        if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
            $basUrl = _PS_BASE_URL_SSL_;
            $basepath = _PS_BASE_URL_SSL_ . __PS_BASE_URI__;
        } else {
            $basUrl = _PS_BASE_URL_;
            $basepath = _PS_BASE_URL_ . __PS_BASE_URI__;
        }
        $context = \Context::getContext();
        $product = new Product($params['id_product'], false, $context->language->id);
        $name = $product->name;
        /*fetch extra tax */
        if ($product->id_tax_rules_group) {
            $sql = "SELECT price_display_method from " . _DB_PREFIX_ . "group WHERE id_group='" . $context->customer->id_default_group . "'";
            $rq = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            if ($rq['0']['price_display_method'] == 0) {
                $tax_sql = "SELECT t.rate FROM " . _DB_PREFIX_ . "tax AS t," . _DB_PREFIX_ . "tax_rule AS tr WHERE id_tax_rules_group=" . $product->id_tax_rules_group . "
				AND tr.id_country = " . $context->country->id . " AND tr.id_tax = t.id_tax";
                $result_tax = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($tax_sql);
                $tax = $result_tax ? $result_tax[0]['rate'] : 0;
            } else {
                $tax = 0;
            }
        } else {
            $tax = 0;
        }
        $description = strip_tags($product->description);
        $price = $product->price;
        // fetch combinations from store and store the same in 3 different variables//
        $combinations = $product->getAttributeCombinations((int) ($context->cookie->id_lang));
        $combinations3 = $combinations;
        // Initialize other variables //
        $combArray = $combArrayIds = $variants = $id_product_attribute_array = $allVariants = array();
        $kk = 0;
        $tot_counter = 1;
        $tot_counter2 = 1;
        $tot_counter3 = 1;
        $id_product_attribute_arrayyy2 = array();
        $color_array = array();
        // Loop the array to get combinations //
        foreach ($combinations as $key => $comb) {
            if (!in_array($comb['id_product_attribute'], $id_product_attribute_arrayyy2)) {
                array_push($id_product_attribute_arrayyy2, $comb['id_product_attribute']);
            }
            if (($comb['is_color_group'] == '1') && ($comb['group_name'] == 'Color') && (!in_array($comb['attribute_name'], $color_array))) {
                array_push($color_array, $comb['attribute_name']);
                $id_product_attribute = $comb['id_product_attribute'];
                if (!in_array($id_product_attribute, $id_product_attribute_array)) {
                    $sql_fetch = "SELECT color FROM " . _DB_PREFIX_ . "attribute WHERE id_attribute = " . $comb['id_attribute'] . "";
                    $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_fetch);
                    array_push($id_product_attribute_array, $id_product_attribute);
                    $variants[$id_product_attribute]['id'] = $id_product_attribute;
                    $variants[$id_product_attribute]['name'] = $name;
                    $variants[$id_product_attribute]['description'] = $description;

                    // for Thumbnail //
                    $sql = "Select pai.id_image from " . _DB_PREFIX_ . "product_attribute_image as pai join " . _DB_PREFIX_ . "image as im on im.id_image = pai.id_image
					where pai.id_product_attribute=" . $id_product_attribute . " order by im.position asc";
                    $rq = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                    $thumbnail = '';
                    if (sizeof($rq) > 0) {
                        $image = new Image($rq[0]['id_image']);
                        $thumbnail = $basUrl . _THEME_PROD_DIR_ . $image->getExistingImgPath() . "-small_default.jpg";
                    }
                    $variants[$id_product_attribute]['thumbnail'] = $thumbnail;
                    $variants[$id_product_attribute]['price'] = $price;
                    $variants[$id_product_attribute]['tax'] = $tax;
                    // Get Product Categories //
                    $cat_array = array();
                    $sql_cat = "SELECT id_category FROM " . _DB_PREFIX_ . "category_product WHERE id_product = '" . $params['id_product'] . "'";
                    $rq_cat = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_cat);
                    foreach ($rq_cat as $kc2 => $vc2) {
                        array_push($cat_array, $vc2['id_category']);
                    }
                    $variants[$id_product_attribute]['ConfcatIds'] = $cat_array;

                    // for color //
                    $imageUrlExit = file_exists(_PS_IMG_DIR_ . 'co' . '/' . (int) $comb['id_attribute'] . '.jpg');
                    $imageUrl = $basepath . 'img/' . 'co' . '/' . (int) $comb['id_attribute'] . '.jpg';
                    $variants[$id_product_attribute]['xeColor'] = $comb['attribute_name'];
                    $variants[$id_product_attribute]['xe_color_id'] = $comb['id_attribute'];
                    $variants[$id_product_attribute]['colorUrl'] = $imageUrlExit ? $imageUrl : $result[0]['color'];
                    $kk++;
                }
            }
            $tot_counter++;
        }
        // for Size //
        $id_product_attribute_array3 = array();
        foreach ($combinations3 as $key3 => $comb3) {
            $id_product_attribute = $comb3['id_product_attribute'];
            if ($comb3['is_color_group'] == '0' && $comb3['id_product_attribute'] == $variants[$id_product_attribute]['id']) {
                $variants[$id_product_attribute]['xe_size_id'] = $comb3['id_attribute'];
            }
            $tot_counter3++;
        }
        foreach ($variants as $varKey => $varValue) {
            array_push($allVariants, $varValue);
        }
        return $allVariants;
    }
    /**
     *add color value and name in prestashop store
     *
     * @param (String)colorname
     * @param (String)imagename
     * @return array
     *
     */
    public function addAttributeColorOptionValue($colorname, $imagename)
    {

        $sql_fetch = "SELECT id_attribute_group FROM " . _DB_PREFIX_ . "attribute_group_lang WHERE name = 'Color'";
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_fetch);
        $sql = "SELECT MAX( position ) FROM `" . _DB_PREFIX_ . "_attribute` WHERE id_attribute_group = '" . $result[0]['id_attribute_group'] . "'";
        $result_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        $id_lang = Context::getContext()->language->id;
        //insert color value and name
        $insert_sql = 'INSERT INTO `' . _DB_PREFIX_ . 'attribute` (`id_attribute_group`, `color`,`position`) VALUES
		 (' . intval($result[0]['id_attribute_group']) . ",'" . $imagename . "'," . intval($result_data['0']['MAX( position )'] + 1) . ')';
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($insert_sql);
        $lastId = Db::getInstance()->Insert_ID();
        $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'attribute_lang` (`id_attribute`,`id_lang`, `name`) VALUES (' . intval($lastId) . "," . intval($id_lang) . ",'" . $colorname . "')";
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($sql);
        $id_shop = (int) Context::getContext()->shop->id;
        $sql_shop = 'INSERT INTO `' . _DB_PREFIX_ . 'attribute_shop` (`id_attribute`,`id_shop`) VALUES (' . intval($lastId) . "," . intval($id_shop) . ")";
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($sql_shop);
        $optionss['attribute_id'] = $lastId;
        $optionss['attribute_value'] = $colorname;
        $optionss['status'] = 'success';

        return json_encode($optionss);
    }
    /**
     *get color name from the prestashop store
     *
     * @param (Int)lastLoaded
     * @param (Int)loadCount
     * @return color array
     *
     */
    public function getColorArr($lastLoaded, $loadCount, $productid)
    {
        if (isset($lastLoaded) && isset($loadCount)) {
            $sql_fetch = "SELECT al.name as label,al.id_attribute as value FROM " . _DB_PREFIX_ . "attribute_group_lang as agl,
			" . _DB_PREFIX_ . "attribute_lang as al, " . _DB_PREFIX_ . "attribute as at
			 WHERE agl.name = 'Color' and at.id_attribute_group = agl.id_attribute_group
			 and al.id_attribute= at.id_attribute order by al.id_attribute desc limit " . $lastLoaded . "," . $loadCount . "";
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_fetch);
        } else {
            $context = \Context::getContext();
            $product = new Product($productid, false, $context->language->id);
            $combinations = $product->getAttributeCombinations((int) ($context->cookie->id_lang));
            $resultArr = array();
            foreach ($combinations as $k => $v) {
                if (($v['is_color_group'] == 1) && ($v['group_name'] == 'Color')) {
                    $resultArr[$k]['value'] = $v['id_attribute'];
                    $resultArr[$k]['label'] = $v['attribute_name'];
                    $resultArr[$k]['swatchImage'] = '';
                }
            }
            $resultArr = array_unique($resultArr, SORT_REGULAR);
            $result = array_values($resultArr);
        }
        return json_encode($result);
    }
    /**
     *Update color value and name by in option_id prestashop store
     *
     * @param (Int)option_id
     * @param (String)colorname
     * @param (String)imagename
     * @return color array
     *
     */
    public function editAttributeColorOptionValue($option_id, $colorname, $imagename)
    {
        $query = "UPDATE " . _DB_PREFIX_ . "attribute SET color= '" . $imagename . "' WHERE id_attribute_group = '" . $option_id . "'";
        Db::getInstance()->Execute($query);
        $query2 = "UPDATE `" . _DB_PREFIX_ . "attribute_lang` SET `name`= '" . $colorname . "' WHERE `id_attribute` ='" . $option_id . "'";
        Db::getInstance()->Execute($query2);
        $optionss['attribute_id'] = $option_id;
        $optionss['attribute_value'] = $colorname;
        $optionss['status'] = 'success';
        return json_encode($optionss);

    }

    /**
     * addProductToCart() - Add product to Cart
     *
     * @param array $id_lang Language id
     * @return array of Product variants
     *
     */
    public function addProductToCart($data = [])
    {
        global $cookie;
        $context = \Context::getContext();
        $cart = null;
        $errors = [];

        // Initialize Cart //
        $cartObj = new CartCore();
        $cartObj->id = (int) $context->cookie->id_cart;
        $prodetails = $cartObj->getProducts();

        if (!isset($data['id'])) {
            return ["status" => "error", "message" => "Missing required arguments."];
        }

        if ($context->cookie->id_cart) {
            $cart = new \Cart((int) $context->cookie->id_cart);
        }
        // Initialize Cart //
        if (!$cart->id) {
            $cart = new \Cart();
            $cart->id_customer = (int) $context->cookie->id_customer;
            $cart->id_guest = (int) $context->cookie->id_guest;
            $cart->id_address_delivery = (int) (\Address::getFirstCustomerAddressId($cart->id_customer));
            $cart->id_address_invoice = $cart->id_address_delivery;
            $cart->id_lang = (int) ($context->cookie->id_lang);
            $cart->id_currency = (int) ($context->cookie->id_currency);
            $cart->id_carrier = 1;
            $cart->recyclable = 0;
            $cart->gift = 0;
            $cart->add();
            $context->cookie->__set('id_cart', (int) $cart->id);
            $cart->update();
        }

        if ($cart->id) {
            $product = new \Product((int) $data['id']);
            $customization_id = false;
            if (!$product->id) {
                return ["status" => "error", "message" => "Cannot find data in database."];
            }

            /*Initialize cart variables */
            $id_address_delivery = (int) (\Address::getFirstCustomerAddressId($cart->id_customer));
            $id_product_attribute = ($data['id_product_attribute'] != '') ? $data['id_product_attribute'] : null;
            $quantity = ($data['quantity'] != '') ? $data['quantity'] : 1;
            $cart->updateQty($quantity, (int) $product->id, $id_product_attribute, $customization_id, 'up', $id_address_delivery, null, true, $data['ref_id']);
            $cart->update();

            $shop_id = 1; // Assuming single Store //
            if (!is_null(Shop::getContextShopID())) {
                $shop_id = Shop::getContextShopID();
            }
            $product_price = $product->price;
            $custom_price = $product_price + $data['addedprice'];

            // Insert the custom price to "inkxe_cart_custom_price" table //
            $ise_sql = "INSERT INTO " . _DB_PREFIX_ . "inkxe_cart_custom_price SET id_cart = '" . $cart->id . "', id_product = '" . (int) $product->id . "', id_product_attribute = '" . $id_product_attribute . "', id_shop = '" . $shop_id . "', custom_price = '" . $custom_price . "', ref_id = '" . $data['ref_id'] . "'";
            $ise_qur = Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($ise_sql);
            $context = \Context::getContext();
            $rest = substr(_PS_VERSION_, 0, 3);
            if ($rest > 1.6) {
                $cart_url = $this->getCartSummaryURLS();
            } else {
                $cart_url = $context->link->getPageLink($order_process, true);
            }
            $msg['status'] = "success";
            $msg['url'] = $cart_url;
            return $msg;
        } else {
            return $msg['status'] = "error";
        }
    }
    /**
     *Get cart page url from prestashop store
     *
     * @param Nothing
     * @return string
     *
     */
    public function getCartSummaryURLS()
    {
        $context = \Context::getContext();
        return $context->link->getPageLink(
            'cart',
            null,
            $context->language->id,
            ['action' => 'show']
        );
    }
    /**
     *Get All order id from prestashop store
     *
     * @param (Int)lastOrderId
     * @param (Int)range
     * @return json array
     *
     */
    public function orderIdFromStore($lastOrderId, $range)
    {
        $sql = "SELECT id_order FROM " . _DB_PREFIX_ . "orders where id_order>" . $lastOrderId . " limit " . $range . "";
        $result_color = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        $resutl = array();
        foreach ($result_color as $key => $order) {
            $resutl[$key]['order_id'] = $order['id_order'];
            $resutl[$key]['order_incremental_id'] = $order['id_order'];
        }
        $resultArr['order_list'] = $resutl;
        return json_encode($resultArr);

    }
    /**
     *Get all  customisation product id from store
     *
     * @param nothing
     * @return json array
     *
     */
    public function checkIsCustomiseProduct()
    {
        $sql = "SELECT id_product FROM " . _DB_PREFIX_ . "product where customize='1'";
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        $resutlArr = array();
        foreach ($result as $key => $product) {
            $resutlArr[$key]['product_id'] = $product['id_product'];
        }
        return json_encode($resutlArr);
    }
    /**
     *Check customisation product by productid
     *
     * @param (Int)productid
     * @return json array
     *
     */
    public function checkIsCustomiseByProductId($productid)
    {
        global $cookie;
        $context = \Context::getContext();
        $sql = "SELECT count(*) as no FROM " . _DB_PREFIX_ . "product where id_product = " . $productid . " and
		active = 1 and available_for_order=1 and customize = 1";
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        $resultArr = array();
        $resultArr[0]['active_product'] = $result[0]['no'] ? 1 : 0;
        $resultArr[0]['customer_id'] = $context->cookie->id_customer ? $context->cookie->id_customer : 0;
        return $resultArr;
    }
    /**
     *Fetch product by cart id froom store
     *
     * @param (Int)cartId
     * @return json array
     *
     */
    public function fetchProductBycartId($cartId)
    {
        global $cookie;
        $sql = "SELECT * FROM " . _DB_PREFIX_ . "cart_product where id_cart=" . $cartId . "";
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        $resutlArr = array();

        foreach ($result as $key => $product) {
            $resutlArr[$key]['product_id'] = $product['id_product'];
            $resutlArr[$key]['ref_id'] = $product['ref_id'];
            $resutlArr[$key]['id_product_attribute'] = $product['id_product_attribute'];
            $resutlArr[$key]['id_address_delivery'] = $product['id_address_delivery'];
            $resutlArr[$key]['id_address_delivery'] = $product['id_address_delivery'];
            $resutlArr[$key]['id_shop'] = $product['id_shop'];
            $resutlArr[$key]['quantity'] = $product['quantity'];
        }
        return $resutlArr;
    }
    /**
     * Constructor
     *
     * @param string $name Module unique name
     * @param Context $context
     */
    public function __construct($name = null, Context $context = null)
    {
        if (isset($this->ps_versions_compliancy) && !isset($this->ps_versions_compliancy['min'])) {
            $this->ps_versions_compliancy['min'] = '1.4.0.0';
        }

        if (isset($this->ps_versions_compliancy) && !isset($this->ps_versions_compliancy['max'])) {
            $this->ps_versions_compliancy['max'] = _PS_VERSION_;
        }

        if (strlen($this->ps_versions_compliancy['min']) == 3) {
            $this->ps_versions_compliancy['min'] .= '.0.0';
        }

        if (strlen($this->ps_versions_compliancy['max']) == 3) {
            $this->ps_versions_compliancy['max'] .= '.999.999';
        }

        // Load context and smarty
        $this->context = $context ? $context : Context::getContext();
        if (is_object($this->context->smarty)) {
            $this->smarty = $this->context->smarty->createData($this->context->smarty);
        }
        // If the module has no name we gave him its id as name
        if ($this->name === null) {
            $this->name = $this->id;
        }
        // If the module has the name we load the corresponding data from the cache
        if ($this->name != null) {
            // If cache is not generated, we generate it
            if (self::$modules_cache == null && !is_array(self::$modules_cache)) {
                $id_shop = (Validate::isLoadedObject($this->context->shop) ? $this->context->shop->id : Configuration::get('PS_SHOP_DEFAULT'));

                self::$modules_cache = array();
                // Join clause is done to check if the module is activated in current shop context
                $result = Db::getInstance()->executeS('
                SELECT m.`id_module`, m.`name`, (
                    SELECT id_module
                    FROM `' . _DB_PREFIX_ . 'module_shop` ms
                    WHERE m.`id_module` = ms.`id_module`
                    AND ms.`id_shop` = ' . (int) $id_shop . '
                    LIMIT 1
                ) as mshop
                FROM `' . _DB_PREFIX_ . 'module` m');
                foreach ($result as $row) {
                    self::$modules_cache[$row['name']] = $row;
                    self::$modules_cache[$row['name']]['active'] = ($row['mshop'] > 0) ? 1 : 0;
                }
            }
            // We load configuration from the cache
            if (isset(self::$modules_cache[$this->name])) {
                if (isset(self::$modules_cache[$this->name]['id_module'])) {
                    $this->id = self::$modules_cache[$this->name]['id_module'];
                }
                foreach (self::$modules_cache[$this->name] as $key => $value) {
                    if (array_key_exists($key, $this)) {
                        $this->{$key} = $value;
                    }
                }
                $this->_path = __PS_BASE_URI__ . 'modules/' . $this->name . '/';
            }
            if (!$this->context->controller instanceof Controller) {
                self::$modules_cache = null;
            }
            //$this->local_path = _PS_MODULE_DIR_.$this->name.'/';
        }
    }
    /**
     *Module installed in backend store admin
     *
     * @param nothing
     * @return sting
     *
     */
    public function installModule()
    {
        $this->addTableAndColumn(); //to call to create table
        $this->alterTable(); //alter a table and a composite key
        /* $path = XEPATH."xetool/module_list.json";//get mdule list
        $data = file_get_contents($path);
        $module_list = json_decode($data,true);
        $resultData = Module::isInstalled($module_list['module_list'][0]['name']);
        if(empty($resultData)){
        $this->addTableAndColumn();//to call to create table
        $this->alterTable();//alter a table and a composite key
        foreach ($module_list['module_list'] as $k => $v) {
        $this->name = $v['name'];
        $hook_name = $v['hook_name'];
        $version = $v['version'];
        $shop_list = null;
        $return = true;
        if (is_array($hook_name)) {
        $hook_names = $hook_name;
        } else {
        $hook_names = array($hook_name);
        }
        //module in install
        //Check module name validation
        if (!Validate::isModuleName($this->name)) {
        $msg = 'Unable to install the module (Module name is not valid).';
        }
        // Check PS version compliancy
        if (!$this->checkCompliancy()) {
        $msg = 'The version of your module is not compliant with your PrestaShop version.';
        }
        // Check if module is installed
        $result = Module::isInstalled($this->name);
        if ($result) {
        $msg = 'This module has already been installed';
        }
        // Install overrides
        try {
        $this->installOverrides($this->name);
        } catch (Exception $e) {
        $msg = sprintf(Tools::displayError('Unable to install override: %s'), $e->getMessage());
        $this->uninstallOverrides();
        }
        if (!$this->installControllers()) {
        $msg = 'Technical error: PrestaShop could not install this module in installControllers.';
        //return false;
        }
        // Install module and retrieve the installation id
        $result = Db::getInstance()->insert('module', array(
        'name' => $this->name,
        'active' => 1,
        'version' => $version,
        ));
        if (!$result) {
        $msg = 'Technical error: PrestaShop could not install this module.';
        }
        $this->id = Db::getInstance()->Insert_ID();
        Cache::clean('Module::isInstalled'.$this->name);
        // Enable the module for current shops in context
        $this->enable();
        // Permissions management
        Db::getInstance()->execute('
        INSERT INTO `'._DB_PREFIX_.'module_access` (`id_profile`, `id_module`, `view`, `configure`, `uninstall`) (
        SELECT id_profile, '.(int)$this->id.', 1, 1, 1
        FROM '._DB_PREFIX_.'access a
        WHERE id_tab = (
        SELECT `id_tab` FROM '._DB_PREFIX_.'tab
        WHERE class_name = \'AdminModules\' LIMIT 1)
        AND a.`view` = 1)');

        Db::getInstance()->execute('
        INSERT INTO `'._DB_PREFIX_.'module_access` (`id_profile`, `id_module`, `view`, `configure`, `uninstall`) (
        SELECT id_profile, '.(int)$this->id.', 1, 0, 0
        FROM '._DB_PREFIX_.'access a
        WHERE id_tab = (
        SELECT `id_tab` FROM '._DB_PREFIX_.'tab
        WHERE class_name = \'AdminModules\' LIMIT 1)
        AND a.`view` = 0)');

        // Adding Restrictions for client groups
        Group::addRestrictionsForModule($this->id, Shop::getShops(true, null, true));
        Hook::exec('actionModuleInstallAfter', array('object' => $this));
        //end module intasll//
        foreach ($hook_names as $hook_name) {
        if (!isset($this->id) || !is_numeric($this->id)) {
        return false;
        }

        // Retrocompatibility
        $hook_name_bak = $hook_name;
        if ($alias = Hook::getRetroHookName($hook_name)) {
        $hook_name = $alias;
        }
        // Get hook id
        $id_hook = Hook::getIdByName($hook_name);
        // If hook does not exist, we create it
        if (!$id_hook) {
        $new_hook = new Hook();
        $new_hook->name = pSQL($hook_name);
        $new_hook->title = pSQL($hook_name);
        $new_hook->live_edit = (bool)preg_match('/^display/i', $new_hook->name);
        $new_hook->position = (bool)$new_hook->live_edit;
        $new_hook->add();
        $id_hook = $new_hook->id;
        if (!$id_hook) {
        return false;
        }
        }
        // If shop lists is null, we fill it with all shops
        if (is_null($shop_list)) {
        $shop_list = Shop::getCompleteListOfShopsID();
        }

        $shop_list_employee = Shop::getShops(true, null, true);

        foreach ($shop_list as $shop_id) {
        // Check if already register
        $sql = 'SELECT hm.`id_module`
        FROM `'._DB_PREFIX_.'hook_module` hm, `'._DB_PREFIX_.'hook` h
        WHERE hm.`id_module` = '.(int)$this->id.' AND h.`id_hook` = '.$id_hook.'
        AND h.`id_hook` = hm.`id_hook` AND `id_shop` = '.(int)$shop_id;
        if (Db::getInstance()->getRow($sql)) {
        continue;
        }
        // Get module position in hook
        $sql = 'SELECT MAX(`position`) AS position
        FROM `'._DB_PREFIX_.'hook_module`
        WHERE `id_hook` = '.(int)$id_hook.' AND `id_shop` = '.(int)$shop_id;
        if (!$position = Db::getInstance()->getValue($sql)) {
        $position = 0;
        }
        // Register module in hook
        $return &= Db::getInstance()->insert('hook_module', array(
        'id_module' => (int)$this->id,
        'id_hook' => (int)$id_hook,
        'id_shop' => (int)$shop_id,
        'position' => (int)($position + 1),
        ));

        if (!in_array($shop_id, $shop_list_employee)) {
        $where = '`id_module` = '.(int)$this->id.' AND `id_shop` = '.(int)$shop_id;
        $return &= Db::getInstance()->delete('module_shop', $where);
        }
        }
        }
        }
        if($this->id){
        $this->tableValueInterChange();
        $msg = "Module installed";
        }
        }else{
        $msg = "Module installed";
        } */
        return $msg = "Module installed";
    }
    /**
     *Uninstall overides file
     *
     * @param nothing
     * @return boolean
     *
     */
    public function uninstallOverrides()
    {
        if (!is_dir($this->getLocalPath() . 'override')) {
            return true;
        }
        $result = true;
        foreach (Tools::scandir($this->getLocalPath() . 'override', 'php', '', true) as $file) {
            $class = basename($file, '.php');
            if (PrestaShopAutoload::getInstance()->getClassPath($class . 'Core') || Module::getModuleIdByName($class)) {
                $result &= $this->removeOverride($class);
            }
        }
        return $result;
    }
    /**
     *Check PS version compliancy
     *
     * @param nothing
     * @return boolean
     *
     */
    public function checkCompliancy()
    {
        if (version_compare(_PS_VERSION_, $this->ps_versions_compliancy['min'], '<') || version_compare(_PS_VERSION_, $this->ps_versions_compliancy['max'], '>')) {
            return false;
        } else {
            return true;
        }
    }
    /**
     *Create table for custom price table
     *
     * @param nothing
     * @return nothing
     *
     */
    public function addTableAndColumn()
    {
        $sql = array();
        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'inkxe_cart_custom_price` (
			  `id_cart` int(10) unsigned NOT NULL,
			  `id_product` int(10) unsigned NOT NULL,
			  `id_product_attribute` int(10) unsigned NOT NULL,
			  `id_shop` int(10) unsigned NOT NULL,
			  `custom_price` decimal(20,6) NOT NULL,
			  `ref_id` int(10) unsigned NOT NULL,
			  PRIMARY KEY  (`id_cart`, `id_product`, `id_product_attribute`, `id_shop`, `ref_id`)
			) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';
        foreach ($sql as $_sql) {
            Db::getInstance()->Execute($_sql);
        }
    }
    /**
     * Install module's controllers using public property $controllers
     * @return bool
     */
    private function installControllers()
    {
        $themes = Theme::getThemes();
        $theme_meta_value = array();
        foreach ($this->controllers as $controller) {
            $page = 'module-' . $this->name . '-' . $controller;
            $result = Db::getInstance()->getValue('SELECT * FROM ' . _DB_PREFIX_ . 'meta WHERE page="' . pSQL($page) . '"');
            if ((int) $result > 0) {
                continue;
            }

            $meta = new Meta();
            $meta->page = $page;
            $meta->configurable = 1;
            $meta->save();
            if ((int) $meta->id > 0) {
                foreach ($themes as $theme) {
                    /** @var Theme $theme */
                    $theme_meta_value[] = array(
                        'id_theme' => $theme->id,
                        'id_meta' => $meta->id,
                        'left_column' => (int) $theme->default_left_column,
                        'right_column' => (int) $theme->default_right_column,
                    );
                }
            } else {
                $this->_errors[] = sprintf(Tools::displayError('Unable to install controller: %s'), $controller);
            }
        }
        if (count($theme_meta_value) > 0) {
            return Db::getInstance()->insert('theme_meta', $theme_meta_value);
        }
        return true;
    }
    /**
     * Activate current module.
     *
     * @param bool $force_all If true, enable module for all shop
     */
    public function enable($force_all = false)
    {
        // Retrieve all shops where the module is enabled
        $list = Shop::getContextListShopID();
        if (!$this->id || !is_array($list)) {
            return false;
        }
        $sql = 'SELECT `id_shop` FROM `' . _DB_PREFIX_ . 'module_shop`
				WHERE `id_module` = ' . (int) $this->id .
            ((!$force_all) ? ' AND `id_shop` IN(' . implode(', ', $list) . ')' : '');
        // Store the results in an array
        $items = array();
        if ($results = Db::getInstance($sql)->executeS($sql)) {
            foreach ($results as $row) {
                $items[] = $row['id_shop'];
            }
        }
        // Enable module in the shop where it is not enabled yet
        foreach ($list as $id) {
            if (!in_array($id, $items)) {
                Db::getInstance()->insert('module_shop', array(
                    'id_module' => $this->id,
                    'id_shop' => $id,
                ));
            }
        }
        return true;
    }
    /**
     *Update translation after instalation mudule
     *
     * @param nothing
     * @return boolean
     *
     */
    public static function updateTranslationsAfterInstall($update = true)
    {
        Module::$update_translations_after_install = (bool) $update;
    }
    /**
     *Update translation after instalation mudule
     *
     * @param nothing
     * @return boolean
     *
     */
    public function updateModuleTranslations()
    {
        return Language::updateModulesTranslations(array($this->name));
    }
    /**
     * Install overrides files for the module
     *
     * @return bool
     */
    public function installOverrides($name)
    {
        if (!is_dir($this->getLocalPath($name) . 'override')) {
            return true;
        }
        $result = true;
        foreach (Tools::scandir($this->getLocalPath($name) . 'override', 'php', '', true) as $file) {
            $class = basename($file, '.php');
            if (PrestaShopAutoload::getInstance()->getClassPath($class . 'Core') || Module::getModuleIdByName($class)) {
                $result &= $this->addOverride($class, $name);
            }
        }
        return $result;
    }
    /**
     * Add all methods in a module override to the override class
     *
     * @param string $classname
     * @return bool
     */
    public function addOverride($classname, $name)
    {
        $orig_path = $path = PrestaShopAutoload::getInstance()->getClassPath($classname . 'Core');
        if (!$path) {
            $path = 'modules' . DIRECTORY_SEPARATOR . $classname . DIRECTORY_SEPARATOR . $classname . '.php';
        }
        $path_override = $this->getLocalPath($name) . 'override' . DIRECTORY_SEPARATOR . $path;
        if (!file_exists($path_override)) {
            return false;
        } else {
            file_put_contents($path_override, preg_replace('#(\r\n|\r)#ism', "\n", file_get_contents($path_override)));
        }
        $pattern_escape_com = '#(^\s*?\/\/.*?\n|\/\*(?!\n\s+\* module:.*?\* date:.*?\* version:.*?\*\/).*?\*\/)#ism';
        // Check if there is already an override file, if not, we just need to copy the file
        if ($file = PrestaShopAutoload::getInstance()->getClassPath($classname)) {
            // Check if override file is writable
            $override_path = _PS_ROOT_DIR_ . '/' . $file;

            if ((!file_exists($override_path) && !is_writable(dirname($override_path))) || (file_exists($override_path) && !is_writable($override_path))) {
                throw new Exception(sprintf(Tools::displayError('file (%s) not writable'), $override_path));
            }

            // Get a uniq id for the class, because you can override a class (or remove the override) twice in the same session and we need to avoid redeclaration
            do {
                $uniq = uniqid();
            } while (class_exists($classname . 'OverrideOriginal_remove', false));

            // Make a reflection of the override class and the module override class
            $override_file = file($override_path);
            $override_file = array_diff($override_file, array("\n"));
            eval(preg_replace(array('#^\s*<\?(?:php)?#', '#class\s+' . $classname . '\s+extends\s+([a-z0-9_]+)(\s+implements\s+([a-z0-9_]+))?#i'), array(' ', 'class ' . $classname . 'OverrideOriginal' . $uniq), implode('', $override_file)));
            $override_class = new ReflectionClass($classname . 'OverrideOriginal' . $uniq);

            $module_file = file($path_override);
            $module_file = array_diff($module_file, array("\n"));
            eval(preg_replace(array('#^\s*<\?(?:php)?#', '#class\s+' . $classname . '(\s+extends\s+([a-z0-9_]+)(\s+implements\s+([a-z0-9_]+))?)?#i'), array(' ', 'class ' . $classname . 'Override' . $uniq), implode('', $module_file)));
            $module_class = new ReflectionClass($classname . 'Override' . $uniq);
            // Check if none of the methods already exists in the override class
            foreach ($module_class->getMethods() as $method) {
                if ($override_class->hasMethod($method->getName())) {
                    $method_override = $override_class->getMethod($method->getName());
                    if (preg_match('/module: (.*)/ism', $override_file[$method_override->getStartLine() - 5], $name) && preg_match('/date: (.*)/ism', $override_file[$method_override->getStartLine() - 4], $date) && preg_match('/version: ([0-9.]+)/ism', $override_file[$method_override->getStartLine() - 3], $version)) {
                        throw new Exception(sprintf(Tools::displayError('The method %1$s in the class %2$s is already overridden by the module %3$s version %4$s at %5$s.'), $method->getName(), $classname, $name[1], $version[1], $date[1]));
                    }
                    throw new Exception(sprintf(Tools::displayError('The method %1$s in the class %2$s is already overridden.'), $method->getName(), $classname));
                }
                $module_file = preg_replace('/((:?public|private|protected)\s+(static\s+)?function\s+(?:\b' . $method->getName() . '\b))/ism', "/*\n    * module: " . $this->name . "\n    * date: " . date('Y-m-d H:i:s') . "\n    * version: " . $this->version . "\n    */\n    $1", $module_file);
                if ($module_file === null) {
                    throw new Exception(sprintf(Tools::displayError('Failed to override method %1$s in class %2$s.'), $method->getName(), $classname));
                }
            }
            // Check if none of the properties already exists in the override class
            foreach ($module_class->getProperties() as $property) {
                if ($override_class->hasProperty($property->getName())) {
                    throw new Exception(sprintf(Tools::displayError('The property %1$s in the class %2$s is already defined.'), $property->getName(), $classname));
                }
                $module_file = preg_replace('/((?:public|private|protected)\s)\s*(static\s)?\s*(\$\b' . $property->getName() . '\b)/ism', "/*\n    * module: " . $this->name . "\n    * date: " . date('Y-m-d H:i:s') . "\n    * version: " . $this->version . "\n    */\n    $1$2$3", $module_file);
                if ($module_file === null) {
                    throw new Exception(sprintf(Tools::displayError('Failed to override property %1$s in class %2$s.'), $property->getName(), $classname));
                }
            }
            foreach ($module_class->getConstants() as $constant => $value) {
                if ($override_class->hasConstant($constant)) {
                    throw new Exception(sprintf(Tools::displayError('The constant %1$s in the class %2$s is already defined.'), $constant, $classname));
                }
                $module_file = preg_replace('/(const\s)\s*(\b' . $constant . '\b)/ism', "/*\n    * module: " . $this->name . "\n    * date: " . date('Y-m-d H:i:s') . "\n    * version: " . $this->version . "\n    */\n    $1$2", $module_file);
                if ($module_file === null) {
                    throw new Exception(sprintf(Tools::displayError('Failed to override constant %1$s in class %2$s.'), $constant, $classname));
                }
            }
            // Insert the methods from module override in override
            $copy_from = array_slice($module_file, $module_class->getStartLine() + 1, $module_class->getEndLine() - $module_class->getStartLine() - 2);
            array_splice($override_file, $override_class->getEndLine() - 1, 0, $copy_from);
            $code = implode('', $override_file);
            file_put_contents($override_path, preg_replace($pattern_escape_com, '', $code));
        } else {
            $override_src = $path_override;
            $override_dest = _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . 'override' . DIRECTORY_SEPARATOR . $path;
            $dir_name = dirname($override_dest);
            if (!$orig_path && !is_dir($dir_name)) {
                $oldumask = umask(0000);
                @mkdir($dir_name, 0777);
                umask($oldumask);
            }
            if (!is_writable($dir_name)) {
                throw new Exception(sprintf(Tools::displayError('directory (%s) not writable'), $dir_name));
            }
            $module_file = file($override_src);
            $module_file = array_diff($module_file, array("\n"));
            if ($orig_path) {
                do {
                    $uniq = uniqid();
                } while (class_exists($classname . 'OverrideOriginal_remove', false));
                eval(preg_replace(array('#^\s*<\?(?:php)?#', '#class\s+' . $classname . '(\s+extends\s+([a-z0-9_]+)(\s+implements\s+([a-z0-9_]+))?)?#i'), array(' ', 'class ' . $classname . 'Override' . $uniq), implode('', $module_file)));
                $module_class = new ReflectionClass($classname . 'Override' . $uniq);
                // For each method found in the override, prepend a comment with the module name and version
                foreach ($module_class->getMethods() as $method) {
                    $module_file = preg_replace('/((:?public|private|protected)\s+(static\s+)?function\s+(?:\b' . $method->getName() . '\b))/ism', "/*\n    * module: " . $this->name . "\n    * date: " . date('Y-m-d H:i:s') . "\n    * version: " . $this->version . "\n    */\n    $1", $module_file);
                    if ($module_file === null) {
                        throw new Exception(sprintf(Tools::displayError('Failed to override method %1$s in class %2$s.'), $method->getName(), $classname));
                    }
                }

                // Same loop for properties
                foreach ($module_class->getProperties() as $property) {
                    $module_file = preg_replace('/((?:public|private|protected)\s)\s*(static\s)?\s*(\$\b' . $property->getName() . '\b)/ism', "/*\n    * module: " . $this->name . "\n    * date: " . date('Y-m-d H:i:s') . "\n    * version: " . $this->version . "\n    */\n    $1$2$3", $module_file);
                    if ($module_file === null) {
                        throw new Exception(sprintf(Tools::displayError('Failed to override property %1$s in class %2$s.'), $property->getName(), $classname));
                    }
                }

                // Same loop for constants
                foreach ($module_class->getConstants() as $constant => $value) {
                    $module_file = preg_replace('/(const\s)\s*(\b' . $constant . '\b)/ism', "/*\n    * module: " . $this->name . "\n    * date: " . date('Y-m-d H:i:s') . "\n    * version: " . $this->version . "\n    */\n    $1$2", $module_file);
                    if ($module_file === null) {
                        throw new Exception(sprintf(Tools::displayError('Failed to override constant %1$s in class %2$s.'), $constant, $classname));
                    }
                }
            }
            file_put_contents($override_dest, preg_replace($pattern_escape_com, '', $module_file));

            // Re-generate the class index
            Tools::generateIndex();
        }
        return true;
    }
    /**
     * Get local path for module
     *
     * @since 1.5.0
     * @return string
     */
    public function getLocalPath($name)
    {
        return $this->local_path = _PS_MODULE_DIR_ . $name . '/';
    }
    /**
     *get cms page id
     *
     * @param nothing
     * @return int
     *
     */
    public function getCmsPageId()
    {
        $lang_id = Context::getContext()->language->id;
        $id_shop = (int) Context::getContext()->shop->id;
        $select_sql = "SELECT id_cms FROM " . _DB_PREFIX_ . "cms_lang where meta_title='Designer Tool' AND id_lang='$lang_id' AND id_shop='$id_shop' ";
        $result_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($select_sql);
        return $result_data[0]['id_cms'];

    }
    /**
     *Add product attribute in store backend
     *
     * @param nothing
     * @return int
     *
     */
    public function addProduct()
    {
        //added new predecoproduct
        $this->alterProdutTable();
        $lang_id = Context::getContext()->language->id;
        $product_name = 'inkXE Tshirt';
        $sql = "SELECT count(*) as nos FROM " . _DB_PREFIX_ . "product_lang where name='" . $product_name . "' and id_lang=" . $lang_id . "";
        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if (empty($row[0]['nos'])) {
            $sql_lang = "Select id_lang FROM " . _DB_PREFIX_ . "lang";
            $lang_ids = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_lang);
            $this->addCustomColumnProduct();
            $description = 'Round Neck inkXE Black Tshirt for Men';
            $short_description = 'inkXE Black Tshirt';
            $n_link_rewrite = 'customize';
            $n_meta_title = 'inkXE Black Tshirt';
            $n_meta_description = 'Round Neck inkXE Black Tshirt for Men';
            $n_meta_keywords = 'round neck tshirt, black tshirt, tshirt for men';
            $n_available_now = 'Available for order';
            $curDate = date('d-m-Y', time());
            $n_available_later = 'Available from ' . $curDate . '';
            $now = date('Y-m-d H:i:s', time());
            $qty = 1000;
            $totalQantity = 0;
            $id_category = $this->addCategoryByProduct($lang_ids);
            $price = '100.00';
            $sku = 'inkxe_demo';
            $id_shop = (int) Context::getContext()->shop->id;
            $insert_sql = "INSERT INTO " . _DB_PREFIX_ . "product(id_supplier,id_manufacturer,id_category_default,id_tax_rules_group,price,reference,active,redirect_type,indexed,cache_default_attribute,date_add,date_upd,customize)
			VALUES('1','1','$id_category','1','$price','$sku','1','404','1','','$now','$now','1')";
            Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($insert_sql);
            $productId = Db::getInstance()->Insert_ID();
            //ps_product_shop
            $product_shop_sql = "INSERT INTO `" . _DB_PREFIX_ . "product_shop` (`id_product`, `id_shop`, `id_category_default`, `id_tax_rules_group`,
			`minimal_quantity`, `price`, `active`, `redirect_type`, `id_product_redirected`, `available_for_order`, `condition`, `show_price`, `indexed`, `visibility`,
			`cache_default_attribute`, `advanced_stock_management`, `date_add`, `date_upd`, `pack_stock_type`)
			 VALUES ('$productId', '$id_shop', '$id_category', '1', '1', '$price', '1', '404', '0', '1', 'new', '1', '1', 'both', '0', '0', '$now', '$now', '3')";
            Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($product_shop_sql);
            //ps_product_lang
            $link_rewrite = strtolower($n_link_rewrite);
            if (preg_match('/\s/', $link_rewrite)) {
                $link_rewrite = str_replace(' ', '-', $link_rewrite);
            }
            foreach ($lang_ids as $v) {
                $product_lang_sql = "INSERT INTO " . _DB_PREFIX_ . "product_lang(id_product,id_shop,id_lang,description,description_short,link_rewrite,
				meta_description,meta_keywords,meta_title,name,available_now,available_later)
				VALUES('$productId','$id_shop','" . $v['id_lang'] . "','$description','$short_description','$link_rewrite','$n_meta_description','$n_meta_keywords','$n_meta_title','$product_name','$n_available_now','$n_available_later')";
                Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($product_lang_sql);
            }
            //add Category to product //
            $this->addToCategoriesToProduct($id_category, $productId);
            //product image add
            $url[] = PS_SHOP_PATH . "xetool/install/prestashop17/wizard/images/install_image/configurable.png";
            $image_id = $this->addImageByProductId($url, $productId, $product_name, $lang_ids, $id_shop);
            //addAttributeToProduct//
            $attArr = $this->addAttributes($lang_ids);
            $color_id = $attArr['color_id'];
            $size_id = $attArr['size_id'];
            $attrId = $this->addProductAttributesByProductIds($size_id, $productId, $sku, $color_id, $attr_id, $id_shop);
            if ($attrId) {
				$this->upadteCacheProductAttrId($productId,$attrId);
                $this->addProductStock($attrId, $productId, $totalQantity, $qty);
                $this->updateTotalQuantityByPid($productId);
                $this->addImageAttributes($attrId, $image_id);
            }
            $msg = 'Product added successfully';
        } else {
            $msg = 'Name already exit';
        }
        return $msg;
    }
	public function upadteCacheProductAttrId($productId,$attrId){
		$upadteSql = "UPDATE " . _DB_PREFIX_ . "product set cache_default_attribute =".$attrId[0]." WHERE id_product = ".$productId."";
		Db::getInstance()->Execute($upadteSql);
		$sql = "UPDATE " . _DB_PREFIX_ . "product_shop set cache_default_attribute =".$attrId[0]." WHERE id_product = ".$productId."";
		Db::getInstance()->Execute($sql);
	}
    public function addAttributes($lang_ids)
    {
        $id_lang = Context::getContext()->language->id;
        $id_shop = (int) Context::getContext()->shop->id;
        //add color attribute
        $colornName = 'Color';
        $sql = "SELECT id_attribute_group from " . _DB_PREFIX_ . "attribute_group_lang where name='" . $colornName . "' and id_lang=" . $id_lang . "";
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if (empty($result[0]['id_attribute_group'])) {
            $sql = "SELECT position FROM " . _DB_PREFIX_ . "attribute_group order by position desc limit 1";
            $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            $insert_sql = "INSERT INTO `" . _DB_PREFIX_ . "attribute_group` (`group_type`,`is_color_group`,`position`) VALUES('color','1','" . intval($row['0']['position'] + 1) . "')";
            Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($insert_sql);
            $groupId = Db::getInstance()->Insert_ID();
            foreach ($lang_ids as $v) {
                $insert_sql1 = "INSERT INTO " . _DB_PREFIX_ . "attribute_group_lang (id_attribute_group,id_lang,name,public_name) VALUES(" . $groupId . ",'" . $v['id_lang'] . "','$colornName','color')";
                Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($insert_sql1);
            }
            $insert_sql2 = "INSERT INTO " . _DB_PREFIX_ . "attribute_group_shop (id_attribute_group,id_shop) VALUES(" . $groupId . "," . $id_shop . ")";
            Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($insert_sql2);

            $newAttribute = new Attribute();
            $newAttribute->name = $this->createMultiLangFields('Black');
            $newAttribute->id_attribute_group = $groupId;
            $newAttribute->color = '#434A54';
            $newAttribute->position = 0;
            $id = $newAttribute->add();
        } else {
            $checkExit = $this->isAttributeExit($result[0]['id_attribute_group'], 'Black', $id_lang);
            if (empty($checkExit)) {
                $newAttribute = new Attribute();
                $newAttribute->name = $this->createMultiLangFields('Black');
                $newAttribute->id_attribute_group = $result[0]['id_attribute_group'];
                $newAttribute->color = '#434A54';
                $newAttribute->position = 0;
                $id = $newAttribute->add();
            }
        }
        //add size attribue
        $sizeName = 'Size';
        $sqlZize = "SELECT id_attribute_group from " . _DB_PREFIX_ . "attribute_group_lang where name='" . $sizeName . "' and id_lang=" . $id_lang . "";
        $resultSize = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sqlZize);
        if (empty($resultSize[0]['id_attribute_group'])) {
            $sql = "SELECT position FROM " . _DB_PREFIX_ . "attribute_group order by position desc limit 1";
            $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            $insert_sql = "INSERT INTO `" . _DB_PREFIX_ . "attribute_group` (`group_type`,`position`) VALUES('select','" . intval($row['0']['position'] + 1) . "')";
            Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($insert_sql);
            $groupId = Db::getInstance()->Insert_ID();
            foreach ($lang_ids as $v1) {
                $insert_sql1 = "INSERT INTO " . _DB_PREFIX_ . "attribute_group_lang (id_attribute_group,id_lang,name,public_name) VALUES(" . $groupId . ",'" . $v1['id_lang'] . "','$sizeName','size')";
                Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($insert_sql1);
            }
            $insert_sql2 = "INSERT INTO " . _DB_PREFIX_ . "attribute_group_shop (id_attribute_group,id_shop) VALUES(" . $groupId . "," . $id_shop . ")";
            Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($insert_sql2);
            $attribute = new Attribute();
            $attribute->name = $this->createMultiLangFields('L');
            $attribute->id_attribute_group = $groupId;
            $attribute->color = '';
            $attribute->position = 0;
            $attribute->add();
        } else {
            $exitResult = $this->isAttributeExit($resultSize[0]['id_attribute_group'], 'L', $id_lang);
            if (empty($exitResult)) {
                $attribute = new Attribute();
                $attribute->name = $this->createMultiLangFields('L');
                $attribute->id_attribute_group = $resultSize[0]['id_attribute_group'];
                $attribute->color = '';
                $attribute->position = 0;
                $attribute->add();
            }
        }
        $size_sql = "SELECT id_attribute from " . _DB_PREFIX_ . "attribute_lang where name='L' and id_lang=" . intval($id_lang) . "";
        $row_size = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($size_sql);
        $size_color = "SELECT id_attribute from " . _DB_PREFIX_ . "attribute_lang where name='Black' and id_lang=" . intval($id_lang) . "";
        $row_color = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($size_color);

        $attArr['size_id'] = $row_size[0]['id_attribute'];
        $attArr['color_id'] = $row_color[0]['id_attribute'];
        return $attArr;

    }
    public function addProductAttributesByProductIds($size_id, $productId, $sku, $color_id, $attr_id, $id_shop)
    {
        $attr_sql1 = "INSERT INTO " . _DB_PREFIX_ . "product_attribute(id_product,reference) VALUES('$productId','$sku')";
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($attr_sql1);
        $attrId[] = $attr_id = Db::getInstance()->Insert_ID();
        //add product atttribute size and color
        $sql_insert = "INSERT INTO " . _DB_PREFIX_ . "product_attribute_combination(id_attribute,id_product_attribute) VALUES('$color_id','$attr_id')";
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($sql_insert);
        $sql_insert1 = "INSERT INTO " . _DB_PREFIX_ . "product_attribute_combination(id_attribute,id_product_attribute) VALUES('$size_id','$attr_id')";
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($sql_insert1);
        //ps_product_atrribute_shop
        $sql_pashop = "INSERT INTO " . _DB_PREFIX_ . "product_attribute_shop(id_product,id_product_attribute,id_shop,default_on)
		VALUES('$productId','$attr_id','$id_shop','1')";
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($sql_pashop);
        return $attrId;

    }
    /**
     *add a column in product table
     *
     * @param nothing
     * @return nothing
     *
     */
    public function addCustomColumnProduct()
    {
        $sql = 'ALTER TABLE ' . _DB_PREFIX_ . 'product ADD COLUMN `customize` tinyint(1) DEFAULT 0';
        Db::getInstance()->Execute($sql);
        $sql_cart = 'ALTER TABLE ' . _DB_PREFIX_ . 'cart_product ADD COLUMN `ref_id` int(11)';
        Db::getInstance()->Execute($sql_cart);
    }
    /**
     *Active product for customization after add a product
     *
     * @param (int)productid
     * @return nothing
     *
     */
    public function activeProductCusomization($product)
    {
        Db::getInstance()->update('product', array('customize' => '1'), ' id_product = ' . $product['id']);
    }
    /**
     *copy product iamge
     *
     * @param (int)id_entity
     * @param (String)url
     * @param (int)id_image
     * @param (String)entity
     * @param (boolean)regenerate
     * @return boolean
     *
     */
    public function copyImg($id_entity, $id_image = null, $url, $entity = 'products', $regenerate = true)
    {
        $tmpfile = tempnam(_PS_TMP_IMG_DIR_, 'ps_import');
        $watermark_types = explode(',', Configuration::get('WATERMARK_TYPES'));
        switch ($entity) {
            default:
            case 'products':
                $image_obj = new Image($id_image);
                $path = $image_obj->getPathForCreation();
                break;
            case 'categories':
                $path = _PS_CAT_IMG_DIR_ . (int) $id_entity;
                break;
            case 'manufacturers':
                $path = _PS_MANU_IMG_DIR_ . (int) $id_entity;
                break;
            case 'suppliers':
                $path = _PS_SUPP_IMG_DIR_ . (int) $id_entity;
                break;
        }
        $url = urldecode(trim($url));
        $parced_url = parse_url($url);
        if (isset($parced_url['path'])) {
            $uri = ltrim($parced_url['path'], '/');
            $parts = explode('/', $uri);
            foreach ($parts as &$part) {
                $part = rawurlencode($part);
            }
            unset($part);
            $parced_url['path'] = '/' . implode('/', $parts);
        }
        if (isset($parced_url['query'])) {
            $query_parts = array();
            parse_str($parced_url['query'], $query_parts);
            $parced_url['query'] = http_build_query($query_parts);
        }
        if (!function_exists('http_build_url')) {
            require_once _PS_TOOL_DIR_ . 'http_build_url/http_build_url.php';
        }
        $url = http_build_url('', $parced_url);
        $orig_tmpfile = $tmpfile;
        if (Tools::copy($url, $tmpfile)) {
            // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
            if (!ImageManager::checkImageMemoryLimit($tmpfile)) {
                @unlink($tmpfile);
                return false;
            }
            $tgt_width = $tgt_height = 0;
            $src_width = $src_height = 0;
            $error = 0;
            ImageManager::resize($tmpfile, $path . '.jpg', null, null, 'jpg', false, $error, $tgt_width, $tgt_height, 5,
                $src_width, $src_height);
            $images_types = ImageType::getImagesTypes($entity, true);
            if ($regenerate) {
                $previous_path = null;
                $path_infos = array();
                $path_infos[] = array($tgt_width, $tgt_height, $path . '.jpg');
                foreach ($images_types as $image_type) {
                    $tmpfile = self::get_best_paths($image_type['width'], $image_type['height'], $path_infos);

                    if (ImageManager::resize($tmpfile, $path . '-' . stripslashes($image_type['name']) . '.jpg', $image_type['width'],
                        $image_type['height'], 'jpg', false, $error, $tgt_width, $tgt_height, 5,
                        $src_width, $src_height)) {
                        // the last image should not be added in the candidate list if it's bigger than the original image
                        if ($tgt_width <= $src_width && $tgt_height <= $src_height) {
                            $path_infos[] = array($tgt_width, $tgt_height, $path . '-' . stripslashes($image_type['name']) . '.jpg');
                        }
                        if ($entity == 'products') {
                            if (is_file(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int) $id_entity . '.jpg')) {
                                unlink(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int) $id_entity . '.jpg');
                            }
                            if (is_file(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int) $id_entity . '_' . (int) Context::getContext()->shop->id . '.jpg')) {
                                unlink(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int) $id_entity . '_' . (int) Context::getContext()->shop->id . '.jpg');
                            }
                        }
                    }
                    if (in_array($image_type['id_image_type'], $watermark_types)) {
                        Hook::exec('actionWatermark', array('id_image' => $id_image, 'id_product' => $id_entity));
                    }
                }
            }
        } else {
            @unlink($orig_tmpfile);
            return false;
        }
        unlink($orig_tmpfile);
        return true;
    }
    /**
     *Get product image path from store
     *
     * @param Int($tgt_width)
     * @param Int($tgt_height)
     * @param Array($path_infos)
     * @return String
     *
     */
    public function get_best_paths($tgt_width, $tgt_height, $path_infos)
    {
        $path_infos = array_reverse($path_infos);
        $path = '';
        foreach ($path_infos as $path_info) {
            list($width, $height, $path) = $path_info;
            if ($width >= $tgt_width && $height >= $tgt_height) {
                return $path;
            }
        }
        return $path;
    }
    /**
     *Add category to product
     *
     * @param nothing
     * @return integer
     *
     */
    public function addCategoryByProduct($lang_ids)
    {
        $categoryName = array((int) Configuration::get('PS_LANG_DEFAULT') => 'inkXE');
        $description = 'Demo Category';
        $link_rewrite = array((int) Configuration::get('PS_LANG_DEFAULT') => 'inkxe-tshirt');
        $meta_title = '';
        $meta_keywords = '';
        $meta_description = '';
        $data['id_parent'] = Configuration::get('PS_HOME_CATEGORY');
        $data['level_depth'] = $this->calcLevelDepth($data['id_parent']);
        $data['id_shop_default'] = (int) Context::getContext()->shop->id;
        $data['active'] = 1;
        $now = date('Y-m-d H:i:s', time());
        $data['date_add'] = $now;
        $data['date_upd'] = $now;
        $data['position'] = 1;
        $id_lang = Context::getContext()->language->id;
        $shop_id = Context::getContext()->shop->id;
        $sql = "SELECT id_category from " . _DB_PREFIX_ . "category_lang where name='" . $categoryName[1] . "' and id_shop=" . intval($shop_id) . " and id_lang =" . intval($id_lang) . "";
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if ($result[0]['id_category']) {
            return $id_category = $result[0]['id_category'];
        } else {
            if (DB::getInstance()->insert('category', $data)) {
                $id_category = Db::getInstance()->Insert_ID();
                foreach ($lang_ids as $v) {
                    $datal['id_category'] = $id_category;
                    $datal['id_shop'] = (int) $shop_id;
                    $datal['id_lang'] = $v['id_lang'];
                    $datal['name'] = pSQL($categoryName[1]);
                    $datal['description'] = pSQL($description);
                    $datal['link_rewrite'] = pSQL($link_rewrite[1]);
                    $datal['meta_title'] = pSQL($meta_title);
                    $datal['meta_keywords'] = pSQL($meta_keywords);
                    $datal['meta_description'] = pSQL($meta_description);
                    if (!DB::getInstance()->insert('category_lang', $datal)) {
                        die('Error in category lang insert : ' . $id_category);
                    }

                }
                $dataShop['id_category'] = $id_category;
                $dataShop['id_shop'] = (int) $shop_id;
                $dataShop['position'] = 1;
                if (!DB::getInstance()->insert('category_shop', $dataShop)) {
                    die('Error in category shop insert : ' . $id_category);
                }

                $this->regenerateEntireNtreeCategory();
                $this->updateGroup($this->groupBox, $id_category);
            } else {
                die('Error in category insert : ' . $data['id_parent']);
            }
            return $id_category;
        }
    }
    /**
     *clean group before update group
     *
     * @param (int)id_category
     * @return integer
     *
     */
    public function cleanGroups($id_category)
    {
        return Db::getInstance()->delete('category_group', 'id_category = ' . (int) $id_category);
    }
    /**
     *update category group
     *
     * @param (string)list
     * @param (int)id_category
     * @return nothing
     *
     */
    public function updateGroup($list, $id_category)
    {
        $this->cleanGroups($id_category);
        if (empty($list)) {
            $list = array(Configuration::get('PS_UNIDENTIFIED_GROUP'), Configuration::get('PS_GUEST_GROUP'), Configuration::get('PS_CUSTOMER_GROUP'));
        }
        $this->addGroups($list, $id_category);
    }
    /**
     *Add category group
     *
     * @param (string)groups
     * @param (int)id_category
     * @return nothing
     *
     */
    public function addGroups($groups, $id_category)
    {
        foreach ($groups as $group) {
            if ($group !== false) {
                Db::getInstance()->insert('category_group', array('id_category' => (int) $id_category, 'id_group' => (int) $group));
            }
        }
    }
    /**
     *Generate entity tree category
     *
     * @param nothing
     * @return nothing
     *
     */
    public function regenerateEntireNtreeCategory()
    {
        $id = Context::getContext()->shop->id;
        $id_shop = $id ? $id : Configuration::get('PS_SHOP_DEFAULT');
        $categories = Db::getInstance()->executeS('
		SELECT c.`id_category`, c.`id_parent`
		FROM `' . _DB_PREFIX_ . 'category` c
		LEFT JOIN `' . _DB_PREFIX_ . 'category_shop` cs
		ON (c.`id_category` = cs.`id_category` AND cs.`id_shop` = ' . (int) $id_shop . ')
		ORDER BY c.`id_parent`, cs.`position` ASC');
        $categories_array = array();
        foreach ($categories as $category) {
            $categories_array[$category['id_parent']]['subcategories'][] = $category['id_category'];
        }
        $n = 1;
        if (isset($categories_array[0]) && $categories_array[0]['subcategories']) {
            $this->subTree($categories_array, $categories_array[0]['subcategories'][0], $n);
        }
    }
    /**
     *Assaign category under a category
     *
     * @param (Array)categories_array
     * @param (Int)id_category
     * @param (Int)n
     * @return nothing
     *
     */
    public function subTree(&$categories, $id_category, &$n)
    {
        $left = $n++;
        if (isset($categories[(int) $id_category]['subcategories'])) {
            foreach ($categories[(int) $id_category]['subcategories'] as $id_subcategory) {
                $this->subTree($categories, (int) $id_subcategory, $n);
            }
        }
        $right = (int) $n++;
        Db::getInstance()->execute('
		UPDATE ' . _DB_PREFIX_ . 'category
		SET nleft = ' . (int) $left . ', nright = ' . (int) $right . '
		WHERE id_category = ' . (int) $id_category . ' LIMIT 1');
    }
    /**
     * Get the depth level for the category
     *
     * @return int Depth level
     */
    public function calcLevelDepth($id_parent)
    {
        /* Root category */
        if (!$id_parent) {
            return 0;
        }
        $parent_category = new Category((int) $id_parent);
        if (!Validate::isLoadedObject($parent_category)) {
            throw new PrestaShopException('Parent category does not exist');
        }
        return $parent_category->level_depth + 1;
    }
    //end //

    /**
     *Add product combination/atrribute image
     *
     * @param (Int)idProductAttribute
     * @param (Int)image_id
     * @return nothing
     *
     */
    public function addImageAttribute($idProductAttribute, $image_id)
    {
        $sql = "INSERT INTO " . _DB_PREFIX_ . "product_attribute_image (id_product_attribute,id_image) VALUES(" . intval($idProductAttribute) . "," . intval($image_id) . ")";
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($sql);
    }
    /**
     *To check attribute is exist or not
     *
     * @param (Int)id_attribute_group
     * @param (String)name
     * @param (Int)id_lang
     * @return integer
     *
     */
    public function isAttributeExit($id_attribute_group, $name, $id_lang)
    {
        $result = Db::getInstance()->getValue('
			SELECT COUNT(*)
			FROM `' . _DB_PREFIX_ . 'attribute_group` ag
			LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl
				ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . (int) $id_lang . ')
			LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a
				ON a.`id_attribute_group` = ag.`id_attribute_group`
			LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al
				ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int) $id_lang . ')
			' . Shop::addSqlAssociation('attribute_group', 'ag') . '
			' . Shop::addSqlAssociation('attribute', 'a') . '
			WHERE al.`name` = \'' . pSQL($name) . '\' AND ag.`id_attribute_group` = ' . (int) $id_attribute_group . '
			ORDER BY agl.`name` ASC, a.`position` ASC
		');
        return ((int) $result > 0);
    }
    /**
     *To update between two hooks position after installation a module
     *
     * @param nothing
     * @return nothing
     *
     */
    public function tableValueInterChange()
    {
        $sql_hook = "SELECT id_hook from " . _DB_PREFIX_ . "hook where name='displayProductButtons' ";
        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_hook);
        $sql_module = "SELECT id_module from " . _DB_PREFIX_ . "module where name='inkxedesignertool' ";
        $row_module = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_module);

        $sql_hook1 = "SELECT id_module from " . _DB_PREFIX_ . "module where name='productpaymentlogos' ";
        $row_module1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_hook1);
        $sql = "UPDATE
		    " . _DB_PREFIX_ . "hook_module AS rule1
		    JOIN " . _DB_PREFIX_ . "hook_module AS rule2 ON
		           ( rule1.id_module = " . $row_module1[0]['id_module'] . " AND rule2.id_module = " . $row_module[0]['id_module'] . " AND rule1.id_hook = " . $row[0]['id_hook'] . " AND rule2.id_hook = " . $row[0]['id_hook'] . ")
		        OR ( rule1.id_module = " . $row_module[0]['id_module'] . " AND rule2.id_module = " . $row_module1[0]['id_module'] . " AND rule1.id_hook = " . $row[0]['id_hook'] . " AND rule2.id_hook = " . $row[0]['id_hook'] . ")
		SET
		    rule1.position = rule2.position,
		    rule2.position = rule1.position";
        Db::getInstance()->Execute($sql);
    }
    /**
     *To create ref_id as composite key in cart_product table
     *
     * @param nothing
     * @return nothing
     *
     */
    public function alterTable()
    {
        $sql_insert = "ALTER TABLE " . _DB_PREFIX_ . "cart_product DROP PRIMARY KEY,
                ADD PRIMARY KEY (`id_cart`, `id_product`, `id_address_delivery`, `id_product_attribute`, `ref_id`)";
        Db::getInstance()->Execute($sql_insert);
    }
    /**
     *fetch only customize order
     *
     * @param (Int)order_id
     * @return integer
     *
     */
    public function customizeOrder($order_id)
    {
        $sql_ref = "SELECT count(*) as nos FROM " . _DB_PREFIX_ . "cart_product as cp,
		" . _DB_PREFIX_ . "orders as o
		where o.id_order ='" . $order_id . "' and o.id_cart =cp.id_cart and cp.ref_id>0";
        $rq_ref = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_ref);
        return $rq_ref[0]['nos'] ? 1 : 0;
    }
    /**
     *Alter custom column 'xe_is_temp' for predeco product
     *
     * @param nothing
     * @return integer
     *
     */
    public function alterProdutTable()
    {
        $status = 0;
        $sql = "SHOW COLUMNS FROM " . _DB_PREFIX_ . "product LIKE 'xe_is_temp'";
        $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if (!empty($rows)) {
            $status = 1;
        } else {
            $sql = "ALTER TABLE " . _DB_PREFIX_ . "product ADD COLUMN `xe_is_temp` enum('0', '1') DEFAULT '0'";
            $status = Db::getInstance()->Execute($sql);
        }
        $sql_pa = "SHOW COLUMNS FROM " . _DB_PREFIX_ . "product_attribute LIKE 'xe_is_temp'";
        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_pa);
        if (!empty($row)) {
            $status = 1;
        } else {
            $sql1 = "ALTER TABLE " . _DB_PREFIX_ . "product_attribute ADD COLUMN `xe_is_temp` enum('0', '1') DEFAULT '0'";
            $status = Db::getInstance()->Execute($sql1);
        }
        $sql_pca = "SHOW COLUMNS FROM " . _DB_PREFIX_ . "product_attribute_combination LIKE 'xe_is_temp'";
        $rows1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_pca);
        if (!empty($rows1)) {
            $status = 1;
        } else {
            $sql2 = "ALTER TABLE " . _DB_PREFIX_ . "product_attribute_combination ADD COLUMN `xe_is_temp` enum('0', '1') DEFAULT '0'";
            $status = Db::getInstance()->Execute($sql2);
        }
        return $msg = $status ? $status : 0;
    }
    /**
     *fetch only getSizeArr
     *
     * @param nothing
     * @return array
     *
     */
    public function getSizeArr()
    {
        $lang_id = (int) Context::getContext()->cookie->id_lang;
        $sql = "select al.id_attribute,al.name from " . _DB_PREFIX_ . "attribute_lang as al," . _DB_PREFIX_ . "attribute atr
		where  al.id_attribute = atr.id_attribute and al.id_lang ='$lang_id'
		and atr.color ='' and (al.name !='active' and al.name !='inactive')";
        $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        $resultArr = array();
        foreach ($rows as $k => $v) {
            $resultArr[$k]['value'] = $v['id_attribute'];
            $resultArr[$k]['label'] = $v['name'];
        }
        return json_encode($resultArr);
    }
    /**
     *add pre decorated product by product id
     *
     * @param (Array)data
     * @return json array
     *
     */
    public function addTemplateProducts($data)
    {
        extract($data['data']);
        extract($data);
        //get product details by product id
        $pid = $simpleproduct_id;
        $congId = $simpleproduct_id;
        $context = \Context::getContext();
        $product = new Product($pid, false, $context->language->id);
        $combinations = $product->getAttributeCombinations((int) ($context->cookie->id_lang));
        foreach ($combinations as $v1) {
            if (($v1['is_color_group'] == 1) && ($v1['group_name'] == 'Color') && ($varColor == $v1['id_attribute'])) {
				$productDetails = $this->getSimpleProducts($v1['id_product_attribute'], $pid);
				$productDetails = json_decode($productDetails, true);
				extract($productDetails);
				if(!empty($sides)){
					$now = date('Y-m-d H:i:s', time());
					$sql_lang = "Select id_lang FROM " . _DB_PREFIX_ . "lang";
					$lang_id = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_lang);
					$id_shop = (int) Context::getContext()->shop->id;
					$sizeCount = count($varSize);
					$quantity = $totalQantity = $sizeCount ? $qty * $sizeCount : $qty;
					// Custom attribute 'Pdp' added for predeco product
					$customAttr = $this->addNewCustomAttribute($lang_id);
					$product_name = pSQL($product_name);
					$sku = pSQL($sku);
					$description = pSQL($description);
					$short_description = pSQL($short_description);
					//After configurable product added successfully then added new combination/variant
					if ($conf_id) {
						$image_id = $this->addImageByProductId($configFile, $conf_id, $product_name, $lang_id, $id_shop);
						//addAttributeToProduct//
						$attrId = $this->addProductAttributesByProductId($varSize, $conf_id, $sku, $varColor, $attr_id, $id_shop, false, true, $customAttr['activeId'], $pdpInact = '');
						if ($attrId) {
							$this->addProductStock($attrId, $conf_id, $totalQantity, $qty, $product_id = '');
							$this->addImageAttributes($attrId, $image_id);
						}
					} else {
						//added new predecoproduct
						$insert_sql = "INSERT INTO " . _DB_PREFIX_ . "product(id_supplier,id_manufacturer,id_category_default,id_tax_rules_group,price,reference,active,redirect_type,indexed,cache_default_attribute,date_add,date_upd,customize,xe_is_temp)
						VALUES('1','1','$cat_id[0]','1','$price','$sku','1','404','1','','$now','$now','$is_customized','1')";
						Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($insert_sql);
						$productId = Db::getInstance()->Insert_ID();
						//ps_product_shop
						$product_shop_sql = "INSERT INTO `" . _DB_PREFIX_ . "product_shop` (`id_product`, `id_shop`, `id_category_default`, `id_tax_rules_group`,
						 `minimal_quantity`, `price`, `active`, `redirect_type`, `id_product_redirected`, `available_for_order`, `condition`, `show_price`, `indexed`, `visibility`,
							`cache_default_attribute`, `advanced_stock_management`, `date_add`, `date_upd`, `pack_stock_type`)
						 VALUES ('$productId', '$id_shop', '$cat_id[0]', '1', '1', '$price', '1', '404', '0', '1', 'new', '1', '1', 'both', '0', '0', '$now', '$now', '3')";
						Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($product_shop_sql);
						//ps_product_lang
						$link_rewrite = strtolower($product_name);
						if (preg_match('/\s/', $link_rewrite)) {
							$link_rewrite = str_replace(' ', '-', $link_rewrite);
						}
						foreach ($lang_id as $v) {
							$product_lang_sql = "INSERT INTO " . _DB_PREFIX_ . "product_lang(id_product,id_shop,id_lang,description,description_short,link_rewrite,
								meta_description,meta_keywords,meta_title,name,available_now,available_later)
							VALUES('$productId','$id_shop','" . $v['id_lang'] . "','$description','$short_description','$link_rewrite','','','','$product_name','','')";
							Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($product_lang_sql);
						}
						//add Category to product //
						$this->addToCategoriesToProduct($cat_id, $productId);
						//product image add
						$image_id = $this->addImageByProductId($configFile, $productId, $product_name, $lang_id, $id_shop);
						//addAttributeToProduct//
						$attrId = $this->addProductAttributesByProductId($varSize, $productId, $sku, $varColor, $attr_id, $id_shop, false, false, $customAttr['activeId'], $pdpInact = '');
						if ($attrId) {
							$this->addProductStock($attrId, $productId, $totalQantity, $qty);
							$this->addImageAttributes($attrId, $image_id);
						}
					}
					$productId = $conf_id ? $conf_id : $productId;
					//After configurable product added success add simple product by cofigurable product
					if ($productId) {
						$congId = $v1['id_product_attribute'];
						$productDetails = $this->getSimpleProducts($congId, $pid);
						$productDetails = json_decode($productDetails, true);
						extract($productDetails);
						if ($conf_id) {
							$sql_quant = "SELECT sa.quantity FROM " . _DB_PREFIX_ . "stock_available as sa," . _DB_PREFIX_ . "product_attribute as pa
							WHERE sa.id_product_attribute=pa.id_product_attribute
							AND pa.xe_is_temp ='0' AND sa.id_product='$conf_id' ORDER by sa.quantity DESC limit 1";
							$result_qunt = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_quant);
							$quanntity = $result_qunt[0]['quantity'];
							$sql_totquant = "SELECT quantity FROM " . _DB_PREFIX_ . "stock_available WHERE id_product_attribute=0 AND id_product=$conf_id ";
							$result_totqunt = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_totquant);
							$totalQantity = $result_totqunt[0]['quantity'] + $quantity;

						} else {
							$totalQantity = $totalQantity + $quanntity;
						}
						$varColor1 = $xe_color_id;
						$xeSizeArr[] = $xe_size_id;
						//Add product image add
						$image_id1 = $this->addImageByProductId($sides, $productId, pSQL($pname), $lang_id, $id_shop, true);
						//Add attribute to product//
						$attrId1 = $this->addProductAttributesByProductId($varSize, $productId, pSQL($reference), $varColor, $attrId1, $id_shop, true, false, $pdpAct = '', $customAttr['inActiveId']);
						if ($attrId1) {
							$this->addProductStock($attrId1, $productId, $totalQantity, $quanntity, true, 0);
							$this->addImageAttributes($attrId1, $image_id1);
						}
						$context = \Context::getContext();
						$product = new Product($productId, false, $context->language->id);
						$combinations1 = $product->getAttributeCombinations((int) ($context->cookie->id_lang));
						$resultArr[0]['status'] = 'success';
						$resultArr[0]['conf_id'] = $productId;
						foreach ($combinations1 as $k => $v) {
							foreach ($attrId as $k2 => $v2) {
								if ($v['id_product_attribute'] == $v2) {
									if ($v['is_color_group'] == 0 && $v['group_name'] != 'Pdp') {
										$result[$k2]['color_id'] = $varColor;
										$result[$k2]['var_id'] = $v2;
										$result[$k2]['sizeid'][] = $v['id_attribute'];
									}
								}
							}
						}
						$msg = $this->updateTotalQuantityByPid($productId);
						if($msg){
							return json_encode(array("status" => "success", "conf_id" => $productId, "variants" => $result));
						}
					} else {
						return json_encode(array("status" => "failed"));
					}
				}
            }
		}	
    }
    /**
     *add pre decorated  product iamge by product id
     *
     * @param (Int)productId
     * @param (Array)configFile
     * @param (String)product_name
     * @param (Int)lang_id
     * @param (Int)id_shop
     * @return  array
     *
     */
    public function addImageByProductId($configFile, $productId, $product_name, $lang_id, $id_shop, $isExitProduct = false)
    {
        if ($isExitProduct) {
            foreach ($configFile as $imageUrl) {
                $position = Image::getHighestPosition($productId) + 1;
                $image_sql1 = "INSERT INTO " . _DB_PREFIX_ . "image(id_product,position) VALUES('$productId','$position')";
                Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($image_sql1);
                $image_id[] = $id_iamge = Db::getInstance()->Insert_ID();
                //image_lang
                foreach ($lang_id as $v) {
                    $image_lan_sql = "INSERT INTO " . _DB_PREFIX_ . "image_lang(id_image,id_lang,legend) VALUES('$id_iamge','" . $v['id_lang'] . "','$product_name')";
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($image_lan_sql);
                }
                //image_shop
                $image_lan_sql = "INSERT INTO " . _DB_PREFIX_ . "image_shop(id_product,id_image,id_shop) VALUES('$productId','$id_iamge','$id_shop')";
                Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($image_lan_sql);
                //copy product image
                self::copyImg($productId, $id_iamge, $imageUrl, 'products', true);
            }
        } else {
            $i = 0;
            foreach ($configFile as $imageUrl) {
                $position = Image::getHighestPosition($productId) + 1;
                $cover = true; // or false;
                if ($i == 0) {
                    $image_sql = "INSERT INTO " . _DB_PREFIX_ . "image(id_product,position,cover) VALUES('$productId','$position','1')";
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($image_sql);
                    $image_id[] = $id_iamge = Db::getInstance()->Insert_ID();
                    //image_lang
                    foreach ($lang_id as $v1) {
                        $image_lan_sql = "INSERT INTO " . _DB_PREFIX_ . "image_lang(id_image,id_lang,legend) VALUES('$id_iamge','" . $v1['id_lang'] . "','$product_name')";
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($image_lan_sql);
                    }
                    //image_shop
                    $image_lan_sql = "INSERT INTO " . _DB_PREFIX_ . "image_shop(id_product,id_image,id_shop,cover) VALUES('$productId','$id_iamge','$id_shop','1')";
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($image_lan_sql);
                    //copy product image
                    self::copyImg($productId, $id_iamge, $imageUrl, 'products', true);
                } else {
                    $image_sql1 = "INSERT INTO " . _DB_PREFIX_ . "image(id_product,position) VALUES('$productId','$position')";
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($image_sql1);
                    $image_id[] = $id_iamge = Db::getInstance()->Insert_ID();
                    //image_lang
                    foreach ($lang_id as $v2) {
                        $image_lan_sql = "INSERT INTO " . _DB_PREFIX_ . "image_lang(id_image,id_lang,legend) VALUES('$id_iamge','" . $v2['id_lang'] . "','$product_name')";
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($image_lan_sql);
                    }
                    //image_shop
                    $image_lan_sql = "INSERT INTO " . _DB_PREFIX_ . "image_shop(id_product,id_image,id_shop) VALUES('$productId','$id_iamge','$id_shop')";
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($image_lan_sql);
                    //copy product image
                    self::copyImg($productId, $id_iamge, $imageUrl, 'products', true);

                }
                $i++;
            }
        }
        return $image_id;
    }
    /**
     *add product attribute by product id
     *
     * @param (Int)productId
     * @param (Array)varSize
     * @param (Array)attr_id
     * @param (String)sku
     * @param (Int)lang_id
     * @param (Int)id_shop
     * @return  array
     *
     */
    public function addProductAttributesByProductId($varSize, $productId, $sku, $varColor, $attr_id, $id_shop, $isExitProduct = false, $isExitAttr = false, $pdpAct = '', $pdpInact = '')
    {
        if ($isExitProduct) {
            foreach ($varSize as $v1) {
                $attr_sql1 = "INSERT INTO " . _DB_PREFIX_ . "product_attribute(id_product,reference) VALUES('$productId','$sku')";
                Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($attr_sql1);
                $attrId[] = $attr_id = Db::getInstance()->Insert_ID();
                //add product atttribute size and color
                $sql_insert = "INSERT INTO " . _DB_PREFIX_ . "product_attribute_combination(id_attribute,id_product_attribute,xe_is_temp) VALUES('$varColor','$attr_id','1')";
                Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($sql_insert);
                $sql_insert1 = "INSERT INTO " . _DB_PREFIX_ . "product_attribute_combination(id_attribute,id_product_attribute,xe_is_temp) VALUES('$v1','$attr_id','1')";
                Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($sql_insert1);
                $sql_insert2 = "INSERT INTO " . _DB_PREFIX_ . "product_attribute_combination(id_attribute,id_product_attribute,xe_is_temp) VALUES('$pdpInact','$attr_id','1')";
                Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($sql_insert2);
                //ps_product_atrribute_shop
                $sql_pashop = "INSERT INTO " . _DB_PREFIX_ . "product_attribute_shop(id_product,id_product_attribute,id_shop)
				VALUES('$productId','$attr_id','$id_shop')";
                Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($sql_pashop);

            }

        } else {
            $j = 0;
            foreach ($varSize as $v3) {
                if ($isExitAttr) {
                    $attr_sql1 = "INSERT INTO " . _DB_PREFIX_ . "product_attribute(id_product,reference,xe_is_temp) VALUES('$productId','$sku','1')";
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($attr_sql1);
                    $attrId[] = $attr_id = Db::getInstance()->Insert_ID();
                    //add product atttribute size and color
                    $sql_insert = "INSERT INTO " . _DB_PREFIX_ . "product_attribute_combination(id_attribute,id_product_attribute) VALUES('$varColor','$attr_id')";
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($sql_insert);
                    $sql_insert1 = "INSERT INTO " . _DB_PREFIX_ . "product_attribute_combination(id_attribute,id_product_attribute) VALUES('$v3','$attr_id')";
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($sql_insert1);

                    $sql_insert2 = "INSERT INTO " . _DB_PREFIX_ . "product_attribute_combination(id_attribute,id_product_attribute,xe_is_temp) VALUES('$pdpAct','$attr_id','1')";
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($sql_insert2);
                    //ps_product_atrribute_shop
                    $sql_pashop = "INSERT INTO " . _DB_PREFIX_ . "product_attribute_shop(id_product,id_product_attribute,id_shop)
					VALUES('$productId','$attr_id','$id_shop')";
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($sql_pashop);
                } else {
                    if ($j == 0) {
                        $attr_sql = "INSERT INTO " . _DB_PREFIX_ . "product_attribute(id_product,reference,default_on,xe_is_temp) VALUES('$productId','$sku','1','1')";
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($attr_sql);
                        $attrId[] = $attr_id = Db::getInstance()->Insert_ID();
                        //add product atttribute size and color
                        $sql_insert = "INSERT INTO " . _DB_PREFIX_ . "product_attribute_combination(id_attribute,id_product_attribute) VALUES('$varColor','$attr_id')";
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($sql_insert);
                        $sql_patrr = "INSERT INTO " . _DB_PREFIX_ . "product_attribute_combination(id_attribute,id_product_attribute) VALUES('$v3','$attr_id')";
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($sql_patrr);
                        $sql_insert2 = "INSERT INTO " . _DB_PREFIX_ . "product_attribute_combination(id_attribute,id_product_attribute,xe_is_temp) VALUES('$pdpAct','$attr_id','1')";
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($sql_insert2);
                        //ps_product_atrribute_shop
                        $sql_pashop = "INSERT INTO " . _DB_PREFIX_ . "product_attribute_shop(id_product,id_product_attribute,id_shop,default_on)
						VALUES('$productId','$attr_id','$id_shop','1')";
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($sql_pashop);

                    } else {
                        $attr_sql1 = "INSERT INTO " . _DB_PREFIX_ . "product_attribute(id_product,reference,xe_is_temp) VALUES('$productId','$sku','1')";
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($attr_sql1);
                        $attrId[] = $attr_id = Db::getInstance()->Insert_ID();
                        //add product atttribute size and color
                        $sql_insert = "INSERT INTO " . _DB_PREFIX_ . "product_attribute_combination(id_attribute,id_product_attribute) VALUES('$varColor','$attr_id')";
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($sql_insert);
                        $sql_insert1 = "INSERT INTO " . _DB_PREFIX_ . "product_attribute_combination(id_attribute,id_product_attribute) VALUES('$v3','$attr_id')";
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($sql_insert1);
                        $sql_insert2 = "INSERT INTO " . _DB_PREFIX_ . "product_attribute_combination(id_attribute,id_product_attribute,xe_is_temp) VALUES('$pdpAct','$attr_id','1')";
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($sql_insert2);
                        //ps_product_atrribute_shop
                        $sql_pashop = "INSERT INTO " . _DB_PREFIX_ . "product_attribute_shop(id_product,id_product_attribute,id_shop)
						VALUES('$productId','$attr_id','$id_shop')";
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($sql_pashop);
                    }
                }
                $j++;
            }
        }
        return $attrId;
    }
    /**
     *Add product combination/atrribute image
     *
     * @param (Int)idProductAttribute
     * @param (Int)image_id
     * @return nothing
     *
     */
    public function addProductStock($attrId = array(), $productId, $totalQantity, $qty, $isExitProduct = false, $conf_id = '')
    {
        if (!is_array($attrId)) {
            $attrId = array($attrId);
        }
        $id_shop = (int) Context::getContext()->shop->id;
        if ($isExitProduct) {
            $totalQantity = $conf_id ? $totalQantity : $totalQantity + $qty;
            $query = "UPDATE " . _DB_PREFIX_ . "stock_available SET quantity= '" . $totalQantity . "' WHERE id_product = " . $productId . " AND id_product_attribute='0'";
            Db::getInstance()->Execute($query);
            foreach ($attrId as $k => $v) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "stock_available (id_product,id_product_attribute,id_shop,id_shop_group,quantity,
		       		out_of_stock) VALUES('$productId','$v','$id_shop','','$qty','2')";
                Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($sql);
            }
        } else {
            $sql = "INSERT INTO " . _DB_PREFIX_ . "stock_available (id_product,id_product_attribute,id_shop,id_shop_group,quantity,
			       		out_of_stock) VALUES('$productId','','$id_shop','','$totalQantity','2')";
            Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($sql);
            foreach ($attrId as $k => $v) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "stock_available (id_product,id_product_attribute,id_shop,id_shop_group,quantity,
		       		out_of_stock) VALUES('$productId','$v','$id_shop','','$qty','2')";
                Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($sql);
            }
        }
    }
    /**
     *Add product combination/atrribute image
     *
     * @param (Int)idProductAttribute
     * @param (Int)image_id
     * @return nothing
     *
     */
    public function addImageAttributes($idProductAttribute, $image_id = array())
    {
        if (!is_array($image_id)) {
            $image_id = array($image_id);
        }
        foreach ($image_id as $v) {
            foreach ($idProductAttribute as $v1) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "product_attribute_image (id_product_attribute,id_image) VALUES(" . intval($v1) . "," . intval($v) . ")";
                Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($sql);
            }

        }

    }
    /**
     * addToCategories add this product to the category/ies if not exists.
     *
     * @param mixed $categories id_category or array of id_category
     * @return bool true if succeed
     */
    public function addToCategoriesToProduct($categories = array(), $productId)
    {
        if (!is_array($categories)) {
            $categories = array($categories);
        }
        $categories = array_map('intval', $categories);
        $current_categories = $this->getCategories();
        $current_categories = array_map('intval', $current_categories);
        // for new categ, put product at last position
        $res_categ_new_pos = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT id_category, MAX(position)+1 newPos
			FROM `' . _DB_PREFIX_ . 'category_product`
			WHERE `id_category` IN(' . implode(',', $categories) . ')
			GROUP BY id_category');
        foreach ($res_categ_new_pos as $array) {
            $new_categories[(int) $array['id_category']] = (int) $array['newPos'];
        }
        $new_categ_pos = array();
        foreach ($categories as $id_category) {
            $new_categ_pos[$id_category] = isset($new_categories[$id_category]) ? $new_categories[$id_category] : 0;
        }
        $product_cats = array();
        foreach ($categories as $new_id_categ) {
            if (!in_array($new_id_categ, $current_categories)) {
                $product_cats[] = array(
                    'id_category' => (int) $new_id_categ,
                    'id_product' => (int) $productId,
                    'position' => (int) $new_categ_pos[$new_id_categ],
                );
            }
        }
        Db::getInstance()->insert('category_product', $product_cats);
    }
    /**
     *Add new custom attribute for predeco product
     *
     * @param nothing
     * @return Array
     *
     */
    public function addNewCustomAttribute($lang_ids)
    {
        //add size attribue
        $id_lang = Context::getContext()->language->id;
        $id_shop = (int) Context::getContext()->shop->id;
        $attributeName = 'Pdp';
        $sqlZize = "SELECT id_attribute_group from " . _DB_PREFIX_ . "attribute_group_lang where name='" . $attributeName . "' and id_lang=" . $id_lang . "";
        $resultSize = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sqlZize);
        if (empty($resultSize[0]['id_attribute_group'])) {
            $sql = "SELECT position FROM " . _DB_PREFIX_ . "attribute_group order by position desc limit 1";
            $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            $insert_sql = "INSERT INTO `" . _DB_PREFIX_ . "attribute_group` (`group_type`,`position`) VALUES('select','" . intval($row['0']['position'] + 1) . "')";
            Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($insert_sql);
            $groupId = Db::getInstance()->Insert_ID();
            foreach ($lang_ids as $v) {
                $insert_sql1 = "INSERT INTO " . _DB_PREFIX_ . "attribute_group_lang (id_attribute_group,id_lang,name,public_name) VALUES(" . $groupId . "," . $v['id_lang'] . ",'$attributeName','pdp')";
                Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($insert_sql1);
            }
            $insert_sql2 = "INSERT INTO " . _DB_PREFIX_ . "attribute_group_shop (id_attribute_group,id_shop) VALUES(" . $groupId . "," . $id_shop . ")";
            Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($insert_sql2);

            $attribute = new Attribute();
            $attribute->name = $this->createMultiLangFields('active');
            $attribute->id_attribute_group = $groupId;
            $attribute->color = '';
            $attribute->position = 0;
            $attribute->add();

            $attribute1 = new Attribute();
            $attribute1->name = $this->createMultiLangFields('inactive');
            $attribute1->id_attribute_group = $groupId;
            $attribute1->color = '';
            $attribute1->position = 0;
            $attribute1->add();

        } else {
            $exitResult = $this->isAttributeExit($resultSize[0]['id_attribute_group'], 'active', $id_lang);
            if (empty($exitResult)) {
                $attribute = new Attribute();
                $attribute->name = $this->createMultiLangFields('active');
                $attribute->id_attribute_group = $resultSize[0]['id_attribute_group'];
                $attribute->color = '';
                $attribute->position = 0;
                $attribute->add();
            }
            $exitResult = $this->isAttributeExit($resultSize[0]['id_attribute_group'], 'inactive', $id_lang);
            if (empty($exitResult)) {
                $attribute = new Attribute();
                $attribute->name = $this->createMultiLangFields('inactive');
                $attribute->id_attribute_group = $resultSize[0]['id_attribute_group'];
                $attribute->color = '';
                $attribute->position = 0;
                $attribute->add();
            }
        }
        $sql = "SELECT id_attribute from " . _DB_PREFIX_ . "attribute_lang where name='active' and id_lang=" . intval($id_lang) . "";
        $row_active = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        $resultArr['activeId'] = $row_active[0]['id_attribute'];

        $sql_attr = "SELECT id_attribute from " . _DB_PREFIX_ . "attribute_lang where name='inactive' and id_lang=" . intval($id_lang) . "";
        $row_inactive = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_attr);
        $resultArr['inActiveId'] = $row_inactive[0]['id_attribute'];
        return $resultArr;
    }
    /**
     *Create multi language in prestasho store
     *
     * @param (String)field
     * @return Boolean
     *
     */
    public function createMultiLangFields($field)
    {
        $res = array();
        foreach (Language::getIDs(false) as $id_lang) {
            $res[$id_lang] = $field;
        }

        return $res;
    }
    /**
     *Update total quantity by product id after predeco product successfully added
     *
     * @param (Int)productId
     * @return nothing
     *
     */
    public function updateTotalQuantityByPid($productId)
    {
        $sql = "SELECT quantity FROM " . _DB_PREFIX_ . "stock_available WHERE id_product = " . $productId . " AND id_product_attribute !='0'";
        $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        foreach ($rows as $k => $v) {
            $totalQantity += $v['quantity'];
        }
        $query = "UPDATE " . _DB_PREFIX_ . "stock_available SET quantity= '" . $totalQantity . "' WHERE id_product = " . $productId . " AND id_product_attribute='0'";
        return Db::getInstance()->Execute($query);
    }
    /**
     *Fetch product info by productId in when predeco product addtocart
     *
     * @param (Arrayy)data
     * @return Json Array
     *
     */
    public function getProductInfo($data)
    {
        $configId = $data['configId'];
        $smplProdID = $data['smplProdID'];
        $context = \Context::getContext();
        $product = new Product($smplProdID, false, $context->language->id);
        $combinations = $product->getAttributeCombinations((int) ($context->cookie->id_lang));
        $resultArr = array();
        $resultArr[0]['is_predeco_product_template'] = true;
        $resultArr[0]['qty'] = intval($data['qty']);
        $resultArr[0]['id'] = intval($smplProdID);
        $resultArr[0]['refid'] = intval($data['refid']);
        $resultArr[0]['addedprice'] = "0";
        foreach ($combinations as $v) {
            if ($v['id_product_attribute'] == $configId) {
                if ($v['is_color_group'] == 0 && $v['group_name'] != 'Pdp') {
                    $result['xe_size'] = $v['attribute_name'];
                    $result['xe_size_id'] = $v['id_attribute'];
                }
                if (($v['is_color_group'] == 1) && ($v['group_name'] == 'Color')) {
                    $color_id = $v['id_attribute'];
                }
            }
        }
        $sql_fetch = "SELECT color FROM " . _DB_PREFIX_ . "attribute WHERE id_attribute=" . $color_id . "";
        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_fetch);
        $result['xe_color'] = $row[0]['color'];
        $result['simpleProductId'] = $configId;
        $resultArr[0]['simple_product'] = $result;
        return json_encode($resultArr);
    }
    /**
     *Get prestashop current verion
     *
     * @param Nothing
     * @return Float value
     *
     */
    public function storeVersion()
    {
        $version = _PS_VERSION_;
        return $version;
    }
    /**
     *get Product cover image
     *
     *@param id_product(int)
     *@return int
     */
    public function getProductCoverImageId($id_product, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }
        $cache_id = 'Product::getCover_' . (int) $id_product . '-' . (int) $context->shop->id;
        if (!Cache::isStored($cache_id)) {
            $sql = 'SELECT image_shop.`id_image`
					FROM `' . _DB_PREFIX_ . 'image` i
					' . Shop::addSqlAssociation('image', 'i') . '
					WHERE i.`id_product` = ' . (int) $id_product . '
					AND image_shop.`cover` = 1';
            $result = Db::getInstance()->getRow($sql);
            Cache::store($cache_id, $result);
            return $result;
        }
        return Cache::retrieve($cache_id);
    }
    /**
     * Get all size and quanties by product id and combination id from store
     *
     * @param (int)productId
     * @param (int)combinationsId
     * @return json array
     */
    public function getSizeVariants($productId, $combinationsId)
    {
        try {
            $lang_id = (int) Context::getContext()->cookie->id_lang;
            $id_shop = (int) Context::getContext()->shop->id;
            $product_sql = "SELECT p.id_product,p.price,pa.id_product_attribute FROM " . _DB_PREFIX_ . "product as p,
			" . _DB_PREFIX_ . "product_lang as pl," . _DB_PREFIX_ . "product_attribute as pa WHERE p.id_product = " . $productId . " AND
			p.id_product = pl.id_product AND p.id_product = pa.id_product AND pl.id_lang = " . $lang_id . " AND pl.id_shop = " . $id_shop . "";
            $rowsData = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($product_sql);

            foreach ($rowsData as $k1 => $v) {
                $result[$k1]['simpleProductId'] = $v['id_product_attribute'];
                //get size array
                $sql_sizes = "select sa.quantity,al.id_attribute,al.name from " . _DB_PREFIX_ . "attribute_lang as al
				left join " . _DB_PREFIX_ . "product_attribute_combination as pac on al.id_attribute = pac.id_attribute
				left join " . _DB_PREFIX_ . "attribute atr on al.id_attribute = atr.id_attribute
				join " . _DB_PREFIX_ . "stock_available sa on pac.id_product_attribute = sa.id_product_attribute
				where atr.color = '' and al.name !='active' and al.name != 'inactive'
				and al.id_lang = " . $lang_id . "
				and pac.id_product_attribute = '" . $v['id_product_attribute'] . "' ";
                $result_sizes = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_sizes);
                $result[$k1]['xe_size'] = $result_sizes[0]['name'];
                $result[$k1]['xe_size_id'] = $result_sizes[0]['id_attribute'];
                $result[$k1]['quantity'] = intval($result_sizes[0]['quantity']);
                //get color array
                $sql_colors = "select al.name as colorName,atr.id_attribute as color_id from " . _DB_PREFIX_ . "attribute_lang as al,
					" . _DB_PREFIX_ . "product_attribute_combination as pac," . _DB_PREFIX_ . "attribute as atr
					where al.id_attribute = pac.id_attribute
					and atr.id_attribute = al.id_attribute
					and al.id_lang = " . $lang_id . "
					and atr.id_attribute_group = (select ag.id_attribute_group from " . _DB_PREFIX_ . "attribute_group as ag, " . _DB_PREFIX_ . "attribute_group_lang agl where ag.is_color_group = 1 and agl.id_attribute_group = ag.id_attribute_group and id_lang = " . $lang_id . " limit 1)
					and pac.id_product_attribute = '" . $v['id_product_attribute'] . "' ";
                $result_colors = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_colors);
                $result[$k1]['xe_color'] = $result_colors[0]['colorName'];
                $result[$k1]['xe_color_id'] = $result_colors[0]['color_id'];
                $result[$k1]['price'] = $rowsData[0]['price'];
            }

            $resultArr = array();
            $resultArr['quantities'] = array_values($result);
            return json_encode($resultArr);
        } catch (PrestaShopDatabaseException $e) {
            echo 'Database error: <br />' . $e->displayMessage();
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
        $context = \Context::getContext();
        $order_process = Configuration::get('PS_ORDER_PROCESS_TYPE') ? 'order-opc' : 'order';
        $rest_str = substr(_PS_VERSION_, 0, 3);
        if ($rest_str > 1.6) {
            $cart_url = $this->getCartSummaryURLS();
        } else {
            $cart_url = $context->link->getPageLink($order_process, true);
        }
        $cart_id = $context->cookie->id_cart;
        $cart_item_result = array();
        $sql = "SELECT SUM(quantity) as Total FROM " . _DB_PREFIX_ . "cart_product WHERE id_cart = " . $cart_id . "";
        $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        $cart_item_result['is_Fault'] = 0;
        $cart_item_result['totalCartItem'] = $rows[0]['Total'] ? $rows[0]['Total'] : 0;
        $cart_item_result['checkoutURL'] = $cart_url;
        return $cart_item_result;
    }
	/**
     * Get refid by order_details_id
     *
     * @param   Int($orderDetailId)
     * @return  Int
     *
     */
    public function getRefIdByOrderDetailId($orderDetailId){
        $sql = "SELECT ref_id from "._DB_PREFIX_."order_detail where id_order_detail='".$orderDetailId."'";
        $resultSize = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        return $resultSize[0]['ref_id'];
    }
	/**
     * get pending order count by last order id
     *
     * @param   Int($last_id)
     * @return  Array($result)
     *
     */
	public function getPendingOrdersCount($lastId){
		$sql = 'SELECT distinct o.id_order FROM ' . _DB_PREFIX_ . 'orders as o
		left join ' . _DB_PREFIX_ . 'cart_product as cp on o.id_cart = cp.id_cart
		left join ' . _DB_PREFIX_ . 'order_history oh on o.id_order = oh.id_order where cp.ref_id >0 and o.id_order >'.$lastId.'';
        $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		$result = array("lastOrderID" =>$lastId,"pendingOrderCount"=>count($rows));
		return $result;
	}
}
