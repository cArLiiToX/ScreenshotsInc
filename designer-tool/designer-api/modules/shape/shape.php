<?php
/* Check Un-authorize Access */
if (!defined('accessUser')) {
    die("Error");
}

class Shape extends UTIL
{

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *remove Shape category
     *
     *@param (String)apikey
     *@return array
     *
     */
    public function allShapeCatagory()
    {
        $catagoryArray = array();
        try {
            $sql = "SELECT distinct id,category_name FROM " . TABLE_PREFIX . "shape_cat";
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
     *fetch shapes by search in admin
     *
     *@param (String)apikey
     *@param (String)categoryValue
     *@param (String)searchval
     *@param (Int)lastLoaded
     *@param (Int)loadCount
     *@return json data
     *
     */
    public function fetchShapesBySearchForAdmin()
    {
        try {
            $category = $this->_request['categoryValue'];
            //$subCategoryValue = $this->_request['subCategoryValue'];
            $searchval = $this->_request['searchval'];
            $shapeLastLoaded = $this->_request['lastLoaded'];
            $shapeLimit = $this->_request['loadCount'];
            $categoryValue = ($category != '') ? " and sc.category_name='" . $category . "'" : "";
            $joinText = '';
            if ($category != '' && $searchval == '') {
                $sql = "SELECT DISTINCT s.id, s.shape_name, s.file_name, s.price, s.status
                FROM " . TABLE_PREFIX . "shapes s, " . TABLE_PREFIX . "shape_cat_rel scr, " . TABLE_PREFIX . "shape_cat sc
                WHERE s.id = scr.shape_id
                AND scr.category_id = sc.id$categoryValue";
            } else if ($category != '' && $searchval != '') {
                $sql = "select distinct s.id,s.shape_name ,s.file_name,s.price,s.status from " . TABLE_PREFIX . "shapes s,
                " . TABLE_PREFIX . "shape_tags st," . TABLE_PREFIX . "shape_tag_rel str," . TABLE_PREFIX . "shape_cat_rel scr," . TABLE_PREFIX . "shape_cat sc
                WHERE s.id= scr.shape_id AND scr.category_id = sc.id$categoryValue and (( s.shape_name LIKE '%$searchval%') OR (s.id=str.shape_id and str.tag_id = st.id and st.name like '%$searchval%'))";
            } else if ($category == '' && $searchval != '') {
                $sql = "select distinct s.id,s.shape_name ,s.file_name,s.price,s.status from " . TABLE_PREFIX . "shapes s,
                " . TABLE_PREFIX . "shape_tags st," . TABLE_PREFIX . "shape_tag_rel str," . TABLE_PREFIX . "shape_cat_rel scr," . TABLE_PREFIX . "shape_cat sc
                WHERE 1 and (( s.shape_name LIKE '%$searchval%') OR (s.id=str.shape_id and str.tag_id = st.id and st.name like '%$searchval%'))";
            } else {
                $sql = "select distinct s.id,s.shape_name ,s.file_name,s.price,s.status from " . TABLE_PREFIX . "shapes s ";
            }
            $sql .= " ORDER BY s.id DESC "; //LIMIT $designLastLoaded, $designLimit
            // gettting total number of records present based on condition
            $rows = $this->executeGenericDQLQuery($sql);
            $count = sizeof($rows);
            $sql .= " LIMIT $shapeLastLoaded, $shapeLimit";
            //getting deatiles of records by limitations
            $rows = $this->clearArray($rows);
            $rows = $this->executeGenericDQLQuery($sql);
            //$this->log($sql) ;
            $shapeArray = array();
            for ($i = 0; $i < sizeof($rows); $i++) {
                $shapeArray[$i]['id'] = $rows[$i]['id'];
                $shapeArray[$i]['name'] = $rows[$i]['shape_name'];
                // $designArray[$i]['url'] = $this->getDesignImageURL().$rows[$i]['file_name'].'.svg';
                $shapeArray[$i]['url'] = $this->getShapeImageURL() . $rows[$i]['file_name'] . '.svg';
                $shapeArray[$i]['file_name'] = $rows[$i]['file_name'] . '.svg';
                $shapeArray[$i]['price'] = $rows[$i]['price'];
                $shapeArray[$i]['count'] = $count;
            }
            $sql = "SELECT COUNT(id) as total FROM " . TABLE_PREFIX . "shapes";
            $countShape = $this->executeFetchAssocQuery($sql);
            $x = array();
            $x['count'] = $count;
            $x['total_count'] = $countShape[0]['total'];
            $x['shapes'] = $shapeArray;
            $this->closeConnection();
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
     *Add shape and shape category
     *
     *@param (String)apikey
     *@param (String)shape_name
     *@param (Array)id
     *@param (Array)tags
     *@param (Array)files
     *@return json data
     *
     */
    //TRUNCATE shapes;Truncate shape_cat_rel;
    public function addBulkShape()
    {
        $status = 0;
        try {
            if (!empty($this->_request) && isset($this->_request['shape_name'])) {
                if (!empty($this->_request['files'])) {
                    $sql = array();
                    $rsql = '';
                    $shape_id = array();
                    $fname = array();
                    $thumbBase64Data = array();
                    $isql = "INSERT INTO " . TABLE_PREFIX . "shapes (shape_name, price) VALUES";
                    $usql = "UPDATE " . TABLE_PREFIX . "shapes SET file_name = CASE id";
                    $usql1 = '';
                    $usql2 = '';
                    $shape_tag_rel_sql = '';
                    $shape_cat_rel_sql = '';

                    $dir = $this->getShapeSvgPath();
                    if (!$dir) {
                        $this->response('', 204);
                    }
                    //204 - immediately termiante this request
                    if (!file_exists($dir)) {
                        mkdir($dir, 0777, true);
                    }

                    if (!empty($this->_request['tags'])) {
                        foreach ($this->_request['tags'] as $k => $v) {
                            $tag_sql = "SELECT id,count( * ) AS nos FROM " . TABLE_PREFIX . "shape_tags WHERE name = '" . $v . "'";
                            $res = $this->executeFetchAssocQuery($tag_sql);
                            if (!$res[0]['nos']) {
                                $tag_sql1 = "INSERT INTO " . TABLE_PREFIX . "shape_tags(name) VALUES('" . $v . "')";
                                $tag_arr[] = $this->executeGenericInsertQuery($tag_sql1);
                            } else {
                                $tag_arr[] = $res[0]['id'];
                            }
                        }
                    }

 					$this->_request['price'] = (isset($this->_request['price']) && $this->_request['price'])?$this->_request['price']:0.00;
                    foreach ($this->_request['files'] as $k => $v) {
                        $sql[$k] = $isql . "('" . $this->_request['shape_name'] . "','" . $this->_request['price'] . "')";
                        $shape_id[$k] = $this->executeGenericInsertQuery($sql[$k]);
                        $fname[$k] = 's_' . $shape_id[$k];

                        $thumbBase64Data[$k] = base64_decode($v);
                        file_put_contents($dir . $fname[$k] . '.svg', $thumbBase64Data[$k]);

                        $usql1 .= ' WHEN ' . $shape_id[$k] . " THEN '" . $fname[$k] . "'";
                        $usql2 .= ',' . $shape_id[$k];

                        if (!empty($tag_arr)) {
                            foreach ($tag_arr as $v1) {
                                $shape_tag_rel_sql .= ",('" . $shape_id[$k] . "','" . $v1 . "')";
                            }
                        }
                        if (!empty($this->_request['category_id'])) {
                            foreach ($this->_request['category_id'] as $v2) {
                                $shape_cat_rel_sql .= ",('" . $shape_id[$k] . "','" . $v2 . "')";
                            }
                        }
                    }
                    if (strlen($usql2)) {
                        $usql = $usql . $usql1 . ' END WHERE id IN(' . substr($usql2, 1) . ')';
                        $status = $this->executeGenericDMLQuery($usql);
                    }
                    if (strlen($shape_tag_rel_sql)) {
                        $shape_tag_rel_sql = "INSERT INTO " . TABLE_PREFIX . "shape_tag_rel (shape_id,tag_id) VALUES " . substr($shape_tag_rel_sql, 1);
                        $status = $this->executeGenericDMLQuery($shape_tag_rel_sql);
                    }
                    if (strlen($shape_cat_rel_sql)) {
                        $shape_cat_rel_sql = "INSERT INTO " . TABLE_PREFIX . "shape_cat_rel (shape_id,category_id) VALUES " . substr($shape_cat_rel_sql, 1);
                        $status = $this->executeGenericDMLQuery($shape_cat_rel_sql);
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
     *date modified 15-4-2016(dd-mm-yy)
     *getting shape details
     *
     *@param (String)apikey
     *@param (int)shape_id
     *@return array
     *
     */
    public function getShapeDetails()
    {
        $shapeId = $this->_request['shape_id'];
        $shapeData = array();
        try {
            $sql = "select distinct s.id as shape_id ,s.file_name,s.shape_name,s.price,s.status , c.id as category_id, c.category_name as category_name ,st.name as tag_name from " . TABLE_PREFIX . "shapes s left join  " . TABLE_PREFIX . "shape_cat_rel scr  on  s.id =scr.shape_id  left join  " . TABLE_PREFIX . "shape_cat c on scr.category_id = c.id
                left join " . TABLE_PREFIX . "shape_tag_rel str  on s.id = str.shape_id  left join " . TABLE_PREFIX . "shape_tags st on str.tag_id = st.id  where s.id = $shapeId";

            $rows = $this->executeGenericDQLQuery($sql);
            //fetching design detailes
            $shapeData['shape_details'] = array();
            $shapeData['shape_details']['shape_id'] = $rows[0]['shape_id'];
            $shapeData['shape_details']['file_name'] = $rows[0]['file_name'];
            $shapeData['shape_details']['shape_name'] = $rows[0]['shape_name'];
            $shapeData['shape_details']['price'] = $rows[0]['price'];
            $shapeData['shape_details']['status'] = $rows[0]['status'];

            //fetching categories and sub categories
            //$sql = "select * from ".TABLE_PREFIX."design_category_sub_category_rel dcsr where shape_id = 9";
            $shapeData['categories'] = array();
            for ($i = 0; $i < sizeof($rows); $i++) {
                $shapeData['categories'][$i]['category_id'] = $rows[$i]['category_id'];
                $shapeData['categories'][$i]['category_name'] = $rows[$i]['category_name'];
            }
            //$shapeData['categories'] = array_unique($shapeData['categories']);
            $shapeData['categories'] = $this->uniqueObjArray($shapeData['categories'], "category_name");
            // fetching tags
            $shapeData['tags'] = array();
            for ($i = 0; $i < sizeof($rows) && $rows[$i]['tag_name'] != null; $i++) {
                //$designData['tags'][$i]['category_id'] = $rows[$i]['category_id'];
                $shapeData['tags'][$i]['tag_name'] = $rows[$i]['tag_name'];
            }
            // $shapeData['tags'] = array_unique($shapeData['tags']);
            $shapeData['tags'] = $this->uniqueObjArray($shapeData['tags'], "tag_name");
            $this->response($this->json($shapeData), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Add shape data by id
     *
     *@param (String)apikey
     *@param (String)shape_name
     *@param (Array)id
     *@param (Array)tags
     *@return json data
     *
     */
    public function updateShapeData()
    {
        $status = 0;
        try {
            if (!empty($this->_request) && isset($this->_request['id']) && isset($this->_request['shape_name'])) {
                extract($this->_request);
                $id_str = implode(',', $id);
				$price = (isset($price) & $price)?$price:0.00;
				
				$sql = "UPDATE " . TABLE_PREFIX . "shapes SET shape_name = '" . $shape_name . "', price = '" . $price . "' WHERE id IN(" . $id_str . ")";
                $status = $this->executeGenericDMLQuery($sql);

                $sql = "DELETE FROM " . TABLE_PREFIX . "shape_tag_rel WHERE shape_id IN(" . $id_str . ")";
                $status = $this->executeGenericDMLQuery($sql);
                
				$sql = "DELETE FROM " . TABLE_PREFIX . "shape_cat_rel WHERE shape_id IN(" . $id_str . ")";
                $status = $this->executeGenericDMLQuery($sql);
                
				$shape_tag_rel_sql = '';
                $shape_cat_rel_sql = '';
                $tag_arr = array();

                if (!empty($this->_request['tags'])) {
                    foreach ($this->_request['tags'] as $k => $v) {
                        $tag_sql = "SELECT id,count( * ) AS nos FROM " . TABLE_PREFIX . "shape_tags WHERE name = '" . $v . "'";
                        $res = $this->executeFetchAssocQuery($tag_sql);
                        if (!$res[0]['nos']) {
                            $tag_sql1 = "INSERT INTO " . TABLE_PREFIX . "shape_tags(name) VALUES('" . $v . "')";
                            $tag_arr[] = $this->executeGenericInsertQuery($tag_sql1);
                        } else {
                            $tag_arr[] = $res[0]['id'];
                        }
                    }
                }
                foreach ($this->_request['id'] as $k => $v) {
                    $shape_id[$k] = $v;

                    if (!empty($tag_arr)) {
                        foreach ($tag_arr as $v1) {
                            $shape_tag_rel_sql .= ",('" . $shape_id[$k] . "','" . $v1 . "')";
                        }
                    }
                    if (!empty($this->_request['category_id'])) {
                        foreach ($this->_request['category_id'] as $v2) {
                            $shape_cat_rel_sql .= ",('" . $shape_id[$k] . "','" . $v2 . "')";
                        }
                    }
                }
                if (strlen($shape_tag_rel_sql)) {
                    $shape_tag_rel_sql = "INSERT INTO " . TABLE_PREFIX . "shape_tag_rel (shape_id,tag_id) VALUES " . substr($shape_tag_rel_sql, 1);
                    $status = $this->executeGenericDMLQuery($shape_tag_rel_sql);
                }
                if (strlen($shape_cat_rel_sql)) {
                    $shape_cat_rel_sql = "INSERT INTO " . TABLE_PREFIX . "shape_cat_rel (shape_id,category_id) VALUES " . substr($shape_cat_rel_sql, 1);
                    $status = $this->executeGenericDMLQuery($shape_cat_rel_sql);
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
     *Add shape category
     *
     *@param (String)apikey
     *@param (String)category_name
     *@return json data
     *
     */
    public function addShapeCategory()
    {
        $status = 0;
        try {
            if (!empty($this->_request) && isset($this->_request['category_name']) && $this->_request['category_name']) {
                $query = "select count(*) count from " . TABLE_PREFIX . "shape_cat where category_name = '" . $this->_request['category_name'] . "'";
                $rows = $this->executeGenericDQLQuery($query);
                $response = array();
                if ($rows[0]['count'] == "0") {
                    $sql = "INSERT INTO " . TABLE_PREFIX . "shape_cat (category_name) VALUES ('" . $this->_request['category_name'] . "')";
                    $status = $this->executeGenericDMLQuery($sql);
                    $msg['status'] = ($status) ? 'Success' : 'Failure';
                } else {
                    $msg['status'] = "Failure";
                }
            } else {
                $msg['status'] = "nodata";
            }
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
     *upadte Shape category name
     *
     * @param (String)apikey
     * @param (int)id
     * @param (String)name
     * @return json data
     *
     */
    public function updateShapeCategory()
    {
        $status = 0;
        if (!empty($this->_request) && $this->_request['id'] && isset($this->_request['name'])) {
            extract($this->_request);
            try {
                $chk_duplicate = "SELECT COUNT(*) AS duplicate FROM " . TABLE_PREFIX . "shape_cat WHERE category_name='" . $name . "' AND id !='" . $id . "'";
                $res = $this->executeFetchAssocQuery($chk_duplicate);

                if ($res[0]['duplicate']) {
                    $msg['msg'] = 'Duplicate Entry';
                } else {
                    $sql = "UPDATE " . TABLE_PREFIX . "shape_cat SET category_name='" . $name . "' WHERE id='" . $id . "'";
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
     *remove Shape category
     *
     *@param (String)apikey
     *@param (int)removeCategory
     *@return array
     *
     */
    public function removeShapeCategory()
    {
        $pCategory = $this->_request['removeCategory'];
        try {
            $sql = "select count(*) count from " . TABLE_PREFIX . "shape_cat where category_name = '$pCategory'";
            $row = $this->executeGenericDQLQuery($sql);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
        $response = array();
        if ($row[0]['count'] == "0") {
            // category not present error
            $response['status'] = false;
            $response['message'] = 'ERROR cateory not present';
        } else {
            try {
                $sql = "DELETE FROM " . TABLE_PREFIX . "shape_cat WHERE category_name= '$pCategory'";
                $this->executeGenericDMLQuery($sql);
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
            $response['status'] = true;
            $response['message'] = "'$pCategory' cateory delete successful !!";
        }
        $this->response($this->json($response), 200);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Get Sahapes
     *
     *@param (String)apikey
     *@param (String)categoryValue
     *@param (int)tabType
     *@param (int)sides
     *@return json data
     *
     */
    public function loadShapes()
    {
        try {
            $shapeArray = array();
            $categoryValue = $this->_request['categoryValue'];
            $searchedDesign = $this->_request['searchval'];
            $tabType = $this->_request['tabType'];
            $sides = $this->_request['sides'];
            $searchedDesign .= "%";
            if ($tabType == "N Shape") {
                $tabType = "nShape";
            } else {
                $tabType = "starShape";
            }
            $searchedDesign .= "_" . $tabType . "%";
            if ($sides != '') {
                $searchedDesign .= "_" . $sides;
            }
            $sql = "SELECT * FROM " . TABLE_PREFIX . "designs d inner join des_cat c on (d.category_id=c.id) ";
            $sql .= " WHERE lower(design_name) like lower('$searchedDesign') "; //%_nShape%_0
            if ($categoryValue && $categoryValue != 'All' && $categoryValue != '') {
                $sql .= " and c.category_name='$categoryValue' ";
            }
            $sql .= " and d.is_shape=1 and c.is_shape=1";

            $designsFromCatagory = $this->executeGenericDQLQuery($sql);
            foreach ($designsFromCatagory as $i => $row) {
                $shapeArray[$i]['id'] = $row['id'];
                $shapeArray[$i]['name'] = $row['design_name'];
                $shapeArray[$i]['url'] = $this->getDesignImageURL() . $row['file_name'] . '.svg';
            }
            $this->closeConnection();
            $this->response($this->json($shapeArray), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Get Sahape category
     *
     *@param (String)apikey
     *@param (Int)isShape
     *@return json data
     *
     */
    public function getShapeCatagories()
    {
        try {
            $isShape = $this->_request["isShape"];
            $catagoryArray = array();
            $sql = ($isShape == "true" || $isShape == undefined) ? "SELECT * FROM " . TABLE_PREFIX . "des_cat where is_shape=1" : "SELECT * FROM " . TABLE_PREFIX . "des_cat where is_shape=0";
            //$allCatagory = mysqli_query($con,$sql);
            $allCatagory = mysqli_query($this->db, $sql);
            while ($row = mysqli_fetch_array($allCatagory)) {
                array_push($catagoryArray, $row['category_name']);
            }
            $this->closeConnection();
            $this->response($this->json($catagoryArray), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Fetch shape tags
     *
     *@param (String)apikey
     *@param (String)category
     *@return json data
     *
     */
    public function fetchShapeTags()
    {
        try {
            $sql = "select distinct t.name from " . TABLE_PREFIX . "shape_tags t ";
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
     *delete shape by shape id
     *
     *@param (String)apikey
     *@param (int)shape_id
     *@return json data
     *
     */
    public function deleteShapeById()
    {
        $pShapeId = $this->_request['shape_id'];
        try {
            $sql = 'SELECT file_name FROM ' . TABLE_PREFIX . 'shapes WHERE id=' . $pShapeId;
            $res = $this->executeFetchAssocQuery($sql);
            $file = $res[0]['file_name'];
            $ds = DIRECTORY_SEPARATOR;
            $file = $this->getShapeImagePath() . $ds . $file;
            if (file_exists($file)) {
                @chmod($file, 07777);
                @unlink($file);
            }

            $sql = "delete from " . TABLE_PREFIX . "shapes where id=$pShapeId";
            $this->executeGenericDMLQuery($sql);
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
     *date modified 15-4-2016(dd-mm-yy)
     *saving shapes detials
     *
     *@param (String)apikey
     *@param (String)fileName
     *@param (String)shapeName
     *@param (Flopat)price
     *@param (String)categoryIdTxt
     *@param (String)tagsText
     *@return json data
     *
     */
    public function saveShapeDetails()
    {
        try {
            $fileName = $this->_request['fileName'];
            $shapeName = $this->_request['shapeName'];
            // to be parse category sub category objects
            $category_txt = $this->_request['categoryIdTxt'];
            $tagsText = $this->_request['tagsText'];
            $price = floatval($this->_request['price']);
            $shapeId = $this->getShapeId($fileName, $shapeName, $price);
            $tagIdArr = $this->getShapeTagIdArr($tagsText);
            $this->mapShapeTagRel($shapeId, $tagIdArr);
            $this->mapShapeCategoryRel($shapeId, $category_txt);
            $msg['status'] = ($shapeId) ? 'success' : 'failed';
        } catch (Exception $e) {
            $msg = array('Caught exception:' => $e->getMessage());
        }
        $this->response($this->json($msg), 200);
        // $this->mapDesign_category_subCategory_rel($shapeId,$category_subCategory_txt);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     * Get shape id
     *
     *@param (String)apikey
     *@param (String)fileName
     *@param (String)shapeName
     *@param (Flopat)price
     *@return json data
     *
     */
    public function getShapeId($fileName, $shapeName, $price)
    {
        /*trimming the data */
        try {
            $fileName = trim($fileName);
            $shapeName = trim($shapeName);
            $price = trim($price);
            //$isShape = trim($isShape);
            $sql = "select id from " . TABLE_PREFIX . "shapes  where file_name = '$fileName'";
            $row = $this->executeGenericDQLQuery($sql);
            $shapeId;
            if (sizeof($row) == 0) {
                // inser the detile of desings , get the id
                $sql = "insert into " . TABLE_PREFIX . "shapes(file_name,shape_name,price) values('$fileName','$shapeName',$price)";
                $shapeId = $this->executeGenericInsertQuery($sql);
            } else {
                // get the id
                $shapeId = $row[0]['id'];
                $sql = "UPDATE " . TABLE_PREFIX . "shapes set shape_name = '$shapeName' , price = $price where id = $shapeId";
                $this->executeGenericDMLQuery($sql);
            }
            return $shapeId;
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     * Get shape and tags
     *
     *@param (String)apikey
     *@param (String)tagsText
     *@return array
     *
     */
    public function getShapeTagIdArr($tagsText)
    {
        $reqTag = explode(",", $tagsText);
        try {
            $sql = "select id,name from " . TABLE_PREFIX . "shape_tags";
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
                    $sql = "insert into " . TABLE_PREFIX . "shape_tags(name) values('$reqTag[$i]')";
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
     *date modified 15-4-2016(dd-mm-yy)
     * mapping shape and tags
     *
     *@param (String)apikey
     *@param (Array)tagIdArr
     *@param (int)shapeId
     *@return array
     *
     */
    public function mapShapeTagRel($shapeId, $tagIdArr)
    {
        try {
            $sql = "select shape_id , tag_id from " . TABLE_PREFIX . "shape_tag_rel";
            $rows = $this->executeGenericDQLQuery($sql);
            for ($j = 0; $j < sizeof($tagIdArr); $j++) {
                $found = false;
                for ($k = 0; $k < sizeof($rows); $k++) {
                    if ($rows[$k]['shape_id'] == $shapeId && $rows[$k]['tag_id'] == $tagIdArr[$j]) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $sql = "insert into " . TABLE_PREFIX . "shape_tag_rel(shape_id,tag_id) values('$shapeId' , '$tagIdArr[$j]')";
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
     *date modified 15-4-2016(dd-mm-yy)
     *map shape,category rel
     *
     *@param (String)apikey
     *@param (String)category_txt
     *@param (int)shapeId
     *@return array
     *
     */
    public function mapShapeCategoryRel($shapeId, $category_txt)
    {
        $category_arr = explode("*", $category_txt);
        try {
            $sql = "select shape_id , category_id from " . TABLE_PREFIX . "shape_cat_rel";
            $rows = $this->executeGenericDQLQuery($sql);
            for ($i = 0; $i < sizeof($category_arr); $i++) {
                $found = false;
                for ($j = 0; $j < sizeof($rows); $j++) {
                    if ($rows[$j]['shape_id'] == $shapeId && $rows[$j]['category_id'] == $category_arr[$i]) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $sql = "insert into  " . TABLE_PREFIX . "shape_cat_rel (shape_id , category_id) values($shapeId,$category_arr[$i])";
                    $this->executeGenericDMLQuery($sql);
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
     *date modified 15-4-2016(dd-mm-yy)
     *un map shape from rel table
     *
     *@param (String)apikey
     *@param (String)relTableName
     *@param (int)pId
     *@return array
     *
     */
    public function unMapShapeFromRelTable($pId, $relTableName)
    {
        try {
            $sql = "delete from $relTableName where $relTableName.shape_id = $pId";
            $this->executeGenericDMLQuery($sql);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Edit shape details
     *
     *@param (String)apikey
     *@param (int)categoryId
     *@param (String)categoryName
     *@return array
     *
     */
    public function editShapeCategory()
    {
        $categoryId = $this->_request['categoryId'];
        $categoryName = $this->_request['categoryName'];
        try {
            $sql = "update " . TABLE_PREFIX . "shape_cat set category_name ='$categoryName' where shape_cat.id = $categoryId";
            $row = $this->executeGenericDMLQuery($sql);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
        $response = array();
        $response['status'] = true;
        $response['message'] = "category name changed to $categoryName";
        $this->response($this->json($response), 200);
    }
}
