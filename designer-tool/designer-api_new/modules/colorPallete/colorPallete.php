<?php
/* Check Un-authorize Access */
if (!defined('accessUser')) {
    die("Error");
}

class ColorPallete extends UTIL
{
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Fetch paletta category
     *
     *@param (String)apikey
     *@return json data
     *
     */
    public function fetchPaletteCategories()
    {
        $apiKey = $this->_request['apikey'];
        if ($this->isValidCall($apiKey)) {
            try {
                $catagoryArray = array();
                if ($this->_request['customer'] && $this->_request['customer'] == '1') {
                    $sql = "SELECT * FROM " . TABLE_PREFIX . "palette_category WHERE is_available=1";
                } else {
                    $sql = "SELECT * FROM " . TABLE_PREFIX . "palette_category";
                }
				$sql .= " ORDER BY name";
                $categoryDetail = array();
                $rows = $this->executeGenericDQLQuery($sql);
                if ($this->_request['customer'] && $this->_request['customer'] == '1') {
                    $categoryDetail[0]['id'] = '0';
                    $categoryDetail[0]['category_name'] = 'Default';
                    for ($i = 1; $i <= sizeof($rows); $i++) {
                        $categoryDetail[$i]['id'] = $rows[$i - 1]['id'];
                        $categoryDetail[$i]['category_name'] = $rows[$i - 1]['name'];
                    }
                } else {
                    for ($i = 0; $i < sizeof($rows); $i++) {
                        $categoryDetail[$i]['id'] = $rows[$i]['id'];
                        $categoryDetail[$i]['category_name'] = $rows[$i]['name'];
                        $categoryDetail[$i]['is_available'] = intval($rows[$i]['is_available']);
                    }
                }
                if (isset($this->_request['returns']) && $this->_request['returns'] == true) {
                    return $categoryDetail;
                } else {
                    $this->closeConnection();
                    $this->response($this->json($categoryDetail), 200);
                }
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        } else {
            $msg = array("status" => "invalid");
            if (isset($this->_request['returns']) && $this->_request['returns'] == true) {
                return $this->json($msg);
            } else {
                $this->response($this->json($msg), 200);
            }

        }
    }

    /**
     *
     *date of created 9-3-2016(dd-mm-yy)
     *date of Modified 13-4-2016(dd-mm-yy)
     *get status of all colors
     *
     * @param (String)apikey
     * @return JSON  data
     *
     */
    public function getColorStatus()
    {
        if (isset($this->_request['apikey']) && $this->isValidCall($this->_request['apikey'])) {
            try {
                $sql = "SELECT DISTINCT  p.is_pattern
				FROM " . TABLE_PREFIX . "palette_category AS pc
				RIGHT JOIN " . TABLE_PREFIX . "palette_category_rel AS pcl ON pc.id = pcl.category_id
				JOIN " . TABLE_PREFIX . "palettes AS p ON pcl.palette_id = p.id
				WHERE pc.is_available = '1'";
                $rows = $this->executeFetchAssocQuery($sql);
                $rec = array();
                $result = array();
                if (!empty($rows)) {
                    foreach ($rows as $k => $v) {
                        $rec[$k] = $v['is_pattern'];
                    }
                }
                $result[0]['rgb'] = (in_array(0, $rec)) ? 1 : 0;
                $result[0]['pattern'] = (in_array(1, $rec)) ? 1 : 0;
                $result[0]['cmyk'] = (in_array(2, $rec)) ? 1 : 0;
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        } else {
            $result['status'] = 'invalidapikey';
        }

        $this->response($this->json($result), 200);
    }

    /**
     *date of created 10-3-2016(dd-mm-yy)
     *date of Modified 13-4-2016(dd-mm-yy)
     *fetch paletta list in admin without print_id
     *
     * @param (String)apikey
     * @param (int)srtIndex
     * @param (int)range
     * @param (int)is_pattern
     * @return JSON  data
     *
     */
    public function getPatelletByCategoryAdmin()
    {
        extract($this->_request);
        if (!empty($this->_request) && !empty($apikey) && $this->isValidCall($apikey)) {
            try {
                $sql = "SELECT DISTINCT  p.*
						FROM " . TABLE_PREFIX . "palette_category AS pc
						RIGHT JOIN " . TABLE_PREFIX . "palette_category_rel AS pcl ON pc.id = pcl.category_id
						JOIN " . TABLE_PREFIX . "palettes AS p ON pcl.palette_id = p.id
						WHERE pc.is_available = '1'";
                if ((isset($categoryId) && $categoryId) || !empty($categoryId)) {
                    $sql .= " AND pc.id IN(" . $categoryId . ")";
                }
                if (isset($is_pattern)) {
                    $sql .= " AND p.is_pattern='" . $is_pattern . "'";
                }
                $sql .= " ORDER BY p.id DESC";
                $srtIndex = (isset($srtIndex) && $srtIndex) ? $srtIndex : 0;
                if ($range != '') {
                    $sql .= " LIMIT $srtIndex,$range";
                }

                $colorArray = array();
                $i = 0;
                $rows = $this->executeFetchAssocQuery($sql);
                if (!empty($rows)) {
                    foreach ($rows as $v) {
                        $colorArray[$i]['id'] = $v['id'];
                        $colorArray[$i]['value'] = $v['value'];
                        if ($v['is_pattern'] == '2') {
                            $colorArray[$i]['cmyk']['c'] = $v["c"];
                            $colorArray[$i]['cmyk']['m'] = $v["m"];
                            $colorArray[$i]['cmyk']['y'] = $v["y"];
                            $colorArray[$i]['cmyk']['k'] = $v["k"];
                        }
                        $colorArray[$i]['name'] = $v['name'];
                        $colorArray[$i]['price'] = $v['price'];
                        $colorArray[$i]['is_pattern'] = intval($v['is_pattern']);
                        $sql_paletta = "SELECT category_id FROM " . TABLE_PREFIX . "palette_category_rel WHERE palette_id='" . $v['id'] . "'";
                        $row = $this->executeFetchAssocQuery($sql_paletta);
                        foreach ($row as $k => $v1) {
                            $categoryIdsArray[$k] = $v1['category_id'];
                        }
                        $colorArray[$i]['categoryIds'] = array_unique($categoryIdsArray);
                        $i++;
                    }
                }
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        } else {
            $colorArray['status'] = "invalidapikey";
        }

        $this->response($this->json($colorArray), 200);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Add paletta category
     *
     *@param (String)apikey
     *@param (Int)categoryId
     *@param (String)categoryName
     *@return json data
     *
     */
    public function addPaletteCategory()
    {
        $apiKey = $this->_request['apikey'];
        if ($this->isValidCall($apiKey)) {
            try {
                $categoryName = $this->_request['categoryName'];
                $sql = "select count(*) duplicate from " . TABLE_PREFIX . "palette_category where name = '$categoryName'";
                $row = $this->executeGenericDQLQuery($sql);
                $response = array();
                if ($row[0]['duplicate'] == "0") {
                    $sql = "insert into " . TABLE_PREFIX . "palette_category(name) values('$categoryName')";
                    $this->executeGenericDMLQuery($sql);
                    $response['status'] = "success";
                    $response['message'] = 'Category was added successfully.';
                } else {
                    $response['status'] = "failed";
                    $response['message'] = 'Category is already present.';
                }
            } catch (Exception $e) {
                $response = array('Caught exception:' => $e->getMessage());
            }
        } else {
            $response['status'] = "invalid";
        }
        $settingObj = Flight::setting();
        $settingObj->allSettingsDetails(1);
        $this->closeConnection();
        $this->response($this->json($response), 200);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *upadte paletta category name
     *
     * @param (String)apikey
     * @param (int)id
     * @param (String)name
     * @return json data
     *
     */
    public function updatePaletteCatName()
    {
        $status = 0;
        if (!empty($this->_request) && $this->_request['id'] && isset($this->_request['name'])) {
            extract($this->_request);
            try {
                $chk_duplicate = "SELECT count(*) duplicate FROM " . TABLE_PREFIX . "palette_category WHERE name = '" . $name . "' AND id !='" . $id . "'";
                $row = $this->executeGenericDQLQuery($chk_duplicate);
                $response = array();
                if ($row[0]['duplicate']) {
                    $msg['msg'] = 'Duplicate entry';
                } else {
                    $sql = "UPDATE " . TABLE_PREFIX . "palette_category SET name='" . $name . "' WHERE id='" . $id . "'";
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
     *remove paletta category
     *
     *@param (String)apikey
     *@param (Int)categoryId
     *@return json data
     *
     */
    public function removePaletteCategory()
    {
        $apiKey = $this->_request['apikey'];
        if ($this->isValidCall($apiKey)) {
            try {
                $categoryId = $this->_request['categoryId'];
                $sql = "DELETE FROM " . TABLE_PREFIX . "palette_category_rel WHERE category_id = $categoryId";
                $status = $this->executeGenericDMLQuery($sql);
                if ($status) {
                    $sql = "DELETE FROM " . TABLE_PREFIX . "palette_category WHERE id = $categoryId";
                    $result = $this->executeGenericDMLQuery($sql);
                    if ($result) {
                        $msg = array("status" => "success");
                    } else {
                        $msg = array("status" => "failed", "sql" => $sql);
                    }

                } else {
                    $msg = array("status" => "failed", "sql" => $sql);
                }

                $settingObj = Flight::setting();
                $settingObj->allSettingsDetails(1);
                $this->closeConnection();
                $this->response($this->json($msg), 200);
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        } else {
            $msg = array("status" => "invalid");
            $this->response($this->json($msg), 200);
        }
    }

	
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Add bulk of paletta
     *
     *@param (String)apikey
     *@param (String) name
     *@param (Enm) is_pattern
     *@param (Float)price
     *@return json data
     *
     */
    public function addBulkPalette()
    {
        $status = 0;
        try {
            if (!empty($this->_request) && isset($this->_request['name']) && isset($this->_request['is_pattern'])) {
                $sql = array();
                $fname = array();

                $palette_sql = "INSERT INTO " . TABLE_PREFIX . "palettes (name,price,is_pattern) VALUES ";
                $cat_scat_rel_sql = '';
                $print_method_re_sql = '';
                $usql = 'UPDATE ' . TABLE_PREFIX . 'palettes SET value = CASE id';
                $usql1 = '';
                $usql2 = '';
		$this->_request['price'] = (isset($this->_request['price']) && $this->_request['price'])?$this->_request['price']:0.00;
                if ($this->_request['is_pattern'] == '1') {
                    $dir = $this->getPalettePath();
                    if (!$dir) {
                        $this->response('', 204);
                    }
                    //204 - immediately termiante this request
                    if (!file_exists($dir)) {
                        mkdir($dir, 0777, true);
                    }

                    foreach ($this->_request['pattern'] as $k => $v) {
                        $sql[$k] = $palette_sql . "('" . $this->_request['name'] . "','" . $this->_request['price'] . "','" . $this->_request['is_pattern'] . "')";
                        $palette_id[$k] = $this->executeGenericInsertQuery($sql[$k]);
                        if (!empty($this->_request['category_id'])) {
                            foreach ($this->_request['category_id'] as $k1 => $v1) {
                                $cat_scat_rel_sql .= ",('" . $palette_id[$k] . "','" . $v1 . "')";
                            }
                        }
                        if (!empty($this->_request['print_method_id'])) {
                            foreach ($this->_request['print_method_id'] as $v2) {
                                $print_method_re_sql .= ",('" . $palette_id[$k] . "','" . $v2 . "')";
                            }
                        }
                        $fname[$k] = 'p' . $palette_id[$k] . '.' . $v['type'];
                        $thumbBase64Data[$k] = base64_decode($v['base64']);
                        file_put_contents($dir . $fname[$k], $thumbBase64Data[$k]);
                        $usql1 .= ' WHEN ' . $palette_id[$k] . " THEN '" . $fname[$k] . "'";
                        $usql2 .= $palette_id[$k] . ',';
                    }
                }
                if ($this->_request['is_pattern'] == '2') {
					if(!empty($this->_request['cmyk']) && isset($this->_request['cmyk'])){
						foreach ($this->_request['cmyk'] as $k => $v) {
							if (!empty($this->_request['name'])) {
								$palette = "INSERT INTO " . TABLE_PREFIX . "palettes (name,price,is_pattern,c,m,y,k) VALUES ";
								$sql[$k] = $palette . "('" . $this->_request['name'] . "','" . $this->_request['price'] . "',
									'" . $this->_request['is_pattern'] . "','" . $v['c'] . "',
									'" . $v['m'] . "','" . $v['y'] . "','" . $v['k'] . "')";
								$palette_id[$k] = $this->executeGenericInsertQuery($sql[$k]);
							}
							if (!empty($this->_request['category_id'])) {
								foreach ($this->_request['category_id'] as $k1 => $v1) {
									$cat_scat_rel_sql .= ",('" . $palette_id[$k] . "','" . $v1 . "')";
								}
							}
							if (!empty($this->_request['print_method_id'])) {
								foreach ($this->_request['print_method_id'] as $v2) {
									$print_method_re_sql .= ",('" . $palette_id[$k] . "','" . $v2 . "')";
								}
							}
							$usql1 .= ' WHEN ' . $palette_id[$k] . " THEN '" . $v['hexValue'] . "'";
							$usql2 .= $palette_id[$k] . ',';
						}
					}
					if(!empty($this->_request['csvcmyk']) && isset($this->_request['csvcmyk'])){
						foreach ($this->_request['csvcmyk'] as $k1 => $v1) {
							if (!empty($v1['name'])) {
								$palette = "INSERT INTO " . TABLE_PREFIX . "palettes (name,price,is_pattern,c,m,y,k) VALUES ";
								$sqlcsv[$k1] = $palette . "('" . $v1['name'] . "','" . $v1['price'] . "',
									'" . $this->_request['is_pattern'] . "','" . $v1['c'] . "',
									'" . $v1['m'] . "','" . $v1['y'] . "','" . $v1['k'] . "')";
								$palette_ids[$k1] = $this->executeGenericInsertQuery($sqlcsv[$k1]);
							}
							if(!empty($v1['category_id'])){
								$cat_scat_rel_sql .= ",('" . $palette_ids[$k1] . "','" . $v1['category_id'] . "')";
							}
							if (!empty($this->_request['print_method_id'])) {
								foreach ($this->_request['print_method_id'] as $v3) {
									$print_method_re_sql .= ",('" . $palette_ids[$k1] . "','" . $v3 . "')";
								}
							}
							$usql1 .= ' WHEN ' . $palette_ids[$k1] . " THEN '" . $v1['hexValue'] . "'";
							$usql2 .= $palette_ids[$k1] . ',';
						}
					}	
                }
                if ($this->_request['is_pattern'] == '0') {
                    if (isset($this->_request['csv']) && ($this->_request['csv'] != '')) {
                        foreach ($this->_request['csv'] as $k1 => $v1) {
                            $sql[$k1] = $palette_sql . "('" . $v1['name'] . "','" . $v1['price'] . "','" . $this->_request['is_pattern'] . "')";
                            $palette_id[$k1] = $this->executeGenericInsertQuery($sql[$k1]);
                            if (!empty($v1['category_id'])) {
                                $cat_scat_rel_sql .= ",('" . $palette_id[$k1] . "','" . $v1['category_id'] . "')";
                            }
                            if (!empty($this->_request['print_method_id'])) {
                                foreach ($this->_request['print_method_id'] as $v5) {
                                    $print_method_re_sql .= ",('" . $palette_id[$k1] . "','" . $v5 . "')";
                                }
                            }
                            $usql1 .= ' WHEN ' . $palette_id[$k1] . " THEN '" . $v1['color_code'] . "'";
                            $usql2 .= $palette_id[$k1] . ',';
                            $usql2 .= $palette_id[$k1] . ',';
                        }
                    } else {
                        foreach ($this->_request['color'] as $k => $v) {
                            $sql[$k] = $palette_sql . "('" . $this->_request['name'] . "','" . $this->_request['price'] . "','" . $this->_request['is_pattern'] . "')";
                            $palette_id[$k] = $this->executeGenericInsertQuery($sql[$k]);

                            if (!empty($this->_request['category_id'])) {
                                foreach ($this->_request['category_id'] as $k1 => $v1) {
                                    $cat_scat_rel_sql .= ",('" . $palette_id[$k] . "','" . $v1 . "')";
                                }
                            }
                            if (!empty($this->_request['print_method_id'])) {
                                foreach ($this->_request['print_method_id'] as $v2) {
                                    $print_method_re_sql .= ",('" . $palette_id[$k] . "','" . $v2 . "')";
                                }
                            }
                            $usql1 .= ' WHEN ' . $palette_id[$k] . " THEN '" . $v . "'";
                            $usql2 .= $palette_id[$k] . ',';
                        }
                    }
                }

                $usql2 = substr($usql2, 0, strlen($usql2) - 1);
                $usql = $usql . $usql1 . ' END WHERE id IN(' . $usql2 . ')';
                $status = $this->executeGenericDMLQuery($usql);
                if (strlen($cat_scat_rel_sql)) {
                    $cat_scat_rel_sql = "INSERT INTO " . TABLE_PREFIX . "palette_category_rel (palette_id,category_id) VALUES " . substr($cat_scat_rel_sql, 1);
                    $status = $this->executeGenericDMLQuery($cat_scat_rel_sql);
                }
                if (strlen($print_method_re_sql)) {
                    $print_method_re_sql = "INSERT INTO " . TABLE_PREFIX . "print_method_palette_rel (palette_id,print_method_id) VALUES " . substr($print_method_re_sql, 1);
                    $status = $this->executeGenericDMLQuery($print_method_re_sql);
                }
                $settingObj = Flight::setting();
                $settingObj->allSettingsDetails(1);
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
     *Upadte palleta by id
     *
     *@param (String)apikey
     *@param (String) name
     *@param (int) id
     *@param (Float)price
     *@return json data
     *
     */
    public function updatePaletteData()
    {
        try {
            $status = 0;
            if (!empty($this->_request) && !empty($this->_request['id']) && isset($this->_request['name']) && isset($this->_request['price'])) {
                extract($this->_request);
                $id_str = implode(',', $id);
                if (sizeof($id) > 1) {
                    $sql = "UPDATE " . TABLE_PREFIX . "palettes SET name='" . $name . "',price='" . $price . "' WHERE id IN(" . $id_str . ")";
                    $status = $this->executeGenericDMLQuery($sql);
                } else {
                    if ($isPattern == '1') {
                        $sql = "UPDATE " . TABLE_PREFIX . "palettes SET name='" . $name . "',price='" . $price . "' WHERE id IN(" . $id_str . ")";
                        $status = $this->executeGenericDMLQuery($sql);
                    }
                    if ($isPattern == '0') {
                        $sql = "UPDATE " . TABLE_PREFIX . "palettes SET value='" . $paletteValue . "',name='" . $name . "',price='" . $price . "' WHERE id IN(" . $id_str . ")";
                        $status = $this->executeGenericDMLQuery($sql);
                    }
                    if ($isPattern == '2') {
                        $sql = "UPDATE " . TABLE_PREFIX . "palettes SET value='" . $cmyk['hexValue'] . "',name='" . $name . "',
						price='" . $price . "',c='" . $cmyk['c'] . "',m='" . $cmyk['m'] . "',y='" . $cmyk['y'] . "',k='" . $cmyk['k'] . "' WHERE id IN(" . $id_str . ")";
                        $status = $this->executeGenericDMLQuery($sql);
                    }
                }
                $sql = "DELETE FROM " . TABLE_PREFIX . "palette_category_rel WHERE palette_id IN(" . $id_str . ")";
                $status = $this->executeGenericDMLQuery($sql);
                $cat_scat_rel_sql = '';
                $print_method_re_sql = '';
                foreach ($id as $k => $v) {
                    $palette_id[$k] = $v;
                    if (!empty($category_id)) {
                        $category_id = array_unique($category_id);
                        foreach ($category_id as $k1 => $v1) {
                            $cat_scat_rel_sql .= ",('" . $palette_id[$k] . "','" . $v1 . "')";
                        }
                    }
                }
                if (strlen($cat_scat_rel_sql)) {
                    $cat_scat_rel_sql = "INSERT INTO " . TABLE_PREFIX . "palette_category_rel (palette_id,category_id) VALUES " . substr($cat_scat_rel_sql, 1);
                    $status = $this->executeGenericDMLQuery($cat_scat_rel_sql);
                }
            }
            $settingObj = Flight::setting();
            $settingObj->allSettingsDetails(1);
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
     *remove paletta category
     *
     *@param (String)apikey
     *@param (int)paletteIds
     *@param (String)fileNames
     *@return json data
     *
     */
    public function removePalettes(){
        $apiKey = $this->_request['apikey'];
	$status = 0;
        if ($this->isValidCall($apiKey)) {
            try {
		if(!empty($this->_request['paletteIds'])){
			$ids = implode(',', $this->_request['paletteIds']);
			$sql = "DELETE FROM " . TABLE_PREFIX . "print_method_palette_rel WHERE palette_id in ($ids)"; // table might not be used plz check again.
			$status += $this->executeGenericDMLQuery($sql);

			$sql = "DELETE FROM " . TABLE_PREFIX . "print_method_color_group_rel WHERE color_group_id in ($ids)";
			$status += $this->executeGenericDMLQuery($sql);

			$sql = 'DELETE cpgrl,cpg FROM ' . TABLE_PREFIX . 'color_price_group_rel AS cpgrl 
				INNER JOIN ' . TABLE_PREFIX . 'color_price_group AS cpg ON cpgrl.color_price_group_id=cpg.pk_id 
				WHERE cpgrl.color_id IN(' . $ids . ')';
			$status += $this->executeGenericDMLQuery($sql);

			$sql = "DELETE FROM " . TABLE_PREFIX . "palettes WHERE id in ($ids)";
			$status += $this->executeGenericDMLQuery($sql);
					
			if ($status > 0) {
				$dir = $this->getPaletteImagePath();
				if (!$dir) {
					$this->response('', 204);
				}
				foreach($this->_request['fileNames'] as $fileNamesArray) {
					$filePath = $dir . $fileNamesArray;
					if (file_exists($filePath)) {
						if (is_file($filePath)) {
							@chmod($filePath,0755);
							@unlink($filePath);
						}
					}
				}
			}
			$settingObj = Flight::setting();
			$settingObj->allSettingsDetails(1);
		}
            } catch (Exception $e) {
                $msg = array('Caught exception:' => $e->getMessage());
            }
		$msg['status'] = ($status > 0)?'success':'failed';
        } else {
            $msg['status'] = 'invalid';
        }
	$this->response($this->json($msg), 200);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Get Palleta by CategoryId
     *
     *@param (String)apikey
     *@param (int)print_method_id
     *@param (int)categoryId
     *@param (int)srtIndex
     *@param (int)range
     *@param (int)is_pattern
     *@return json data
     *
     */
    public function getPatelletByCategory($print_method_id = 0, $categoryId = 0, $srtIndex = 0, $range = 0, $is_pattern = '')
    {
        try {
            $sql = "SELECT DISTINCT  p.*
					FROM " . TABLE_PREFIX . "palette_category AS pc
					RIGHT JOIN " . TABLE_PREFIX . "palette_category_rel AS pcl ON pc.id = pcl.category_id
					JOIN " . TABLE_PREFIX . "palettes AS p ON pcl.palette_id = p.id
					JOIN " . TABLE_PREFIX . "print_method_palette_category AS pmpc ON pmpc.palette_category_id = pc.id
					WHERE pc.is_available = '1'";
            if ((isset($is_pattern) && $is_pattern != '')) {
                $sql .= "  AND p.is_pattern ='" . $is_pattern . "'";
            }
            if (isset($print_method_id) && $print_method_id) {
                $sql .= " AND pmpc.print_method_id ='" . $print_method_id . "'";
            }

            if ((isset($categoryId) && $categoryId) || !empty($categoryId)) {
                $sql .= " AND pc.id IN(" . $categoryId . ")";
				}
            $sql .= " ORDER BY p.id DESC";

            $srtIndex = (isset($srtIndex) && $srtIndex) ? $srtIndex : 0;
            if ($range != '') {
                $sql .= " LIMIT $srtIndex,$range";
            }

        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
        $colorArray = array();
        $i = 0;
        $colorsFromValue = mysqli_query($this->db, $sql);
        while ($row = mysqli_fetch_array($colorsFromValue)) {
            $colorArray[$i]['id'] = $row['id'];
            $colorArray[$i]['value'] = $row['value'];
            if ($row['is_pattern'] == '2') {
                $colorArray[$i]['cmyk']['c'] = $row['c'];
                $colorArray[$i]['cmyk']['m'] = $row['m'];
                $colorArray[$i]['cmyk']['y'] = $row['y'];
                $colorArray[$i]['cmyk']['k'] = $row['k'];
            }
            $colorArray[$i]['name'] = $row['name'];
            $colorArray[$i]['price'] = $row['price'];
            $colorArray[$i]['is_pattern'] = intval($row['is_pattern']);

            $sql = "SELECT category_id FROM " . TABLE_PREFIX . "palette_category_rel WHERE palette_id='" . $row['id'] . "'";
            $categoryIdsFromValue = mysqli_query($this->db, $sql);
            $categoryIdsArray = array();
            while ($rows = mysqli_fetch_array($categoryIdsFromValue)) {
                array_push($categoryIdsArray, $rows['category_id']);
            }
            $colorArray[$i]['categoryIds'] = array_unique($categoryIdsArray);

            $i++;
        }
        return $colorArray;
    }

    /**
     *
     *date created 19-1-2015 (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Fetch Palettes
     *
     *@param (String)apikey
     *@param (Int)returns
     *@return json data
     *
     */
    public function fetchPalettes()
    {
        try {
            $apiKey = $this->_request['apikey'];
            if ($this->isValidCall($apiKey)) {
                extract($this->_request);
                $res = $this->getPatelletByCategory($printTypeId, $categoryId, $srtIndex, $range, $is_pattern);

                if (isset($this->_request['returns']) && $this->_request['returns']) {
                    return $res;
                } else {
                    $this->response($this->json($res), 200);
                }

            } else {
                $msg = array("status" => "invalid");
                if (isset($this->_request['returns']) && $this->_request['returns']) {
                    return $this->json($msg);
                } else {
                    $this->response($this->json($msg), 200);
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
     *Save paletta Data
     *
     *@param (String)apikey
     *@param (Array)colorArray
     *@param (String)paletteName
     *@param (Flaot)palettePrice
     *@param (Array)printTypes
     *@param (Array)categoryIds
     *@param (Flaot)is_pattern
     *@param (Array)fileExtensions
     *@param (String)data
     *@return json data
     *
     */
    public function savePaletteDetails()
    {
        $apiKey = $this->_request['apikey'];
        if ($this->isValidCall($apiKey)) {
            try {
                $colorArray = $this->_request['colorArray'];
                $paletteName = $this->_request['paletteName'];
                $palettePrice = floatval($this->_request['palettePrice']);
                $printTypeArray = $this->_request['printTypes'];
                $categoryArray = $this->_request['categoryIds'];
                $isPattern = $this->_request['is_pattern'];
                $fileExtensionsArray = $this->_request['fileExtensions'];
                $base64DataArray = $this->_request['data'];
                if ($isPattern) {
                    $base64Data = base64_decode($data);
                    $dir = $this->getPaletteImagePath();

                    if (!$dir) {
                        $this->response('Invalid Directory', 204);
                    }
                    //204 - immediately termiante this request
                    if (!file_exists($dir)) {
                        mkdir($dir, 0777, true);
                    }

                    for ($j = 0; $j < sizeof($base64DataArray); $j++) {
                        //$patternId =  $this->getDBUniqueId('patterns', 'id');
                        $maxId = $this->getMaxId('palettes', 'id');
                        $patternId = $maxId + 1;

                        $fileName = 'p' . $patternId . '.' . $fileExtensionsArray[$j];
                        $data = $base64DataArray[$j];
                        $base64Data = base64_decode($data);

                        $filePath = $dir . $fileName;
                        $fileStatus = file_put_contents($filePath, $base64Data);
                        $msg = '';
                        if ($fileStatus) {
                            $sql = "insert into " . TABLE_PREFIX . "palettes(id,name,price,is_pattern) values($patternId,'$fileName','$paletteName',$palettePrice,$isPattern)";
                            $status = $this->executeGenericDMLQuery($sql);

                            if ($status) {
                                $this->assignPrinttypeAndCategoryToPallet($patternId, $printTypeArray, $categoryArray);
                            }

                        }
                    }
                 } else {
                    for ($j = 0; $j < sizeof($colorArray); $j++) {
                        $colorValue = $colorArray[$j][0];
                        $colorName = $colorArray[$j][1];
                        $sql = "insert into " . TABLE_PREFIX . "palettes(name,price,is_pattern) values('$colorValue','$colorName',$palettePrice,$isPattern)";
                        $status = $this->executeGenericDMLQuery($sql);
                        if ($status) {
                            $sql = "SELECT id FROM " . TABLE_PREFIX . "palettes WHERE value='" . $colorValue . "'";
                            $colorsFromValue = mysqli_query($this->db, $sql);
                            $colorId = mysqli_fetch_array($colorsFromValue);
                            $this->assignPrinttypeAndCategoryToPallet($colorId[id], $printTypeArray, $categoryArray);
                        }
                    }
                }
                if ($status) {
                    $msg = array("status" => "success");
                } else {
                    $msg = array("status" => "failed", "sql" => $sql);
                }

                $this->closeConnection();
                $this->response($this->json($msg), 200);
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        } else {
            $msg = array("status" => "invalid");
            $this->response($this->json($msg), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Assign print type and category to paletta paletta
     *
     *@param (String)apikey
     *@param (int)palletId
     *@param (Array)printTypeArray
     *@param (Array)categoryArray
     *@return json data
     *
     */
    private function assignPrinttypeAndCategoryToPallet($palletId, $printTypeArray, $categoryArray)
    {
        try {
            for ($k = 0; $k < sizeof($printTypeArray); $k++) {
                $sql = "insert into " . TABLE_PREFIX . "print_method_palette_rel(palette_id,print_method_id) values('$palletId','$printTypeArray[$k]')";
                $printTypeDataStatus = $this->executeGenericDMLQuery($sql);
            }
            for ($k = 0; $k < sizeof($categoryArray); $k++) {
                $sql = "insert into " . TABLE_PREFIX . "palette_category_rel(palette_id,category_id) values('$palletId','$categoryArray[$k]')";
                $categoryDataStatus = $this->executeGenericDMLQuery($sql);
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
     *Update Max palletta
     *
     *@param (String)apikey
     *@param (int)printTypeId
     *@param (int)maxPalettes
     *@return json data
     *
     */
    public function updateMaxPalettes()
    {
        $apiKey = $this->_request['apikey'];
        if ($this->isValidCall($apiKey)) {
            $printTypeId = $this->_request['printTypeId'];
            $maxPalettes = intval($this->_request['maxPalettes']);
            try {
                $sql = "SELECT max_palettes_limit FROM " . TABLE_PREFIX . "printing_details WHERE id = $printTypeId";
                $result = mysqli_query($this->db, $sql);
                $row = mysqli_fetch_assoc($result);
                $prevMaxPalettes = intval($row['max_palettes_limit']);

                $sql = "UPDATE " . TABLE_PREFIX . "printing_details SET max_palettes_limit = $maxPalettes WHERE id = $printTypeId";
                $status = $this->executeGenericDMLQuery($sql);

                if ($status) {
                    $sql = "SELECT id FROM " . TABLE_PREFIX . "print_order_range WHERE printtype_id=$printTypeId";
                    $rangeIdFromValue = mysqli_query($this->db, $sql);
                    if ($maxPalettes > $prevMaxPalettes) {
                        while ($rows = mysqli_fetch_array($rangeIdFromValue)) {
                            $orderRangeId = $rows['id'];
                            for ($j = $prevMaxPalettes + 1; $j <= $maxPalettes; $j++) {
                                $sql = "insert into " . TABLE_PREFIX . "palette_range_price(order_range_id,num_palettes,price) values($orderRangeId,$j,0.00)";
                                $status .= $this->executeGenericDMLQuery($sql);
                            }
                        }
                    } else {
                        for ($j = $maxPalettes + 1; $j <= $prevMaxPalettes; $j++) {
                            $sql = "delete from " . TABLE_PREFIX . "palette_range_price where num_palettes = $j";
                            $status .= $this->executeGenericDMLQuery($sql);
                        }
                    }
                }
                $settingObj = Flight::setting();
                $settingObj->allSettingsDetails(1);
                $msg['status'] = ($status) ? 'success' : 'failed';
            } catch (Exception $e) {
                $msg = array('Caught exception:' => $e->getMessage());
            }
        } else {
            $msg = array("status" => "invalid");

        }
        $this->response($this->json($msg), 200);
    }

}
