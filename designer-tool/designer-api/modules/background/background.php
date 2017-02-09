<?php
/* Check Un-authorize Access */
if (!defined('accessUser')) {
    die("Error");
}

class Background extends UTIL
{
    /*
     *
     *date created 12-7-2016(dd-mm-yy)
     *date modified 4-8-2016(dd-mm-yy)
     *getBackgroundDesignCatagory
     *purpose:for displaying added categories in category drop down menu
     *
     */
    public function getBackgroundDesignCatagory()
    {
        try {
            $print_method = $this->_request['print_method'];
            $sql = "SELECT dbc.* FROM " . TABLE_PREFIX . "design_background_category as dbc";
            if (isset($print_method) && $print_method != '') {
                $sql .= " left join " . TABLE_PREFIX . "design_back_cate_printmethod_rel as dbcpr on dbc.category_id = dbcpr.background_category_id
				where dbcpr.print_method_id =$print_method";
            }
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
     *date created 12-7-2016(dd-mm-yy)
     *date modified 4-8-2016(dd-mm-yy)
     *fetchBackgroundDesignsUploaded
     *purpose:for displaying all background_designs list
     *
     */
    public function fetchBackgroundDesignsUploaded()
    {
        try {
            $categoryValue = $this->_request['categoryValue'];
            $searchval = $this->_request['searchval'];
            $designLastLoaded = $this->_request['lastLoaded'];
            $designLimit = $this->_request['loadCount'];
            $print_method = $this->_request['print_method'];
            $exesql = '';
            $sql = "select distinct db.id,db.background_design_name ,db.file_name,db.price ,db.isScalable ,db.is_image ,db.color_value from " . TABLE_PREFIX . "design_background as db ";
            if (isset($print_method) && $print_method != '') {
                if ($categoryValue == '0' && $searchval == '') {
                    $sql .= "join " . TABLE_PREFIX . "design_back_cat_rel dbcr on db.id = dbcr.background_id
					left join " . TABLE_PREFIX . "design_back_cate_printmethod_rel dbcpr on dbcr.background_category_id = dbcpr.background_category_id
					WHERE dbcpr.print_method_id='" . $print_method . "'";
                }
                if (isset($searchval) && $searchval != '' && $categoryValue == '0') {
                    $sql .= "join " . TABLE_PREFIX . "design_back_cat_rel dbcr on db.id = dbcr.background_id
					left join " . TABLE_PREFIX . "design_back_cate_printmethod_rel dbcpr on dbcr.background_category_id = dbcpr.background_category_id
					left join " . TABLE_PREFIX . "design_background_tags_rel dbtr on db.id = dbtr.background_id
					left join " . TABLE_PREFIX . "design_background_tags dbt on dbtr.tag_id = dbt.id
					WHERE dbcpr.print_method_id='" . $print_method . "' and db.background_design_name like '%$searchval%' or dbt.name like '%$searchval%' ";
                }
                if ($categoryValue != 0 && $searchval != '') {
                    $sql .= "join " . TABLE_PREFIX . "design_back_cat_rel dbcr on db.id = dbcr.background_id
					left join " . TABLE_PREFIX . "design_back_cate_printmethod_rel dbcpr on dbcr.background_category_id = dbcpr.background_category_id
					left join " . TABLE_PREFIX . "design_background_tags_rel dbtr on db.id = dbtr.background_id
					left join " . TABLE_PREFIX . "design_background_tags dbt on dbtr.tag_id = dbt.id
					WHERE  dbcr.background_category_id =$categoryValue and dbcpr.print_method_id='" . $print_method . "' and ((db.background_design_name like '%$searchval%') or (dbt.name like '%$searchval%')) ";
                }
                if ($categoryValue != 0 && $searchval == '') {
                    $sql .= "join " . TABLE_PREFIX . "design_back_cat_rel dbcr on db.id = dbcr.background_id
					left join " . TABLE_PREFIX . "design_back_cate_printmethod_rel dbcpr on dbcr.background_category_id = dbcpr.background_category_id
					WHERE dbcpr.print_method_id='" . $print_method . "' AND dbcr.background_category_id = '" . $categoryValue . "'";
                }
            } else {
                if ($categoryValue != 0 && $searchval == '') {
                    $sql .= "join " . TABLE_PREFIX . "design_back_cat_rel dbcr on db.id = dbcr.background_id
					WHERE dbcr.background_category_id = '" . $categoryValue . "'";
                } else {
                    $sql .= "join " . TABLE_PREFIX . "design_back_cat_rel dbcr on db.id = dbcr.background_id";
                }
            }
            $exesql = $sql . ' ORDER BY db.id DESC ';
            $rows = $this->executeGenericDQLQuery($exesql);
            $count = sizeof($rows);
            $exesql .= " LIMIT $designLastLoaded, $designLimit";
            //getting details of records by limitations
            $rows = $this->executeGenericDQLQuery($exesql);
            $designArray = array();
            for ($i = 0; $i < sizeof($rows); $i++) {
                $designArray[$i]['id'] = $rows[$i]['id'];
                $designArray[$i]['name'] = $rows[$i]['background_design_name'];
                $designArray[$i]['value'] = '';
                if (file_exists($this->getBackgroundDesignImagePath() . $rows[$i]['file_name'])) {
                    $designArray[$i]['value'] = $this->getBackgroundDesignImageURL() . $rows[$i]['file_name'];
                }
                if (file_exists($this->getBackgroundDesignImagePath() . 'thumb_' . $rows[$i]['file_name'])) {
                    $designArray[$i]['value'] = $this->getBackgroundDesignImageURL() . 'thumb_' . $rows[$i]['file_name'];
                }
                $designArray[$i]['is_thumb'] = '1';
                $designArray[$i]['file_name'] = $rows[$i]['file_name'];
                $designArray[$i]['price'] = $rows[$i]['price'];
                $designArray[$i]['isScalable'] = $rows[$i]['isScalable'];
                $designArray[$i]['is_image'] = $rows[$i]['is_image'];
                $designArray[$i]['color_value'] = $rows[$i]['color_value'];
            }
            $sql = "SELECT COUNT(id) as total FROM " . TABLE_PREFIX . "design_background";
            $countDesign = $this->executeGenericDQLQuery($sql);
            $x = array();
            $x['count'] = $count;
            $x['total_count'] = $countDesign[0]['total'];
            $x['design_background'] = $designArray;
            $this->closeConnection();
            $this->response($this->json($x), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }
    /*
     *
     *date created 7-11-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *addBulkBackgroundDesign
     *purpose:for uploading new background_designs
     *
     */
    public function addBulkBackgroundDesign()
    {
        extract($this->_request);
        $status = 0;
        $sql = array();
        $fname = array();
        $dir = $this->getBackgroundDesignImagePath();
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
        $design_tag_rel_sql = '';
        $tag_arr = array();
        if (!empty($tags)) {
            foreach ($tags as $k2 => $v2) {
                $tag_sql = "SELECT id,count( * ) AS nos FROM " . TABLE_PREFIX . "design_background_tags WHERE name = '" . $v2 . "'";
                $res = $this->executeFetchAssocQuery($tag_sql);
                if (!$res[0]['nos']) {
                    $tag_sql1 = "INSERT INTO " . TABLE_PREFIX . "design_background_tags(name) VALUES('" . $v2 . "')";
                    $tag_arr[] = $this->executeGenericInsertQuery($tag_sql1);
                } else {
                    $tag_arr[] = $res[0]['id'];
                }
            }
        }
        if (isset($color) && $is_image == 0) {
            $sql = "INSERT INTO " . TABLE_PREFIX . "design_background (background_design_name,price,isScalable,is_image,color_value) values('" . $design_back_name . "','" . $price . "','" . $isScalable . "','" . $is_image . "','" . $color . "')";
            $design_back_id = $this->executeGenericInsertQuery($sql);
            if (strlen($design_back_id) && $design_back_id != '') {
                $update_sql = "UPDATE " . TABLE_PREFIX . "design_background SET file_name ='" . $design_back_id . '.png' . "' where id =$design_back_id";
                $status = $this->executeGenericDMLQuery($update_sql);
            }
            if (!empty($category_id)) {
                foreach ($category_id as $k1 => $v1) {
                    $cat_scat_rel_sql .= ",('" . $v1 . "','" . $design_back_id . "')";
                }
            }
            if (!empty($tag_arr)) {
                foreach ($tag_arr as $v3) {
                    $design_tag_rel_sql .= ",('" . $design_back_id . "','" . $v3 . "')";
                }
            }
        } else if ($is_image == 1) {
            $dir = $this->getBackgroundDesignImagePath();
            if (!$dir) {
                $this->response('dir' . $dir, 204);
            }
            //204 - immediately termiante this request
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }

            foreach ($this->_request['files'] as $k => $v) {
                $sql[$k] = "INSERT INTO " . TABLE_PREFIX . "design_background (background_design_name,price,isScalable,is_image) values('" . $design_back_name . "','" . $price . "','" . $isScalable . "','" . $is_image . "')";
                $design_back_id[$k] = $this->executeGenericInsertQuery($sql[$k]);
                $fname[$k] = $design_back_id[$k] . '.' . $v['type'];
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
                $usql1 .= ' WHEN ' . $design_back_id[$k] . " THEN '" . $fname[$k] . "'";
                $usql2 .= ',' . $design_back_id[$k];

                if (!empty($category_id)) {
                    foreach ($category_id as $k1 => $v1) {
                        $cat_scat_rel_sql .= ",('" . $v1 . "','" . $design_back_id[$k] . "')";
                    }
                }
                if (!empty($tag_arr)) {
                    foreach ($tag_arr as $v3) {
                        $design_tag_rel_sql .= ",('" . $design_back_id[$k] . "','" . $v3 . "')";
                    }
                }
            }
            if (strlen($usql1) && strlen($usql2)) {
                $usql = 'UPDATE ' . TABLE_PREFIX . 'design_background SET file_name = CASE id' . $usql1 . ' END WHERE id IN(' . substr($usql2, 1) . ')';
                $status = $this->executeGenericDMLQuery($usql);
            }
        }
        if (strlen($cat_scat_rel_sql)) {
            $cat_scat_rel_sql = "INSERT INTO " . TABLE_PREFIX . "design_back_cat_rel(background_category_id,background_id) VALUES " . substr($cat_scat_rel_sql, 1);
            $status = $this->executeGenericDMLQuery($cat_scat_rel_sql);
        }
        if (strlen($design_tag_rel_sql)) {
            $design_tag_rel_sql = "INSERT INTO " . TABLE_PREFIX . "design_background_tags_rel (background_id,tag_id) VALUES " . substr($design_tag_rel_sql, 1);
            $status = $this->executeGenericDMLQuery($design_tag_rel_sql);
        }
        $msg['status'] = ($status) ? 'Success' : 'failed';
        $this->response($this->json($msg), 200);
    }
    /*
     *
     *date created 7-12-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *getBackDesignDetails
     *purpose:displays info regarding the back_design selected for editing
     *
     */
    public function getBackDesignDetails()
    {
        try {
            $designId = $this->_request['design_background_id'];
            $designData = array();
            $sql = "select db.id as design_background_id ,db.file_name,db.background_design_name,db.isScalable,db.price
					,db.is_image,db.color_value,dbc.category_id, dbc.name
			from " . TABLE_PREFIX . "design_background db
			left join " . TABLE_PREFIX . "design_back_cat_rel dcr  on  db.id =dcr.background_id
			left join  " . TABLE_PREFIX . "design_background_category dbc on dcr.background_category_id = dbc.category_id
			where db.id = $designId";
            $rows = $this->executeGenericDQLQuery($sql);
            //fetching design detailes
            $designData['background_detail'] = array();
            $designData['background_detail']['design_background_id'] = $rows[0]['design_background_id'];
            $designData['background_detail']['file_name'] = $rows[0]['file_name'];
            $designData['background_detail']['background_design_name'] = $rows[0]['background_design_name'];
            $designData['background_detail']['price'] = $rows[0]['price'];
            $designData['background_detail']['isScalable'] = $rows[0]['isScalable'];
            $designData['background_detail']['is_image'] = $rows[0]['is_image'];
            $designData['background_detail']['color_value'] = $rows[0]['color_value'];

            //fetching categories
            $designData['category_category'] = array();
            for ($i = 0; $i < sizeof($rows); $i++) {
                $designData['category'][$i]['category_id'] = $rows[$i]['category_id'];
                $designData['category'][$i]['category_name'] = $rows[$i]['name'];
            }
            // fetching tags
            $sql = "select distinct dbt.id as tag_id , dbt.name as tag_name from " . TABLE_PREFIX . "design_background db ,
			" . TABLE_PREFIX . "design_background_tags dbt ,
			" . TABLE_PREFIX . "design_background_tags_rel dbtr where
			db.id = dbtr.background_id and dbt.id = dbtr.tag_id and db.id = $designId";
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
     *date created 7-13-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *updateBackgroundDesign
     *purpose:for saving all edited info about the back_designs
     *
     */
    public function updateBackgroundDesign()
    {
        $background_design_name = $this->_request['background_design_name'];
        $id = $this->_request['id'];
        $color_value = $this->_request['color_value'];
        $status = 0;
        if (!empty($this->_request) && isset($this->_request['background_design_name']) && isset($this->_request['isScalable'])) {
            if (!empty($this->_request['id'])) {
                $id_str = implode(',', $this->_request['id']);
                if (!empty($color_value)) {
                    $sql = "UPDATE " . TABLE_PREFIX . "design_background SET background_design_name = '" . $background_design_name . "',isScalable = '" . $this->_request['isScalable'] . "',price = '" . $this->_request['price'] . "',color_value='" . $color_value . "' WHERE id IN(" . $id_str . ")";
                    $status = $this->executeGenericDMLQuery($sql);
                }
                $sql = "UPDATE " . TABLE_PREFIX . "design_background SET background_design_name = '" . $background_design_name . "',isScalable = '" . $this->_request['isScalable'] . "',price = '" . $this->_request['price'] . "' WHERE id IN(" . $id_str . ")";
                $status = $this->executeGenericDMLQuery($sql);
                $sql = "DELETE FROM " . TABLE_PREFIX . "design_back_cat_rel WHERE background_id IN(" . $id_str . ")";
                $status = $this->executeGenericDMLQuery($sql);
                $sql = "DELETE FROM " . TABLE_PREFIX . "design_background_tags_rel WHERE background_id IN(" . $id_str . ")";
                $status = $this->executeGenericDMLQuery($sql);
                $design_tag_rel_sql = '';
                $cat_scat_rel_sql = '';
                $tag_arr = array();
                if (!empty($this->_request['tags'])) {
                    foreach ($this->_request['tags'] as $k => $v) {
                        $tag_sql = "SELECT id,count( * ) AS nos FROM " . TABLE_PREFIX . "design_background_tags WHERE name = '" . $v . "'";
                        $res = $this->executeFetchAssocQuery($tag_sql);
                        if (!$res[0]['nos']) {
                            $tag_sql1 = "INSERT INTO " . TABLE_PREFIX . "design_background_tags(name) VALUES('" . $v . "')";
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
                            $design_back_tag_rel_sql .= ",('" . $v . "','" . $v3 . "')";
                        }
                    }
                }
                if (strlen($design_back_tag_rel_sql)) {
                    $design_tag_rel_sql = "INSERT INTO " . TABLE_PREFIX . "design_background_tags_rel (background_id,tag_id) VALUES " . substr($design_back_tag_rel_sql, 1);
                    $status = $this->executeGenericDMLQuery($design_tag_rel_sql);
                }
                if (strlen($cat_scat_rel_sql)) {
                    $cat_scat_rel_sql = "INSERT INTO " . TABLE_PREFIX . "design_back_cat_rel (background_category_id,background_id) VALUES " . substr($cat_scat_rel_sql, 1);
                    $status = $this->executeGenericDMLQuery($cat_scat_rel_sql);
                }
            }
        }
        $msg['status'] = ($status) ? 'Success' : 'Failure';
        $this->response($this->json($msg), 200);
    }
    /*
     *
     *date created 7-9-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *saveBackGround_Designcategory
     *purpose:adding new category in manage category section
     *
     */
    public function saveBackGround_Designcategory()
    {
        $cate_name = $this->_request['cate_name'];
        if (isset($cate_name)) {
            $select_sql = "SELECT name from " . TABLE_PREFIX . "design_background_category WHERE name='" . $cate_name . "'";
            $rows = $this->executeFetchAssocQuery($select_sql);
            if ($rows) {
                $msg['status'] = 'name exit';
            } else {
                $sql_insert = "INSERT INTO " . TABLE_PREFIX . "design_background_category (name) values('" . $cate_name . "')";
                $status = $this->executeGenericDMLQuery($sql_insert);
                $msg['status'] = ($status) ? 'success' : 'failed';
            }
            $this->response($this->json($msg), 200);
        }
    }
    /*
     *
     *date created 7-9-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *updateBackgroundDesignCategory
     *purpose:for updating the category names that are edited
     *
     */
    public function updateBackgroundDesignCategory()
    {
        $status = 0;
        if (!empty($this->_request) && $this->_request['id'] && isset($this->_request['cate_name'])) {
            extract($this->_request);
            $chk_duplicate = "SELECT COUNT(*) AS duplicate FROM " . TABLE_PREFIX . "design_background_category WHERE name='" . $cate_name . "' AND category_id !='" . $id . "'";
            $res = $this->executeFetchAssocQuery($chk_duplicate);
            if ($res[0]['duplicate']) {
                $msg['msg'] = 'Duplicate Entry';
            } else {
                $sql = "UPDATE " . TABLE_PREFIX . "design_background_category  SET name = '" . $cate_name . "' WHERE category_id='" . $id . "'";
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
    public function removeBackground_DesignCategory()
    {
        $pCategory = $this->_request['pCategory'];
        $sql = "select category_id ,name from design_background_category where design_background_category.name = '" . $pCategory . "'";

        $row = $this->executeGenericDQLQuery($sql);
        $response = array();
        if (sizeof($row) == "0") {
            $response['status'] = false;
            $response['message'] = 'ERROR category not present';
        } else {
            // perform delete
            $sql = "DELETE FROM design_background_category WHERE design_background_category.name= '" . $pCategory . "'";
            $this->executeGenericDMLQuery($sql);
            $pCategoryId = $row[0]['id'];
            $sql = "DELETE FROM design_back_cat_rel  WHERE design_back_cat_rel.background_category_id= '" . $pCategoryId . "'";
            $this->executeGenericDMLQuery($sql);
            $response['status'] = true;
            $response['message'] = "'$pCategory' category is deleted successfully !!";
        }
        $this->closeConnection();
        $this->response($this->json($response), 200);
    }
    /*
     *
     *date created 7-13-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *deleteBackgroundDesignById
     *purpose:deleting the back_designs in the list one by one/multiple
     *
     */
    public function deleteBackgroundDesignById($pBackgroundDesignId)
    {
        $sql = 'SELECT file_name FROM ' . TABLE_PREFIX . 'design_background WHERE id=' . $pBackgroundDesignId;
        $res = $this->executeFetchAssocQuery($sql);
        $file_name = $res[0]['file_name'];
        $ds = DIRECTORY_SEPARATOR;
        $path = $this->getBackgroundDesignImagePath() . $ds;
        $file = $path . $file_name;
        if (file_exists($file)) {
            @chmod($file, 0777);
            @unlink($file);
        }
        $file = $path . 'thumb_' . $file_name;
        if (file_exists($file)) {
            @chmod($file, 0777);
            @unlink($file);
        }

        $sql = "DELETE FROM " . TABLE_PREFIX . "design_background WHERE id IN(" . $pBackgroundDesignId . ")";
        $this->executeGenericDMLQuery($sql);
    }
}
