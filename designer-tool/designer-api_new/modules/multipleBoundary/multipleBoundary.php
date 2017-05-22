<?php
/* Check Un-authorize Access */
if (!defined('accessUser')) {
    die("Error");
}

class MultipleBoundary extends UTIL
{
    /*
     *
     *date created 15-12-2016(dd-mm-yy)
     *date modified 27-12-2016(dd-mm-yy)
     *
     *purpose: saving multiple boundary details.
     *@param varchar $pid productId
     *@param string $maskData mask data in json
     *@return json
     *
     */
    public function saveMultipleBoundary($pid, $MultiBoundData)
    {
        $maskDataAr = $this->formatJSONToArray($MultiBoundData);
        $multiBoundDir = $this->getMultipleBondaryPath();
        if (!file_exists($multiBoundDir)) {
            mkdir($multiBoundDir, 0777, true);
        }
        try {
            //falsify existing print type of the product
            $updatePrintType = "INSERT INTO " . TABLE_PREFIX . "product_printarea_type(productid, mask, bounds, custom_size, custom_mask) VALUES ('" . $pid . "', 'false', 'false', 'false', 'false') ON DUPLICATE KEY UPDATE mask='false',bounds='false',custom_size='false', custom_mask='false'";
            $updatePT = $this->executeGenericDMLQuery($updatePrintType);
            // clear existing mask relation for this product
            $deleteRec = "DELETE FROM m1 , m2, m3 USING  " . TABLE_PREFIX . "multiple_boundary_rel as m1 INNER JOIN " . TABLE_PREFIX . "multi_bound_print_profile_rel as m2 INNER JOIN " . TABLE_PREFIX . "multiple_boundary_settings as m3 WHERE m1.product_id = '" . $pid . "' AND m2.product_id = m1.product_id AND m3.boundary_rel_id = m1.id";
            $flush = $this->executeGenericDMLQuery($deleteRec);
            $deleteSingleMask = "DELETE FROM " . TABLE_PREFIX . "mask_data WHERE productid = '" . $pid . "'";
            $clear = $this->executeGenericDMLQuery($deleteSingleMask);
            //  For each side
            $sideIDX = 1;
            foreach ($maskDataAr as $key => $side) {
                $parentMaskId = $side['id'];
                // check if new boundary or old
                if ($parentMaskId > 0) {
                    $clearOldMask = "DELETE FROM m1 , m2 USING  " . TABLE_PREFIX . "multiple_boundary as m1 INNER JOIN " . TABLE_PREFIX . "multiple_boundary_child as m2 WHERE m1.id = '" . $parentMaskId . "' AND m2.parent_mask_id = m1.id";
                    $removeOldMask = $this->executeGenericDMLQuery($clearOldMask);
                }
                $svgdata = $side['svg'];
                $name = $side['name'];
                $maskBase64Data = base64_decode($mask_image);
                $maskImage = str_replace(array(' ', '"', '\\', '/', ':', '?', '*', '<', '>', '|'), '_', $name) . '.svg';
                $svgImageFilePath = $multiBoundDir . $maskImage;
                file_put_contents($svgImageFilePath, $maskBase64Data);
                $maskheight = 0.00; //real data will be inserted in future
                $maskwidth = 0.00; //real data will be inserted in future
                $newMaskQry = "INSERT INTO " . TABLE_PREFIX . "multiple_boundary(name, svg_data, thumb_image, mask_height, mask_width) VALUES ('$name', '" . $svgdata . "', '$maskImage', '" . $maskheight . "', '" . $maskwidth . "')";
                $parentMaskId = $this->executeGenericInsertQuery($newMaskQry);
                // insert child mask data of new multiple boundary
                foreach ($side['child_masks'] as $childMask) {
                    $newChildQry = "INSERT INTO " . TABLE_PREFIX . "multiple_boundary_child(parent_mask_id, name) VALUES('" . $parentMaskId . "', '" . $childMask['boundary_name'] . "')";
                    $newChildQry .=
                    $newMask = $this->executeGenericDMLQuery($newChildQry);
                }
                // get id of child masks for the multiple boundary
                $childQry = "SELECT id FROM " . TABLE_PREFIX . "multiple_boundary_child WHERE parent_mask_id = '" . $parentMaskId . "'";
                $childArr = $this->executeGenericDQLQuery($childQry);
                foreach ($childArr as $key => $child) {
                    $relQry1 = "INSERT INTO " . TABLE_PREFIX . "multiple_boundary_rel(product_id, side_index, parent_mask_id, child_mask_id) VALUES ('" . $pid . "', '" . $sideIDX . "', '" . $parentMaskId . "', '" . $child['id'] . "')";
                    $newId = $this->executeGenericInsertQuery($relQry1);
                    //  insert into setting table
                    $relQry = "INSERT INTO " . TABLE_PREFIX . "multiple_boundary_settings(boundary_rel_id, restrict_design) VALUES ('" . $newId . "', '" . $side['child_masks'][$key]['restrict_design'] . "')";
                    $newSet = $this->executeGenericDMLQuery($relQry);
                    // insert into print profile table
                    foreach ($side['child_masks'][$key]['print_methods'] as $methodId) {
                        $printQry = "INSERT INTO " . TABLE_PREFIX . "multi_bound_print_profile_rel(product_id, side_index, parent_mask_id, child_mask_id, print_profile_id) VALUES ('" . $pid . "', '" . $sideIDX . "', '" . $parentMaskId . "', '" . $child['id'] . "', '" . $methodId . "')";
                        $PrintMet = $this->executeGenericDMLQuery($printQry);
                    }
                }
                $sideIDX++;
            }
            if ($PrintMet) {
                $msg = array("status" => "success");
            } else {
                $msg = array("status" => "Multiple Boundary Could not be saved!");
            }

        } catch (Exception $e) {
            $msg = array('Caught exception:' => $e->getMessage());
        }
        $this->response($this->json($msg), 200);
    }

    /*
     *
     *date created 15-12-2016(dd-mm-yy)
     *date modified 27-12-2016(dd-mm-yy)
     *
     *purpose: Getting list of existing multiple boundaries.
     *
     */
    public function getMultipleBoundaryList()
    {
        $status = 0;
        try {
            $sql = "SELECT * from " . TABLE_PREFIX . "multiple_boundary ORDER BY id DESC";
            $rows = $this->executeGenericDQLQuery($sql);
            $resultArr = array();
            $url = $this->getCurrentUrl() . '/designer-tool' . self::HTML5_MULTIPLE_BOUNDARY_DIR;
            $count = sizeof($rows);
            for ($i = 0; $i < $count; $i++) {
                $resultArr[$i]['id'] = $rows[$i]['id'];
                $resultArr[$i]['name'] = $rows[$i]['name'];
                $resultArr[$i]['url'] = $url . $rows[$i]['thumb_image'];
                $resultArr[$i]['thumb_image'] = $rows[$i]['thumb_image'];
                $resultArr[$i]['maskheight'] = $rows[$i]['mask_height'];
                $resultArr[$i]['maskwidth'] = $rows[$i]['mask_width'];
            }
            if ($rows) {
                $this->response($this->json($resultArr), 200);
            } else {
                $msg = array("status" => "nodata");
            }

        } catch (Exception $e) {
            $msg = array('Caught exception:' => $e->getMessage());
        }
        $this->response($this->json($msg), 200);
    }

    /*
     *
     *date created 15-12-2016(dd-mm-yy)
     *date modified 27-12-2016(dd-mm-yy)
     *
     *Not used for now. will be implemented in near future
     *
     */
    public function updateMultipleBoundary()
    {
        if (isset($this->_request['apikey']) && $this->isValidCall($this->_request['apikey'])) {
            extract($this->_request);
            try {
                foreach ($MultipleBoundaries as $mb) {
                    $sql = "UPDATE " . TABLE_PREFIX . "multiple_boundary set name= '" . $mb['maskName'] . "',mask_height= '" . $mb['maskHeight'] . "', mask_width= '" . $mb['maskWidth'] . "', date_modified= '" . date('Y-m-d H:i:s', date()) . "' where id= '" . $mb['id'] . "';";
                    $status = $this->executeGenericDMLQuery($sql);
                }
                $settingsObj = Flight::setting();
                $settingsObj->allSettingsDetails(1);
                $msg['status'] = ($status) ? $this->getMultipleBoundaryList() : 'failed';
            } catch (Exception $e) {
                $msg = array('Caught exception:' => $e->getMessage());
            }
        } else {
            $msg = array('status' => 'invaliedkey');
        }
        $this->response($this->json($msg), 200);

    }

    /*
     *
     *date created 15-12-2016(dd-mm-yy)
     *date modified 27-12-2016(dd-mm-yy)
     *
     *purpose: Not used for now. will be implemented in near future
     *
     */
    public function deleteMultipleBoundary()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('!POST', 406);
        }
        if (isset($this->_request['apikey']) && $this->isValidCall($this->_request['apikey'])) {
            extract($this->_request);
            try {
                $sql = "DELETE FROM m1 , m2 , m3 , m4 , m5 USING  " . TABLE_PREFIX . "multiple_boundary as m1 INNER JOIN " . TABLE_PREFIX . "multiple_boundary_child as m2 INNER JOIN  " . TABLE_PREFIX . "multiple_boundary_rel as m3 INNER JOIN " . TABLE_PREFIX . "multi_bound_print_profile_rel as m4 INNER JOIN  " . TABLE_PREFIX . "multiple_boundary_settings as m5 WHERE m1.id = '" . $maskId . "' AND m2.parent_mask_id = m1.id AND m3.parent_mask_id = m1.id AND m4.parent_mask_id = m1.id AND m5.boundary_rel_id = m3.id;";
                $status = $this->executeGenericDMLQuery($sql);
                $settingsObj = Flight::setting();
                $settingsObj->allSettingsDetails(1);
                $msg['status'] = ($status) ? $this->getMultipleBoundaryList() : 'failed';
            } catch (Exception $e) {
                $msg = array('Caught exception:' => $e->getMessage());
            }
        } else {
            $msg = array('status' => 'invaliedkey');
        }
        $this->response($this->json($msg), 200);

    }

    /*
     *
     *date created 15-12-2016(dd-mm-yy)
     *date modified 27-12-2016(dd-mm-yy)
     *
     *purpose: Getting multiple boundary details for a product
     *@param varchar $pid productId
     *@return Array
     */
    public function getMultiBoundMaskData($pid)
    {
        try {
            $parentMaskId = "";
            $sideId = "";
            $multBound = array();
            $prodQry = "SELECT DISTINCT side_index,parent_mask_id from " . TABLE_PREFIX . "multiple_boundary_rel WHERE product_id = '" . $pid . "'";
            $maskSVG = $this->executeGenericDQLQuery($prodQry);
            // loop throgh all sides with it's parent mask id
            foreach ($maskSVG as $side) {
                $sideId = $side['side_index'];
                $parentMaskId = $side['parent_mask_id'];
                $childMaskData = array();
                $childMaskDet = array();
                $maskDet = "SELECT m1.*, m2.id as id1, m2.parent_mask_id, m2.name as name1 FROM " . TABLE_PREFIX . "multiple_boundary m1 INNER JOIN" . TABLE_PREFIX . " multiple_boundary_child m2 ON m1.id=m2.parent_mask_id WHERE m1.id = " . $parentMaskId;
                $childMasks = $this->executeGenericDQLQuery($maskDet);
                // print_r($childMasks);exit();
                foreach ($childMasks as $key => $child) {
                    $childMaskDet[$key]['boundary_name'] = $child['name1'];
                    $childMaskID = $child['id1'];
                    // Get all settings for child mask
                    $childSet = "SELECT m1.id as pkid, m2.* FROM " . TABLE_PREFIX . "multiple_boundary_rel m1 INNER JOIN" . TABLE_PREFIX . " multiple_boundary_settings m2 ON m1.id=m2.boundary_rel_id WHERE m1.product_id =" . $pid . " AND m1.side_index = " . $sideId . " AND m1.parent_mask_id =" . $parentMaskId . " AND m1.child_mask_id = " . $childMaskID;
                    $maskSettings = $this->executeGenericDQLQuery($childSet);
                    $childMaskDet[$key]['restrict_design'] = $maskSettings[0]['restrict_design'];
                    // get print profiles of child masks
                    $thisPrintPro = array();
                    $PrintProfQry = "SELECT print_profile_id from" . TABLE_PREFIX . " multi_bound_print_profile_rel WHERE product_id = " . $pid . " AND side_index = " . $sideId . " AND parent_mask_id = " . $parentMaskId . " AND child_mask_id = " . $child['id1'];
                    $printArr = $this->executeGenericDQLQuery($PrintProfQry);
                    foreach ($printArr as $childMask) {
                        $thisPrintPro[] = $childMask['print_profile_id'];
                    }
                    $childMaskDet[$key]['print_methods'] = $thisPrintPro;
                }
                $childMaskData['child_masks'] = $childMaskDet;
                $childMaskData['svg'] = $childMasks[0]['svg_data'];
                $childMaskData['name'] = $childMasks[0]['name'];
                $childMaskData['id'] = $childMasks[0]['id'];
                $multBound[] = $childMaskData;
            }

        } catch (Exception $e) {
            $msg = array('Caught exception:' => $e->getMessage());
        }
        return $multBound;
    }

    /*
     *
     *date created 6-1-2017(dd-mm-yy)
     *date modified 6-1-2017(dd-mm-yy)
     *
     *purpose: Getting multiple boundary printmethods per product
     *@param varchar $pid productId
     *@param varchar $apiket api key
     *@return jason
     */
    public function getMultiBoundPrintMethods()
    {
        $pid = $this->_request['pid'];
        $printArr = array();
        if (isset($this->_request['apikey']) && $this->isValidCall($this->_request['apikey'])) {
            try {
                $sql = "SELECT m.*, c.name as child_name FROM " . TABLE_PREFIX . "multi_bound_print_profile_rel as m INNER JOIN " . TABLE_PREFIX . "multiple_boundary_child as c  WHERE m.product_id ='" . $pid . "' AND c.id = child_mask_id";
                $allPrintRecords = $this->executeGenericDMLQuery($sql);
                if ($allPrintRecords->num_rows > 0) {
                    foreach ($allPrintRecords as $method) {
                        $thisIDX = $method['side_index'];
                        $sectionDetail['boundary_name'] = $method['child_name'];
                        $sectionDetail['print_profile_id'] = $method['print_profile_id'];
                        $printArr['pid'] = $pid;
                        $printArr[$thisIDX][] = $sectionDetail;
                    }
                }
            } catch (Exception $e) {
                $msg = array('Caught exception:' => $e->getMessage());
            }
        } else {
            $msg = array('status' => 'invalied key');
        }
        $this->response($this->json($printArr), 200);
    }

}
