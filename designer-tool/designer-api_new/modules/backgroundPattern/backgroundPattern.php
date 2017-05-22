<?php
/* Check Un-authorize Access */
if (!defined('accessUser')) {
    die("Error");
}

class BackgroundPattern extends UTIL
{

    /*
     *
     *date created 5-10-2016(dd-mm-yy)
     *date modified 5-10-2016(dd-mm-yy)
     *getBackgroundPatternCategory
     *purpose:for displaying added categories in category drop down menu
     *
     */
    public function getBackgroundPatternCategory()
    {
        try {
            $print_method = $this->_request['print_method'];
            $sql = "SELECT bpc.* FROM " . TABLE_PREFIX . "background_pattern_category as bpc";
            if (isset($print_method) && $print_method != '') {
                $sql .= " left join " . TABLE_PREFIX . "back_pattern_cate_printmethod_rel as bpcpr on bpc.category_id = bpcpr.pattern_category_id
				where bpcpr.print_method_id =" . $print_method;
            }
			$sql .=" ORDER BY bpc.name";
            $rows = $this->executeGenericDQLQuery($sql);
            $categoryDetail = array();
            for ($i = 0; $i < sizeof($rows); $i++) {
                $categoryDetail[$i]['id'] = $rows[$i]['category_id'];
                $categoryDetail[$i]['category_name'] = $rows[$i]['name'];
            }
            $this->response($this->json($categoryDetail), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /*
     *
     *date created 6-10-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *fetchBackgroundPatternsUploaded
     *purpose:for displaying all background_designs list
     *
     */
    public function fetchBackgroundPatternsUploaded()
    {
        try {
            $categoryValue = $this->_request['categoryValue'];
            $searchval = $this->_request['searchval'];
            $designLastLoaded = $this->_request['lastLoaded'];
            $designLimit = $this->_request['loadCount'];
            $print_method = $this->_request['print_method'];
            $exesql = '';
            $sql = "select distinct bp.id,bp.background_pattern_name,bp.file_name,bp.price from " . TABLE_PREFIX . "background_pattern as bp ";
            if (isset($print_method) && $print_method != '') {
                if ($categoryValue == '0' && $searchval == '') {
                    $sql .= "join " . TABLE_PREFIX . "background_pattern_category_rel bpcr on bp.id = bpcr.pattern_id
					left join " . TABLE_PREFIX . "back_pattern_cate_printmethod_rel bpcpr on bpcr.pattern_category_id = bpcpr.pattern_category_id
					WHERE bpcpr.print_method_id='" . $print_method . "'";
                }
                if (isset($searchval) && $searchval != '' && $categoryValue == '0') {
                    $sql .= "join " . TABLE_PREFIX . "background_pattern_category_rel bpcr on bp.id = bpcr.pattern_id
					left join " . TABLE_PREFIX . "back_pattern_cate_printmethod_rel bpcpr on bpcr.pattern_category_id = bpcpr.pattern_category_id
					left join " . TABLE_PREFIX . "background_pattern_tags_rel bptr on bp.id = bptr.pattern_id
					left join " . TABLE_PREFIX . "background_pattern_tags bpt on bptr.tag_id = bpt.id
					WHERE bpcpr.print_method_id='" . $print_method . "' and bp.background_pattern_name like '%$searchval%' or bpt.name like '%$searchval%' ";
                }
                if ($categoryValue != 0 && $searchval != '') {
                    $sql .= "join " . TABLE_PREFIX . "background_pattern_category_rel bpcr on bp.id = bpcr.pattern_id
					left join " . TABLE_PREFIX . "back_pattern_cate_printmethod_rel bpcpr on bpcr.pattern_category_id = bpcpr.pattern_category_id
					left join " . TABLE_PREFIX . "background_pattern_tags_rel bptr on bp.id = bptr.pattern_id
					left join " . TABLE_PREFIX . "background_pattern_tags bpt on bptr.tag_id = bpt.id
					WHERE  bpcr.pattern_category_id =$categoryValue and bpcpr.print_method_id='" . $print_method . "' and ((bp.background_pattern_name like '%$searchval%') or (bpt.name like '%$searchval%')) ";
                }
                if ($categoryValue != 0 && $searchval == '') {
                    $sql .= "join " . TABLE_PREFIX . "background_pattern_category_rel bpcr on bp.id = bpcr.pattern_id
					left join " . TABLE_PREFIX . "back_pattern_cate_printmethod_rel bpcpr on bpcr.pattern_category_id = bpcpr.pattern_category_id
					WHERE bpcpr.print_method_id='" . $print_method . "' AND bpcr.pattern_category_id = '" . $categoryValue . "'";
                }
            } else {
                if ($categoryValue != 0 && $searchval == '') {
                    $sql .= "join " . TABLE_PREFIX . "background_pattern_category_rel bpcr on bp.id = bpcr.pattern_id
					WHERE bpcr.pattern_category_id = '" . $categoryValue . "'";
                } else {
                    $sql .= "join " . TABLE_PREFIX . "background_pattern_category_rel bpcr on bp.id = bpcr.pattern_id";
                }
            }
            $exesql = $sql . ' ORDER BY bp.id DESC ';
            $rows = $this->executeGenericDQLQuery($exesql);
            $count = sizeof($rows);
            $exesql .= " LIMIT $designLastLoaded, $designLimit";
            //getting details of records by limitations
            $rows = $this->executeGenericDQLQuery($exesql);
            $designArray = array();
            for ($i = 0; $i < sizeof($rows); $i++) {
                $designArray[$i]['id'] = $rows[$i]['id'];
                $designArray[$i]['name'] = $rows[$i]['background_pattern_name'];
                $designArray[$i]['url'] = '';
                if (file_exists($this->getBackgroundPatternImagePath() . $rows[$i]['file_name'])) {
                    $designArray[$i]['url'] = $this->getBackgroundPatternImageURL() . 'thumb_' . $rows[$i]['file_name'];
                }
                if (file_exists($this->getBackgroundPatternImagePath() . $rows[$i]['file_name'])) {
                    $designArray[$i]['url'] = $this->getBackgroundPatternImageURL() . $rows[$i]['file_name'];
                }
                $designArray[$i]['is_thumb'] = '0';
                $designArray[$i]['file_name'] = $rows[$i]['file_name'];
                $designArray[$i]['price'] = $rows[$i]['price'];
            }
            $sql = "SELECT COUNT(id) as total FROM " . TABLE_PREFIX . "background_pattern";
            $countDesign = $this->executeGenericDQLQuery($sql);
            $x = array();
            $x['count'] = $count;
            $x['total_count'] = $countDesign[0]['total'];
            $x['background_pattern'] = $designArray;
            $this->closeConnection();
            $this->response($this->json($x), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /*
     *
     *date created 5-10-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *saveBackGroundPatternCategory
     *purpose:adding new category in manage category section
     *
     */
    public function saveBackGroundPatternCategory()
    {
        $cate_name = $this->_request['cate_name'];
        if (isset($cate_name)) {
            $select_sql = "SELECT name from " . TABLE_PREFIX . "background_pattern_category WHERE name='" . $cate_name . "'";
            $rows = $this->executeFetchAssocQuery($select_sql);
            if ($rows) {
                $msg['status'] = 'name exit';
            } else {
                $sql_insert = "INSERT INTO " . TABLE_PREFIX . "background_pattern_category (name) values('" . $cate_name . "')";
                $status = $this->executeGenericDMLQuery($sql_insert);
                $msg['status'] = ($status) ? 'success' : 'failed';
            }
            $this->response($this->json($msg), 200);
        }
    }

    /*
     *
     *date created 5-10-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *updateBackgroundPatternCategory
     *purpose:for updating the category names that are edited
     *
     */
    public function updateBackgroundPatternCategory()
    {
        $status = 0;
        if (!empty($this->_request) && $this->_request['id'] && isset($this->_request['cate_name'])) {
            extract($this->_request);
            $chk_duplicate = "SELECT COUNT(*) AS duplicate FROM " . TABLE_PREFIX . "background_pattern_category WHERE name='" . $cate_name . "'";
            $res = $this->executeFetchAssocQuery($chk_duplicate);
            if ($res[0]['duplicate']) {
                $msg['msg'] = 'Duplicate Entry';
            } else {
                $sql = "UPDATE " . TABLE_PREFIX . "background_pattern_category  SET name = '" . $cate_name . "' WHERE category_id='" . $id . "'";
                $status = $this->executeGenericDMLQuery($sql);
            }
            $msg['status'] = ($status) ? 'success' : 'failed';
        } else {
            $msg['status'] = 'nodata';
        }

        $this->response($this->json($msg), 200);
    }

    /*
     *
     *date created 7-9-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *removeBackground_DesignCategory
     *purpose:for deleting the category from the list
     *
     */
    public function removeBackgroundPatternCategory()
    {
        $pCategory = $this->_request['pCategory'];
        $sql = "select category_id ,name from background_pattern_category where background_pattern_category.name = '" . $pCategory . "'";

        $row = $this->executeGenericDQLQuery($sql);
        $response = array();
        if (sizeof($row) == "0") {
            $response['status'] = false;
            $response['message'] = 'ERROR category not present';
        } else {
            // perform delete
            $sql = "DELETE FROM background_pattern_category WHERE background_pattern_category.name= '" . $pCategory . "'";
            $this->executeGenericDMLQuery($sql);
            $pCategoryId = $row[0]['id'];
            $sql = "DELETE FROM background_pattern_category_rel  WHERE background_pattern_category_rel.pattern_category_id= '" . $pCategoryId . "'";
            $this->executeGenericDMLQuery($sql);
            $response['status'] = true;
            $response['message'] = "'$pCategory' category is deleted successfully !!";
        }
        $this->closeConnection();
        $this->response($this->json($response), 200);
    }

    /*
     *
     *date created 7-11-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *addBulkBackgroundPattern
     *purpose:for uploading new background_designs
     *
     */
    public function addBulkBackgroundPattern()
    {
        extract($this->_request);
        $status = 0;
        $sql = array();
        $fname = array();
        $dir = $this->getBackgroundPatternImagePath();
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
        $pattern_tag_rel_sql = '';
        $tag_arr = array();
        if (!empty($this->_request['tags'])) {
            foreach ($this->_request['tags'] as $k2 => $v2) {
                $tag_sql = "SELECT id,count( * ) AS nos FROM " . TABLE_PREFIX . "background_pattern_tags WHERE name = '" . $v2 . "'";
                $res = $this->executeFetchAssocQuery($tag_sql);
                if (!$res[0]['nos']) {
                    $tag_sql1 = "INSERT INTO " . TABLE_PREFIX . "background_pattern_tags(name) VALUES('" . $v2 . "')";
                    $tag_arr[] = $this->executeGenericInsertQuery($tag_sql1);
                } else {
                    $tag_arr[] = $res[0]['id'];
                }
            }
        }
        //echo '<pre>';print_r($this->_request);exit;
        foreach ($this->_request['file'] as $k => $v) {
            $sql[$k] = "INSERT INTO " . TABLE_PREFIX . "background_pattern (background_pattern_name,price) values('" . $background_pattern_name . "','" . $price . "')";
            $background_pattern_id[$k] = $this->executeGenericInsertQuery($sql[$k]);
            $fname[$k] = $background_pattern_id[$k] . '.' . $v['type'];
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
            $usql1 .= ' WHEN ' . $background_pattern_id[$k] . " THEN '" . $fname[$k] . "'";
            $usql2 .= ',' . $background_pattern_id[$k];

            if (!empty($this->_request['category_id'])) {
                foreach ($this->_request['category_id'] as $k1 => $v1) {
                    $cat_scat_rel_sql .= ",('" . $v1 . "','" . $background_pattern_id[$k] . "')";
                }
            }
            if (!empty($tag_arr)) {
                foreach ($tag_arr as $v3) {
                    $pattern_tag_rel_sql .= ",('" . $background_pattern_id[$k] . "','" . $v3 . "')";
                }
            }
        }

        if (strlen($usql1) && strlen($usql2)) {
            $usql = 'UPDATE ' . TABLE_PREFIX . 'background_pattern SET file_name = CASE id' . $usql1 . ' END WHERE id IN(' . substr($usql2, 1) . ')';
            $status = $this->executeGenericDMLQuery($usql);
        }
        if (strlen($cat_scat_rel_sql)) {
            $cat_scat_rel_sql = "INSERT INTO " . TABLE_PREFIX . "background_pattern_category_rel (pattern_category_id,pattern_id) VALUES " . substr($cat_scat_rel_sql, 1);
            $status = $this->executeGenericDMLQuery($cat_scat_rel_sql);
        }
        if (strlen($pattern_tag_rel_sql)) {
            $pattern_tag_rel_sql = "INSERT INTO " . TABLE_PREFIX . "background_pattern_tags_rel (pattern_id,tag_id) VALUES " . substr($pattern_tag_rel_sql, 1);
            $status = $this->executeGenericDMLQuery($pattern_tag_rel_sql);
        }
        $msg['status'] = ($status) ? 'Success' : 'failed';
        $this->response($this->json($msg), 200);
    }

    /*
     *
     *date created 6-10-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *getBackgroundPatternDetails
     *purpose:displays info regarding the back_design selected for editing
     *
     */
    public function getBackgroundPatternDetails()
    {
        try {
            $designId = $this->_request['background_pattern_id'];
            $designData = array();
            $sql = "select bp.id as background_pattern_id ,bp.file_name,bp.background_pattern_name,bp.price
					,bpc.category_id, bpc.name
			from " . TABLE_PREFIX . "background_pattern bp
			left join " . TABLE_PREFIX . "background_pattern_category_rel bpcr  on  bp.id =bpcr.pattern_id
			left join  " . TABLE_PREFIX . "background_pattern_category bpc on bpcr.pattern_category_id = bpc.category_id
			where bp.id = $designId";
            $rows = $this->executeGenericDQLQuery($sql);
            //fetching design detailes
            $designData['pattern_detail'] = array();
            $designData['pattern_detail']['background_pattern_id'] = $rows[0]['background_pattern_id'];
            $designData['pattern_detail']['file_name'] = $rows[0]['file_name'];
            $designData['pattern_detail']['background_pattern_name'] = $rows[0]['background_pattern_name'];
            $designData['pattern_detail']['price'] = $rows[0]['price'];

            //fetching categories
            $designData['category_category'] = array();
            for ($i = 0; $i < sizeof($rows); $i++) {
                $designData['category'][$i]['category_id'] = $rows[$i]['category_id'];
                $designData['category'][$i]['category_name'] = $rows[$i]['name'];
            }
            // fetching tags
            $sql = "select distinct bpt.id as tag_id , bpt.name as tag_name from " . TABLE_PREFIX . "background_pattern bp ,
			" . TABLE_PREFIX . "background_pattern_tags bpt ,
			" . TABLE_PREFIX . "background_pattern_tags_rel bptr where
			bp.id = bptr.pattern_id and bpt.id = bptr.tag_id and bp.id = $designId";
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
            $this->response($this->json($designData), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /*
     *
     *date created 6-10-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *updateBackgroundPattern
     *purpose:for saving all edited info about the background pattern
     *
     */
    public function updateBackgroundPattern()
    {
        $background_pattern_name = $this->_request['background_pattern_name'];
        $id = $this->_request['id'];
        //$color_value = $this->_request['color_value'];
        $status = 0;
        if (!empty($this->_request) && isset($this->_request['background_pattern_name'])) {
            if (!empty($this->_request['id'])) {
                $id_str = implode(',', $this->_request['id']);
                $sql = "UPDATE " . TABLE_PREFIX . "background_pattern SET background_pattern_name = '" . $background_pattern_name . "',price = '" . $this->_request['price'] . "' WHERE id IN(" . $id_str . ")";
                $status = $this->executeGenericDMLQuery($sql);
                $sql = "DELETE FROM " . TABLE_PREFIX . "background_pattern_category_rel WHERE pattern_id IN(" . $id_str . ")";
                $status = $this->executeGenericDMLQuery($sql);
                $sql = "DELETE FROM " . TABLE_PREFIX . "background_pattern_tags_rel WHERE pattern_id IN(" . $id_str . ")";
                $status = $this->executeGenericDMLQuery($sql);
                $background_pattern_tag_rel_sql = '';
                $cat_scat_rel_sql = '';
                $tag_arr = array();
                if (!empty($this->_request['tags'])) {
                    foreach ($this->_request['tags'] as $k => $v) {
                        $tag_sql = "SELECT id,count( * ) AS nos FROM " . TABLE_PREFIX . "background_pattern_tags WHERE name = '" . $v . "'";
                        $res = $this->executeFetchAssocQuery($tag_sql);
                        if (!$res[0]['nos']) {
                            $tag_sql1 = "INSERT INTO " . TABLE_PREFIX . "background_pattern_tags(name) VALUES('" . $v . "')";
                            $tag_arr[] = $this->executeGenericInsertQuery($tag_sql1);
                        } else {
                            $tag_arr[] = $res[0]['id'];
                        }
                    }
                }
                foreach ($this->_request['id'] as $k => $v) {
                    if (!empty($this->_request['category_id'])) {
                        foreach ($this->_request['category_id'] as $k1 => $v1) {
                            $cat_scat_rel_sql .= ",('" . $v1 . "','" . $v . "')";
                        }
                    }
                    if (!empty($tag_arr)) {
                        foreach ($tag_arr as $v3) {
                            $background_pattern_tag_rel_sql .= ",('" . $v . "','" . $v3 . "')";
                        }
                    }
                }
                if (strlen($background_pattern_tag_rel_sql)) {
                    $background_pattern_tag_rel_sql = "INSERT INTO " . TABLE_PREFIX . "background_pattern_tags_rel (pattern_id,tag_id) VALUES " . substr($background_pattern_tag_rel_sql, 1);
                    $status = $this->executeGenericDMLQuery($background_pattern_tag_rel_sql);
                }
                if (strlen($cat_scat_rel_sql)) {
                    $cat_scat_rel_sql = "INSERT INTO " . TABLE_PREFIX . "background_pattern_category_rel (pattern_category_id,pattern_id) VALUES " . substr($cat_scat_rel_sql, 1);
                    $status = $this->executeGenericDMLQuery($cat_scat_rel_sql);
                }
            }
        }
        $msg['status'] = ($status) ? 'Success' : 'Failure';
        $this->response($this->json($msg), 200);
    }
    /*
     *
     *date created 6-10-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *deleteBackgroundPatternById
     *purpose:deleting the background pattern in the list one by one/multiple
     *
     */
    public function deleteBackgroundPatternById()
    {
        $pBackgroundPatternId = $this->_request['background_pattern_id'];
        $sql = "SELECT file_name FROM " . TABLE_PREFIX . "background_pattern WHERE id IN(" . $pBackgroundPatternId . ")";
        $res = $this->executeFetchAssocQuery($sql);

        foreach ($res as $v) {
            $file_name = $v['file_name'];
            $ds = DIRECTORY_SEPARATOR;
            $path = $this->getBackgroundPatternImagePath() . $ds . $file;
            $file = $path . $file_name;
            if (file_exists($file)) {
                @chmod($file, 0777);
                @unlink($file);
            }
            /*$file = $path.'thumb_'.$file_name;
        if(file_exists($file)){
        @chmod($file,0777);
        @unlink($file);
        }*/
        }

        $sql = "DELETE FROM " . TABLE_PREFIX . "background_pattern WHERE id IN(" . $pBackgroundPatternId . ")";
        $this->executeGenericDMLQuery($sql);
        $response['status'] = 'success';
        $this->response($this->json($response), 200);
    }

}
