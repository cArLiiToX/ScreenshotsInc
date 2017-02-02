<?php
/* Check Un-authorize Access */
if (!defined('accessUser')) {
    die("Error");
}

class Products extends ProductsStore
{

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Get mask data
     *
     *@param (String)apikey
     *@param (Int)productid
     *@return json data
     *
     */
    public function getMaskData($sides = 1)
    {
        //Modified
        $apiKey = $this->_request['apikey'];
        if (isset($this->_request['isTemplate'])) {
            $isTemplate = $this->_request['isTemplate'];
        }
        if (isset($this->_request['productid'])) {
            $productid = $this->_request['productid'];
        } else {
            $this->response('invalid info', 204);
        }
        //204 - immediately termiante this request

        if ($this->isValidCall($apiKey)) {
            try {
                $sql = "Select * from " . TABLE_PREFIX . "mask_data where productid='$productid' order by side";
                $result = $this->executeFetchAssocQuery($sql);
                $data = array();
                if (!empty($result)) {
                    $dimension_sql = "SELECT ps.height,ps.width FROM " . TABLE_PREFIX . "product_sides_sizes AS pss JOIN " . TABLE_PREFIX . "print_size AS ps ON pss.printsize = ps.name WHERE pss.productid='" . $productid . "' ORDER BY pss.side";
                    $dimensions = $this->executeFetchAssocQuery($dimension_sql);
                    $resultLength = sizeof($result);
                    foreach ($result as $k => $v) {
                        $data[$k]['dimensions']['boundheight'] = $dimensions[$k]['height'];
                        $data[$k]['dimensions']['boundwidth'] = $dimensions[$k]['width'];
                        $data[$k]['mask'] = str_replace('\"', '', $v['mask_json_data']);
                        $data[$k]['bounds'] = $v['bounds_json_data'];
                        $data[$k]['customsize'] = $v['custom_size_data'];
                        $data[$k]['custom_mask'] = $v['custom_mask'];
                        $data[$k]['mask_height'] = $v['mask_height'];
                        $data[$k]['mask_width'] = $v['mask_width'];
                        $data[$k]['mask_price'] = $v['mask_price'];
                        $data[$k]['scale_ratio'] = $v['scale_ratio'];
                        $data[$k]['side'] = $v['side'];
                        $data[$k]['is_cropMark'] = $v['is_cropMark'];
                        $data[$k]['is_safeZone'] = $v['is_safeZone'];
                        $data[$k]['cropValue'] = $v['cropValue'];
                        $data[$k]['safeValue'] = $v['safeValue'];
                        $data[$k]['scaleRatio_unit'] = $v['scaleRatio_unit'];
                        $data[$k]['custom_min_height'] = $v['cust_min_height'];
                        $data[$k]['custom_min_width'] = $v['cust_min_width'];
                        $data[$k]['custom_max_height'] = $v['cust_max_height'];
                        $data[$k]['custom_max_width'] = $v['cust_max_width'];
                        $data[$k]['custom_boundary_price'] = $v['cust_bound_price'];
                        $data[$k]['mask_name'] = $v['mask_name'];
                        $data[$k]['mask_id'] = $v['mask_id'];
                        $data[$k]['custom_boundary_unit'] = $v['custom_boundary_unit'];
                        $data[$k]['custom_min_height_mask'] = $v['custom_mask_min_height'];
                        $data[$k]['custom_min_width_mask'] = $v['custom_mask_min_width'];
                        $data[$k]['custom_max_height_mask'] = $v['custom_mask_max_height'];
                        $data[$k]['custom_max_width_mask'] = $v['custom_mask_max_width'];
                        $data[$k]['isBorderEnable'] = $v['isBorderEnable'];
						$data[$k]['isSidesAdded'] = $v['isSidesAdded'];
						$data[$k]['sidesAllowed'] = $v['sidesAllowed'];
                    }
                    $this->log('maskDataRow :: mask : ' . json_encode($data));
                    if ($resultLength < $sides) {
                        for ($i = $resultLength; $i < $sides; $i++) {
                            $data[$i] = $arr;
                        }
                    }
                } else {
                    $sql = "SELECT bounds FROM " . TABLE_PREFIX . "general_setting LIMIT 1";
                    $res = $this->executeFetchAssocQuery($sql);
                    $a = $this->formatJSONToArray($res[0]['bounds']);
                    $sql = "SELECT height AS boundheight,width AS boundwidth FROM " . TABLE_PREFIX . "print_size WHERE name='A3' LIMIT 1";
                    $dimensions = $this->executeFetchAssocQuery($sql);

                    $arr = array(
                        'dimensions' => $dimensions[0],
                        'mask' => $a['mask'],
                        'bounds' => $a['bounds'],
                        'customsize' => $a['customsize'],
                        'custom_mask' => $a['custom_mask'],
                        'mask' => $a['mask'],
                        'mask_height' => $a['mask_height'],
                        'mask_width' => $a['mask_width'],
                        'mask_price' => 0.00,
                        'scale_ratio' => $a['scale_ratio'],
                        'side' => $a['side'],
                        'is_cropMark' => 0, 'is_safeZone' => 0, 'cropValue' => 0.00, 'safeValue' => 0.00, 'scaleRatio_unit' => 1, 'mask_name' => '', 'mask_id' => '0', 'isBorderEnable' => 0,'isSidesAdded' => 0,'sidesAllowed' => 0
                    );
                    for ($i = 0; $i < $sides; $i++) {
                        $data[$i] = $arr;
                    }
                }
                if (isset($this->_request['returns']) && $this->_request['returns'] == true) {
                    $this->log('if');

                    return $this->json($data);
                } else {
                    $this->log('if');
                    if ($isTemplate == 1) {
                        return $this->json($data);
                    } else {
                        $this->response($this->json($data), 200);
                    }
                }
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        } else {
            $msg = array("status" => "invalidkey");
            if (isset($this->_request['returns']) && $this->_request['returns'] == true) {
                return $this->json($msg);
            } else {
                $this->response($this->json($msg), 200);
            }

        }
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Get Dtg print sizes of prodfuct side wise
     *
     *@param (String)apikey
     *@param (Int)productid
     *@return json data
     *
     */
    public function getDtgPrintSizesOfProductSides($productid)
    {
        if ($productid) {
            $printArray = array();
            try {
                $sql = "SELECT pss.printsize,pss.side,pss.is_transition, ps.pk_id FROM " . TABLE_PREFIX . "product_sides_sizes AS pss JOIN " . TABLE_PREFIX . "print_size AS ps ON pss.printsize = ps.name WHERE pss.productid='" . $productid . "' ORDER BY pss.side";
                $res = $this->executeFetchAssocQuery($sql);
                if (!empty($res)) {
                    foreach ($res as $k => $v) {
                        $printArray[$k]['size'] = $v['printsize'];
                        $printArray[$k]['side'] = $v['side'];
                        $printArray[$k]['id'] = $v['pk_id'];
                        $printArray[$k]['is_transition'] = $v['is_transition'];
                    }
                } else {
                    $printArray = array("status" => "nodata");
                }
                //$this->closeConnection();
                return $printArray;
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                return $result;
            }
        } else {
            $msg = array("status" => "invalid");
            return $msg;
        }
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified (dd-mm-yy)
     *Get print area type
     *@param (int)productId
     *
     */
    public function getPrintareaType($productId)
    {
        $apiKey = $this->_request['apikey'];
        $dataArray = array();
        $sql = "SELECT * FROM " . TABLE_PREFIX . "product_printarea_type WHERE productid = " . $productId;

        $dataFromValue = mysqli_query($this->db, $sql);

        if (mysqli_num_rows($dataFromValue) > 0) {
            if ($row = mysqli_fetch_array($dataFromValue)) {
                $dataArray['mask'] = $row['mask'];
                $dataArray['bounds'] = $row['bounds'];
                $dataArray['custom_size'] = $row['custom_size'];
                $dataArray['customMask'] = $row['custom_mask'];
                $dataArray['unit_id'] = intval($row['unit_id']);
                $dataArray['pricePerUnit'] = floatval($row['price_per_unit']);
                $dataArray['maxWidth'] = floatval($row['max_width']);
                $dataArray['maxHeight'] = floatval($row['max_height']);

                $sql = "SELECT name FROM " . TABLE_PREFIX . "units WHERE id = " . $dataArray['unit_id'];
                $unitData = mysqli_fetch_assoc(mysqli_query($this->db, $sql));
                $dataArray['unit_name'] = $unitData['name'];
            }
        } else {
            $dataArray['mask'] = "false";
            $dataArray['bounds'] = "true";
            $dataArray['custom_size'] = "false";
            $dataArray['customMask'] = "false";
            $dataArray['unit_id'] = 1;
            $dataArray['unit_name'] = 'in';
            $dataArray['pricePerUnit'] = 0;
            $dataArray['maxWidth'] = 500;
            $dataArray['maxHeight'] = 500;
        }
        return $dataArray;
    }
    ####################################################
    ############## syncOrdersZip ###################
    ####################################################
    public function fetchProductFeatures($productId, $productCategoryIdArray)
    {
        $apiKey = $this->_request['apikey'];
        if ($this->isValidCall($apiKey)) {
            $featureIdsArray = array();
            $featureNamesArray = array();
            $featureTypesArray = array();
            $result = array();
            $sql = "SELECT id,name,type FROM " . TABLE_PREFIX . "features WHERE mandatory_status=0 && status=1 && product_level_status=1";
            $featuresFromValue = mysqli_query($this->db, $sql);
            while ($row = mysqli_fetch_array($featuresFromValue)) {
                array_push($featureIdsArray, $row['id']);
                array_push($featureNamesArray, $row['name']);
                array_push($featureTypesArray, $row['type']);
            }
            $productFeaturesStatusData = array();

            $sql = "SELECT feature_id FROM " . TABLE_PREFIX . "product_feature_rel WHERE product_id= '" . $productId . "' && status = 1";
            $productFeatures = mysqli_query($this->db, $sql);
            if (mysqli_num_rows($productFeatures) > 0) {
                while ($row = mysqli_fetch_array($productFeatures)) {
                    $productFeatureId = $row['feature_id'];
                    $key = array_search($productFeatureId, $featureIdsArray);
                    $productFeature = $featureTypesArray[$key];
                    array_push($result, $productFeature);
                }
            } else {
                $categoryFeaturesList = array();
                for ($j = 0; $j < sizeof($productCategoryIdArray); $j++) {
                    $sql = "SELECT feature_id FROM " . TABLE_PREFIX . "productcategory_feature_rel WHERE product_category_id= " . $productCategoryIdArray[$j] . " && status = 1";
                    $categoryFeatures = mysqli_query($this->db, $sql);
                    if (mysqli_num_rows($categoryFeatures) > 0) {
                        while ($row = mysqli_fetch_array($categoryFeatures)) {
                            $categoryFeatureId = $row['feature_id'];
                            $key = array_search($categoryFeatureId, $featureIdsArray);
                            $categoryFeature = $featureTypesArray[$key];
                            if (!in_array($categoryFeature, $categoryFeaturesList)) {
                                array_push($result, $productFeature);
                            }
                            if (sizeof($categoryFeaturesList) == sizeof($featureTypesArray)) {
                                break;
                            }

                        }
                    }
                }
                $result = $categoryFeaturesList;
            }
            return $result;
        } else {
            $msg = array("status" => "invalid");
            return $this->json($msg);
        }
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *fetch Size variant additional price in client
     *
     *@param (String)apikey
     *@param (Int)confProductId
     *@param (Int)print_method_id
     *@return json data
     *
     */
    public function getSizeVariantAdditionalPriceClient($confProductId, $print_method_id)
    {
        if (isset($confProductId) && $confProductId && isset($print_method_id) && $print_method_id) {
            try {
                $sql = "SELECT svap.xe_size_id,svap.percentage FROM " . TABLE_PREFIX . "size_variant_additional_price as svap WHERE svap.product_id=" . $confProductId . " AND svap.print_method_id=" . $print_method_id . ' ORDER BY svap.xe_size_id DESC';
                $result = $this->executeFetchAssocQuery($sql);
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
            }
            return $result;
        } else {
            $msg = array("invalid");
            return $msg;
        }
    }
    /**
     *
     *date created 3-6-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *get template side by product id and product category id
     *
     *@param (String)apikey
     *@param (int)confProductId
     *@return json data
     *
     */
    public function getProductTemplateByProductId($confProductId){
        $imageurl = $this->getProductTemplatePath();
        $side_sql = "SELECT distinct pts.pk_id,pts.side_name,pts.image,ptr.temp_id
                    FROM " . TABLE_PREFIX . "product_template AS pt ," . TABLE_PREFIX . "product_temp_rel AS ptr ,
                    " . TABLE_PREFIX . "product_temp_side AS pts
                    WHERE ptr.product_id='" . $confProductId . "' AND pts.product_temp_id =ptr.temp_id ORDER BY pts.sort_order";
        $row = $this->executeFetchAssocQuery($side_sql);
		
		$side_arr = array();
        if (!empty($row)) {
            $side_arr['tepmlate_id'] = $row[0]['temp_id'];
            foreach ($row as $key => $v) {
                $side_arr['side_id'][] = $v['pk_id'];
                $side_arr['thumbsides'][] = $imageurl . $v['temp_id'] . '/' . $v['pk_id'] . '.' . $v['image'];
                $side_arr['sides'][] = $imageurl . $v['temp_id'] . '/' . $v['pk_id'] . '.' . $v['image'];
            }
        }
        return $side_arr;
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Get additional printing prices of each color variants
     *
     *@param (String)apikey
     *@param (Int)productid
     *@param (Int)variantid
     *@return json data
     *
     */
    public function getAdditionalPrintingPriceOfVariants($productid, $variantid)
    {
        if ($productid) {
            try {
                $sql = "SELECT pm.pk_id, pm.name, pap.price,pap.is_whitebase FROM " . TABLE_PREFIX . "print_method as pm JOIN " . TABLE_PREFIX . "product_additional_prices AS pap ON pm.pk_id=pap.print_method_id
                WHERE pap.product_id=" . $productid . " AND variant_id=" . $variantid; //." AND pm.pk_id=".$printingType;
                $res = $this->executeFetchAssocQuery($sql);
                $printArray = array();
                $printArray = array();
                if (empty($res)) {
                    $sql = "SELECT distinct pm.pk_id as printid,pm.name as printName
                            FROM " . TABLE_PREFIX . "print_method pm
                            JOIN " . TABLE_PREFIX . "print_setting  pst ON pm.pk_id=pst.pk_id
                            LEFT JOIN " . TABLE_PREFIX . "print_method_setting_rel pmsr ON pst.pk_id=pmsr.print_setting_id where pst.is_default=1";
                    $default_id = $this->executeFetchAssocQuery($sql);
                    $printArray[0]['prntMthdId'] = $default_id[0]["printid"];
                    $printArray[0]['prntMthdName'] = $default_id[0]["printName"];
                    $printArray[0]['prntMthdPrice'] = '0.00';
                    $printArray[0]['is_whitebase'] = '0';
                } else {
                    foreach ($res as $k => $v) {
                        $printArray[$k]['prntMthdId'] = $v['pk_id']; //$productid;
                        $printArray[$k]['prntMthdName'] = $v['name'];
                        $printArray[$k]['prntMthdPrice'] = $v['price'];
                        $printArray[$k]['is_whitebase'] = intval($v['is_whitebase']);
                    }
                }
                return $printArray;
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        } else {
            $msg = array("inputData" => "invalid");
            return $msg;
        }
    }

    /**
     *
     *date of created 8-2-2016(dd-mm-yy)
     *date of Modified 13-4-2016(dd-mm-yy)
     *get discount data map to product by product id
     *
     * @param (String)apikey
     * @param (int)pid
     * @return JSON  data
     *
     */
    public function getDiscountToProduct($pid)
    {
        try {
            $sql = "SELECT * FROM " . TABLE_PREFIX . "product_print_discount_rel WHERE product_id ='" . $pid . "'";
            $rows = $this->executeGenericDQLQuery($sql);
            $countRows = sizeof($rows);
            $result = array();
            $resultData = array();
            $resultDiscount = array();
            for ($j = 0; $j < $countRows; $j++) {
                $resultData[$j]['print_id'] = $rows[$j]['print_id'];
                $resultData[$j]['discount_id'] = $rows[$j]['discount_id'];
                $sql_feth = "SELECT name FROM " . TABLE_PREFIX . "discount WHERE pk_id ='" . $rows[$j]['discount_id'] . "'";
                $row = $this->executeGenericDQLQuery($sql_feth);
                for ($i = 0; $i < sizeof($row); $i++) {
                    $result = $row[$i]['name'];
                }
                $resultData[$j]['discount_name'] = $result;
            }
        } catch (Exception $e) {
            $resultData = array('Caught exception:' => $e->getMessage());
        }
        return (array_values($resultData));
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Set size variant additionalprice
     *
     *@param (String)apikey
     *@param (Int)product_id
     *@param (Array)print_detail
     *@return json data
     *
     */
    public function setSizeVariantAdditionalPrice()
    {
        try {
            $status = 0;
            $insert_success = array('status' => 'success');
            if (!empty($this->_request) && isset($this->_request['product_id']) && !empty($this->_request['print_detail'])) {
                extract($this->_request);
                $str = '';
                foreach ($print_detail as $k => $v) {
                    foreach ($v['sizePrice'] as $v1) {
                        if ($v1['pk_id']) {
                            $usql = "UPDATE " . TABLE_PREFIX . "size_variant_additional_price SET percentage='" . $v1['percentage'] . "' WHERE pk_id='" . $v1['pk_id'] . "'";
                            $status = $this->executeGenericDMLQuery($usql);
                        } else {
							$v1['print_method_id'] = (isset($v1['print_method_id']) && $v1['print_method_id'])?$v1['print_method_id']:0;
							$sql = "INSERT INTO " . TABLE_PREFIX . "size_variant_additional_price(product_id,print_method_id,xe_size_id,percentage) VALUES('" . $product_id . "','" . $v1['print_method_id'] . "','" . $v['xe_size_id'] . "','" . $v1['percentage'] . "')";
                            $status = $this->executeGenericDMLQuery($sql);
                        }

                    }
                }
                $insert_success = $this->getSizeVariantAdditionalPrice($product_id);
            }
            if ($status) {
                $msg = $insert_success;
            } else {
                $msg['status'] = 'failure';
            }
        } catch (Exception $e) {
            $msg = array('Caught exception:' => $e->getMessage());
        }
        $this->response($this->json($msg), 200);
    }
}
