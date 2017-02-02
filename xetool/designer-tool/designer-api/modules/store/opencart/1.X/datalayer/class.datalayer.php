<?php
require_once dirname(__FILE__) . '/../../../../../../../config.php';
class Datalayer
{
    public $con = '';
    public function __construct()
    {
        $this->con = mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        if (mysqli_connect_errno()) {
            $this->con = '';
        }
        mysqli_set_charset($this->con, "utf8");
    }
    public function userAuthenticate($username = '', $key = '')
    {
        $key = md5($key);
        $sql = "SELECT * FROM `onj_api_user` WHERE username='" . API_USERNAME . "' AND `key` = '" . API_KEY . "'";
        /*
        $query = mysqli_query($this->con,$sql);
        if(mysqli_num_rows($query)){
        return true;
        } */
        //return false;
        return true;
    }
    public function getCategories()
    {
        $categories = array();
        $sql = "SELECT c.category_id,cd.name FROM " . DB_PREFIX . "category c INNER JOIN " . DB_PREFIX . "category_description cd ON (cd.category_id = c.category_id) WHERE c.parent_id=0";
        $query = mysqli_query($this->con, $sql);
        $i = 0;
        while ($row = mysqli_fetch_array($query, MYSQL_ASSOC)) {
            $categories[$i]['id'] = $row['category_id'];
            $categories[$i]['name'] = $row['name'];
            $i++;
        }
        return $categories;
    }
    public function getCategoryId($category)
    {
        $this->log('category:' . $category);
        $sql = "SELECT c.category_id FROM " . DB_PREFIX . "category c INNER JOIN " . DB_PREFIX . "category_description cd ON (cd.category_id = c.category_id) WHERE cd.name='" . $category . "'";
        $query = mysqli_query($this->con, $sql);
        $row = mysqli_fetch_array($query, MYSQL_ASSOC);

        return $row['category_id'];
    }
    public function getCategoryList($categoryid)
    {
        $categories[] = $categoryid;
        $sql = "SELECT c.category_id FROM " . DB_PREFIX . "category c WHERE c.parent_id='" . (int) $categoryid . "'";
        $query = mysqli_query($this->con, $sql);
        while ($row = mysqli_fetch_array($query, MYSQL_ASSOC)) {
            $categories[] = $row['category_id'];
        }
        return $categories;
    }
    public function getProductCategoryList($productid)
    {
        $sql = "SELECT c.category_id FROM " . DB_PREFIX . "product_to_category c WHERE c.product_id='" . (int) $productid . "'";
        $query = mysqli_query($this->con, $sql);
        while ($row = mysqli_fetch_array($query, MYSQL_ASSOC)) {
            $categories[] = $row['category_id'];
        }
        return $categories;
    }
    public function getSubCategory($categoryid)
    {
        $subcategory = array();
        $sql = "SELECT c.category_id,cd.name FROM " . DB_PREFIX . "category c INNER JOIN " . DB_PREFIX . "category_description cd ON (cd.category_id = c.category_id) WHERE c.parent_id='" . (int) $categoryid . "'";
        $query = mysqli_query($this->con, $sql);
        $i = 0;
        while ($row = mysqli_fetch_array($query, MYSQL_ASSOC)) {
            $subcategory[$i]['id'] = $row['category_id'];
            $subcategory[$i]['name'] = $row['name'];
            $i++;
        }
        return $subcategory;
    }
    public function getParentCategory($categoryid)
    {
        $sql = "SELECT parent_id FROM " . DB_PREFIX . "category WHERE category_id='" . (int) $categoryid . "'";
        $query = mysqli_query($this->con, $sql);
        $row = mysqli_fetch_assoc($query);
        return $row['parent_id'];
    }
    public function getProductCount($catid = '')
    {
        $categoryList = ($catid != '') ? $this->getCategoryList($catid) : array();
        $sql = "SELECT status FROM " . DB_PREFIX . "product WHERE status='1' and is_variant='0'";
        if (!empty($categoryList)) {
            $sql .= "  AND product_id IN(SELECT p2c.product_id FROM " . DB_PREFIX . "product_to_category p2c WHERE p2c.category_id IN (" . implode(',', $categoryList) . "))";
        }
        $result = mysqli_query($this->con, $sql);
        $products_count = mysqli_num_rows($result);
        return $products_count;
    }
    public function getAllProducts($data, $catid = '')
    {
        $products = array();
        $query = mysqli_query($this->con, "SELECT option_id FROM `" . DB_PREFIX . "option_description` WHERE name='xe_is_design'");
        $row = mysqli_fetch_array($query, MYSQL_ASSOC);
        $option_id = $row['option_id'];

        //$categoryList = ($catid!='')?$this->getCategoryList($catid):array();
        $sql = "SELECT p.product_id,p.image as thumbnail,pd.name, pd.description, p.price FROM " . DB_PREFIX . "product p INNER JOIN " . DB_PREFIX . "product_description pd ON(p.product_id = pd.product_id) WHERE p.product_id != '' and p.is_variant='0'";

        $sql .= "  AND p.product_id IN(SELECT po.product_id FROM " . DB_PREFIX . "product_option po WHERE po.option_id = '" . (int) $option_id . "' and po.value=1)";
        if (!isset($data->preDecorated) || !$data->preDecorated) {
            $refid_query = mysqli_query($this->con, "SELECT option_id FROM `" . DB_PREFIX . "option_description` WHERE name='refid'");
            $refid_query_row = mysqli_fetch_array($refid_query, MYSQL_ASSOC);
            $refid_option_id = $refid_query_row['option_id'];
            $sql .= "  AND p.product_id NOT IN(SELECT po.product_id FROM " . DB_PREFIX . "product_option po WHERE po.option_id = '" . (int) $refid_option_id . "' and po.value!='')";
        }

        $limit = '';
        $start = (int) $data->range * ((int) $data->offset - 1);
        if (isset($data->offset) && isset($data->range)) {
            $limit = ' limit ' . (int) $start . ' ,' . (int) $data->range;
        }

        if ((isset($data->categoryid) && $data->categoryid != "") && (isset($data->subcategoryid) && $data->subcategoryid != "")) {
            $cat_array = array($data->categoryid, $data->subcategoryid);
            $sql .= " AND p.product_id IN(SELECT p2c.product_id FROM " . DB_PREFIX . "product_to_category p2c WHERE p2c.category_id IN (" . implode(',', $cat_array) . "))";
        }
        if (isset($data->categoryid) && $data->categoryid != "") {
            $cat_ids = $this->getCategoryList($data->categoryid);
            $sql .= " AND p.product_id IN(SELECT p2c.product_id FROM " . DB_PREFIX . "product_to_category p2c WHERE p2c.category_id IN (" . implode(',', $cat_ids) . "))";
        }
        if (isset($data->searchstring) && $data->searchstring != "") {
            $sql .= " AND pd.name like '%" . $data->searchstring . "%' ";
        }
        /* if(!empty($categoryList))
        {
        $sql .= " AND p.product_id IN(SELECT p2c.product_id FROM ".DB_PREFIX."product_to_category p2c WHERE p2c.category_id IN (".implode(',', $categoryList)."))";
        } */
        $sql .= " order by product_id DESC ";
        $sql .= $limit;
        $query = mysqli_query($this->con, $sql);
        $count = mysqli_num_rows($query);
        $i = 0;
        while ($row = mysqli_fetch_array($query, MYSQL_ASSOC)) {
            $products[$i]['id'] = $row['product_id'];
            $products[$i]['name'] = $row['name'];
            $products[$i]['price'] = $row['price'];
            $products[$i]['description'] = strip_tags(htmlspecialchars_decode($row['description']));
            $products[$i]['category'] = $this->getCategoriesByProduct($row['product_id']);
            $thumb = $this->resize($row['thumbnail'], 140, 140);
            $products[$i]['thumbnail'] = HTTP_SERVER . 'image/' . $thumb;
            $i++;
        }
        return array('product' => $products, 'count' => $count);
    }
    public function getVariants($data)
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "product p WHERE p.product_id = '" . (int) $data->conf_pid . "' and p.is_variant='0'";
        if (mysqli_num_rows(mysqli_query($this->con, $sql)) > 0) {
            $config_id = (int) $data->conf_pid;
        } else {
            $sql = "SELECT variant_id FROM " . DB_PREFIX . "product_variant WHERE product_id = '" . (int) $data->conf_pid . "'";
            $query = mysqli_query($this->con, $sql);
            $row = mysqli_fetch_array($query, MYSQL_ASSOC);
            $config_id = $row['variant_id'];
        }

        $sql = "SELECT * FROM " . DB_PREFIX . "product_variant pv LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = pv.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (pd.product_id = p.product_id)WHERE pv.variant_id= '" . (int) $config_id . "'";
        $sql1 = "SELECT * FROM " . DB_PREFIX . "product_variant pv LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = pv.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (pd.product_id = p.product_id)WHERE pv.variant_id= '" . (int) $config_id . "'";

        $limit = '';
        $count = 0;
        $sql1 = $sql;
        $limit = '';
        $start = (int) $data->range * ((int) $data->offset - 1);
        if (isset($data->offset) && isset($data->range)) {
            $limit = ' limit ' . (int) $start . ' ,' . (int) $data->range;
        }
/*             if(isset($data->start) && $data->start!='undefined'){
$limit = ' limit '.(int)$data->start;
}
if(isset($data->start) && $data->start!='undefined' && $data->start<$data->range)
{
$limit = ' limit 0';
}
if(isset($data->range) && $limit!=''){
if($data->range == 'undefined')
$range = 100;
else
$range = (int)$data->range;
$limit .= ', '.$range;
} */
        $sql .= $limit;
        $query = mysqli_query($this->con, $sql);
        $query1 = mysqli_query($this->con, $sql1);
        $i = 0;
        if (mysqli_num_rows($query) > 0) {
            while ($row = mysqli_fetch_array($query, MYSQL_ASSOC)) {
                $color = $this->getProductOptionsModified($row['product_id'], 'color');
                $variants[$i]['id'] = $row['product_id'];
                $variants[$i]['name'] = $row['name'];
                $variants[$i]['xeColor'] = isset($color['xe_color']) ? $color['xe_color'] : '';
                $variants[$i]['price'] = $row['price'];
                $variants[$i]['colorUrl'] = isset($color['option_value_id']) ? $color['option_value_id'] . ".png" : '';
                $thumb = $this->resize($row['image'], 140, 140);
                $variants[$i]['thumbnail'] = HTTP_SERVER . 'image/' . $thumb;
                $variants[$i]['xe_color_id'] = $color['option_value_id'];
                $i++;
            }
            $count = mysqli_num_rows($query1);
        } else if ($start == 0) {
            $sql = "SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (pd.product_id = p.product_id)WHERE p.product_id= '" . (int) $data->conf_pid . "'";
            $query = mysqli_query($this->con, $sql);
            $row = mysqli_fetch_array($query, MYSQL_ASSOC);
            $sizes = array();
            $color = $this->getProductOptionsModified($row['product_id'], 'color');
            $variants[0]['id'] = $row['product_id'];
            $variants[0]['name'] = $row['name'];
            $variants[0]['xeColor'] = isset($color['xe_color']) ? $color['xe_color'] : '';
            $variants[0]['price'] = $row['price'];
            $variants[0]['colorUrl'] = isset($color['option_value_id']) ? $color['option_value_id'] . ".png" : '';
            $thumb = $this->resize($row['image'], 140, 140);
            $variants[0]['thumbnail'] = HTTP_SERVER . 'image/' . $thumb;
            $variants[$i]['xe_color_id'] = $color['option_value_id'];
        } else {
            $variants = array();
        }
        return array('variants' => $variants, 'count' => $count);

    }
    public function getProductById($data)
    {
        $product_info = array();
        $sql = "SELECT * FROM " . DB_PREFIX . "product p WHERE p.product_id = '" . (int) $data->id . "' and p.is_variant='0'";
        if (mysqli_num_rows(mysqli_query($this->con, $sql)) > 0) {
            $config_id = (int) $data->id;
        } else {
            $sql = "SELECT variant_id FROM " . DB_PREFIX . "product_variant WHERE product_id = '" . (int) $data->id . "'";
            $query = mysqli_query($this->con, $sql);
            $row = mysqli_fetch_array($query, MYSQL_ASSOC);
            $config_id = $row['variant_id'];
        }
        $variant_id = ($config_id != (int) $data->id) ? (int) $data->id : '';
        $sql = "SELECT p.product_id,p.price,p.quantity,p.image,pd.name,pd.description FROM " . DB_PREFIX . "product p INNER JOIN " . DB_PREFIX . "product_description pd ON(p.product_id = pd.product_id) WHERE p.product_id='" . (int) $config_id . "'";
        $query = mysqli_query($this->con, $sql);
        if ($row = mysqli_fetch_array($query, MYSQL_ASSOC)) {
            $variants = $this->getProductVariants($row['product_id'], $variant_id);
            $product_info['pid'] = $row['product_id'];
            $product_info['pidtype'] = 'configurable';
            $product_info['pname'] = $row['name'];
            $product_info['shortdescription'] = strip_tags(html_entity_decode($row['description']));
            $product_info['category'] = $this->getCategoriesByProduct($row['product_id']);
            $product_info['pvid'] = $variants['id'];
            $product_info['pvname'] = $variants['name'];
            $product_info['xecolor'] = $variants['xe_color'];
            $product_info['xe_color_id'] = $variants['xe_color_id'];
            $product_info['xesize'] = $variants['size'];
            $product_info['xe_size_id'] = $variants['xe_size_id'];
            $product_info['quanntity'] = $variants['quantity'];
            $product_info['price'] = $variants['price'];
            $product_info['taxrate'] = $variants['tax'];
            $product_info['attributes'] = $variants['attributes'];
            $product_info['thumbsides'] = $variants['thumbsides'];
            $product_info['sides'] = $variants['sides'];
            $product_info['isPreDecorated'] = ($variants['refid'] != '') ? true : false;
        }
        return $product_info;
    }
    public function getSizeAndQuantity($data)
    {
        if ((int) $data->productId != (int) $data->simplePdctId) {
            $sql = "SELECT * FROM " . DB_PREFIX . "product_variant pv LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = pv.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (pd.product_id = p.product_id)WHERE pv.variant_id= '" . (int) $data->productId . "' AND pv.product_id= '" . (int) $data->simplePdctId . "'";
        } else {
            $sql = "SELECT * FROM " . DB_PREFIX . "product WHERE product_id='" . (int) $data->productId . "'";
        }
        $query = mysqli_query($this->con, $sql);
        if (mysqli_num_rows(mysqli_query($this->con, $sql)) > 0) {
            while ($row = mysqli_fetch_array($query, MYSQL_ASSOC)) {
                $sizes = $this->getProductOptionsModified($row['product_id'], 'size');
                $color = $this->getProductOptionsModified($row['product_id'], 'color');
                $attributes = $this->getProductOptionsModified($row['product_id'], 'all');
                $attributes['xe_color'] = isset($color['xe_color']) ? $color['xe_color'] : '';
                $attributes['xe_color_id'] = isset($color['option_value_id']) ? $color['option_value_id'] : '';
                if (!empty($sizes)) {
                    $j = 0;
                    foreach ($sizes as $value) {
                        $attributes['xe_size'] = $value['name'];
                        $attributes['xe_size_id'] = $value['option_value_id'];
                        $price = ($value['price_prefix'] == '+') ? $row['price'] + $value['price'] : $row['price'] - $value['price'];
                        $quantities[$j]['simpleProductId'] = $row['product_id'];
                        $quantities[$j]['xe_size_id'] = $value['option_value_id'];
                        $quantities[$j]['xe_color_id'] = isset($color['option_value_id']) ? $color['option_value_id'] : '';
                        $quantities[$j]['xe_size'] = $value['name'];
                        $quantities[$j]['xe_color'] = isset($color['xe_color']) ? $color['xe_color'] : '';
                        $quantities[$j]['quantity'] = (int) $this->getProductQuantity($row['product_id'], $value['option_value_id']);
                        $quantities[$j]['price'] = $price;
                        $quantities[$j]['minQuantity'] = (int) $row['minimum'];
                        $quantities[$j]['attributes'] = $attributes;
                        $j++;
                    }
                } else {
                    $attributes['xe_size'] = '';
                    $attributes['xe_size_id'] = '';
                    $quantities['simpleProductId'] = $row['product_id'];
                    $quantities['xe_size_id'] = '';
                    $quantities['xe_color_id'] = isset($color['option_value_id']) ? $color['option_value_id'] : '';
                    $quantities['xe_size'] = '';
                    $quantities['xe_color'] = isset($color['xe_color']) ? $color['xe_color'] : '';
                    $quantities['quantity'] = (int) $this->getProductQuantity($row['product_id'], $value['option_value_id']);
                    $quantities['price'] = $row['price'];
                    $quantities['minQuantity'] = (int) $row['minimum'];
                    $quantities['attributes'] = $attributes;

                }
            }
        } else {
            $sql = "SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (pd.product_id = p.product_id)WHERE p.product_id= '" . (int) $product_id . "'";
            $query = mysqli_query($this->con, $sql);
            $row = mysqli_fetch_array($query, MYSQL_ASSOC);
            $sizes = $this->getProductOptionsModified($row['product_id'], 'size');
            $color = $this->getProductOptionsModified($row['product_id'], 'color');
            $attributes = $this->getProductOptionsModified($row['product_id'], 'all');
            $attributes['xe_color'] = isset($color['xe_color']) ? $color['xe_color'] : '';
            $attributes['xe_color_id'] = isset($color['option_value_id']) ? $color['option_value_id'] : '';
            if (!empty($sizes)) {
                $j = 0;
                foreach ($sizes as $value) {
                    $attributes['xe_size'] = $value['name'];
                    $attributes['xe_size_id'] = $value['option_value_id'];
                    $price = ($value['price_prefix'] == '+') ? $row['price'] + $value['price'] : $row['price'] - $value['price'];
                    $quantities[$j]['simpleProductId'] = $row['product_id'];
                    $quantities[$j]['xe_size'] = $value['name'];
                    $quantities[$j]['xe_size_id'] = $value['option_value_id'];
                    $quantities[$j]['xe_color'] = isset($color['xe_color']) ? $color['xe_color'] : '';
                    $quantities[$j]['xe_color_id'] = isset($color['option_value_id']) ? $color['option_value_id'] : '';
                    $quantities[$j]['quantity'] = $this->getProductQuantity($row['product_id'], $value['option_value_id']);
                    $quantities[$j]['price'] = $price;
                    $quantities[$j]['minQuantity'] = (int) $row['minimum'];
                    $quantities[$j]['attributes'] = $attributes;
                }
            } else {
                $attributes['xe_size'] = '';
                $attributes['xe_size_id'] = '';
                $quantities['simpleProductId'] = $row['product_id'];
                $quantities['xe_size'] = '';
                $quantities['xe_size_id'] = '';
                $quantities['xe_color'] = isset($color['xe_color']) ? $color['xe_color'] : '';
                $quantities['xe_color_id'] = isset($color['option_value_id']) ? $color['option_value_id'] : '';
                $quantities['quantity'] = $this->getProductQuantity($row['product_id'], $value['option_value_id']);
                $quantities['price'] = $price;
                $quantities['minQuantity'] = (int) $row['minimum'];
                $quantities['attributes'] = $attributes;

            }
        }
        return array('quantities' => $quantities);
    }

    public function getSizeVariants($data)
    {
        if ((int) $data->productId != (int) $data->simplePdctId) {
            $sql = "SELECT * FROM " . DB_PREFIX . "product_variant pv LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = pv.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (pd.product_id = p.product_id)WHERE pv.variant_id= '" . (int) $data->productId . "'";
        } else {
            $sql = "SELECT * FROM " . DB_PREFIX . "product WHERE product_id='" . (int) $data->productId . "'";
        }
        $query = mysqli_query($this->con, $sql);
        if (mysqli_num_rows(mysqli_query($this->con, $sql)) > 0) {
            $size_array = array();
            $j = 0;
            while ($row = mysqli_fetch_array($query, MYSQL_ASSOC)) {
                $sizes = $this->getProductOptionsModified($row['product_id'], 'size');
                $color = $this->getProductOptionsModified($row['product_id'], 'color');
                if (!empty($sizes)) {
                    foreach ($sizes as $value) {
                        if (empty($size_array) || !in_array($value['name'], $size_array)) {
                            $price = ($value['price_prefix'] == '+') ? $row['price'] + $value['price'] : $row['price'] - $value['price'];
                            $quantities[$j]['simpleProductId'] = $row['product_id'];
                            $quantities[$j]['xe_size_id'] = $value['option_value_id'];
                            $quantities[$j]['xe_color_id'] = isset($color['option_value_id']) ? $color['option_value_id'] : '';
                            $quantities[$j]['xe_size'] = $value['name'];
                            $quantities[$j]['xe_color'] = isset($color['xe_color']) ? $color['xe_color'] : '';
                            $quantities[$j]['quantity'] = (int) $this->getProductQuantity($row['product_id'], $value['option_value_id']);
                            $quantities[$j]['price'] = $price;
                            $size_array[] = $value['name'];
                            $j++;
                        }
                    }
                } else {
                    $quantities['simpleProductId'] = $row['product_id'];
                    $quantities['xe_size_id'] = '';
                    $quantities['xe_color_id'] = '';
                    $quantities['xe_size'] = '';
                    $quantities['xe_color'] = isset($color['xe_color']) ? $color['xe_color'] : '';
                    $quantities['quantity'] = (int) $this->getProductQuantity($row['product_id'], $value['option_value_id']);
                    $quantities['price'] = $row['price'];

                }
            }
        } else {
            $sql = "SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (pd.product_id = p.product_id)WHERE p.product_id= '" . (int) $product_id . "'";
            $query = mysqli_query($this->con, $sql);
            $row = mysqli_fetch_array($query, MYSQL_ASSOC);
            $sizes = $this->getProductOptionsModified($row['product_id'], 'size');
            $color = $this->getProductOptionsModified($row['product_id'], 'color');
            if (!empty($sizes)) {
                $j = 0;
                foreach ($sizes as $value) {
                    $price = ($value['price_prefix'] == '+') ? $row['price'] + $value['price'] : $row['price'] - $value['price'];
                    $quantities[$j]['simpleProductId'] = $row['product_id'];
                    $quantities[$j]['xe_size'] = $value['name'];
                    $quantities[$j]['xe_color'] = isset($color['xe_color']) ? $color['xe_color'] : '';
                    $quantities[$j]['quantity'] = $this->getProductQuantity($row['product_id'], $value['option_value_id']);
                    $quantities[$j]['price'] = $price;
                }
            } else {
                $quantities['simpleProductId'] = $row['product_id'];
                $quantities['xe_size'] = '';
                $quantities['xe_color'] = isset($color['xe_color']) ? $color['xe_color'] : '';
                $quantities['quantity'] = $this->getProductQuantity($row['product_id'], $value['option_value_id']);
                $quantities['price'] = $price;

            }
        }
        return array('quantities' => $quantities);
    }

    public function getCategoriesByProduct($product_id)
    {
        $categories = array();
        $query = mysqli_query($this->con, "SELECT category_id FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int) $product_id . "'");
        while ($row = mysqli_fetch_array($query, MYSQL_ASSOC)) {
            $categories[] = $row['category_id'];
        }
        return $categories;
    }
    public function getProductOptionsModified($product_id, $type)
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int) $product_id . "'  ORDER BY o.sort_order";
        $query = mysqli_query($this->con, $sql);
        $color = array();
        $size = array();
        $refid = array();
        $attributeArray = array();
        while ($row = mysqli_fetch_array($query, MYSQL_ASSOC)) {
            if ($row['type'] == 'select' || $row['type'] == 'radio' || $row['type'] == 'checkbox' || $row['type'] == 'image') {
                $query2 = mysqli_query($this->con, "SELECT pov.option_value_id, pov.price, pov.price_prefix, ovd.name FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_id = '" . (int) $product_id . "' AND pov.product_option_id = '" . (int) $row['product_option_id'] . "'  ORDER BY ov.sort_order");
                $temp_color = array();
                $i = 0;
                while ($row2 = mysqli_fetch_array($query2, MYSQL_ASSOC)) {
                    if (strtolower($row['name']) == 'xe_color') {
                        $color[$row['name']] = $row2['name'];
                        $color['option_value_id'] = $row2['option_value_id'];
                    } elseif (strtolower($row['name']) == 'xe_size') {
                        $size[$i]['name'] = $row2['name'];
                        $size[$i]['price'] = $row2['price'];
                        $size[$i]['price_prefix'] = $row2['price_prefix'];
                        $size[$i]['option_value_id'] = $row2['option_value_id'];
                        $i++;
                    } else {
                        $attributeArray[$row['name']] = $row2['name'];
                        $attributeArray[$row['name'] . '_id'] = $row2['option_value_id'];
                    }
                }
            } elseif ($row['type'] == 'text') {
                if (strtolower($row['name']) == 'xe_color') {
                    $color[$row['name']] = $row['value'];
                } elseif (strtolower($row['name']) == 'refid') {
                    $refid[$row['name']] = $row['value'];
                } elseif (strtolower($row['name'] != 'xe_is_design')) {
                    $attributeArray[$row['name']] = $row['value'];
                    $attributeArray[$row['name'] . '_id'] = $row['option_id'];
                }
            }
        }
        if ($type == 'color') {
            return $color;
        } elseif ($type == 'size') {
            return $size;
        } elseif ($type == 'refid') {
            return $refid;
        } elseif ($type == 'all') {
            return $attributeArray;
        }
    }
    public function getProductAttributes($product_id)
    {
        $product_attribute_group_data = array();

        $product_attribute_group_query = array();

        $query = mysqli_query($this->con, "SELECT ag.attribute_group_id, agd.name FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN " . DB_PREFIX . "attribute_group ag ON (a.attribute_group_id = ag.attribute_group_id) LEFT JOIN " . DB_PREFIX . "attribute_group_description agd ON (ag.attribute_group_id = agd.attribute_group_id) WHERE pa.product_id = '" . (int) $product_id . "'  GROUP BY ag.attribute_group_id ORDER BY ag.sort_order, agd.name");

        while ($row = mysqli_fetch_array($query, MYSQL_ASSOC)) {
            $product_attribute_group_query[] = $row;
        }

        foreach ($product_attribute_group_query as $product_attribute_group) {
            $product_attribute_data = array();

            $product_attribute_query = array();

            $query = mysqli_query($this->con, "SELECT a.attribute_id, ad.name, pa.text FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id) WHERE pa.product_id = '" . (int) $product_id . "' AND a.attribute_group_id = '" . (int) $product_attribute_group['attribute_group_id'] . "'  ORDER BY a.sort_order, ad.name");

            while ($row = mysqli_fetch_array($query, MYSQL_ASSOC)) {
                $product_attribute_query[] = $row;
            }

            foreach ($product_attribute_query as $product_attribute) {
                $product_attribute_group_data[$product_attribute_group['name']] = array(
                    $product_attribute['name'] => $product_attribute['text'],
                );
            }

        }
        return $product_attribute_group_data;
    }
    public function getProductOptions($product_id)
    {

        $product_option_data = array();

        $sql = "SELECT * FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int) $product_id . "'  ORDER BY o.sort_order";

        $query = mysqli_query($this->con, $sql);

        while ($row = mysqli_fetch_array($query, MYSQL_ASSOC)) {

            if ($row['type'] == 'select' || $row['type'] == 'radio' || $row['type'] == 'checkbox' || $row['type'] == 'image') {

                $query2 = mysqli_query($this->con, "SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_id = '" . (int) $product_id . "' AND pov.product_option_id = '" . (int) $row['product_option_id'] . "'  ORDER BY ov.sort_order");

                while ($row2 = mysqli_fetch_array($query2, MYSQL_ASSOC)) {
                    $product_option_data[$row['name']][] = array($row2['name'] => $row2['option_value_id']);
                }
            }
        }
        return $product_option_data;
    }

    public function add_to_cart($data)
    {
        require_once DIR_SYSTEM . 'library/session.php';
        $this->log(DIR_SYSTEM . 'library/session.php', true, 'logctest.log');
        $session = new Session();
        /* $product_data = (array)$data;
        foreach($product_data as $data)
        {
        $data = (object)$data; */
        if (isset($data->product_id)) {
            $product = array();
            $product['product_id'] = $data->product_id;
            if (isset($data->option)) {
                $product['option'] = $data->option;
            }
            $key = base64_encode(serialize($product));
            $_SESSION['cart'][$key] = $data->quantity;
        }
        //}
    }
    public function isProductExists($product_id)
    {
        $sql = "select product_id from " . DB_PREFIX . "product WHERE product_id ='" . (int) $product_id . "'";

        $result = mysqli_query($this->con, $sql);

        return mysqli_num_rows($result);

    }

    public function log($text, $append = true, $fileName = '')
    {
        $file = 'log_datalayer.log';
        if ($fileName) {
            $file = $fileName;
        }

        $file = dirname(__FILE__) . '/../' . $file;

        // Write the contents to the file,
        // using the FILE_APPEND flag to append the content to the end of the file
        // and the LOCK_EX flag to prevent anyone else writing to the file at the same time
        //file_put_contents($file, $text, FILE_APPEND | LOCK_EX);

        if ($append) {
            file_put_contents($file, $text . PHP_EOL, FILE_APPEND | LOCK_EX);
        } else {
            file_put_contents($file, $text);
        }

    }
    public function getProductRelatedOptions($product_id)
    {
        $options = array();
        $sql = "SELECT po.product_option_id, od.name FROM " . DB_PREFIX . "product_option po LEFT JOIN " . DB_PREFIX . "option_description od ON (po.option_id = od.option_id) WHERE po.product_id = '" . (int) $product_id . "'";
        $query = mysqli_query($this->con, $sql);
        $i = 0;
        while ($row = mysqli_fetch_array($query, MYSQL_ASSOC)) {
            $options[$i]['id'] = $row['product_option_id'];
            $options[$i]['name'] = $row['name'];
            $i++;
        }
        return $options;
    }
    public function getProductVariants($product_id, $variant_id)
    {
        $variant = array();
        $sql = "SELECT * FROM " . DB_PREFIX . "product_variant pv LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = pv.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (pd.product_id = p.product_id)WHERE pv.variant_id= '" . (int) $product_id . "'";
        if ($variant_id != '') {
            $sql .= " AND pv.product_id='" . (int) $variant_id . "'";
        }

        $sql .= " LIMIT 1";
        $query = mysqli_query($this->con, $sql);
        $res = mysqli_query($this->con, "SELECT * FROM " . DB_PREFIX . "setting s WHERE s.key='config_tax'");
        $exe = mysqli_fetch_array($res, MYSQL_ASSOC);
        if (mysqli_num_rows(mysqli_query($this->con, $sql)) > 0) {
            while ($row = mysqli_fetch_array($query, MYSQL_ASSOC)) {
                if ($row['tax_class_id'] != 0 && $exe['value'] == 1) {
                    $que = mysqli_query($this->con, "SELECT * FROM " . DB_PREFIX . "tax_rate trt LEFT JOIN " . DB_PREFIX . "tax_rule trl ON (trt.tax_rate_id = trl.tax_rate_id) WHERE trl.tax_rate_id='" . $row['tax_class_id'] . "'");
                    $tax_row = mysqli_fetch_array($que, MYSQL_ASSOC);
                    $tax_price = 0;
                    foreach ($tax_row as $tax_rate) {
                        if ($tax_rate['type'] == 'F') {
                            //$tax_price += $tax_rate['rate'];
                            $tax_price = $tax_price;
                        } elseif ($tax_rate['type'] == 'P') {
                            $tax_price += $tax_rate['rate']; //($row['price'] / 100 * $tax_rate['rate']);
                        }
                    }
                } else {
                    $tax_price = 0;
                }
                $attributes = $this->getProductOptionsModified($row['product_id'], 'all');
                $color = $this->getProductOptionsModified($row['product_id'], 'color');
                $attributes['xe_color'] = isset($color['xe_color']) ? $color['xe_color'] : '';
                $attributes['xe_color_id'] = isset($color['option_value_id']) ? $color['option_value_id'] : '';
                $variants['id'] = $row['product_id'];
                $variants['name'] = $row['name'];
                $variants['xe_color'] = isset($color['xe_color']) ? $color['xe_color'] : '';
                $variants['xe_color_id'] = isset($color['option_value_id']) ? $color['option_value_id'] : '';
                $variants['quantity'] = $row['quantity'];
                $variants['price'] = $row['price'];
                $variants['tax'] = $tax_price;
                $refid = $this->getProductOptionsModified($row['product_id'], 'refid');
                $variants['refid'] = isset($refid['refid']) ? $refid['refid'] : '';
                $sizes = $this->getProductOptionsModified($row['product_id'], 'size');
                if (!empty($sizes)) {
                    $i = 0;
                    foreach ($sizes as $value) {
                        $variants['size'] = $value['name'];
                        $variants['xe_size_id'] = $value['option_value_id'];
                        $attributes['xe_size'] = $value['name'];
                        $attributes['xe_size_id'] = $value['option_value_id'];
                        $i++;
                        if ($i == 1) {
                            break;
                        }

                    }
                } else {
                    $variants['size'] = '';

                }
                $images = $this->getProductImages($row['product_id']);
                $thumb = $this->getProductThumbImages($row['product_id']);
                $variants['thumbsides'] = $thumb;
                $variants['sides'] = $images;
                $variants['attributes'] = $attributes;
            }
        } else {
            $sql = "SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (pd.product_id = p.product_id)WHERE p.product_id= '" . (int) $product_id . "'";
            $query = mysqli_query($this->con, $sql);
            $row = mysqli_fetch_array($query, MYSQL_ASSOC);
            if ($row['tax_class_id'] != 0 && $exe['value'] == 1) {
                $que = mysqli_query($this->con, "SELECT * FROM " . DB_PREFIX . "tax_rate trt LEFT JOIN " . DB_PREFIX . "tax_rule trl ON (trt.tax_rate_id = trl.tax_rate_id) WHERE trl.tax_rate_id='" . $row['tax_class_id'] . "'");
                $tax_row = mysqli_fetch_array($que, MYSQL_ASSOC);
                $tax_price = 0;
                foreach ($tax_row as $tax_rate) {
                    if ($tax_rate['type'] == 'F') {
                        $tax_price += $tax_rate['rate'];
                    } elseif ($tax_rate['type'] == 'P') {
                        $tax_price += ($row['price'] / 100 * $tax_rate['rate']);
                    }
                }
            } else {
                $tax_price = 0;
            }
            $attributes = $this->getProductOptionsModified($row['product_id'], 'all');
            $color = $this->getProductOptionsModified($row['product_id'], 'color');
            $attributes['xe_color'] = isset($color['xe_color']) ? $color['xe_color'] : '';
            $attributes['xe_color_id'] = isset($color['option_value_id']) ? $color['option_value_id'] : '';
            $variants['id'] = $row['product_id'];
            $variants['name'] = $row['name'];
            $variants['xe_color'] = isset($color['xe_color']) ? $color['xe_color'] : '';
            $variants['xe_color_id'] = isset($color['option_value_id']) ? $color['option_value_id'] : '';
            $variants['quantity'] = $row['quantity'];
            $variants['price'] = $row['price'];
            $variants['tax'] = $tax_price;
            $refid = $this->getProductOptionsModified($row['product_id'], 'refid');
            $variants['refid'] = isset($refid['refid']) ? $refid['refid'] : '';
            $sizes = $this->getProductOptionsModified($row['product_id'], 'size');
            if (!empty($sizes)) {
                $i = 0;
                foreach ($sizes as $value) {
                    $variants['size'] = $value['name'];
                    $variants['xe_size_id'] = $value['option_value_id'];
                    $attributes['xe_size'] = $value['name'];
                    $attributes['xe_size_id'] = $value['option_value_id'];
                    $i++;
                    if ($i == 1) {
                        break;
                    }

                }
            } else {
                $variants['size'] = '';

            }
            $images = $this->getProductImages($row['product_id']);
            $thumb = $this->getProductThumbImages($row['product_id']);
            $variants['thumbsides'] = $thumb;
            $variants['sides'] = $images;
            $variants['attributes'] = $attributes;
        }
        return $variants;
    }
    public function getProductSize($product_id)
    {
        $size = array();
        $sql = "SELECT * FROM " . DB_PREFIX . "product_variant pv LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = pv.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (pd.product_id = p.product_id)WHERE pv.variant_id= '" . (int) $product_id . "'";

        $query = mysqli_query($this->con, $sql);
        $i = 0;
        if (mysqli_num_rows(mysqli_query($this->con, $sql)) > 0) {
            while ($row = mysqli_fetch_array($query, MYSQL_ASSOC)) {
                $sizes = $this->getProductOptionsModified($row['product_id'], 'size');
                $size[] = $sizes;
            }
        } else {
            $sql = "SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (pd.product_id = p.product_id)WHERE p.product_id= '" . (int) $product_id . "'";
            $query = mysqli_query($this->con, $sql);
            $row = mysqli_fetch_array($query, MYSQL_ASSOC);
            $sizes = $this->getProductOptionsModified($row['product_id'], 'size');
            $size[] = $sizes;
        }
        return $size;
    }
    public function getProductColors($product_id)
    {
        $colors = array();
        $sql = "SELECT * FROM " . DB_PREFIX . "product_variant pv LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = pv.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (pd.product_id = p.product_id)WHERE pv.variant_id= '" . (int) $product_id . "'";

        $query = mysqli_query($this->con, $sql);
        $i = 0;
        if (mysqli_num_rows(mysqli_query($this->con, $sql)) > 0) {
            while ($row = mysqli_fetch_array($query, MYSQL_ASSOC)) {
                $color = $this->getProductOptionsModified($row['product_id'], 'color');
                $colors[] = $color;
            }
        } else {
            $sql = "SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (pd.product_id = p.product_id)WHERE p.product_id= '" . (int) $product_id . "'";
            $query = mysqli_query($this->con, $sql);
            $row = mysqli_fetch_array($query, MYSQL_ASSOC);
            $sizes = array();
            $color = $this->getProductOptionsModified($row['product_id'], 'color');
            $colors[] = $color;

        }
        return $colors;
    }
    public function getProductImages($product_id)
    {
        $images = array();
        $additional_images = mysqli_query($this->con, "SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id='" . (int) $product_id . "' ORDER BY sort_order ASC");
        //$z =2;
        $z = 1;
        while ($row = mysqli_fetch_array($additional_images)) {
            $images[] = HTTP_SERVER . 'image/' . $row['image'];
            $z++;
        }
        return $images;
    }
    public function getProductQuantity($product_id, $option_value_id)
    {
        $query = mysqli_query($this->con, "SELECT * FROM " . DB_PREFIX . "product_option_value WHERE product_id='" . (int) $product_id . "' AND option_value_id='" . (int) $option_value_id . "'");
        $row = mysqli_fetch_array($query, MYSQL_ASSOC);
        return $row['quantity'];
    }
    public function getProductOptionValue($option, $id)
    {
        $query = mysqli_query($this->con, "SELECT p.product_option_value_id FROM " . DB_PREFIX . "option_value_description o, " . DB_PREFIX . "product_option_value p  WHERE p.option_value_id = o.option_value_id AND o.name='" . $option . "' AND p.product_option_id='" . $id . "'");

        $queryForTextOption = mysqli_query($this->con, "SELECT value FROM " . DB_PREFIX . "product_option WHERE product_option_id='" . $id . "'");
        if (mysqli_num_rows($query) > 0) {
            $row = mysqli_fetch_array($query, MYSQL_ASSOC);
            $value_id = $row['product_option_value_id'];
        } else {
            $row = mysqli_fetch_array($queryForTextOption, MYSQL_ASSOC);
            $value_id = $row['value'];
        }

        return $value_id;
    }
    public function getProductThumbImages($product_id)
    {
        $images = array();
        $additional_images = mysqli_query($this->con, "SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id='" . (int) $product_id . "' ORDER BY sort_order ASC");
        //$z =2;
        $z = 1;
        while ($row = mysqli_fetch_array($additional_images)) {
            $images[] = HTTP_SERVER . 'image/' . $this->resize($row['image'], 140, 140);
            $z++;
        }
        return $images;
    }
    public function getOptions($data = array())
    {
        $options = array();
        $i = 0;
        if ($data['filter_name'] == 'xe_color' && isset($data['oldConfId']) && $data['oldConfId'] != 0) {
            $productvariant = $this->getProductInfo($data['oldConfId']);
            $query = mysqli_query($this->con, "SELECT language_id FROM `" . DB_PREFIX . "language` WHERE status=1");
            $row = mysqli_fetch_array($query, MYSQL_ASSOC);
            foreach ($productvariant as $variant) {
                $query = mysqli_query($this->con, "SELECT name FROM `" . DB_PREFIX . "option_value_description` WHERE language_id='" . $row['language_id'] . "' and option_value_id='" . $variant['color_id'] . "'");
                $color = mysqli_fetch_array($query, MYSQL_ASSOC);

                $options[$i]['value'] = $variant['color_id'];
                $options[$i]['label'] = $color['name'];
                $options[$i]['swatchImage'] = '';
                $i++;
            }

        } else {
            $query = mysqli_query($this->con, "SELECT language_id FROM `" . DB_PREFIX . "language` WHERE status=1");
            $row = mysqli_fetch_array($query, MYSQL_ASSOC);

            $sql = "SELECT o.option_id FROM `" . DB_PREFIX . "option` o LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE od.language_id = '" . $row['language_id'] . "'";
            //$sql = "SELECT o.option_id FROM `" . DB_PREFIX . "option` o LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE od.language_id = 1"

            if (!empty($data['filter_name'])) {
                $sql .= " AND od.name = '" . $data['filter_name'] . "'";
            }
            $limit = '';
            if (!empty($data['loadCount']) && $data['loadCount'] != 0) {
                $limit = "LIMIT " . $data['lastLoaded'] . "," . $data['loadCount'];
            }

            $row = mysqli_fetch_assoc(mysqli_query($this->con, $sql));
            $option_id = $row['option_id'];

            $sql1 = mysqli_query($this->con, "SELECT * FROM " . DB_PREFIX . "option_value ov LEFT JOIN  " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE ov.option_id='" . $option_id . "' $limit");
            while ($result = mysqli_fetch_array($sql1)) {
                $options[$i]['value'] = $result['option_value_id'];
                $options[$i]['label'] = $result['name'];
                if ($data['filter_name'] == 'xe_color') {
                    $options[$i]['swatchImage'] = '';
                }
                $i++;
            }
        }
        return json_encode($options);
    }
    public function addProduct($data)
    {
        $config_id = 0;
        $productData = json_decode($data);
        $productData = json_decode($productData->data);
        $productData = (array) $productData->productData;
        $config_id = (isset($productData['conf_id']) && $productData['conf_id'] != 0) ? $productData['conf_id'] : $this->addConfigProduct($data, $_FILES);
        $data = $productData;
        $productVariant = array();
        if ($config_id != 0) {
            $pv = $this->getProductInfo($config_id);
            foreach ($data['variants'] as $variants) {
                $variants = (array) $variants;
                $simpleProduct = (array) $variants['simpleProducts'];
                $qty = $simpleProduct[0]->qty;
                $product_price = $simpleProduct[0]->price;
                if (empty($pv) || !in_array($variants['color_id'], $pv)) {
                    $sql = mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product SET model = '',sku = '" . $data['sku'] . "', upc = '', ean = '', jan = '', isbn = '', mpn = '', location = '', quantity = '" . (int) $qty . "', minimum = '1', subtract = '1', stock_status_id = '7', date_available = NOW(), manufacturer_id = '', shipping = '', price = '" . (float) $product_price . "', points = '', weight = '" . (float) $data['weight'] . "', weight_class_id = '1', length = '', width = '', height = '', length_class_id = '1', status = '1', tax_class_id = '0', sort_order = '1', date_added = NOW(), is_variant= '1'");
                    $product_id = mysqli_insert_id($this->con);
                    $query = mysqli_query($this->con, "INSERT INTO `" . DB_PREFIX . "product_variant` SET `product_id` = " . (int) $product_id . ", variant_id = " . (int) $config_id);
                    if (isset($_FILES['configFile']['tmp_name'])) {
                        $filename = str_replace(" ", "_", $_FILES['configFile']['name']);
                        $file = "../../../image/catalog/" . $filename;
                        if (file_exists($file)) {
                            $status = 1;
                        } else {
                            $status = move_uploaded_file($_FILES['configFile']['tmp_name'], $file);
                        }

                        if ($status);
                        {
                            mysqli_query($this->con, "UPDATE " . DB_PREFIX . "product SET image = '" . "catalog/" . $filename . "' WHERE product_id = '" . (int) $product_id . "'");
                        }
                    }
                    $query = mysqli_query($this->con, "SELECT language_id FROM `" . DB_PREFIX . "language` WHERE status=1");
                    $row = mysqli_fetch_array($query, MYSQL_ASSOC);

                    $name = $simpleProduct[0]->product_name;
                    $description = $simpleProduct[0]->description;

                    mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int) $product_id . "', language_id = '" . (int) $row['language_id'] . "', name = '" . $name . "', description = '" . $description . "', tag = '', meta_title = '', meta_description = '', meta_keyword = ''");
                    //Insert Store Product
                    mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int) $product_id . "', store_id = 0");

                    // Insert Size Options
                    if (isset($simpleProduct)) {
                        $query = mysqli_query($this->con, "SELECT option_id FROM `" . DB_PREFIX . "option_description` WHERE name='xe_size'");
                        $row = mysqli_fetch_array($query, MYSQL_ASSOC);
                        $option_id = $row['option_id'];
                        mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int) $product_id . "', option_id = '" . (int) $option_id . "', required = '1'");
                        $product_option_id = mysqli_insert_id($this->con);
                        $product_qty = 0;
                        foreach ($simpleProduct as $product_option) {
                            $product_option = (array) $product_option;

                            if (isset($product_option['sizeId'])) {
                                if ($product_option_id != '') {
                                    $price_prefix = ($product_option['price'] > $data['price']) ? '+' : '-';
                                    $weight_prefix = ($product_option['weight'] > $data['weight']) ? '+' : '-';
                                    if ($product_option['price'] == $data['price']) {
                                        $price = 0;
                                    } else if ($product_option['price'] > $data['price']) {
                                        $price = (int) $product_option['price'] - (int) $data['price'];
                                    } else {
                                        $price = (int) $data['price'] - (int) $product_option['price'];
                                    }

                                    if ($product_option['weight'] == $data['weight']) {
                                        $weight = 0;
                                    } else if ($product_option['weight'] > $data['weight']) {
                                        $weight = (int) $product_option['weight'] - (int) $data['weight'];
                                    } else {
                                        $weight = (int) $data['weight'] - (int) $product_option['weight'];
                                    }

                                    mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int) $product_option_id . "', product_id = '" . (int) $product_id . "', option_id = '" . (int) $option_id . "', option_value_id = '" . (int) $product_option['sizeId'] . "', quantity = '" . (int) $product_option['qty'] . "', subtract = '1', price = '" . (float) $price . "', price_prefix = '" . $price_prefix . "', points = '', points_prefix = '+', weight = '" . (float) $weight . "', weight_prefix = '" . $weight_prefix . "'");

                                    $product_qty += (int) $product_option['qty'];

                                }
                            }
                        }
                    }
                    // Insert Color Option
                    if (isset($variants['color_id'])) {
                        $query = mysqli_query($this->con, "SELECT option_id FROM `" . DB_PREFIX . "option_description` WHERE name='xe_color'");
                        $row = mysqli_fetch_array($query, MYSQL_ASSOC);

                        mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int) $product_id . "', option_id = '" . (int) $row['option_id'] . "', required = '1'");
                        $product_option_id = mysqli_insert_id($this->con);
                        $value = 0;
                        $product_qty = ($data['qty'] != '' && $data['qty'] != 0) ? $data['qty'] : $product_qty;
                        if ($product_option_id != '') {
                            mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int) $product_option_id . "', product_id = '" . (int) $product_id . "', option_id = '" . (int) $row['option_id'] . "', option_value_id = '" . (int) $variants['color_id'] . "', quantity = '" . (int) $product_qty . "', subtract = '1', price = '" . (float) $value . "', price_prefix = '+', points = '', points_prefix = '+', weight = '" . (float) $value . "', weight_prefix = '+'");
                        }

                    }
                    // Insert Refid Option
                    $query = mysqli_query($this->con, "SELECT option_id FROM `" . DB_PREFIX . "option_description` WHERE name='refid'");
                    $row = mysqli_fetch_array($query, MYSQL_ASSOC);

                    mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int) $product_id . "', option_id = '" . (int) $row['option_id'] . "', value = '', required = '0'");

                    // Insert xe_is_design Option
                    $query = mysqli_query($this->con, "SELECT option_id FROM `" . DB_PREFIX . "option_description` WHERE name='xe_is_design'");
                    $row = mysqli_fetch_array($query, MYSQL_ASSOC);

                    mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int) $product_id . "', option_id = '" . (int) $row['option_id'] . "', value = '1', required = '0'");

                    if (isset($data['cat_id'])) {
                        foreach ($data['cat_id'] as $category_id) {
                            mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int) $product_id . "', category_id = '" . (int) $category_id . "'");
                        }
                    }
                    $varintFile = '';
                    //Insert Product Images
                    if (isset($_FILES['simpleFile'])) {
                        $count = 0;
                        foreach ($_FILES['simpleFile']['tmp_name'] as $key => $product_image) {
                            $filename = str_replace(" ", "_", $_FILES['simpleFile']['name'][$key]);
                            $file = "../../../image/catalog/" . $filename;

                            if (file_exists($file)) {
                                $status = 1;
                            } else {
                                $status = move_uploaded_file($product_image, $file);
                            }

                            if ($status);
                            {
                                mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int) $product_id . "', image = 'catalog/" . $filename . "', sort_order = '" . $count . "'");

                                if ($count == 0) {
                                    $varintFile = $filename;
                                }

                            }
                            $count++;
                        }
                    }
                    if (!isset($_FILES['configFile']['tmp_name']) && $varintFile != '') {
                        mysqli_query($this->con, "UPDATE " . DB_PREFIX . "product SET image = '" . "catalog/" . $varintFile . "' WHERE product_id = '" . (int) $product_id . "'");
                    }
                } else {
                    $product_id = $this->getProductId($config_id, $variants['color_id']);
                    // Insert Size Options
                    if (isset($simpleProduct)) {
                        $query = mysqli_query($this->con, "SELECT option_id FROM `" . DB_PREFIX . "option_description` WHERE name='xe_size'");
                        $row = mysqli_fetch_array($query, MYSQL_ASSOC);
                        $query1 = mysqli_query($this->con, "SELECT product_option_id FROM `" . DB_PREFIX . "product_option` WHERE option_id = '" . (int) $row['option_id'] . "' AND product_id = '" . (int) $product_id . "'");
                        if (mysqli_num_rows($query1) > 0) {
                            $row1 = mysqli_fetch_array($query, MYSQL_ASSOC);
                            $product_option_id = $row1['product_option_id'];
                        } else {
                            mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int) $product_id . "', option_id = '" . (int) $row['option_id'] . "', required = '1'");
                            $product_option_id = mysqli_insert_id($this->con);
                        }
                        foreach ($simpleProduct as $product_option) {
                            $product_option = (array) $product_option;
                            if (isset($product_option['sizeId'])) {
                                if ($product_option_id != '') {
                                    $price_prefix = ($product_option['price'] > $data['price']) ? '+' : '-';
                                    $weight_prefix = ($product_option['weight'] > $data['weight']) ? '+' : '-';
                                    if ($product_option['price'] == $data['price']) {
                                        $price = 0;
                                    } else if ($product_option['price'] > $data['price']) {
                                        $price = (int) $product_option['price'] - (int) $data['price'];
                                    } else {
                                        $price = (int) $data['price'] - (int) $product_option['price'];
                                    }

                                    if ($product_option['weight'] == $data['weight']) {
                                        $weight = 0;
                                    } else if ($product_option['weight'] > $data['weight']) {
                                        $weight = (int) $product_option['weight'] - (int) $data['weight'];
                                    } else {
                                        $weight = (int) $data['weight'] - (int) $product_option['weight'];
                                    }

                                    mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int) $product_option_id . "', product_id = '" . (int) $product_id . "', option_id = '" . (int) $row['option_id'] . "', option_value_id = '" . (int) $product_option['sizeId'] . "', quantity = '" . (int) $product_option['qty'] . "', subtract = '1', price = '" . (float) $price . "', price_prefix = '" . $price_prefix . "', points = '', points_prefix = '+', weight = '" . (float) $weight . "', weight_prefix = '" . $weight_prefix . "'");
                                }
                            }
                        }
                    }

                }
            }
            $pvariant = $this->getProductInfo($config_id);
            return array("status" => "success", "conf_id" => $config_id, "variants" => $pvariant);
        } else {
            return json_encode(array("status" => "failed"));
        }

    }
    public function addConfigProduct($data)
    {
        $data = json_decode($data);
        $data = json_decode($data->data);
        $data = (array) $data->productData;
        $sql = mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product SET model = '',sku = '" . $data['sku'] . "', upc = '', ean = '', jan = '', isbn = '', mpn = '', location = '', quantity = '" . (int) $data['qty'] . "', minimum = '1', subtract = '1', stock_status_id = '7', date_available = NOW(), manufacturer_id = '', shipping = '', price = '" . (float) $data['price'] . "', points = '', weight = '" . (float) $data['weight'] . "', weight_class_id = '1', length = '', width = '', height = '', length_class_id = '1', status = '1', tax_class_id = '0', sort_order = '1', date_added = NOW(), is_variant= '0'");
        $product_id = mysqli_insert_id($this->con);
        if (isset($_FILES['configFile']['tmp_name'])) {
            $filename = str_replace(" ", "_", $_FILES['configFile']['name']);
            $file = "../../../image/catalog/" . $filename;
            if (file_exists($file)) {
                $status = 1;
            } else {
                $status = move_uploaded_file($_FILES['configFile']['tmp_name'], $file);
            }

            if ($status);
            {
                mysqli_query($this->con, "UPDATE " . DB_PREFIX . "product SET image = '" . "catalog/" . $filename . "' WHERE product_id = '" . (int) $product_id . "'");
            }
        }
        $query = mysqli_query($this->con, "SELECT language_id FROM `" . DB_PREFIX . "language` WHERE status=1");
        $row = mysqli_fetch_array($query, MYSQL_ASSOC);
        $name = $data['product_name'];
        $description = $data['description'];

        mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int) $product_id . "', language_id = '" . (int) $row['language_id'] . "', name = '" . $name . "', description = '" . $description . "', tag = '', meta_title = '', meta_description = '', meta_keyword = ''");
        $variants = $data['variants'];
        $variants = (array) $variants[0];
        //Insert Store Product
        mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int) $product_id . "', store_id = 0");
        // Insert Size Options
        $simpleProduct = (array) $variants['simpleProducts'];
        if (isset($simpleProduct)) {
            $query = mysqli_query($this->con, "SELECT option_id FROM `" . DB_PREFIX . "option_description` WHERE name='xe_size'");
            $row = mysqli_fetch_array($query, MYSQL_ASSOC);
            $option_id = $row['option_id'];

            mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int) $product_id . "', option_id = '" . (int) $option_id . "', required = '1'");
            $product_option_id = mysqli_insert_id($this->con);
            foreach ($simpleProduct as $product_option) {
                $product_option = (array) $product_option;

                if (isset($product_option['sizeId'])) {
                    if ($product_option_id != '') {
                        $price_prefix = ($product_option['price'] > $data['price']) ? '+' : '-';
                        $weight_prefix = ($product_option['weight'] > $data['weight']) ? '+' : '-';
                        if ($product_option['price'] == $data['price']) {
                            $price = 0;
                        } else if ($product_option['price'] > $data['price']) {
                            $price = (int) $product_option['price'] - (int) $data['price'];
                        } else {
                            $price = (int) $data['price'] - (int) $product_option['price'];
                        }

                        if ($product_option['weight'] == $data['weight']) {
                            $weight = 0;
                        } else if ($product_option['weight'] > $data['weight']) {
                            $weight = (int) $product_option['weight'] - (int) $data['weight'];
                        } else {
                            $weight = (int) $data['weight'] - (int) $product_option['weight'];
                        }

                        mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int) $product_option_id . "', product_id = '" . (int) $product_id . "', option_id = '" . (int) $option_id . "', option_value_id = '" . (int) $product_option['sizeId'] . "', quantity = '" . (int) $product_option['qty'] . "', subtract = '1', price = '" . (float) $price . "', price_prefix = '" . $price_prefix . "', points = '', points_prefix = '+', weight = '" . (float) $weight . "', weight_prefix = '" . $weight_prefix . "'");
                    }
                }
            }
        }

        // Insert Color Option
        if (isset($variants['color_id'])) {
            $query = mysqli_query($this->con, "SELECT option_id FROM `" . DB_PREFIX . "option_description` WHERE name='xe_color'");
            $row = mysqli_fetch_array($query, MYSQL_ASSOC);
            mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int) $product_id . "', option_id = '" . (int) $row['option_id'] . "', required = '1'");
            $product_option_id = mysqli_insert_id($this->con);
            $value = 0;
            if ($product_option_id != '') {
                mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int) $product_option_id . "', product_id = '" . (int) $product_id . "', option_id = '" . (int) $row['option_id'] . "', option_value_id = '" . (int) $variants['color_id'] . "', quantity = '" . (int) $data['qty'] . "', subtract = '1', price = '" . (float) $value . "', price_prefix = '+', points = '', points_prefix = '+', weight = '" . (float) $value . "', weight_prefix = '+'");
            }

        }
        // Insert Refid Option
        $query = mysqli_query($this->con, "SELECT option_id FROM `" . DB_PREFIX . "option_description` WHERE name='refid'");
        $row = mysqli_fetch_array($query, MYSQL_ASSOC);

        mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int) $product_id . "', option_id = '" . (int) $row['option_id'] . "', value = '', required = '0'");

        // Insert xe_is_design Option
        $query = mysqli_query($this->con, "SELECT option_id FROM `" . DB_PREFIX . "option_description` WHERE name='xe_is_design'");
        $row = mysqli_fetch_array($query, MYSQL_ASSOC);

        mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int) $product_id . "', option_id = '" . (int) $row['option_id'] . "', value = '1', required = '0'");

        if (isset($data['cat_id'])) {
            foreach ($data['cat_id'] as $category_id) {
                mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int) $product_id . "', category_id = '" . (int) $category_id . "'");
            }
        }
        //Insert Product Images
        if (isset($_FILES['simpleFile'])) {
            $count = 0;
            foreach ($_FILES['simpleFile']['tmp_name'] as $key => $product_image) {
                $filename = str_replace(" ", "_", $_FILES['simpleFile']['name'][$key]);
                $file = "../../../image/catalog/" . $filename;
                if (file_exists($file)) {
                    $status = 1;
                } else {
                    $status = move_uploaded_file($product_image, $file);
                }

                if ($status);
                {
                    mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int) $product_id . "', image = 'catalog/" . $filename . "', sort_order = '" . $count . "'");
                }
                $count++;
            }
        }
        return $product_id;

    }
    public function getProductInfo($conf_id)
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "product p WHERE p.product_id = '" . (int) $conf_id . "' and p.is_variant='0'";
        if (mysqli_num_rows(mysqli_query($this->con, $sql)) > 0) {
            $config_id = (int) $conf_id;
        } else {
            $sql = "SELECT variant_id FROM " . DB_PREFIX . "product_variant WHERE product_id = '" . (int) $conf_id . "'";
            $query = mysqli_query($this->con, $sql);
            $row = mysqli_fetch_array($query, MYSQL_ASSOC);
            $config_id = $row['variant_id'];
        }
        $sql = "SELECT * FROM " . DB_PREFIX . "product_variant pv LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = pv.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (pd.product_id = p.product_id)WHERE pv.variant_id= '" . (int) $config_id . "'";
        $query = mysqli_query($this->con, $sql);
        $i = 0;
        $variants = array();
        if ((mysqli_num_rows(mysqli_query($this->con, $sql)) > 0)) {
            while ($row = mysqli_fetch_array($query, MYSQL_ASSOC)) {
                $color = $this->getProductOptionsModified($row['product_id'], 'color');
                $size = $this->getProductOptionsModified($row['product_id'], 'size');
                $variants[$i]['color_id'] = $color['option_value_id'];
                $variants[$i]['var_id'] = $row['product_id'];
                $variants[$i]['sizeid'] = array();
                foreach ($size as $siz) {
                    $variants[$i]['sizeid'][] = $siz['option_value_id'];
                }
                $i++;
            }
        }
        return $variants;

    }
    public function getProductId($conf_id, $color_id)
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "product_variant pv LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = pv.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (pd.product_id = p.product_id)WHERE pv.variant_id= '" . (int) $conf_id . "'";
        $query = mysqli_query($this->con, $sql);
        while ($row = mysqli_fetch_array($query, MYSQL_ASSOC)) {
            $color = $this->getProductOptionsModified($row['product_id'], 'color');
            if ($color_id == $color['option_value_id']) {
                $product_id = $row['product_id'];
            }
        }
        return $product_id;

    }
    public function resize($filename, $width, $height)
    {
        if (!is_file(DIR_IMAGE . $filename)) {
            return;
        }

        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        $old_image = $filename;
        $new_image = 'cache/' . substr($filename, 0, strrpos($filename, '.')) . '-' . $width . 'x' . $height . '.' . $extension;

        if (!is_file(DIR_IMAGE . $new_image) || (filectime(DIR_IMAGE . $old_image) > filectime(DIR_IMAGE . $new_image))) {
            $path = '';

            $directories = explode('/', dirname(str_replace('../', '', $new_image)));

            foreach ($directories as $directory) {
                $path = $path . '/' . $directory;

                if (!is_dir(DIR_IMAGE . $path)) {
                    @mkdir(DIR_IMAGE . $path, 0777);
                }
            }

            list($width_orig, $height_orig) = getimagesize(DIR_IMAGE . $old_image);

            if ($width_orig != $width || $height_orig != $height) {
                $image = new ImageResize(DIR_IMAGE . $old_image);
                $image->resize($width, $height);
                $image->save(DIR_IMAGE . $new_image);
            } else {
                copy(DIR_IMAGE . $old_image, DIR_IMAGE . $new_image);
            }
        }

        if ($this->request->server['HTTPS']) {
            return $new_image;
        } else {
            return $new_image;
        }
    }
    public function addColor($color)
    {
        $result = array();
        $query = mysqli_query($this->con, "SELECT language_id FROM `" . DB_PREFIX . "language` WHERE status=1");
        $row2 = mysqli_fetch_array($query, MYSQL_ASSOC);
        $language_id = $row2['language_id'];
        $query = mysqli_query($this->con, "SELECT option_id FROM `" . DB_PREFIX . "option_description` WHERE name='xe_color'");
        $row = mysqli_fetch_array($query, MYSQL_ASSOC);
        $option_id = $row['option_id'];
        $sort_order = 0;
        mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "option_value SET option_id = '" . (int) $option_id . "', image = '', sort_order = '" . (int) $sort_order . "'");

        $option_value_id = mysqli_insert_id($this->con);

        if (mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "option_value_description SET option_value_id = '" . (int) $option_value_id . "', language_id = '" . (int) $language_id . "', option_id = '" . (int) $option_id . "', name = '" . $color . "'"));
        {
            $result['attribute_id'] = $option_value_id;
            $result['attribute_value'] = $color;
            $result['status'] = 'success';
            $result['swatchImage'] = '';
        }
        return json_encode($result);
    }
    public function editColor($data)
    {
        $result = array();
        $query = mysqli_query($this->con, "SELECT option_id FROM `" . DB_PREFIX . "option_description` WHERE name='xe_color'");
        $row = mysqli_fetch_array($query, MYSQL_ASSOC);
        $option_id = $row['option_id'];
        $sort_order = 0;
        $option_value_id = $data['option_id'];
        $color = $data['colorname'];
        if (mysqli_query($this->con, "UPDATE " . DB_PREFIX . "option_value_description SET name = '" . $color . "' WHERE option_id = '" . (int) $option_id . "' AND option_value_id = '" . (int) $option_value_id . "'"));
        {
            $result['attribute_id'] = $option_value_id;
            $result['attribute_value'] = $color;
            $result['status'] = 'success';
            $result['swatchImage'] = '';
        }
        return json_encode($result);
    }
    public function getProductParent($product_id)
    {
        $sql = "SELECT variant_id FROM " . DB_PREFIX . "product_variant WHERE product_id = '" . (int) $product_id . "'";
        $query = mysqli_query($this->con, $sql);
        if (mysqli_num_rows($query) > 0) {
            $row = mysqli_fetch_array($query, MYSQL_ASSOC);
            $parent_id = $row['variant_id'];
        } else {
            $parent_id = $product_id;
        }
        return $parent_id;

    }
    public function addColorImage($option_value_id, $filename)
    {
        $filename = 'catalog/swatchImage/' . $filename;
        if (mysqli_query($this->con, "UPDATE " . DB_PREFIX . "option_value SET image = '" . $filename . "' WHERE option_value_id = '" . (int) $option_value_id . "'")) {
            return json_encode(array("status" => "success"));
        }

        return true;

    }
    public function addTemplateProducts($data)
    {
        $config_id = 0;
        $product_data = $data['data'];
        $config_id = (isset($product_data['conf_id']) && $product_data['conf_id'] != 0) ? $product_data['conf_id'] : $this->addConfigTemplateProduct($data);
        if (isset($product_data['conf_id']) && $product_data['conf_id'] != 0) {
            $query = mysqli_query($this->con, "SELECT price FROM `" . DB_PREFIX . "product` WHERE product_id='" . (int) $config_id . "'");
            $row = mysqli_fetch_array($query, MYSQL_ASSOC);
            $config_price = $row['price'];
            $price_prefix = ($product_data['price'] > $config_price) ? '+' : '-';
            if ($product_data['price'] == $config_price) {
                $price = 0;
            } else if ($product_data['price'] > $config_price) {
                $price = (int) $product_data['price'] - (int) $config_price;
            } else {
                $price = (int) $config_price - (int) $product_data['price'];
            }

            $query = mysqli_query($this->con, "SELECT option_id FROM `" . DB_PREFIX . "option_description` WHERE name='xe_color'");
            $row = mysqli_fetch_array($query, MYSQL_ASSOC);
            $query1 = mysqli_query($this->con, "SELECT product_option_id FROM `" . DB_PREFIX . "product_option` WHERE product_id = '" . (int) $config_id . "' AND option_id = '" . (int) $row['option_id'] . "'");
            $row1 = mysqli_fetch_array($query1, MYSQL_ASSOC);
            $config_product_color_option_id = $row1['product_option_id'];
            $value = 0;
            if ($config_product_color_option_id != '') {
                mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int) $config_product_color_option_id . "', product_id = '" . (int) $config_id . "', option_id = '" . (int) $row['option_id'] . "', option_value_id = '" . (int) $data['varColor'] . "', quantity = '" . (int) $product_data['qty'] . "', subtract = '1', price = '" . (float) $price . "', price_prefix = '" . $price_prefix . "', points = '', points_prefix = '+', weight = '" . (float) $value . "', weight_prefix = '+'");
            }
            $query = mysqli_query($this->con, "SELECT option_id FROM `" . DB_PREFIX . "option_description` WHERE name='xe_size'");
            $row = mysqli_fetch_array($query, MYSQL_ASSOC);
            $query1 = mysqli_query($this->con, "SELECT product_option_id FROM `" . DB_PREFIX . "product_option` WHERE product_id = '" . (int) $config_id . "' AND option_id = '" . (int) $row['option_id'] . "'");
            $row1 = mysqli_fetch_array($query1, MYSQL_ASSOC);
            $config_product_color_option_id = $row1['product_option_id'];
            if ($config_product_color_option_id != '') {
                foreach ($data['varSize'] as $size) {
                    $sql = mysqli_query($this->con, "SELECT * FROM `" . DB_PREFIX . "product_option_value` WHERE product_id = '" . (int) $config_id . "' AND option_id = '" . (int) $row['option_id'] . "' AND product_option_id = '" . (int) $config_product_color_option_id . "' AND option_value_id = '" . (int) $size . "'");
                    if (!mysqli_num_rows($sql) > 0) {
                        mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int) $config_product_color_option_id . "', product_id = '" . (int) $config_id . "', option_id = '" . (int) $row['option_id'] . "', option_value_id = '" . (int) $size . "', quantity = '" . (int) $product_data['qty'] . "', subtract = '1', price = '" . (float) $price . "', price_prefix = '" . $price_prefix . "', points = '', points_prefix = '+', weight = '" . (float) $value . "', weight_prefix = '+'");
                    }
                }
            }

        }
        if ($config_id != 0) {
            $sql = mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product SET model = 'Customized',sku = '" . $product_data['sku'] . "', upc = '', ean = '', jan = '', isbn = '', mpn = '', location = '', quantity = '" . (int) $product_data['qty'] . "', minimum = '" . $product_data['mini_qty'] . "', subtract = '1', stock_status_id = '7', date_available = NOW(), manufacturer_id = '', shipping = '', price = '" . (float) $product_data['price'] . "', points = '', weight = '', weight_class_id = '1', length = '', width = '', height = '', length_class_id = '1', status = '1', tax_class_id = '0', sort_order = '1', date_added = NOW(), is_variant= '1'");
            $product_id = mysqli_insert_id($this->con);
            $query = mysqli_query($this->con, "INSERT INTO `" . DB_PREFIX . "product_variant` SET `product_id` = " . (int) $product_id . ", variant_id = " . (int) $config_id);
            $query = mysqli_query($this->con, "SELECT language_id FROM `" . DB_PREFIX . "language` WHERE status=1");
            $row = mysqli_fetch_array($query, MYSQL_ASSOC);

            $query = mysqli_query($this->con, "SELECT name FROM `" . DB_PREFIX . "option_value_description` WHERE language_id='" . $row['language_id'] . "' and option_value_id='" . $data['varColor'] . "'");
            $color = mysqli_fetch_array($query, MYSQL_ASSOC);

            $name = $product_data['product_name'] . "-" . $color['name'];
            $description = $product_data['description'];
            $short_description = $product_data['short_description'];

            mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int) $product_id . "', language_id = '" . (int) $row['language_id'] . "', name = '" . $name . "', description = '" . $description . "', tag = '', meta_title = '', meta_description = '', meta_keyword = ''");
            //Insert Store Product
            mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int) $product_id . "', store_id = 0");
            $oldConfId = $data['oldConfId'];
            $pv = $this->getProductInfo($oldConfId);
            $variant_id = 0;
            foreach ($pv as $var) {
                if ($var['color_id'] == $data['varColor']) {
                    $variant_id = $var['var_id'];
                }

            }
            $query = mysqli_query($this->con, "SELECT image FROM `" . DB_PREFIX . "product` WHERE  product_id = '" . (int) $variant_id . "'");
            $row = mysqli_fetch_array($query, MYSQL_ASSOC);

            mysqli_query($this->con, "UPDATE " . DB_PREFIX . "product SET image = '" . $row['image'] . "' WHERE product_id = '" . (int) $product_id . "'");

            $query = mysqli_query($this->con, "SELECT option_id FROM `" . DB_PREFIX . "option_description` WHERE name='xe_size'");
            $row = mysqli_fetch_array($query, MYSQL_ASSOC);
            $option_id = $row['option_id'];

            mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int) $product_id . "', option_id = '" . (int) $option_id . "', required = '1'");
            $product_size_option_id = mysqli_insert_id($this->con);
            foreach ($data['varSize'] as $size) {
                mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int) $product_size_option_id . "', product_id = '" . (int) $product_id . "', option_id = '" . (int) $option_id . "', option_value_id = '" . (int) $size . "', quantity = '" . (int) $product_data['qty'] . "', subtract = '1', price = '" . (float) 0 . "', price_prefix = '+', points = '', points_prefix = '+', weight = '" . (float) 0 . "', weight_prefix = '+'");
            }
            if (isset($data['varColor'])) {
                $query = mysqli_query($this->con, "SELECT option_id FROM `" . DB_PREFIX . "option_description` WHERE name='xe_color'");
                $row = mysqli_fetch_array($query, MYSQL_ASSOC);
                mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int) $product_id . "', option_id = '" . (int) $row['option_id'] . "', required = '1'");
                $product_color_option_id = mysqli_insert_id($this->con);
                $value = 0;
                if ($product_color_option_id != '') {
                    mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int) $product_color_option_id . "', product_id = '" . (int) $product_id . "', option_id = '" . (int) $row['option_id'] . "', option_value_id = '" . (int) $data['varColor'] . "', quantity = '" . (int) $product_data['qty'] . "', subtract = '1', price = '" . (float) $value . "', price_prefix = '+', points = '', points_prefix = '+', weight = '" . (float) $value . "', weight_prefix = '+'");
                }

            }
            // Insert Refid Option
            $query = mysqli_query($this->con, "SELECT option_id FROM `" . DB_PREFIX . "option_description` WHERE name='refid'");
            $row = mysqli_fetch_array($query, MYSQL_ASSOC);

            mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int) $product_id . "', option_id = '" . (int) $row['option_id'] . "', value = '" . $product_data['ref_id'] . "', required = '0'");

            // Insert xe_is_design Option
            $query = mysqli_query($this->con, "SELECT option_id FROM `" . DB_PREFIX . "option_description` WHERE name='xe_is_design'");
            $row = mysqli_fetch_array($query, MYSQL_ASSOC);

            mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int) $product_id . "', option_id = '" . (int) $row['option_id'] . "', value = '" . $product_data['is_customized'] . "', required = '0'");

            if (isset($product_data['cat_id'])) {
                foreach ($product_data['cat_id'] as $category_id) {
                    mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int) $product_id . "', category_id = '" . (int) $category_id . "'");
                }
            }
            //Insert Product Images
            $query = mysqli_query($this->con, "SELECT * FROM `" . DB_PREFIX . "product_image` WHERE  product_id = '" . (int) $variant_id . "'");
            if ((mysqli_num_rows($query) > 0)) {
                while ($row = mysqli_fetch_array($query, MYSQL_ASSOC)) {
                    mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int) $product_id . "', image = '" . $row['image'] . "', sort_order = '" . $row['sort_order'] . "'");
                }
            }
            $pvariant = $this->getProductInfo($config_id);
            return json_encode(array("status" => "success", "conf_id" => $config_id, "variants" => $pvariant));
        } else {
            return json_encode(array("status" => "failed"));
        }

    }

    /***** Addition of configurable product ******/
    public function addConfigTemplateProduct($data)
    {
        $product_data = $data['data'];
        $sql = mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product SET model = 'Customized',sku = '" . $product_data['sku'] . "', upc = '', ean = '', jan = '', isbn = '', mpn = '', location = '', quantity = '" . (int) $product_data['qty'] . "', minimum = '" . $product_data['mini_qty'] . "', subtract = '1', stock_status_id = '7', date_available = NOW(), manufacturer_id = '', shipping = '', price = '" . (float) $product_data['price'] . "', points = '', weight = '', weight_class_id = '1', length = '', width = '', height = '', length_class_id = '1', status = '1', tax_class_id = '0', sort_order = '1', date_added = NOW(), is_variant= '0'");
        $product_id = mysqli_insert_id($this->con);
        $imageArr = array();
        if (isset($data['configFile'])) {
            foreach ($data['configFile'] as $imageFile) {
                if (strpos(stripslashes($imageFile), 'capturedImage/') !== false) {
                    $filename = explode("capturedImage/preDecoProduct/", $imageFile);
                    $filename = $filename[1];
                    $destinationImagePath = DIR_IMAGE . 'catalog/preDecoProduct/' . $product_id;
                    $destImageFile = $destinationImagePath . '/' . $filename;
                    $contents = file_get_contents($imageFile);
                    if (!file_exists($mkDirPreviw)) {
                        $exp = explode('/', $destinationImagePath);
                        foreach ($exp as $dir) {
                            $mkDirPreviw .= $dir . "/";
                            $dira[] = $mkDirPreviw;
                            if (!file_exists($mkDirPreviw)) {
                                mkdir($mkDirPreviw, 0755, true);
                            }
                        }
                    }
                    file_put_contents($destImageFile, $contents);
                    $imageArr[] = 'catalog/preDecoProduct/' . $product_id . '/' . $filename;
                } else {
                    $filename = explode("catalog/", $imageFile);
                    $filename = $filename[1];
                    $imageArr[] = 'catalog/' . $filename;
                }
            }
            mysqli_query($this->con, "UPDATE " . DB_PREFIX . "product SET image = '" . $imageArr[0] . "' WHERE product_id = '" . (int) $product_id . "'");
        }
        $query = mysqli_query($this->con, "SELECT language_id FROM `" . DB_PREFIX . "language` WHERE status=1");
        $row = mysqli_fetch_array($query, MYSQL_ASSOC);
        $name = $product_data['product_name'];
        $description = $product_data['description'];
        $short_description = $product_data['short_description'];

        mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int) $product_id . "', language_id = '" . (int) $row['language_id'] . "', name = '" . $name . "', description = '" . $description . "', tag = '', meta_title = '', meta_description = '" . $short_description . "', meta_keyword = ''");
        //Insert Store Product
        mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int) $product_id . "', store_id = 0");

        $query = mysqli_query($this->con, "SELECT option_id FROM `" . DB_PREFIX . "option_description` WHERE name='xe_size'");
        $row = mysqli_fetch_array($query, MYSQL_ASSOC);
        $option_id = $row['option_id'];

        mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int) $product_id . "', option_id = '" . (int) $option_id . "', required = '1'");
        $product_size_option_id = mysqli_insert_id($this->con);
        foreach ($data['varSize'] as $size) {
            mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int) $product_size_option_id . "', product_id = '" . (int) $product_id . "', option_id = '" . (int) $option_id . "', option_value_id = '" . (int) $size . "', quantity = '" . (int) $product_data['qty'] . "', subtract = '1', price = '" . (float) 0 . "', price_prefix = '+', points = '', points_prefix = '+', weight = '" . (float) 0 . "', weight_prefix = '+'");
        }
        if (isset($data['varColor'])) {
            $query = mysqli_query($this->con, "SELECT option_id FROM `" . DB_PREFIX . "option_description` WHERE name='xe_color'");
            $row = mysqli_fetch_array($query, MYSQL_ASSOC);
            mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int) $product_id . "', option_id = '" . (int) $row['option_id'] . "', required = '1'");
            $product_color_option_id = mysqli_insert_id($this->con);
            $value = 0;
            if ($product_color_option_id != '') {
                mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int) $product_color_option_id . "', product_id = '" . (int) $product_id . "', option_id = '" . (int) $row['option_id'] . "', option_value_id = '" . (int) $data['varColor'] . "', quantity = '" . (int) $product_data['qty'] . "', subtract = '1', price = '" . (float) $value . "', price_prefix = '+', points = '', points_prefix = '+', weight = '" . (float) $value . "', weight_prefix = '+'");
            }

        }

        // Insert Refid Option
        $query = mysqli_query($this->con, "SELECT option_id FROM `" . DB_PREFIX . "option_description` WHERE name='refid'");
        $row = mysqli_fetch_array($query, MYSQL_ASSOC);

        mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int) $product_id . "', option_id = '" . (int) $row['option_id'] . "', value = '" . $product_data['ref_id'] . "', required = '0'");

        // Insert xe_is_design Option
        $query = mysqli_query($this->con, "SELECT option_id FROM `" . DB_PREFIX . "option_description` WHERE name='xe_is_design'");
        $row = mysqli_fetch_array($query, MYSQL_ASSOC);

        mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int) $product_id . "', option_id = '" . (int) $row['option_id'] . "', value = '" . $product_data['is_customized'] . "', required = '0'");

        if (isset($product_data['cat_id'])) {
            foreach ($product_data['cat_id'] as $category_id) {
                mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int) $product_id . "', category_id = '" . (int) $category_id . "'");
            }
        }
        //Insert Product Images
        if (!empty($imageArr)) {
            $count = 0;
            foreach ($imageArr as $product_image) {
                mysqli_query($this->con, "INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int) $product_id . "', image = '" . $product_image . "', sort_order = '" . $count . "'");
                $count++;
            }
        }
        return $product_id;
    }

    /*
     * Method to get the tier discount
     * return array
     */
    public function getTierPrice($productId)
    {
        $tier = array();
        $todayDate = date("Y-m-d");
        $tierQuery = "SELECT price, quantity FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . $productId . "' AND (date_start IS NULL  OR date_start >= '" . $todayDate . "') AND (date_end IS NULL OR date_end <= '" . $todayDate . "')";
        $tierExecute = mysqli_query($this->con, $tierQuery);
        if (mysqli_num_rows($tierExecute) > 0) {
            $k = 0;
            while ($tierRow = mysqli_fetch_array($tierExecute, MYSQL_ASSOC)) {
                $tier[$k]['price'] = $tierRow['price'];
                $tier[$k]['quantity'] = $tierRow['quantity'];
                $k++;
            }
        }
        return $tier;
    }

    /*
     * Method to get the number of cart items
     * return string
     */
    public function getTotalCartItem()
    {
        session_start();
        $session_key = '';
        if (isset($_SESSION)) {
            $session_id = '';
            $i = 0;
            foreach ($_SESSION as $key => $value) {
                if ($i > 0) {
                    break;
                }

                $session_key = $key;
                if ($session_id == '' && $key != 'default') {
                    $session_id = $key;
                } else {
                    $session_id = session_id();
                }
                $i++;
            }
        }
        $count = 0;
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $key => $quantity) {
                $count += $quantity;
            }
        } else {
            $cartQuery = mysqli_query($this->con, "SELECT quantity FROM " . DB_PREFIX . "cart WHERE customer_id = '" . (int) $_SESSION[$session_key]['customer_id'] . "' AND session_id = '" . $session_id . "'");
            while ($cartTotal = mysqli_fetch_array($cartQuery, MYSQL_ASSOC)) {
                $count += $cartTotal['quantity'];
            }
        }
        return $count;
    }
}
