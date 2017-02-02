<?php
/* Check Un-authorize Access */
if (!defined('accessUser')) {
    die("Error");
}

class Font extends UTIL
{
    /**
     *
     *date created 9-9-2015 (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get font category
     *
     *@param (String)apikey
     *@param (Int)printId
     *@return json data
     *
		*/
    public function getFontCategories()
    {
        try {
            if (isset($this->_request['printId']) && ($this->_request['printId']) != '') {
                $query = "SELECT distinct * FROM " . TABLE_PREFIX . "font_category fc join " . TABLE_PREFIX . "font_category_printmethod_rel fcppr
				 on fcppr.font_category_id =fc.id where fcppr.print_method_id='" . $this->_request['printId'] . "'";
            } else {
                $query = "SELECT distinct * FROM " . TABLE_PREFIX . "font_category";
            }
            $categoryArray = array();
            $allTags = $this->executeGenericDQLQuery($query);
            foreach ($allTags as $row) {
                array_push($categoryArray, $row['category_name']);
            }
            $this->closeConnection();
            $this->response($this->json(array_unique($categoryArray)), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Save fetch web font
     *
     * @param (String)apikey
     * @param (String)category
     * @param (String)searchString
     * @param (int)printId
     * @param (int)start
     * @param (int)range
     * @return json data
     *
     */
    public function fetchWebfonts()
    {
        $category = $this->_request["category"];
        $searchString = $this->_request["searchString"];
        $start = $this->_request["start"];
        $range = $this->_request["range"];
        $print_method = $this->_request["printId"];
        try {
            $searchByCategory = ($category != '') ? " and c.category_name='" . $category . "'" : "";
            $searchByString = ($searchString != '') ? " and or f.font_name LIKE '" . $searchString . "%'" : "";
            if (isset($print_method) && $print_method != '') {
                $query = "SELECT DISTINCT f.id,f.font_name,f.orgName,f.font_label,f.price,f.is_delete FROM " . TABLE_PREFIX . "fonts f	";			
                if ($category != '' && $searchString == '') {
                    $query .= ", " . TABLE_PREFIX . "font_category_relation fcr, " . TABLE_PREFIX . "font_category c," . TABLE_PREFIX . "font_category_printmethod_rel fcpr WHERE f.is_delete='0' and f.id = fcr.font_id and fcr.category_id = fcpr.font_category_id and f.id = fcr.font_id and fcr.category_id = fcpr.font_category_id and f.id=fcr.font_id and fcr.category_id = c.id$searchByCategory";
                }
                if ($category != '' && $searchString != '') {
                    $query .= ", " . TABLE_PREFIX . "font_tag_relation tr, " . TABLE_PREFIX . "tags t, " . TABLE_PREFIX . "font_category_relation fcr, " . TABLE_PREFIX . "font_category c," . TABLE_PREFIX . "font_category_printmethod_rel fcpr WHERE f.is_delete='0' and  f.id = fcr.font_id and fcr.category_id = fcpr.font_category_id and f.id=fcr.font_id and fcr.category_id = c.id$searchByCategory and ((f.font_name LIKE '" . $searchString . "%') or (f.id=tr.font_id and tr.tag_id = t.id and t.tag_name LIKE '$searchString%')) AND fcpr.print_method_id ='" . $print_method . "'";
                }
                if ($category == '' && $searchString != '') {
                    $query .= ", " . TABLE_PREFIX . "font_tag_relation tr, " . TABLE_PREFIX . "tags t, " . TABLE_PREFIX . "font_category_relation fcr, " . TABLE_PREFIX . "font_category c," . TABLE_PREFIX . "font_category_printmethod_rel fcpr WHERE f.is_delete='0' and 1 and f.id = fcr.font_id and fcr.category_id = fcpr.font_category_id and ((f.font_name LIKE '" . $searchString . "%') or (f.id=tr.font_id and tr.tag_id = t.id and t.tag_name LIKE '$searchString%')) AND fcpr.print_method_id ='" . $print_method . "'";
                }
                if ($category == '' && $searchString == '') {
                    $query .= "	join " . TABLE_PREFIX . "font_category_relation fcr  on f.id = fcr.font_id
					left join " . TABLE_PREFIX . "font_category_printmethod_rel fcpr  on fcr.category_id = fcpr.font_category_id
					where f.is_delete='0' and fcpr.print_method_id='" . $print_method . "'";
                }
                $query .= " ORDER BY f.id DESC";
                $count = $this->executeGenericCountQuery($query);
                $query .= " LIMIT $start, $range";

                $allsearchFonts = $this->executeFetchAssocQuery($query);
                $searchfontArray['webFonts'] = array();
                foreach ($allsearchFonts as $k => $row) {
                    $searchfontArray['webFonts'][$k]['id'] = $row['id'];
                    $searchfontArray['webFonts'][$k]['name'] = $row['font_name'];
                    $searchfontArray['webFonts'][$k]['family'] = $row['orgName'];
                    $searchfontArray['webFonts'][$k]['label'] = $row['font_label'];
                    $searchfontArray['webFonts'][$k]['price'] = $row['price'];
                    $searchfontArray['webFonts'][$k]['is_delete'] = $row['is_delete'];
                }
                $sql = "SELECT COUNT(id) as total FROM " . TABLE_PREFIX . "fonts where is_delete='0'";
                $countWebFonts = $this->executeFetchAssocQuery($sql);
                $x['count'] = $count;
                $x['total_count'] = $countWebFonts[0]['total'];
                $x['fonts'] = $searchfontArray;
            } else {
                $x['status'] = 'no data found';
            }
        } catch (Exception $e) {
            $x = array('Caught exception:' => $e->getMessage());
        }
        $this->response($this->json($x), 200);
    }
    /**
     *
     *date created 19-1-2015 (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Get All web font category
     *
     *@param (String)apikey
     *@return json data
     *
     */
    public function allWebFontsCatagory()
    {
        $catagoryArray = array();
        try {
            $sql = "SELECT distinct id,category_name FROM " . TABLE_PREFIX . "font_category";
            $categoryDetail = array();
            $rows = $this->executeGenericDQLQuery($sql);
            for ($i = 0; $i < sizeof($rows); $i++) {
                $categoryDetail[$i]['id'] = $rows[$i]['id'];
                $categoryDetail[$i]['category_name'] = $rows[$i]['category_name'];
            }
            $this->response($this->json($categoryDetail), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *fetch shapes Font for in admin
     *
     *@param (String)apikey
     *@param (String)category
     *@param (String)searchString
     *@param (Int)start
     *@param (Int)range
     *@param (Int)print_method
     *@return json data
     *
     */
    public function searchFontForAdmin()
    {
        try {
            $category = $this->_request["category"];
            $searchString = $this->_request["searchString"];
            $start = $this->_request["start"];
            $range = $this->_request["range"];
            $print_method = isset($this->_request["print_method"])?$this->_request["print_method"]:0;
            $searchByCategory = ($category != '') ? " and c.category_name='" . $category . "'" : "";
            $searchByString = ($searchString != '') ? " and   or f.font_name LIKE '" . $searchString . "%'" : "";

            if ($category != '' && $searchString == '') {
                $query = "SELECT DISTINCT f.id,f.font_name,f.orgName,f.font_label,f.price,f.is_delete FROM " . TABLE_PREFIX . "fonts f, " . TABLE_PREFIX . "font_category_relation cr, " . TABLE_PREFIX . "font_category c WHERE f.is_delete='0' and  f.id=cr.font_id and cr.category_id = c.id$searchByCategory";
            } else if ($category != '' && $searchString != '') {
                $query = "SELECT DISTINCT f.id,f.font_name,f.orgName,f.font_label,f.price,f.is_delete FROM " . TABLE_PREFIX . "fonts f, font_tag_relation tr, " . TABLE_PREFIX . "tags t, " . TABLE_PREFIX . "font_category_relation cr, " . TABLE_PREFIX . "font_category c WHERE f.is_delete='0' and f.id=cr.font_id and cr.category_id = c.id$searchByCategory and ((f.font_name LIKE '" . $searchString . "%') or (f.id=tr.font_id and tr.tag_id = t.id and t.tag_name LIKE '$searchString%'))";
            } else if ($category == '' && $searchString != '') {
                $query = "SELECT DISTINCT f.id,f.font_name,f.orgName,f.font_label,f.price,f.is_delete FROM " . TABLE_PREFIX . "fonts f, " . TABLE_PREFIX . "font_tag_relation tr, " . TABLE_PREFIX . "tags t, " . TABLE_PREFIX . "font_category_relation cr, " . TABLE_PREFIX . "font_category c WHERE f.is_delete='0' and 1 and ((f.font_name LIKE '" . $searchString . "%') or (f.id=tr.font_id and tr.tag_id = t.id and t.tag_name LIKE '$searchString%')) ";
            } else {
                $query = "SELECT DISTINCT f.id,f.font_name,f.orgName,f.font_label,f.price,f.is_delete FROM " . TABLE_PREFIX . "fonts f where f.is_delete='0'";
            }

            if (isset($print_method) && $print_method != '') {
                $query .= " left join " . TABLE_PREFIX . "print_method_fonts_rel pdf on f.id = pdf.font_id where f.is_delete='0' and pdf.print_method_id='" . $print_method . "' ";
            }
            $query .= " ORDER BY f.id DESC";
            $count = $this->executeGenericCountQuery($query);
            $query .= " LIMIT $start, $range";
            //echo $query;
            $allsearchFonts = $this->executeFetchAssocQuery($query);
            $searchfontArray['webFonts'] = array();
            $i = 0;
            foreach ($allsearchFonts as $row) {
                $searchfontArray['webFonts'][$i]['id'] = $row['id'];
                $searchfontArray['webFonts'][$i]['name'] = $row['font_name'];
                $searchfontArray['webFonts'][$i]['family'] = $row['orgName'];
                $searchfontArray['webFonts'][$i]['label'] = $row['font_label'];
                $searchfontArray['webFonts'][$i]['price'] = $row['price'];
                $searchfontArray['webFonts'][$i]['is_delete'] = $row['is_delete'];
                $i++;
            }
            $sql = "SELECT COUNT(id) as total FROM " . TABLE_PREFIX . "fonts where is_delete='0' ORDER BY id DESC";
            $countWebFonts = $this->executeFetchAssocQuery($sql);
            $x['count'] = $count;
            $x['total_count'] = $countWebFonts[0]['total'];
            $x['fonts'] = $searchfontArray;
            $dir = $this->getWebfontsPath();
            //if(file_exists($dir)) $this->updateFontCss($dir.'fonts.css');
            $this->response($this->json($x), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Add bulk Web Font
     *
     *@param (String)apikey
     *@param (String)font_name
     *@param (String)font_label
     *@param (int)price
     *@param (Array)tags
     *@param (Array)files
     *@return json data
     *
     */
    public function addBulkWebfont()
    {
        $status = 0;
        $fname = array();
        $orgName = array();
        $msg = array();
        try {
            if (!empty($this->_request) && isset($this->_request['font_name']) && isset($this->_request['font_label'])) {
                if (!empty($this->_request['files'])) {
                    $sql = array();
                    $font_id = array();
                    $rsql1 = '';
                    $usql1 = '';
                    $tag_arr = array();
                    $ttfStatus = array();
                    $dir = $this->getWebfontsPath();
                    if (!$dir) {
                        $this->response('INVALID DIR.', 204);
                    }
                    //204 - immediately termiante this request
                    if (!file_exists($dir)) {
                        mkdir($dir, 0777, true);
                    }

                    $isql = "INSERT INTO " . TABLE_PREFIX . "fonts (font_name, font_label, price) VALUES";
                    $font_tag_rel_sql = "INSERT INTO " . TABLE_PREFIX . "font_tag_relation (font_id,tag_id) VALUES ";
                    $font_tag_rel_sql1 = '';
                    $font_cat_rel_sql = "INSERT INTO " . TABLE_PREFIX . "font_category_relation (font_id,category_id) VALUES ";
                    $font_cat_rel_sql1 = '';
                    $print_method_re_sql = "INSERT INTO " . TABLE_PREFIX . "print_method_fonts_rel (font_id,print_method_id) VALUES ";
                    $print_method_re_sql1 = '';

                    if (!empty($this->_request['tags'])) {
                        foreach ($this->_request['tags'] as $k => $v) {
                            $tag_sql = "SELECT id,count( * ) AS nos FROM " . TABLE_PREFIX . "tags WHERE tag_name = '" . $v . "'";
                            $res = $this->executeFetchAssocQuery($tag_sql);
                            if (!$res[0]['nos']) {
                                $tag_sql1 = "INSERT INTO " . TABLE_PREFIX . "tags(tag_name) VALUES('" . $v . "')";
                                $tag_arr[] = $this->executeGenericInsertQuery($tag_sql1);
                            } else {
                                $tag_arr[] = $res[0]['id'];
                            }
                        }
                    }
                    foreach ($this->_request['files'] as $k => $v) {
                        $base64ttfData[$k] = base64_decode($v['base64']);
                        $ttfFilePath[$k] = $dir . $v['font_family'] . '.ttf';
                        $ttfStatus[$k] = file_put_contents($ttfFilePath[$k], $base64ttfData[$k]);
                        $fontinfo[$k] = getFontInfo($ttfFilePath[$k]);
                        $orgName[$k] = $this->executeEscapeStringQuery($fontinfo[$k][1]);
                        $fesql[$k] = 'SELECT id FROM ' . TABLE_PREFIX . 'fonts WHERE orgName="' . $orgName[$k] . '" and is_delete="0" LIMIT 1';
                        $feres[$k] = $this->executeFetchAssocQuery($fesql[$k]);

                        if (!empty($feres[$k]) && isset($feres[$k][0]['id']) && $feres[$k][0]['id']) {
                            $font_id[$k] = $feres[$k][0]['id'];
                            $fusql[$k] = 'UPDATE ' . TABLE_PREFIX . 'fonts SET font_name="' . $this->_request['font_name'] . '", font_label="' . $this->_request['font_label'] . '", price="' . $this->_request['price'] . '" WHERE id=' . $font_id[$k];
                            $this->executeGenericDMLQuery($fusql[$k]);
                        } else {
                            $sql[$k] = $isql . "('" . $this->_request['font_name'] . "','" . $this->_request['font_label'] . "','" . $this->_request['price'] . "')";
                            $font_id[$k] = $this->executeGenericInsertQuery($sql[$k]);
                        }

                        $msg['font_name'][$k] = $this->_request['font_name'];
                        $fname[$k] = str_replace(' ', '_', $orgName[$k]);
                        $msg['extracted_name'][$k] = $fname[$k];
                        $msg['family_name'][$k] = $orgName[$k];
                        $ttfFilePath[$k] = $dir . $fname[$k] . '.ttf';
                        $ttfStatus[$k] = file_put_contents($ttfFilePath[$k], $base64ttfData[$k]);

                        $usql1 .= ",('" . $font_id[$k] . "','" . $orgName[$k] . "','0')";
                        if (!empty($tag_arr)) {
                            foreach ($tag_arr as $v) {
                                $font_tag_rel_sql1 .= ",('" . $font_id[$k] . "','" . $v . "')";
                            }
                        }
                        if (!empty($this->_request['category_id'])) {
                            foreach ($this->_request['category_id'] as $v) {
                                $font_cat_rel_sql1 .= ",('" . $font_id[$k] . "','" . $v . "')";
                            }
                        }
                        if (!empty($this->_request['print_method_id'])) {
                            foreach ($this->_request['print_method_id'] as $v) {
                                $print_method_re_sql1 .= ",('" . $font_id[$k] . "','" . $v . "')";
                            }
                        }
                    }
                    $usql = "INSERT INTO " . TABLE_PREFIX . "fonts (id, orgName,is_delete) VALUES " . substr($usql1, 1) . " ON DUPLICATE KEY UPDATE id=VALUES(id),orgName = VALUES(orgName),is_delete=VALUES(is_delete)";
                    $status = $this->executeGenericDMLQuery($usql);
                    if (strlen($font_tag_rel_sql1)) {
                        $font_tag_rel_sql1 = substr($font_tag_rel_sql1, 1);
                        $font_tag_rel_sql .= $font_tag_rel_sql1;
                        $status = $this->executeGenericDMLQuery($font_tag_rel_sql);
                    }
                    if (strlen($font_cat_rel_sql1)) {
                        $font_cat_rel_sql1 = substr($font_cat_rel_sql1, 1);
                        $font_cat_rel_sql .= $font_cat_rel_sql1;
                        $status = $this->executeGenericDMLQuery($font_cat_rel_sql);
                    }
                    if (strlen($print_method_re_sql1)) {
                        $print_method_re_sql1 = substr($print_method_re_sql1, 1);
                        $print_method_re_sql .= $print_method_re_sql1;
                        $status = $this->executeGenericDMLQuery($print_method_re_sql);
                    }
                    $this->updateFontCss($dir . 'fonts.css');
                }
            }
            $msg['status'] = ($status) ? 'Success' : 'Failure';
            $this->response($this->json($msg), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get detail of categories and tags of a font
     *
     *@param (String)apikey
     *@param (int)WebFont_id
     *@return json data
     *
     */
    public function getFontDetails()
    {
        try {
            $pFontId = $this->_request['FontId'];
            //fethcing Font details
            $sql = "select f.id,f.font_name,f.font_label,f.Price  from " . TABLE_PREFIX . "fonts f where f.id=$pFontId";
            $rows = $this->executeGenericDQLQuery($sql);
            $dbFontDetailArr = array();
            $dbFontDetailArr['id'] = $rows[0]['id'];
            $dbFontDetailArr['font_name'] = $rows[0]['font_name'];
            $dbFontDetailArr['font_label'] = $rows[0]['font_label'];
            $dbFontDetailArr['Price'] = $rows[0]['Price'];

            //fetch print method id by font id
            $sql = "SELECT pmfr.print_method_id FROM  " . TABLE_PREFIX . "print_method_fonts_rel pmfr," . TABLE_PREFIX . "fonts f
				WHERE pmfr.font_id=f.id
				AND f.id=$pFontId";
            $rows = $this->executeGenericDQLQuery($sql);
            $prntmethodIdDetailArr = array();
            for ($j = 0; $j < sizeof($rows); $j++) {
                $prntmethodIdDetailArr[$j] = $rows[$j]['print_method_id'];
            }

            // fetching categories
            $sql = "select distinct fc.id, fc.category_name  from " . TABLE_PREFIX . "fonts f , " . TABLE_PREFIX . "font_category fc , " . TABLE_PREFIX . "font_category_relation fcr where
			 f.id = fcr.font_id and fc.id = fcr.category_id and f.id = '$pFontId'";
            $rows = $this->clearArray($rows);
            $rows = $this->executeGenericDQLQuery($sql);
            $dbCatArr = array();
            for ($i = 0; $i < sizeof($rows); $i++) {
                $dbCatArr[$i]['category_id'] = $rows[$i]['id'];
                $dbCatArr[$i]['category_name'] = $rows[$i]['category_name'];
            }

            // fetching tags
            $sql = "select distinct ft.tag_name  from " . TABLE_PREFIX . "fonts f , " . TABLE_PREFIX . "tags ft , " . TABLE_PREFIX . "font_tag_relation ftr where f.id = ftr.font_id and ft.id = ftr.tag_id and f.id = '$pFontId'";
            $rows = $this->clearArray($rows);
            $rows = $this->executeGenericDQLQuery($sql);
            $dbTagArr = array();
            for ($i = 0; $i < sizeof($rows); $i++) {
                array_push($dbTagArr, $rows[$i]['tag_name']);
            }

            $productDetail = array();
            $productDetail['Font_detail'] = $dbFontDetailArr;
            $productDetail['print_method_id'] = $prntmethodIdDetailArr;
            $productDetail['categories'] = $dbCatArr;
            $productDetail['tags'] = $dbTagArr;
            $this->closeConnection();
            $this->response($this->json($productDetail), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Update WebFonts by id
     *
     *@param (String)apikey
     *@param (String)font_name
     *@param (int)id
     *@param (int)category_id
     *@param (Array)tags
     *@return json data
     *
     */
    public function updateWebFontsData()
    {
        try {
            $status = 0;
            $msg = array();
            if (!empty($this->_request) && isset($this->_request['font_name']) && !empty($this->_request['id'])) {
                $sql = array();
                $font_id = array();
                $tag_arr = array();
                $font_tag_rel_sql = '';
                $font_cat_rel_sql = '';
                $print_method_re_sql = '';
                extract($this->_request);
                $id_str = implode(',', $id);
                $sql = "UPDATE " . TABLE_PREFIX . "fonts SET font_name = '" . $font_name . "', price = '" . $price . "' WHERE id IN(" . $id_str . ")";
                $status = $this->executeGenericDMLQuery($sql);

                $sql = "DELETE FROM " . TABLE_PREFIX . "font_tag_relation WHERE font_id IN(" . $id_str . ")";
                $status = $this->executeGenericDMLQuery($sql);
                $sql = "DELETE FROM " . TABLE_PREFIX . "font_category_relation WHERE font_id IN(" . $id_str . ")";
                $status = $this->executeGenericDMLQuery($sql);
                $sql = "DELETE FROM " . TABLE_PREFIX . "print_method_fonts_rel WHERE font_id IN(" . $id_str . ")";
                $status = $this->executeGenericDMLQuery($sql);

                if (!empty($this->_request['tags'])) {
                    foreach ($this->_request['tags'] as $k => $v) {
                        $tag_sql = "SELECT id,count( * ) AS nos FROM " . TABLE_PREFIX . "tags WHERE tag_name = '" . $v . "'";
                        $res = $this->executeFetchAssocQuery($tag_sql);
                        if (!$res[0]['nos']) {
                            $tag_sql1 = "INSERT INTO " . TABLE_PREFIX . "tags(tag_name) VALUES('" . $v . "')";
                            $tag_arr[] = $this->executeGenericInsertQuery($tag_sql1);
                        } else {
                            $tag_arr[] = $res[0]['id'];
                        }
                    }
                }

                foreach ($this->_request['id'] as $k => $v) {
                    $font_id[$k] = $v;
                    if (!empty($tag_arr)) {
                        foreach ($tag_arr as $v) {
                            $font_tag_rel_sql .= ",('" . $font_id[$k] . "','" . $v . "')";
                        }
                    }
                    if (!empty($this->_request['category_id'])) {
                        foreach ($this->_request['category_id'] as $v) {
                            $font_cat_rel_sql .= ",('" . $font_id[$k] . "','" . $v . "')";
                        }
                    }
                    if (!empty($this->_request['print_method_id'])) {
                        foreach ($this->_request['print_method_id'] as $v) {
                            $print_method_re_sql .= ",('" . $font_id[$k] . "','" . $v . "')";
                        }
                    }
                }
                if (strlen($font_tag_rel_sql)) {
                    $font_tag_rel_sql = "INSERT INTO " . TABLE_PREFIX . "font_tag_relation (font_id,tag_id) VALUES " . substr($font_tag_rel_sql, 1);
                    $status = $this->executeGenericDMLQuery($font_tag_rel_sql);
                }
                if (strlen($font_cat_rel_sql)) {
                    $font_cat_rel_sql = "INSERT INTO " . TABLE_PREFIX . "font_category_relation (font_id,category_id) VALUES " . substr($font_cat_rel_sql, 1);
                    $status = $this->executeGenericDMLQuery($font_cat_rel_sql);
                }
                if (strlen($print_method_re_sql)) {
                    $print_method_re_sql = "INSERT INTO " . TABLE_PREFIX . "print_method_fonts_rel (font_id,print_method_id) VALUES " . substr($print_method_re_sql, 1);
                    $status = $this->executeGenericDMLQuery($print_method_re_sql);
                }
            }
            $msg['status'] = ($status) ? 'Success' : 'Failure';
            $this->response($this->json($msg), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Add web font category
     *
     *@param (String)apikey
     *@param (String)category
     *@return json data
     *
     */
    public function addWebFontCategory()
    {
        try {
            $pCategory = $this->_request['category'];
            $sql = "select count(*) count from " . TABLE_PREFIX . "font_category where category_name = '$pCategory'";
            $row = $this->executeGenericDQLQuery($sql);
            $response = array();
            if ($row[0]['count'] == "0") {
                $sql = "insert into " . TABLE_PREFIX . "font_category(category_name) values('$pCategory')";
                //executeGenericDMLQuery($sql);
                $this->executeGenericDMLQuery($sql);
                $response['status'] = "success";
                $response['message'] = ' category inserted';
            } else {
                $response['status'] = "fail";
                $response['message'] = ' category already present';
            }

        } catch (Exception $e) {
            $response = array('Caught exception:' => $e->getMessage());
        }
        $this->response($this->json($response), 200);
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *upadte web font category by id
     *
     * @param (String)apikey
     * @param (int)id
     * @param (String)name
     * @return json data
     *
     */
    public function updateWebFontCategory()
    {
        $status = 0;
        if (!empty($this->_request) && $this->_request['id'] && isset($this->_request['name'])) {
            extract($this->_request);
            try {
                $chk_duplicate = "SELECT COUNT(*) AS duplicate FROM " . TABLE_PREFIX . "font_category WHERE category_name='" . $name . "' AND id !='" . $id . "'";
                $res = $this->executeFetchAssocQuery($chk_duplicate);

                if ($res[0]['duplicate']) {
                    $msg['msg'] = 'Duplicate entry';
                } else {
                    $sql = "UPDATE " . TABLE_PREFIX . "font_category SET category_name='" . $name . "' WHERE id='" . $id . "'";
                    $status = $this->executeGenericDMLQuery($sql);
                }
                $msg['status'] = ($status) ? 'success' : 'failed';
            } catch (Exception $e) {
                $msg = array('Caught exception:' => $e->getMessage());
            }
        } else {
            $msg['status'] = 'nodata';
        }

        $this->response($this->json($msg), 200);
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Remove web font category
     *
     *@param (String)apikey
     *@param (String)removeCategory
     *@return json data
     *
     */
    public function removeWebFontCategory()
    {
        try {
            $pCategory = $this->_request['removeCategory'];
            $sql = "select count(*) count from " . TABLE_PREFIX . "font_category where category_name = '$pCategory'";
            $row = $this->executeGenericDQLQuery($sql);
            $response = array();
            if ($row[0]['count'] == "0") {
                // category not present error
                $response['status'] = false;
                $response['message'] = 'ERROR cateory not present';
            } else {
                // perform delete
                $sql = "DELETE FROM " . TABLE_PREFIX . "font_category WHERE category_name= '$pCategory'";
                $this->executeGenericDMLQuery($sql);
                $response['status'] = true;
                $response['message'] = "'$pCategory' cateory delete successful !!";
            }
        } catch (Exception $e) {
            $response = array('Caught exception:' => $e->getMessage());
        }
        $this->response($this->json($response), 200);
    }

    /**
     *
     *date created 9-9-2015 (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Search font
     *
     *@param (String)apikey
     *@param (String)category
     *@param (String)searchString
     *@param (Int)start
     *@param (Int)range
     *@return json data
     *
     */
    public function searchFont()
    {
        try {
            $category = $this->_request["category"];
            $searchString = $this->_request["searchString"];
            $start = $this->_request["start"];
            $range = $this->_request["range"];
            $searchByCategory = ($category != '') ? " and c.category_name='" . $category . "'" : "";
            $searchByString = ($searchString != '') ? " and   or f.font_name LIKE '" . $searchString . "%'" : "";
            if ($category != '' && $searchString == '') {
                $query = "SELECT DISTINCT f.id,f.font_name,f.price FROM " . TABLE_PREFIX . "fonts f, font_category_relation cr, font_category c WHERE  f.id=cr.font_id and cr.category_id = c.id$searchByCategory ORDER BY f.id DESC";
            } else if ($category != '' && $searchString != '') {
                $query = "SELECT DISTINCT f.id,f.font_name,f.price FROM " . TABLE_PREFIX . "fonts f, font_tag_relation tr, tags t, font_category_relation cr, font_category c WHERE f.id=cr.font_id and cr.category_id = c.id$searchByCategory and ((f.font_name LIKE '" . $searchString . "%') or (f.id=tr.font_id and tr.tag_id = t.id and t.tag_name LIKE '$searchString%')) ORDER BY f.id DESC";
            } else if ($category == '' && $searchString != '') {
                $query = "SELECT DISTINCT f.id,f.font_name,f.price FROM " . TABLE_PREFIX . "fonts f, font_tag_relation tr, tags t, font_category_relation cr, font_category c WHERE 1 and ((f.font_name LIKE '" . $searchString . "%') or (f.id=tr.font_id and tr.tag_id = t.id and t.tag_name LIKE '$searchString%')) ORDER BY f.id DESC";
            } else {
                $query = "SELECT DISTINCT f.id,f.font_name,f.price FROM " . TABLE_PREFIX . "fonts f ORDER BY f.id DESC";
            }
            $count = $this->executeGenericCountQuery($query);
            $query .= " LIMIT $start, $range";
            $allsearchFonts = $this->executeFetchAssocQuery($query);
            $searchfontArray['webFonts'] = array();
            $i = 0;
            foreach ($allsearchFonts as $row) {
                $searchfontArray['webFonts'][$i]['id'] = $row['id'];
                $searchfontArray['webFonts'][$i]['name'] = $row['font_name'];
                $searchfontArray['webFonts'][$i]['price'] = $row['price'];
                $searchfontArray['webFonts'][$i]['count'] = $count;
                $i++;
            }
            $this->closeConnection();
            $this->response($this->json(array_unique($searchfontArray)), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Get all web fonts tags
     *
     *@param (String)apikey
     *@return json data
     *
     */
    public function fetchAllWebFontsTags()
    {
        //fetching tags
        try {
            $sql = "select distinct t.tag_name from " . TABLE_PREFIX . "tags t ";
            $rows = $this->clearArray($rows);
            $rows = $this->executeGenericDQLQuery($sql);
            $tagArr = array();
            for ($i = 0; $i < sizeof($rows); $i++) {
                array_push($tagArr, $rows[$i]['tag_name']);
            }
            $detailsArr['tags'] = $tagArr;
            $this->closeConnection();
            $this->response($this->json($detailsArr), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *delete WebFont by font id
     *
     *@param (String)apikey
     *@param (int)WebFont_id
     *@return json data
     *
     */
    public function deleteWebFontById()
    {
        try {
            $pWebFontId = $this->_request['WebFont_id'];
            $sql = "UPDATE " . TABLE_PREFIX . "fonts SET is_delete='1' where id=$pWebFontId";
            $dir = $this->getWebfontsPath();
            if (file_exists($dir)) {
                $this->updateFontCss($dir . 'fonts.css');
            }

            $this->executeGenericDMLQuery($sql);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Get webfonts tags id
     *
     *@param (String)apikey
     *@param (Array)tagsText
     *@return json data
     *
     */
    public function getFontTagIdArr($tagsText)
    {
        try {
            $reqTag = $tagsText;
            $sql = "select id,tag_name from " . TABLE_PREFIX . "tags";
            $rows = $this->executeGenericDQLQuery($sql);
            $dbTagArr = array();
            $resTagArr = array();
            for ($i = 0; $i < sizeof($reqTag); $i++) {
                $found = false;
                for ($j = 0; $j < sizeof($rows); $j++) {
                    if ($rows[$j]['tag_name'] == $reqTag[$i]) {
                        $found = true;
                        array_push($resTagArr, $rows[$j]['id']);
                        break;
                    }
                }
                if ($found == false && $reqTag[$i] != '') {
                    // insert category and push the id
                    $sql = "insert into " . TABLE_PREFIX . "tags(tag_name) values('$reqTag[$i]')";
                    $tagId = $this->executeGenericInsertQuery($sql);
                    array_push($resTagArr, $tagId);
                }
            }
            return $resTagArr;
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *map webfonts category rel
     *
     *@param (String)apikey
     *@param (Array)categoryIdArr
     *@param (int)webfontsId
     *@return json data
     *
     */
    public function mapWebfontsCategoryRel($webfontsId, $categoryIdArr)
    {
        try {
            $sql = "select font_id , category_id from " . TABLE_PREFIX . "font_category_relation";
            $rows = $this->executeGenericDQLQuery($sql);
            for ($j = 0; $j < sizeof($categoryIdArr); $j++) {
                $found = false;
                for ($k = 0; $k < sizeof($rows); $k++) {
                    if ($rows[$k]['font_id'] == $webfontsId && $rows[$k]['category_id'] == $categoryIdArr[$j]) {
                        $found = true;
                        break;
                    }
                }
                if ($found == false) {
                    $sql = "insert into " . TABLE_PREFIX . "font_category_relation(font_id,category_id) values('$webfontsId' , '$categoryIdArr[$j]')";
                    $this->executeGenericInsertQuery($sql);
                }
            }
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *map webfonts tags rel
     *
     *@param (String)apikey
     *@param (Array)tagIdArr
     *@param (int)webfontsId
     *@return json data
     *
     */
    public function mapWebfontsTagRel($webfontsId, $tagIdArr)
    {
        try {
            $sql = "select font_id , tag_id from " . TABLE_PREFIX . "font_tag_relation";
            $rows = $this->executeGenericDQLQuery($sql);
            for ($j = 0; $j < sizeof($tagIdArr); $j++) {
                $found = false;
                for ($k = 0; $k < sizeof($rows); $k++) {
                    if ($rows[$k]['font_id'] == $webfontsId && $rows[$k]['tag_id'] == $tagIdArr[$j]) {
                        $found = true;
                        break;
                    }
                }
                if ($found == false) {
                    $sql = "insert into " . TABLE_PREFIX . "font_tag_relation(font_id,tag_id) values('$webfontsId' , '$tagIdArr[$j]')";
                    $this->executeGenericInsertQuery($sql);
                }
            }
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get webfonts id
     *
     *@param (String)apikey
     *@param (String)webfontsName
     *@param (String)webfontsLable
     *@param (Float)price
     *@return json data
     *
     */
    protected function getWebfontsId($webfontsName, $webfontsLable, $price)
    {
        try {
            $errorMsg = '';
            $webfontsId;
            $sql = "select id from " . TABLE_PREFIX . "fonts  where font_name = '$webfontsName'";
            $row = $this->executeGenericDQLQuery($sql);
            $sql_Label = "select id from " . TABLE_PREFIX . "fonts  where font_label = '$webfontsLable'";
            $row_Label = $this->executeGenericDQLQuery($sql_Label);
            if (sizeof($row) != 0) {
                $errorMsg = 'Webfont already Exists';
            } else if (sizeof($row_Label) != 0) {
                $errorMsg = 'Enter different Font Name';
            }

            if ($errorMsg == '') {
                $sql = "insert into " . TABLE_PREFIX . "fonts(font_name,font_label,price) values('$webfontsName','$webfontsLable',$price)";
                $webfontsId = $this->executeGenericInsertQuery($sql);
                return $webfontsId;
            } else {
                return $errorMsg;
            }

        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Update web font category
     *
     *@param (String)apikey
     *@param (String)categoryName
     *@param (Int)categoryId
     *@return json data
     *
     */
    public function editWebFontCategory()
    {
        try {
            $categoryId = $this->_request['categoryId'];
            $categoryName = $this->_request['categoryName'];
            $sql = "update " . TABLE_PREFIX . "font_category set category_name ='$categoryName' where font_category.id = $categoryId";
            $row = $this->executeGenericDMLQuery($sql);
            $response = array();
            $response['status'] = true;
            $response['message'] = "category name changed to $categoryName";

        } catch (Exception $e) {
            $response = array('Caught exception:' => $e->getMessage());
        }
        $this->response($this->json($response), 200);
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *FUNCTION USED FOR GET ALL FONT URLS
     *
     *@param (String)apikey
     *@param (Int)lastfontid
     *@return json data
     *
     */
    public function getFontUrl()
    {
        $lastfontid = $this->_request['lastfontid'];
        try {
            $sql = "SELECT id,orgName FROM (select id,orgName from " . TABLE_PREFIX . "fonts WHERE id >$lastfontid ORDER BY id DESC) AS virtual_table GROUP BY orgName ORDER BY virtual_table.id";
            $fontURLArr = array();
            $res = $this->executeFetchAssocQuery($sql);
            if (!empty($res)) {
                $url = $this->getCurrentUrl() . self::ASSETS_CONTAINER_DIR . self::HTML5_WEBFONTS_DIR;
                foreach ($res as $v) {
                    $fontURLArr[] = array(
                        "font_url" => $url . str_replace(' ', '_', $v['orgName']) . '.ttf',
                        "font_id" => $v['id'],
                    );
                }
            }
            $this->response($this->json($fontURLArr), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *get Webfonts ttf file path
     *
     *@return base image url
     *
     */
    protected function getWebfontsTtfPath()
    {
        $baseImagePath = $this->getBasePath() . self::HTML5_WEBFONTS_DIR;
        return $baseImagePath;
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Add Font category
     *
     *@param (String)apikey
     *@param (String)category_name
     *@return json data
     *
     */
    public function addFontCategory()
    {
        $status = 0;
        try {
            if (!empty($this->_request) && isset($this->_request['category_name']) && $this->_request['category_name']) {
                $sql = "INSERT INTO " . TABLE_PREFIX . "font_category (category_name) VALUES ('" . $this->_request['category_name'] . "')";
                $status = $this->executeGenericDMLQuery($sql);
            }
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

}
