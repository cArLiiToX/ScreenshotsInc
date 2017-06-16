<?php
/* Check Un-authorize Access */
if (!defined('accessUser')) {
    die("Error");
}

class Design extends UTIL
{
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Get design category
     *
     *@param (String)apikey
     *@param (String)printId
     *@return json data
     *
     */
    public function allDesignCatagory()
    {
        try {
            if (isset($this->_request['printId']) && ($this->_request['printId']) != '') {
                $catagoryArray = array();
                $sql = "SELECT dc.id,dc.category_name,dc.sort_order FROM " . TABLE_PREFIX . "des_cat dc join " . TABLE_PREFIX . "design_category_printmethod_rel dcppr
                 on dcppr.design_category_id =dc.id where dcppr.print_method_id='" . $this->_request['printId'] . "' ORDER BY dc.sort_order";
            } else {
                $sql = "SELECT id,category_name FROM " . TABLE_PREFIX . "des_cat ORDER BY sort_order";
            }
            $categoryDetail = array();
            $rows = $this->executeGenericDQLQuery($sql);
            for ($i = 0; $i < sizeof($rows); $i++) {
                $categoryDetail[$i]['id'] = $rows[$i]['id'];
                $categoryDetail[$i]['category_name'] = $rows[$i]['category_name'];
            }
            $this->response($this->json($categoryDetail, 1), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }
    /**
     *
     *date created 9-9-2015 (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Get all design tags
     *
     *@param (String)apikey
     *@return json data
     *
     */
    public function allDesignTags()
    {
        try {
            $tagArray = array();
            $sql = "SELECT * FROM " . TABLE_PREFIX . "des_tags";
            $allTag = $this->executeGenericDQLQuery($sql);
            foreach ($allTag as $row) {
                array_push($tagArray, $row['name']);
            }
            $this->closeConnection();
            $this->response($this->json($tagArray, 1), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }
    /**
     *
     *date created 9-9-2015 (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Fetch design by search
     *
     *@param (String)apikey
     *@param (Int)categoryValue
     *@param (Int)subCategoryValue
     *@param (String)searchval
     *@param (Int)lastLoaded
     *@param (Int)loadCount
     *@param (Int)print_method
     *@return json data
     *
     */
    public function fetchDesignsBySearch()
    {
        $categoryValue = $this->_request['categoryValue'];
        $subCategoryValue = $this->_request['subCategoryValue'];
        $searchval = $this->_request['searchval'];
        $designLastLoaded = $this->_request['lastLoaded'];
        $designLimit = $this->_request['loadCount'];
        $print_method = $this->_request['print_method'];
        $defaultCount = $this->_request['default_count'];
        $exesql = '';

        try {
            if (isset($print_method) && $print_method != '') {
                if ($categoryValue == 0 && $searchval == '' && $subCategoryValue == 0 && $defaultCount == 0) {
                    $csql = 'SELECT ds.id FROM ' . TABLE_PREFIX . 'des_cat AS ds join ' . TABLE_PREFIX . 'design_category_printmethod_rel AS cp on ds.id = cp.design_category_id WHERE is_default = "1" AND print_method_id="' . $print_method . '"';
                    $result = $this->executeGenericDQLQuery($csql);
                    if ($result) {
                        $categoryValue = $result[0]['id'];
                    } else {
                        $rsql = 'SELECT dcs.category_id,dcs.sub_category_id FROM ' . TABLE_PREFIX . 'des_cat_sub_cat_rel AS dcs join ' . TABLE_PREFIX . 'design_category_printmethod_rel AS cp on dcs.category_id = cp.design_category_id WHERE is_default = "1" AND print_method_id="' . $print_method . '"';
                        $result1 = $this->executeGenericDQLQuery($rsql);
                        if ($result1) {
                            $categoryValue = $result1[0]['category_id'];
                            $subCategoryValue = $result1[0]['sub_category_id'];
                        } else {
                            $categoryValue = 0;
                            $subCategoryValue = 0;
                        }
                    }
                    $defaultCount = 1;
                }
                if ($categoryValue == 0 && $searchval == '' && $subCategoryValue == 0 && $defaultCount == 1) {
                    $sql = "select distinct d.id,d.design_name ,d.file_name,d.price , d.isScalable,d.is_svgasfile,d.aheight,d.awidth from " . TABLE_PREFIX . "designs d
                    join " . TABLE_PREFIX . "design_category_sub_category_rel dcsr on d.id = dcsr.design_id
                    left join " . TABLE_PREFIX . "design_category_printmethod_rel dcppr on dcsr.category_id=dcppr.design_category_id
                    WHERE dcppr.print_method_id='" . $print_method . "'";
                }
                if (isset($searchval) && $searchval != '' && $categoryValue == 0 && $defaultCount == 1) {
                    $sql = "select distinct d.id,d.design_name ,d.file_name,d.price , d.isScalable,d.is_svgasfile,d.aheight,d.awidth from " . TABLE_PREFIX . "designs d join " . TABLE_PREFIX . "design_category_sub_category_rel dcsr on d.id = dcsr.design_id
                    left join " . TABLE_PREFIX . "design_category_printmethod_rel dcppr on dcsr.category_id=dcppr.design_category_id
                    left join " . TABLE_PREFIX . "des_tag_rel dtr on d.id = dtr.design_id  left join " . TABLE_PREFIX . "des_tags t on dtr.tag_id = t.id
                    WHERE dcppr.print_method_id='" . $print_method . "' and d.design_name like '%$searchval%' or t.name like '%$searchval%' ";
                }
                if ($categoryValue != 0 && $searchval != '' && $defaultCount == 1) {
                    $sql = "select distinct d.id,d.design_name ,d.file_name,d.price , d.isScalable,d.is_svgasfile,d.aheight,d.awidth from " . TABLE_PREFIX . "designs d
                    left join " . TABLE_PREFIX . "des_tag_rel dtr on d.id = dtr.design_id
                    left join " . TABLE_PREFIX . "des_tags t on dtr.tag_id = t.id
                    left join " . TABLE_PREFIX . "design_category_sub_category_rel dcsr on d.id = dcsr.design_id
                    left join " . TABLE_PREFIX . "design_category_printmethod_rel dcppr on dcsr.category_id=dcppr.design_category_id
                    WHERE  dcsr.category_id =$categoryValue and dcppr.print_method_id='" . $print_method . "' and ((d.design_name like '%$searchval%') or (t.name like '%$searchval%')) ";
                }
                if ($categoryValue != 0 && $searchval == '' && $defaultCount == 1) {
                    $sql = "select distinct d.id,d.design_name ,d.file_name,d.price , d.isScalable,d.is_svgasfile,d.aheight,d.awidth from " . TABLE_PREFIX . "designs d
                    left join " . TABLE_PREFIX . "design_category_sub_category_rel dcsr on d.id = dcsr.design_id
                    left join " . TABLE_PREFIX . "design_category_printmethod_rel dcppr on dcsr.category_id=dcppr.design_category_id
                    WHERE dcppr.print_method_id='" . $print_method . "' AND dcsr.category_id = '" . $categoryValue . "'";
                    if ($categoryValue != 0 && $searchval == '' && $subCategoryValue != 0 && $defaultCount == 1) {
                        $sql = "select distinct d.id,d.design_name ,d.file_name,d.price , d.isScalable,d.is_svgasfile,d.aheight,d.awidth from " . TABLE_PREFIX . "designs d
                        left join " . TABLE_PREFIX . "design_category_sub_category_rel dcsr on d.id = dcsr.design_id
                        left join " . TABLE_PREFIX . "design_category_printmethod_rel dcppr on dcsr.category_id=dcppr.design_category_id
                        WHERE dcppr.print_method_id='" . $print_method . "' AND dcsr.category_id = '" . $categoryValue . "' AND dcsr.sub_category_id = '" . $subCategoryValue . "'";
                    }

                }
                $exesql = $sql;
                $exesql = $exesql . ' ORDER BY d.id DESC ';
                $rows = $this->executeGenericDQLQuery($exesql);
                $count = sizeof($rows);
                $exesql .= " LIMIT $designLastLoaded, $designLimit";
                $rows = $this->clearArray($rows);
                $rows = $this->executeGenericDQLQuery($exesql);
                $designArray = array();
                for ($i = 0; $i < sizeof($rows); $i++) {
                    $designArray[$i]['id'] = intval($rows[$i]['id']);
                    $designArray[$i]['name'] = $rows[$i]['design_name'];
                    if (file_exists($this->getDesignImagePath() . 'thumb_' . $rows[$i]['file_name'])) {
                        $designArray[$i]['url'] = $this->getDesignImageURL() . 'thumb_' . $rows[$i]['file_name'];
                        $designArray[$i]['is_thumb'] = '1';
                    } else {
                        $designArray[$i]['url'] = $this->getDesignImageURL() . $rows[$i]['file_name'];
                        $designArray[$i]['is_thumb'] = '0';
                    }
                    $designArray[$i]['file_name'] = $rows[$i]['file_name'];
                    $designArray[$i]['price'] = $rows[$i]['price'];
                    $designArray[$i]['count'] = $count;
                    $designArray[$i]['isScalable'] = $rows[$i]['isScalable'];
                    $designArray[$i]['is_svgasfile'] = $rows[$i]['is_svgasfile'];
                    $designArray[$i]['actual_height'] = $rows[$i]['aheight'];
                    $designArray[$i]['actual_width'] = $rows[$i]['awidth'];
                }
            } else {
                $designArray['status'] = 'nodata';
            }
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
        $this->response($this->json($designArray, 1), 200);
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *search  Designs in admin
     *
     *@param (String)apikey
     *@param (String)categoryValue
     *@param (String)searchString
     *@param (Int)lastLoaded
     *@param (Int)loadCount
     *@param (String)subCategoryValue
     *@return json data
     *
     */
    public function fetchDesignsBySearchForAdmin()
    {
        try {
            $categoryValue = $this->_request['categoryValue'];
            $subCategoryValue = $this->_request['subCategoryValue'];
            $searchval = $this->_request['searchval'];
            $designLastLoaded = $this->_request['lastLoaded'];
            $designLimit = $this->_request['loadCount'];
            $sql = "select distinct d.id,d.design_name ,d.file_name,d.price ,d.isScalable,d.is_svgasfile,d.aheight,d.awidth   from " . TABLE_PREFIX . "designs d ";
            $joinText = '';
            if ($searchval == '' || $searchval == 'undefined') {
                if ($categoryValue != "All" && $categoryValue != "undefined" && $categoryValue != "") {
                    $joinText .= " join  " . TABLE_PREFIX . "design_category_sub_category_rel dcsr on d.id = dcsr.design_id join " . TABLE_PREFIX . "des_cat c on dcsr.category_id = c.id";
                    if ($subCategoryValue != "All" && $subCategoryValue != "undefined" && $subCategoryValue != "") {
                        $joinText .= " join " . TABLE_PREFIX . "des_sub_cat s on dcsr.sub_category_id = s.id";
                        $joinText .= " where c.category_name = '$categoryValue' and s.name = '$subCategoryValue'";
                    } else {
                        $joinText .= " where c.category_name = '$categoryValue'";
                    }
                }
            } else if ($searchval != '') {
                $joinText .= " , des_tags dt , des_tag_rel dtr where d.id = dtr.design_id and dt.id = dtr.tag_id and dt.name like '%$searchval%'";
            }
            $sql .= $joinText;

            $sql .= " ORDER BY d.id DESC ";
            // gettting total number of records present based on condition
            $rows = $this->executeGenericDQLQuery($sql);
            $count = sizeof($rows);
            $sql .= " LIMIT $designLastLoaded, $designLimit";
            //getting deatiles of records by limitations
            $rows = $this->clearArray($rows);
            $rows = $this->executeGenericDQLQuery($sql);
            $designArray = array();
            for ($i = 0; $i < sizeof($rows); $i++) {
                $designArray[$i]['id'] = $rows[$i]['id'];
                $designArray[$i]['name'] = $rows[$i]['design_name'];
                if (file_exists($this->getDesignImagePath() . 'thumb_' . $rows[$i]['file_name'])) {
                    $designArray[$i]['url'] = $this->getDesignImageURL() . 'thumb_' . $rows[$i]['file_name'];
                    $designArray[$i]['is_thumb'] = '1';
                } else {
                    $designArray[$i]['url'] = $this->getDesignImageURL() . $rows[$i]['file_name'];
                    $designArray[$i]['is_thumb'] = '0';
                }
                $designArray[$i]['file_name'] = $rows[$i]['file_name'];
                $designArray[$i]['price'] = $rows[$i]['price'];
                $designArray[$i]['isScalable'] = $rows[$i]['isScalable'];
                $designArray[$i]['is_svgasfile'] = $rows[$i]['is_svgasfile'];
                $designArray[$i]['actual_height'] = $rows[$i]['aheight'];
                $designArray[$i]['actual_width'] = $rows[$i]['awidth'];
            }
            $sql = "SELECT COUNT(id) as total FROM " . TABLE_PREFIX . "designs";
            $countDesign = $this->executeGenericDQLQuery($sql);
            $x = array();
            $x['count'] = $count;
            $x['total_count'] = $countDesign[0]['total'];
            $x['designs'] = $designArray;
            $this->closeConnection();
            $this->response($this->json($x, 1), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }
    /**
     *
     *date created 9-9-2015 (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Get design present
     *
     *@param (String)apikey
     *@return json data
     *
     */
    public function fetchSubCategory()
    {
        $sql = "select sc.id as id, sc.name as sub_category,sc.sort_order from " . TABLE_PREFIX . "des_sub_cat sc JOIN " . TABLE_PREFIX . "des_cat_sub_cat_rel cscr ON sc.id = cscr.sub_category_id JOIN " . TABLE_PREFIX . "des_cat c ON c.id = cscr.category_id";
        if (isset($this->_request['selectedCategory']) && $this->_request['selectedCategory']) {
            $sql .= ' where c.id =' . $this->_request['selectedCategory'];
        }
        $sql .= ' ORDER BY sc.sort_order';
        $rows = $this->executeGenericDQLQuery($sql);
        $sub_category_detail = array();
        for ($i = 0; $i < sizeof($rows); $i++) {
            $sub_category_detail[$i]['id'] = $rows[$i]['id'];
            $sub_category_detail[$i]['sub_category'] = $rows[$i]['sub_category'];
        }
        $this->closeConnection();
        $this->response($this->json($sub_category_detail, 1), 200);
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Add Bulk Design
     *
     *@param (String)apikey
     *@param (Array)tags
     *@param (String) design_name
     *@param (Enm) isScalable
     *@param (Float)price
     *@param (enm)is_svgasfile
     *@param (File)files
     *@param (int)id
     *@return json data
     *
     */
    public function addBulkDesign()
    {
        $status = 0;
        try {
            if (!empty($this->_request) && isset($this->_request['design_name']) && isset($this->_request['isScalable']) && isset($this->_request['is_svgasfile'])) {
                $sql = array();
                $fname = array();
                if (!empty($this->_request['files'])) {
                    $dir = $this->getDesignImagePath();
                    if (!$dir) {
                        $this->response('', 204);
                    }
                    //204 - immediately termiante this request
                    if (!file_exists($dir)) {
                        mkdir($dir, 0777, true);
                    }

                    $usql1 = '';
                    $usql2 = '';
                    $cat_scat_rel_sql = '';
                    $print_method_re_sql = '';
                    $design_tag_rel_sql = '';
                    $tag_arr = array();
                    if (!empty($this->_request['tags'])) {
                        foreach ($this->_request['tags'] as $k => $v) {
                            $v = addslashes($v);
                            $tag_sql = "SELECT id,count( * ) AS nos FROM " . TABLE_PREFIX . "des_tags WHERE name = '" . $v . "'";
                            $res = $this->executeFetchAssocQuery($tag_sql);
                            if (!$res[0]['nos']) {
                                $tag_sql1 = "INSERT INTO " . TABLE_PREFIX . "des_tags(name) VALUES('" . $v . "')";
                                $tag_arr[] = $this->executeGenericInsertQuery($tag_sql1);
                            } else {
                                $tag_arr[] = $res[0]['id'];
                            }
                        }
                    }

                    $this->_request['no_of_colors'] = (isset($this->_request['no_of_colors']) && $this->_request['no_of_colors']) ? $this->_request['no_of_colors'] : 0;
                    $this->_request['actual_height'] = (isset($this->_request['actual_height']) && $this->_request['actual_height']) ? $this->_request['actual_height'] : 0.00;
                    $this->_request['actual_width'] = (isset($this->_request['actual_width']) && $this->_request['actual_width']) ? $this->_request['actual_width'] : 0.00;
                    $this->_request['price'] = (isset($this->_request['price']) && $this->_request['price']) ? $this->_request['price'] : 0.00;
                    foreach ($this->_request['files'] as $k => $v) {
                        $sql[$k] = "INSERT INTO " . TABLE_PREFIX . "designs (design_name, price,isScalable,no_of_colors,is_svgasfile,aheight,awidth) VALUES ('" . addslashes($this->_request['design_name']) . "','" . $this->_request['price'] . "','" . $this->_request['isScalable'] . "','" . $this->_request['no_of_colors'] . "','" . $this->_request['is_svgasfile'] . "','" . $this->_request['actual_height'] . "','" . $this->_request['actual_width'] . "')";
                        $design_id[$k] = $this->executeGenericInsertQuery($sql[$k]);
                        $fname[$k] = $design_id[$k] . '.' . $v['type'];
                        $thumbBase64Data[$k] = base64_decode($v['base64']);
                        file_put_contents($dir . $fname[$k], $thumbBase64Data[$k]);
                        list($width[$k], $height[$k]) = getimagesize($dir . $fname[$k]);

                        if ($v['type'] != 'svg' && ($width[$k] >= 1024 || $height[$k] >= 800)) {
                            $resizeImage = $this->resize($dir . $fname[$k], $dir . 'thumb_' . $fname[$k], 80, 80);
                            if ($resizeImage != true) {
                                $this->log('SAVEIMAGE :: Create Thumbnail:' . $resizeImage);
                                $msg = array("status" => "Thumbnail generation failed");
                            }
                        }
                        $usql1 .= ' WHEN ' . $design_id[$k] . " THEN '" . $fname[$k] . "'";
                        $usql2 .= ',' . $design_id[$k];

                        if (!empty($this->_request['category_id'])) {
                            foreach ($this->_request['category_id'] as $k1 => $v1) {
                                $scatid[$k1] = (isset($this->_request['sub_category_id'][$k1]) && $this->_request['sub_category_id'][$k1]) ? $this->_request['sub_category_id'][$k1] : 0;
                                $cat_scat_rel_sql .= ",('" . $design_id[$k] . "','" . $v1 . "','" . $scatid[$k1] . "')";
                            }
                        }
                        if (!empty($this->_request['print_method_id'])) {
                            foreach ($this->_request['print_method_id'] as $v2) {
                                $print_method_re_sql .= ",('" . $design_id[$k] . "','" . $v2 . "')";
                            }
                        }
                        if (!empty($tag_arr)) {
                            foreach ($tag_arr as $v3) {
                                $design_tag_rel_sql .= ",('" . $design_id[$k] . "','" . $v3 . "')";
                            }
                        }
                    }
                    if (strlen($usql1) && strlen($usql2)) {
                        $usql = 'UPDATE ' . TABLE_PREFIX . 'designs SET file_name = CASE id' . $usql1 . ' END WHERE id IN(' . substr($usql2, 1) . ')';
                        $status = $this->executeGenericDMLQuery($usql);
                    }
                    if (strlen($cat_scat_rel_sql)) {
                        $cat_scat_rel_sql = "INSERT INTO " . TABLE_PREFIX . "design_category_sub_category_rel (design_id,category_id,sub_category_id) VALUES " . substr($cat_scat_rel_sql, 1);
                        $status = $this->executeGenericDMLQuery($cat_scat_rel_sql);
                    }
                    if (strlen($print_method_re_sql)) {
                        $print_method_re_sql = "INSERT INTO " . TABLE_PREFIX . "print_method_design_rel (design_id,print_method_id) VALUES " . substr($print_method_re_sql, 1);
                        $status = $this->executeGenericDMLQuery($print_method_re_sql);
                    }
                    if (strlen($design_tag_rel_sql)) {
                        $design_tag_rel_sql = "INSERT INTO " . TABLE_PREFIX . "des_tag_rel (design_id,tag_id) VALUES " . substr($design_tag_rel_sql, 1);
                        $status = $this->executeGenericDMLQuery($design_tag_rel_sql);
                    }
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
     *Get design details
     *
     *@param (String)apikey
     *@param (int)design_id
     *@return json data
     *
     */
    public function getDesignDetails()
    {
        try {
            $designId = $this->_request['design_id'];
            $designData = array();
            $sql = "select d.id as design_id ,d.file_name,d.no_of_colors,d.design_name,d.isScalable,d.price,d.status,d.is_svgasfile,d.aheight,d.awidth,c.id as category_id, c.category_name as category_name, s.id as sub_category_id, s.name as sub_category_name
            from " . TABLE_PREFIX . "designs d left join  " . TABLE_PREFIX . "design_category_sub_category_rel dcsr  on  d.id =dcsr.design_id
            left join  " . TABLE_PREFIX . "des_cat c on dcsr.category_id = c.id
            left join " . TABLE_PREFIX . "des_sub_cat s on dcsr.sub_category_id = s.id where d.id = $designId";
            $rows = $this->executeGenericDQLQuery($sql);
            //fetching design detailes
            $designData['design_detail'] = array();
            $designData['design_detail']['design_id'] = $rows[0]['design_id'];
            $designData['design_detail']['file_name'] = $rows[0]['file_name'];
            $designData['design_detail']['design_name'] = $rows[0]['design_name'];
            $designData['design_detail']['price'] = $rows[0]['price'];
            $designData['design_detail']['status'] = $rows[0]['status'];
            $designData['design_detail']['isScalable'] = $rows[0]['isScalable'];
            $designData['design_detail']['no_of_colors'] = $rows[0]['no_of_colors'];
            $designData['design_detail']['is_svgasfile'] = $rows[0]['is_svgasfile'];
            $designData['design_detail']['actual_height'] = $rows[0]['aheight'];
            $designData['design_detail']['actual_width'] = $rows[0]['awidth'];

            //fetching categories and sub categories
            $designData['category_sub_category'] = array();
            for ($i = 0; $i < sizeof($rows); $i++) {
                $designData['category_sub_category'][$i]['category_id'] = $rows[$i]['category_id'];
                $designData['category_sub_category'][$i]['category_name'] = $rows[$i]['category_name'];
                $designData['category_sub_category'][$i]['sub_category_id'] = $rows[$i]['sub_category_id'];
                $designData['category_sub_category'][$i]['sub_category_name'] = $rows[$i]['sub_category_name'];
            }
            // fetching tags
            $sql = "select distinct dt.id as tag_id , dt.name as tag_name from " . TABLE_PREFIX . "designs d , " . TABLE_PREFIX . "des_tags dt , " . TABLE_PREFIX . "des_tag_rel dtr where
            d.id = dtr.design_id and dt.id = dtr.tag_id and d.id = $designId";
            $rows = $this->clearArray($rows);
            $rows = $this->executeGenericDQLQuery($sql);
            $dbTagArr = array();
            $designData['tag'] = array();
            for ($i = 0; $i < sizeof($rows); $i++) {
                $temp = array();
                $temp['tag_id'] = $rows[$i]['tag_id'];
                $temp['tag_name'] = $rows[$i]['tag_name'];
                array_push($dbTagArr, $temp);
            }
            $designData['tag'] = $dbTagArr;
            $this->closeConnection();
            $this->response($this->json($designData, 1), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Update Design
     *
     *@param (String)apikey
     *@param (String) design_name
     *@param (Enm) isScalable
     *@param (Float)price
     *@param (enm)no_of_colors
     *@param (File)isSvgAsFile
     *@param (int)id
     *@return json data
     *
     */
    public function updateDesign()
    {
        $status = 0;
        try {
            if (!empty($this->_request) && isset($this->_request['design_name']) && isset($this->_request['isScalable'])) {
                if (!empty($this->_request['id'])) {
                    $id_str = implode(',', $this->_request['id']);

                    $this->_request['no_of_colors'] = (isset($this->_request['no_of_colors']) && $this->_request['no_of_colors']) ? $this->_request['no_of_colors'] : 0;
                    $this->_request['actual_height'] = (isset($this->_request['actual_height']) && $this->_request['actual_height']) ? $this->_request['actual_height'] : 0.00;
                    $this->_request['actual_width'] = (isset($this->_request['actual_width']) && $this->_request['actual_width']) ? $this->_request['actual_width'] : 0.00;
                    $this->_request['price'] = (isset($this->_request['price']) && $this->_request['price']) ? $this->_request['price'] : 0.00;

                    $sql = "UPDATE " . TABLE_PREFIX . "designs SET design_name = '" . $this->_request['design_name'] . "',isScalable = '" . $this->_request['isScalable'] . "',price = '" . $this->_request['price'] . "',no_of_colors = '" . $this->_request['no_of_colors'] . "',is_svgasfile = '" . $this->_request['isSvgAsFile'] . "',aheight = '" . $this->_request['actual_height'] . "',awidth = '" . $this->_request['actual_width'] . "' WHERE id IN(" . $id_str . ")";
                    $status = $this->executeGenericDMLQuery($sql);
                    $sql = "DELETE FROM " . TABLE_PREFIX . "design_category_sub_category_rel WHERE design_id IN(" . $id_str . ")";
                    $status = $this->executeGenericDMLQuery($sql);
                    //$sql = "DELETE FROM ".TABLE_PREFIX."print_method_design_rel WHERE design_id IN(".$id_str.")";$status = $this->executeGenericDMLQuery($sql);
                    $sql = "DELETE FROM " . TABLE_PREFIX . "des_tag_rel WHERE design_id IN(" . $id_str . ")";
                    $status = $this->executeGenericDMLQuery($sql);
                    $cat_scat_rel_sql = '';
                    $print_method_re_sql = '';
                    $design_tag_rel_sql = '';
                    $tag_arr = array();
                    if (!empty($this->_request['tags'])) {
                        foreach ($this->_request['tags'] as $k => $v) {
                            $v = addslashes($v);
                            $tag_sql = "SELECT id,count( * ) AS nos FROM " . TABLE_PREFIX . "des_tags WHERE name = '" . $v . "'";
                            $res = $this->executeFetchAssocQuery($tag_sql);
                            if (!$res[0]['nos']) {
                                $tag_sql1 = "INSERT INTO " . TABLE_PREFIX . "des_tags(name) VALUES('" . $v . "')";
                                $tag_arr[] = $this->executeGenericInsertQuery($tag_sql1);
                            } else {
                                $tag_arr[] = $res[0]['id'];
                            }
                        }
                    }

                    foreach ($this->_request['id'] as $k => $v) {
                        $design_id[$k] = $v;
                        if (!empty($this->_request['category_id'])) {
                            foreach ($this->_request['category_id'] as $k1 => $v1) {
                                $scatid[$k1] = (isset($this->_request['sub_category_id'][$k1]) && $this->_request['sub_category_id'][$k1]) ? $this->_request['sub_category_id'][$k1] : 0;
                                $cat_scat_rel_sql .= ",('" . $design_id[$k] . "','" . $v1 . "','" . $scatid[$k1] . "')";
                            }
                        }
                        /* if(!empty($this->_request['print_method_id'])){
                        foreach($this->_request['print_method_id'] as $v2){
                        $print_method_re_sql .= ",('".$design_id[$k]."','".$v2."')";
                        }
                        } */
                        if (!empty($tag_arr)) {
                            foreach ($tag_arr as $v3) {
                                $design_tag_rel_sql .= ",('" . $design_id[$k] . "','" . $v3 . "')";
                            }
                        }
                    }
                    if (strlen($cat_scat_rel_sql)) {
                        $cat_scat_rel_sql = "INSERT INTO " . TABLE_PREFIX . "design_category_sub_category_rel (design_id,category_id,sub_category_id) VALUES " . substr($cat_scat_rel_sql, 1);
                        $status = $this->executeGenericDMLQuery($cat_scat_rel_sql);
                    }
                    /* if(strlen($print_method_re_sql)){
                    $print_method_re_sql = "INSERT INTO ".TABLE_PREFIX."print_method_design_rel (design_id,print_method_id) VALUES ".substr($print_method_re_sql,1);
                    $status = $this->executeGenericDMLQuery($print_method_re_sql);
                    } */
                    if (strlen($design_tag_rel_sql)) {
                        $design_tag_rel_sql = "INSERT INTO " . TABLE_PREFIX . "des_tag_rel (design_id,tag_id) VALUES " . substr($design_tag_rel_sql, 1);
                        $status = $this->executeGenericDMLQuery($design_tag_rel_sql);
                    }
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
     *add category
     *
     *@param (String)apikey
     *@param (String)category
     *@return json data
     *
     */
    public function addCategory()
    {
        try {
            $pCategory = addslashes($this->_request['category']);
            $sql = "select count(*) count from " . TABLE_PREFIX . "des_cat where category_name = '$pCategory'";
            $row = $this->executeGenericDQLQuery($sql);
            $response = array();
            if ($row[0]['count'] == "0") {
                $sql = "select id from " . TABLE_PREFIX . "des_cat ORDER BY id DESC";
                $result = $this->executeGenericDQLQuery($sql);
                $order = $result[0][0];
                if ($order == '') {
                    $order = 0;
                }

                $sql = "insert into " . TABLE_PREFIX . "des_cat(category_name,sort_order) values('$pCategory','$order')";
                $this->executeGenericDMLQuery($sql);
                $response['status'] = "success";
                $response['message'] = ' category inserted';
            } else {
                $response['status'] = "fail";
                $response['message'] = ' category already present';
            }
            $this->closeConnection();
            $this->response($this->json($response, 1), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *add sub category to category
     *
     *@param (String)apikey
     *@param (int)pCategoryId
     *@param (int)pSubCategory
     *@return json data
     *
     */
    public function addSubCategoryToCategory()
    {
        $categoryId = $this->_request['pCategoryId'];
        $subCategoryId = $this->_request['pSubCategory'];
        $subCategoryId = $this->addSubCategory();
        // insert into cat-sub-cat rel
        try {
            $sql = "SELECT count(*) AS duplicate FROM " . TABLE_PREFIX . "des_cat_sub_cat_rel WHERE category_id = '" . $categoryId . "' AND sub_category_id = '" . $subCategoryId . "'";
            $res = $this->executeFetchAssocQuery($sql); //echo '<pre>';print_r($res);exit;
            if (!empty($res) && $res[0]['duplicate'] == 0) {
                $sql = "insert into " . TABLE_PREFIX . "des_cat_sub_cat_rel(category_id , sub_category_id) values($categoryId,$subCategoryId)";
                $this->executeGenericDMLQuery($sql);
                $response['status'] = true;
                $response['message'] = "sub category  is added under category";
            } else {
                $response['status'] = false;
                $response['message'] = "duplicate sub category is added under category";
            }
            $this->closeConnection();
            $this->response($this->json($response), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *upadte Design category name
     *
     * @param (String)apikey
     * @param (int)id
     * @param (String)name
     * @return json data
     *
     */
    public function updateDesignCategory()
    {
        $status = 0;
        if (!empty($this->_request) && $this->_request['id'] && isset($this->_request['name'])) {
            extract($this->_request);
            $name = addslashes($name);
            try {
                $chk_duplicate = "SELECT COUNT(*) AS duplicate FROM " . TABLE_PREFIX . "des_cat WHERE category_name='" . $name . "' AND id !='" . $id . "'";
                $res = $this->executeFetchAssocQuery($chk_duplicate);
                if ($res[0]['duplicate']) {
                    $msg['msg'] = 'Duplicate Entry';
                } else {
                    $sql = "UPDATE " . TABLE_PREFIX . "des_cat SET category_name = '" . $name . "' WHERE id='" . $id . "'";
                    $status = $this->executeGenericDMLQuery($sql);
                }
                $msg['status'] = ($status) ? 'success' : 'failed';
            } catch (Exception $e) {
                $msg = array('Caught exception:' => $e->getMessage());
            }
        } else {
            $msg['status'] = 'nodata';
        }

        $this->response($this->json($msg, 1), 200);
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *upadte Shape category name
     *
     * @param (String)apikey
     * @param (int)id
     * @param (String)name
     * @return json data
     *
     */
    public function updateDesignSubCategory()
    {
        $status = 0;
        if (!empty($this->_request) && $this->_request['cid'] && $this->_request['sid'] && isset($this->_request['name'])) {
            extract($this->_request);
            $name = addslashes($name);
            try {
                $chk_duplicate = "SELECT COUNT( * ) AS duplicate FROM " . TABLE_PREFIX . "des_sub_cat AS s JOIN " . TABLE_PREFIX . "des_cat_sub_cat_rel AS rel ON rel.sub_category_id = s.id WHERE rel.category_id ='" . $cid . "' AND rel.sub_category_id !='" . $sid . "' AND s.name = '" . $name . "'";
                $res = $this->executeFetchAssocQuery($chk_duplicate);
                if ($res[0]['duplicate']) {
                    $msg['msg'] = 'Duplicate Entry';
                } else {
                    $sql = "UPDATE " . TABLE_PREFIX . "des_sub_cat SET name = '" . $name . "' WHERE id='" . $sid . "'";
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
     *date modified 15-4-2016 (dd-mm-yy)
     *remove sub category from category
     *
     *@param (String)apikey
     *@param (int)selectedCategoryId
     *@param (int)selectedSubCategoryId
     *@return json data
     *
     */
    public function removeSubCategoryFromCategory()
    {
        try {
            $categoryId = $this->_request['selectedCategoryId'];
            $subCategoryId = $this->_request['selectedSubCategoryId'];
            $sql = "select  cscr.sub_category_id from " . TABLE_PREFIX . "des_cat_sub_cat_rel cscr  where cscr.sub_category_id = $subCategoryId ";
            //$row = $this->clearArray($row);
            $rows = $this->executeGenericDQLQuery($sql);
            $response = array();
            if (sizeof($rows) == 0) {
                $response['status'] = false;
                $response['message'] = "Error !! $pSubCategory is not  under category $pCategory";
            } else {
                if (sizeof($rows) == 1) {
                    // delete sub category from table if it is assign to only one category
                    $sql = "delete from  " . TABLE_PREFIX . "des_sub_cat where id =$subCategoryId ";
                    $this->executeGenericDMLQuery($sql);
                }
                $sql = "delete from  " . TABLE_PREFIX . "des_cat_sub_cat_rel where category_id =$categoryId  and sub_category_id=$subCategoryId ";
                $this->executeGenericDMLQuery($sql);
                $sql = "update " . TABLE_PREFIX . "design_category_sub_category_rel set sub_category_id = 0 where category_id = $categoryId and sub_category_id = $subCategoryId";
                $this->executeGenericDMLQuery($sql);
                $response['status'] = true;
                $response['message'] = "sub cateogry deleted successfully  !!!";
            }
            $this->closeConnection();
            $this->response($this->json($response), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created 9-9-2015 (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Get design present
     *
     *@param (String)apikey
     *@return json data
     *
     */
    public function totalDesignPresent()
    {
        try {
            $sql = "SELECT id FROM " . TABLE_PREFIX . "designs";
            $result = mysqli_query($this->db, $sql);
            $this->closeConnection();
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Save design details
     *
     *@param (String)apikey
     *@param (String)fileName
     *@param (String)designName
     *@param (Object)category_subCategory
     *@param (String)tagsText
     *@param (Float)price
     *@param (int)isShape
     *@param (int)isScalable
     *@return json data
     *
     */
    public function saveDesignDetails()
    {
        try {
            $fileName = $this->_request['fileName'];
            $designName = $this->_request['designName'];
            // to be parse category sub category objects
            $category_subCategory_txt = $this->_request['category_subCategory'];
            $tagsText = $this->_request['tagsText'];
            $price = floatval($this->_request['price']);
            $isShape = $this->_request['isShape'];
            $isScalable = $this->_request['isScalable'];
            if ($isShape == "true") {
                $isShape = 1;
            } else {
                $isShape = 0;
            }
            $designId = $this->getDesignId($fileName, $designName, $price, $isShape, $isScalable);
            $tagIdArr = $this->getTagIdArr($tagsText);
            $this->mapDesignTagRel($designId, $tagIdArr);
            $this->mapDesign_category_subCategory_rel($designId, $category_subCategory_txt);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Get design and subcategory rel
     *
     *@param (String)apikey
     *@param (Array)cat_sub_cat_comb
     *@param (int)designId
     *@return json data
     *
     */
    public function mapDesign_category_subCategory_rel($designId, $category_subCategory_txt) //$designId,$category_subCategory_txt

    {
        $sql = '';
        try {
            $category_subCategory_arr = explode("*", $category_subCategory_txt);
            for ($i = 0; $i < sizeof($category_subCategory_arr); $i++) {
                // getting each combination of category - sub category
                $cat_sub_cat_comb = explode(",", $category_subCategory_arr[$i]);
                if ($cat_sub_cat_comb[1] == '') {
                    $cat_sub_cat_comb[1] = 0;
                }

                $sql = "insert into " . TABLE_PREFIX . "design_category_sub_category_rel(design_id, category_id, sub_category_id) values($designId ," . $cat_sub_cat_comb[0] . " ," . $cat_sub_cat_comb[1] . ")";
                $this->executeGenericInsertQuery($sql);
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
     *Get design ids
     *
     *@param (String)apikey
     *@param (String)fileName
     *@param (String)designName
     *@param (Float)price
     *@param (int)isShape
     *@param (int)isScalable
     *@return json data
     *
     */
    public function getDesignId($fileName, $designName, $price, $isShape, $isScalable)
    {
        /*trimming the data */
        $fileName = trim($fileName);
        $designName = trim($designName);
        $price = trim($price);
        $isShape = trim($isShape);
        try {
            $sql = "select id from " . TABLE_PREFIX . "designs  where file_name = '$fileName'";
            $row = $this->executeGenericDQLQuery($sql);
            $designId;
            if (sizeof($row) == 0) {
                // inser the detile of desings , get the id
                $sql = "insert into " . TABLE_PREFIX . "designs(design_name,file_name,price,is_shape,isScalable) values('$designName','$fileName',$price,$isShape,$isScalable)";
                $designId = $this->executeGenericInsertQuery($sql);
            } else {
                // get the id
                $designId = $row[0]['id'];
                $sql = "UPDATE " . TABLE_PREFIX . "designs set design_name = '$designName' , price = $price where id = $designId";
                $this->executeGenericDMLQuery($sql);
            }
            return $designId;
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Get Sub category ids
     *
     *@param (String)apikey
     *@param (Array)subCategoryText
     *@return json data
     *
     */
    public function getSubCategoryIdArr($subCategoryText)
    {
        try {
            $reqSubCategory = explode(",", $subCategoryText);
            $sql = "select id,name from " . TABLE_PREFIX . "des_sub_cat";
            $dbSubCategoryArr = $this->executeGenericDQLQuery($sql);
            $resSubCategoryArr = array();
            for ($i = 0; $i < sizeof($reqSubCategory); $i++) {
                $found = false;
                $reqSubCategory[$i] = trim($reqSubCategory[$i]);
                for ($j = 0; $j < sizeof($dbSubCategoryArr); $j++) {
                    if ($dbSubCategoryArr[$j]['name'] == $reqSubCategory[$i]) {
                        $found = true;
                        array_push($resSubCategoryArr, $dbSubCategoryArr[$j]['id']);
                        break;
                    }
                }
                if ($found == false && $reqSubCategory[$i] != '') {
                    // insert category and push the id
                    $sql = "insert into " . TABLE_PREFIX . "des_sub_cat(name) values('$reqSubCategory[$i]')";
                    $subCategoryId = $this->executeGenericInsertQuery($sql);
                    array_push($resSubCategoryArr, $subCategoryId);
                }
            }
            return $resSubCategoryArr;
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Get design details
     *
     *@param (String)apikey
     *@param (int)designId
     *@param (Array)subCategoryIdArr
     *@return json data
     *
     */
    public function mapDesignSubCategoryRel($designId, $subCategoryIdArr)
    {
        try {
            $sql = "select design_id , sub_category_id from " . TABLE_PREFIX . "des_sub_cat_rel";
            $rows = $this->executeGenericDQLQuery($sql);
            for ($j = 0; $j < sizeof($subCategoryIdArr); $j++) {
                $found = false;
                for ($k = 0; $k < sizeof($rows); $k++) {
                    if ($rows[$k]['design_id'] == $designId && $rows[$k]['sub_category_id'] == $subCategoryIdArr[$j]) {
                        $found = true;
                        break;
                    }
                }
                if ($found == false) {
                    $sql = "insert into " . TABLE_PREFIX . "des_sub_cat_rel(design_id,sub_category_id) values('$designId' , '$subCategoryIdArr[$j]')";
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
     *Get design details
     *
     *@param (String)apikey
     *@param (Array)designId
     *@param (Array)tagIdArr
     *@return json data
     *
     */
    public function mapDesignTagRel($designId, $tagIdArr)
    {
        try {
            $sql = "select design_id , tag_id from " . TABLE_PREFIX . "des_tag_rel";
            $rows = $this->executeGenericDQLQuery($sql);
            for ($j = 0; $j < sizeof($tagIdArr); $j++) {
                $found = false;
                for ($k = 0; $k < sizeof($rows); $k++) {
                    if ($rows[$k]['design_id'] == $designId && $rows[$k]['tag_id'] == $tagIdArr[$j]) {
                        $found = true;
                        break;
                    }
                }
                if ($found == false) {
                    $sql = "insert into " . TABLE_PREFIX . "des_tag_rel(design_id,tag_id) values('$designId' , '$tagIdArr[$j]')";
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
     *Get design details
     *
     *@param (String)apikey
     *@param (Array)categoryIdArr
     *@param (Array)subCategoryIdArr
     *@return json data
     *
     */
    public function mapCategory_subCategory_Rel($categoryIdArr, $subCategoryIdArr)
    {
        try {
            $sql = "select category_id , sub_category_id from " . TABLE_PREFIX . "des_cat_sub_cat_rel";
            $rows = $this->executeGenericDQLQuery($sql);
            for ($i = 0; $i < sizeof($categoryIdArr); $i++) {
                for ($j = 0; $j < sizeof($subCategoryIdArr); $j++) {
                    $found = false;
                    for ($k = 0; $k < sizeof($rows); $k++) {
                        if ($rows[$k]['category_id'] == $categoryIdArr[$i] && $rows[$k]['sub_category_id'] == $subCategoryIdArr[$j]) {
                            $found = true;
                            break;
                        }
                    }
                    if ($found == false) {
                        $sql = "insert into " . TABLE_PREFIX . "des_cat_sub_cat_rel(category_id,sub_category_id) values('$categoryIdArr[$i]' , '$subCategoryIdArr[$j]')";
                        $this->executeGenericInsertQuery($sql);
                    }

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
     *Fetch design tags
     *
     *@param (String)apikey
     *@return json data
     *
     */
    public function fetchDesignTags()
    {
        try {
            $sql = "select distinct t.name from " . TABLE_PREFIX . "des_tags t ";
            $rows = $this->clearArray($rows);
            $rows = $this->executeGenericDQLQuery($sql);
            $tagArr = array();
            for ($i = 0; $i < sizeof($rows); $i++) {
                array_push($tagArr, $rows[$i]['name']);
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
     *add single sub category
     *
     *@param (String)apikey
     *@param (String)pSubCategory
     *@return json data
     *
     */
    public function addSubCategory()
    {
        try {
            $insertId;
            $pSubCategory = addslashes($this->_request['pSubCategory']);
            $sql = "select  id  from " . TABLE_PREFIX . "des_sub_cat where name = '$pSubCategory'";
            $row = $this->executeGenericDQLQuery($sql);
            if (sizeof($row) == 0) {
                $sql = "select id from " . TABLE_PREFIX . "des_sub_cat ORDER BY id DESC";
                $result = $this->executeGenericDQLQuery($sql);
                $order = $result[0][0];
                if ($order == '') {
                    $order = 0;
                }

                $sql = "insert into " . TABLE_PREFIX . "des_sub_cat(name,sort_order) values('$pSubCategory','$order')";
                $insertId = $this->executeGenericInsertQuery($sql);
            } else {
                $insertId = $row[0]['id'];
            }
            return $insertId;
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *remove sub  category
     *
     *@param (String)apikey
     *@param (String)removeSubCategory
     *@return json data
     *
     */
    public function removeSubCategory()
    {
        $pSubCategory = $this->_request['removeSubCategory'];
        try {
            $sql = "select count(*) count from " . TABLE_PREFIX . "des_sub_cat where name = '$pSubCategory'";
            $row = $this->executeGenericDQLQuery($sql);
            $response = array();
            if ($row[0]['count'] == "0") {
                // category not present error
                $response['status'] = false;
                $response['message'] = "ERROR sub cateory  '$pSubCategory' not present";
            } else {
                // perform delete
                $sql = "DELETE FROM " . TABLE_PREFIX . "des_sub_cat WHERE name= '$pSubCategory'";
                $this->executeGenericDMLQuery($sql);
                $response['status'] = true;
                $response['message'] = "'$pSubCategory' sub cateory delete successful !!";
            }
            $this->closeConnection();
            $this->response($this->json($response), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Edit category name
     *
     *@param (String)apikey
     *@param (int)categoryId
     *@param (String)categoryName
     *@return json data
     *
     */
    public function editCategory()
    {
        /*$pOldCategoryName=$this->_request['oldCategoryName'];
        $pNewCategoryName=$this->_request['newCategoryName'];
        $sql = "update ".TABLE_PREFIX."des_cat set des_cat.category_name = '$pNewCategoryName' where des_cat.category_name = '$pOldCategoryName'";
        $row = $this->executeGenericDQLQuery($sql);
        $response = array();
        $response['status'] = true;
        $response['message'] = "category name changed from $pOldCategoryName to $pNewCategoryName";
        $this->response($this->json($response), 200);*/
        $categoryId = $this->_request['categoryId'];
        $categoryName = $this->_request['categoryName'];
        try {
            $sql = "update " . TABLE_PREFIX . "des_cat set des_cat.category_name ='$categoryName' where des_cat.id = $categoryId";
            $row = $this->executeGenericDMLQuery($sql);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
        $response = array();
        $response['status'] = true;
        $response['message'] = "category name changed to $categoryName";
        $this->closeConnection();
        $this->response($this->json($response), 200);
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Edit sub category name
     *
     *@param (String)apikey
     *@param (int)subCategoryId
     *@param (String)subCategory
     *@return json data
     *
     */
    public function editSubCategory()
    {
        $subCategoryId = $this->_request['subCategoryId'];
        try {
            $subCategory = $this->_request['subCategory'];
            $sql = "update " . TABLE_PREFIX . "des_sub_cat set name = '$subCategory' where id = $subCategoryId";
            $row = $this->executeGenericDMLQuery($sql);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
        $response = array();
        $response['status'] = true;
        $response['message'] = "sub category name changed";
        $this->closeConnection();
        $this->response($this->json($response), 200);
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *delete design by design id
     *
     *@param (String)apikey
     *@param (int)design_id
     *@return json data
     *
     */
    public function deleteDesignById()
    {
        $pDesignId = $this->_request['design_id'];
        try {
            $sql = 'SELECT file_name FROM ' . TABLE_PREFIX . 'designs WHERE id=' . $pDesignId;
            $res = $this->executeFetchAssocQuery($sql);
            $file = $res[0]['file_name'];
            $ds = DIRECTORY_SEPARATOR;
            $file = $this->getDesignImagePath() . $ds . $file;
            if (file_exists($file)) {
                @chmod($file, 0777);
                @unlink($file);
            }

            $sql = "delete from " . TABLE_PREFIX . "designs where id=$pDesignId";
            $this->executeGenericDMLQuery($sql);
            $sql = "DELETE FROM " . TABLE_PREFIX . "print_method_design_rel WHERE design_id='" . $pDesignId . "'";
            $status = $this->executeGenericDMLQuery($sql);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
        /* $response['status'] = "success";
    $response['message'] = "design deleted for design id ".$pDesignId;
    $this->response($this->json($response), 200);  */
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Get Tags ids
     *
     *@param (String)apikey
     *@param (Array)designId
     *@param (Array)categoryIdArr
     *@return json data
     *
     */
    public function getTagIdArr($tagsText)
    {
        try {
            $reqTag = explode(",", $tagsText);
            $sql = "select id,name from " . TABLE_PREFIX . "des_tags";
            $dbTagArr = $this->executeGenericDQLQuery($sql);
            $resTagArr = array();
            for ($i = 0; $i < sizeof($reqTag); $i++) {
                $found = false;
                $reqTag[$i] = trim($reqTag[$i]);
                for ($j = 0; $j < sizeof($dbTagArr); $j++) {
                    if ($dbTagArr[$j]['name'] == $reqTag[$i]) {
                        $found = true;
                        array_push($resTagArr, $dbTagArr[$j]['id']);
                        break;
                    }
                }
                if ($found == false && $reqTag[$i] != '') {
                    // insert category and push the id
                    $sql = "insert into " . TABLE_PREFIX . "des_tags(name) values('$reqTag[$i]')";
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
     *Get category ids
     *
     *@param (String)apikey
     *@param (Array)categoryText
     *@return json data
     *
     */
    public function getCategoryIdArr($categoryText)
    {
        try {
            $reqCategory = explode(",", $categoryText);
            $sql = "select id,category_name from " . TABLE_PREFIX . "des_cat";
            $dbCategoryArr = $this->executeGenericDQLQuery($sql);
            $resCategoryArr = array();
            for ($i = 0; $i < sizeof($reqCategory); $i++) {
                $found = false;
                $reqCategory[$i] = trim($reqCategory[$i]);
                for ($j = 0; $j < sizeof($dbCategoryArr); $j++) {
                    if (trim($dbCategoryArr[$j]['category_name']) == $reqCategory[$i]) {
                        $found = true;
                        array_push($resCategoryArr, $dbCategoryArr[$j]['id']);
                        break;
                    }
                }
                if ($found == false && $reqCategory[$i] != '') {
                    // insert category and push the id
                    $sql = "insert into " . TABLE_PREFIX . "des_cat(category_name) values('$reqCategory[$i]')";
                    $categoryId = $this->executeGenericInsertQuery($sql);
                    array_push($resCategoryArr, $categoryId);
                }
            }
            return $resCategoryArr;
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *update design data
     *
     *@param (String)apikey
     *@param (String)designName
     *@param (String)category_subcategory
     *@param (Float)price
     *@param (String)tagsText
     *@param (int)isShape
     *@param (int)isScalable
     *@return json data
     *
     */
    public function updateDesignData()
    {
        // fetching all the updated parameters
        try {
            $pId = $this->_request['id'];
            $designName = $this->_request['designName'];
            $price = $this->_request['price'];
            $category_subcategory = $this->_request['category_subcategory'];
            $tagsText = $this->_request['tagsText'];
            $isShape = $this->_request['isShape'];
            $isScalable = $this->_request['isScalable'];

            // deleting mappings based on design id
            $this->unMapDesignFromRelTable($pId, 'design_category_sub_category_rel');
            $this->unMapDesignFromRelTable($pId, 'des_tag_rel');

            // update design data and inserting new mapping data
            $sql = "update " . TABLE_PREFIX . "designs set design_name='$designName' , price=$price, isScalable=$isScalable where id=$pId";
            $this->executeGenericDMLQuery($sql);
            $tagIdArr = $this->getTagIdArr($tagsText);
            $this->mapDesignTagRel($pId, $tagIdArr);
            $this->mapDesign_category_subCategory_rel($pId, $category_subcategory);
            $this->closeConnection();
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *delete design from table
     *
     *@param (String)apikey
     *@param (String)relTableName
     *@param (String)pId
     *@return json data
     *
     */
    public function unMapDesignFromRelTable($pId, $relTableName)
    {
        try {
            $sql = "delete from $relTableName where $relTableName.design_id = $pId";
            $this->executeGenericDMLQuery($sql);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 21-03-2017(dd-mm-yy)
     *update Design category List
     *
     * @param (String)category list
     * @return json data
     *
     */
    public function updateDragCategoryList()
    {
        $status = 0;
        $categoryList = $this->_request['categoryData'];
        $prepareSql = '';
        $querySql = '';
        for ($i = 0; $i < sizeof($categoryList); $i++) {
            $querySql .= ' WHEN ' . $categoryList[$i]['id'] . " THEN '" . $categoryList[$i]['sort_order'] . "'";
            $prepareSql .= ',' . $categoryList[$i]['id'];
        }
        if (strlen($querySql) && strlen($prepareSql)) {
            try {
                $usql = 'UPDATE ' . TABLE_PREFIX . 'des_cat SET sort_order = CASE id' . $querySql . ' END WHERE id IN(' . substr($prepareSql, 1) . ')';
                $status = $this->executeGenericDMLQuery($usql);
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        }
        $msg['status'] = ($status) ? 'success' : 'failed';
        $this->response($this->json($msg), 200);
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 21-03-2017(dd-mm-yy)
     *update Design sub category list
     *
     * @param (String)subcategory list
     * @return json data
     *
     */
    public function updateDragSubCategoryList()
    {
        $status = 0;
        $subCategoryList = $this->_request['categoryData'];
        $prepareSql = '';
        $querySql = '';
        for ($i = 0; $i < sizeof($subCategoryList); $i++) {
            $querySql .= ' WHEN ' . $subCategoryList[$i]['id'] . " THEN '" . $subCategoryList[$i]['sort_order'] . "'";
            $prepareSql .= ',' . $subCategoryList[$i]['id'];
        }
        if (strlen($querySql) && strlen($prepareSql)) {
            try {
                $usql = 'UPDATE ' . TABLE_PREFIX . 'des_sub_cat SET sort_order = CASE id' . $querySql . ' END WHERE id IN(' . substr($prepareSql, 1) . ')';
                $status = $this->executeGenericDMLQuery($usql);
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        }
        $msg['status'] = ($status) ? 'success' : 'failed';
        $this->response($this->json($msg), 200);
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 25-03-2017(dd-mm-yy)
     *update Default Category and Subcategory
     *
     * @param (Int)category id
     * @param (Int)subcategory id
     * @return json data
     *
     */
    public function setDefaultCatSubcat()
    {
        $status = 0;
        $catId = $this->_request['catId'];
        $subCatId = $this->_request['subCatId'];
        try {
            $sql1 = 'UPDATE ' . TABLE_PREFIX . 'des_cat_sub_cat_rel AS s, ' . TABLE_PREFIX . 'des_cat AS d SET s.is_default ="0",d.is_default ="0"';
            $result = $this->executeGenericDMLQuery($sql1);
            if (($catId > 0) && ($subCatId > 0)) {
                $sql = 'UPDATE ' . TABLE_PREFIX . 'des_cat_sub_cat_rel SET is_default ="1" WHERE category_id = "' . $catId . '" AND sub_category_id = "' . $subCatId . '"';
            } else if ($catId > 0) {
                $sql = 'UPDATE ' . TABLE_PREFIX . 'des_cat SET is_default ="1" WHERE id= "' . $catId . '"';
            }
            $status = $this->executeGenericDMLQuery($sql);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
        $msg['status'] = ($status) ? 'success' : 'failed';
        $this->response($this->json($msg), 200);
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 25-03-2017(dd-mm-yy)
     *get Design default category and sub category name
     *
     * @return json data
     *
     */
    public function getDefaultCatSubcat()
    {
        try {
            $status = 0;
            $defaultList = array();
            $sql = 'SELECT * FROM ' . TABLE_PREFIX . 'des_cat WHERE is_default="1"';
            $result = $this->executeGenericDQLQuery($sql);
            if ($result) {
                $defaultList['catId'] = $result[0]['id'];
                $defaultList['catName'] = $result[0]['category_name'];
            } else {
                $sql1 = 'SELECT r.category_id,r.sub_category_id,r.is_default,c.category_name,s.name FROM ' . TABLE_PREFIX . 'des_cat_sub_cat_rel as r INNER JOIN ' . TABLE_PREFIX . 'des_cat AS c ON r.category_id=c.id INNER JOIN ' . TABLE_PREFIX . 'des_sub_cat AS s ON r.sub_category_id=s.id WHERE r.is_default ="1" LIMIT 1';
                $catList = $this->executeGenericDQLQuery($sql1);
                if ($catList) {
                    $defaultList['catId'] = $catList[0]['category_id'];
                    $defaultList['catName'] = $catList[0]['category_name'];
                    $defaultList['subCatId'] = $catList[0]['sub_category_id'];
                    $defaultList['subCatName'] = $catList[0]['name'];
                } else {
                    $this->response($this->json($defaultList), 200);
                }
            }
            $this->response($this->json($defaultList), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }
}
