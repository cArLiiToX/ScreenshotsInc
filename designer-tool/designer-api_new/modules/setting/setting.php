<?php
/* Check Un-authorize Access */
if (!defined('accessUser')) {
    die("Error");
}

class Setting extends UTIL
{

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Update theme settings by tabid
     *
     *@param (String)apikey
     *@param (int)themeId
     *@return json data
     *
     */
    public function updateThemeSetting()
    {
        $status = 0;
        $msg = array();
        if (!empty($this->_request) && isset($this->_request['apikey']) && $this->isValidCall($this->_request['apikey'])) {
            extract($this->_request);
            if ($themeId) {
                $sql4old = "SELECT brand_primary,border_color,panel_color,stage_color,text_color FROM " . TABLE_PREFIX . "theme WHERE is_default='1' LIMIT 1";
                $oldrec = $this->executeFetchAssocQuery($sql4old);
                if (!empty($oldrec)) {
                    $oldrec = array_values($oldrec[0]);
                    if (isset($themeColors) && !empty($themeColors)) {
                        $newrec = $themeColors;
                    } else {
                        $sql4new = "SELECT brand_primary,border_color,panel_color,stage_color,text_color FROM " . TABLE_PREFIX . "theme WHERE id = " . $themeId . " LIMIT 1";
                        $newrec = $this->executeFetchAssocQuery($sql4new);
                        $newrec = array_values($newrec[0]);
                    }
                    if (!empty($newrec)) {
                        $file = self::HTML5_THEME_DIR . FOLDER_NAME . '/allless.css';
                        $file_contents = file_get_contents($file);
                        $file_contents = str_replace($oldrec, $newrec, $file_contents, $count);

                        if (!file_exists($file)) {
                            $myfile = fopen($file, "w+") or die("Unable to open file!");
                            @chmod($file, 0777);
                        }
                        $nstatus = file_put_contents($file, $file_contents);
                        if ($count && $nstatus) {
                            $sql = "UPDATE " . TABLE_PREFIX . "theme SET brand_primary = '" . $newrec[0] . "',border_color='" . $newrec[1] . "',panel_color='" . $newrec[2] . "',stage_color='" . $newrec[3] . "',text_color='" . $newrec[4] . "' WHERE id = " . $themeId;
                            $status = $this->executeGenericDMLQuery($sql);
                        }
                    }
                }
                $sql = "UPDATE " . TABLE_PREFIX . "theme SET is_default = '0'";
                $this->executeGenericDMLQuery($sql);
                $sql = "UPDATE " . TABLE_PREFIX . "theme SET is_default = '1' WHERE id = " . $themeId . " LIMIT 1";
                $status = $this->executeGenericDMLQuery($sql);
            }
            if ($status) {
                $this->_request['type'] = 'updateFeature';
                $this->getGeneralSetting();
                //$this->allSettingsDetails(1);
            } else {
                $msg = array("status" => "failed");
            }
        } else {
            $msg = array("status" => "invalidkey");
        }
        $this->response($this->json($msg), 200);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Update language currency unit settings  by languageId
     *
     *@param (String)apikey
     *@param (int)languageId
     *@return json data
     *
     */
    public function updateLangCurUnitSetting()
    {
        if (!empty($this->_request) && isset($this->_request['apikey']) && $this->isValidCall($this->_request['apikey'])) {
            try {
                if (!empty($this->_request['languageId'])) {
                    $msg = $this->updateSettingLanguage($this->_request['languageId']);
                    $this->updateLocalSettingsLanguage();
                } else {
                    $msg = array("status" => "faild");
                    $this->response($this->json($msg), 200);
                }
                $this->_request['type'] = 'updateFeature';
                $this->getGeneralSetting();
                //$this->allSettingsDetails(1);
            } catch (Exception $e) {
                $msg = array("status" => "failed", 'error' => $e->getMessage());
                $this->response($this->json($msg), 200);
            }
        } else {
            $msg = array("status" => "invalidkey");
            $this->response($this->json($msg), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Set general Settings
     *
     *@param (String)apikey
     *@param (Array)settings
     *@return json data
     *
     */
      public function setGeneralSetting()
    {
        $status = 0;
        $msg['status'] = 'Failed';
        try {
            if (!empty($this->_request)) {
                extract($this->_request);
                $no_of_cart = $this->_request['settings']['no_of_chars'] ? $this->_request['settings']['no_of_chars'] : 0;
                $steps = $this->_request['settings']['step'] ? $this->_request['settings']['step'] : 0;
                $font_min = $this->_request['settings']['font_size_min'] ? $this->_request['settings']['font_size_min'] : 0;
                $font_max = $this->_request['settings']['font_size_max'] ? $this->_request['settings']['font_size_max'] : 0;
                $app_id = $this->_request['settings']['app_id'] ? $this->_request['settings']['app_id'] : '';
                $sql = "UPDATE " . TABLE_PREFIX . "general_setting SET is_popup_enable='" . $this->_request['settings']['is_popup_enable'] . "',
                    currency='" . $this->_request['settings']['currency'] . "',unit='" . $this->_request['settings']['unit'] . "',
                    is_direct_cart='" . $this->_request['settings']['is_direct_cart'] . "',
                    terms_condition='" . $this->_request['settings']['terms_condition'] . "',
                    max_file_size=" . $this->_request['settings']['max_file_size'] . ",
                    image_width=" . $this->_request['settings']['image_width'] . ",
                    image_height=" . $this->_request['settings']['image_height'] . ",
                    price_suffix='" . $this->_request['settings']['price_suffix'] . "',
                    price_prefix='" . $this->_request['settings']['price_prefix'] . "',
                    font_size_min=" . $font_min . ",
                    font_size_max=" . $font_max . ",
                    step=" . $steps . ",notes='" . ($this->_request['settings']['notes']) . "',
                    no_of_chars = " . $no_of_cart . ",
                    app_id = '" . $app_id . "',
                    domain_name='" . $this->_request['settings']['domain_name'] . "',
                    site_url='" . $this->_request['settings']['site_url'] . "',
                    is_terms_and_condition_allow='" . $this->_request['settings']['is_terms_and_condition_allow'] . "',
                    img_terms_condition='" . $this->_request['settings']['img_terms_condition'] . "' ,
					is_email_allowed='" . $this->_request['settings']['is_email_allowed'] . "',
                    email='" . $this->_request['settings']['email'] . "' WHERE id=" . $this->_request['settings']['id'] . " ";
                $status = $this->executeGenericDMLQuery($sql);
            }
            if ($status) {
                $this->allSettingsDetails(1);
                $msg = $this->getGeneralSetting();
                $this->response($this->json($msg), 200);
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
     *Update tab settings by tabid
     *
     *@param (String)apikey
     *@param (int)defaultTabId
     *@param (int)tabIds
     *@param (int)subTabIds
     *@return json data
     *
     */
    public function updateTabSetting()
    {
        try {
            $tabstatus = 0;
            $subtabstatus = 0;
            $msg = array();
            if (!empty($this->_request) && isset($this->_request['apikey']) && $this->isValidCall($this->_request['apikey'])) {
                if (isset($this->_request['defaultTabId']) && !empty($this->_request['tabIds']) && !empty($this->_request['subTabIds'])) {
                    extract($this->_request);
                    $sql = "UPDATE " . TABLE_PREFIX . "tabs SET is_default = '0'";
                    $this->executeGenericDMLQuery($sql);
                    $sql = "UPDATE " . TABLE_PREFIX . "tabs SET is_default = '1' WHERE id = '" . $defaultTabId . "'";
                    $tabstatus = $this->executeGenericDMLQuery($sql);

                    for ($k = 0; $k < sizeof($tabIds); $k++) {
                        $sql = "UPDATE " . TABLE_PREFIX . "tabs SET default_subtab_id = '" . $subTabIds[$k] . "' WHERE id = '" . $tabIds[$k] . "'";
                        $subtabstatus = $this->executeGenericDMLQuery($sql);
                    }
                }
                if ($tabstatus && $subtabstatus) {
                    $this->_request['type'] = 'updateFeature';
                    $this->getGeneralSetting();
                    //$this->allSettingsDetails(1);
                } else {
                    $msg = array("status" => "failed");
                }
            } else {
                $msg = array("status" => "invalidkey");
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
     *Update preload item settings  by featureRangeIds
     *
     *@param (String)apikey
     *@param (int)featureRangeValues
     *@param (int)featureRangeIds
     *@return json data
     *
     */
    public function updatePreloadItemSetting()
    {
        $status = 0;
        $msg = array();
        if (!empty($this->_request) && isset($this->_request['apikey']) && $this->isValidCall($this->_request['apikey'])) {
            if (!empty($this->_request['featureRangeIds']) && !empty($this->_request['featureRangeValues'])) {
                extract($this->_request);
                for ($j = 0; $j < sizeof($featureRangeIds); $j++) {
                    $sql = "UPDATE " . TABLE_PREFIX . "preloaded_items SET value = " . $featureRangeValues[$j] . " WHERE pk_id = " . $featureRangeIds[$j];
                    $status = $this->executeGenericDMLQuery($sql);
                }
            }
            if ($status) {
                $this->_request['type'] = 'updateFeature';
                $this->getGeneralSetting();
                //$this->allSettingsDetails(1);
            } else {
                $msg = array("status" => "failed");
                //throw new Exception('Error in features general setting values update');
            }
            //return $msg;
        } else {
            $msg = array("status" => "invalidkey");
            // $this->response($this->json($msg), 200);
        }
        $this->response($this->json($msg), 200);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Update  Item on stage Settings by
     *
     *@param (String)apikey
     *@param (int)itemIds
     *@param (int)itemValues
     *@return json data
     *
     */
    public function updateItemOnStageSetting()
    {
        try {
            $status = 0;
            $msg = array();
            if (!empty($this->_request) && isset($this->_request['apikey']) && $this->isValidCall($this->_request['apikey'])) {
                if (!empty($this->_request['itemIds']) && !empty($this->_request['itemValues'])) {
                    extract($this->_request);
                    for ($j = 0; $j < sizeof($itemIds); $j++) {
                        $sql = "UPDATE " . TABLE_PREFIX . "items_per_module SET value = " . $itemValues[$j] . " WHERE id = " . $itemIds[$j];
                        $status = $this->executeGenericDMLQuery($sql);
                    }
                }
                if ($status) {
                    $this->_request['type'] = 'updateFeature';
                    $this->getGeneralSetting();
                    //$this->allSettingsDetails(1);
                } else {
                    $msg = array("status" => "failed");
                }
            } else {
                $msg = array("status" => "invalidkey");
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
     *Update  feature settings
     *
     *@param (String)apikey
     *@param (int)featureIds
     *@param (Arrary)featureStatusArray
     *@return json data
     *
     */
    public function updateFeatureSetting()
    {
        //updateFeatureStatus
        try {
            $status = 0;
            $msg = array();
            if (!empty($this->_request) && isset($this->_request['apikey']) && $this->isValidCall($this->_request['apikey'])) {
                if (!empty($this->_request['featureIds']) && !empty($this->_request['featureStatusArray'])) {
                    extract($this->_request);
                    if (!empty($featureIds)) {
                        $usql1 = '';
                        $usql2 = '';
                        foreach ($featureIds as $k => $v) {
                            $usql1 .= ' WHEN ' . $v . " THEN '" . $featureStatusArray[$k] . "'";
                            $usql2 .= ',' . $v;
                        }
                        $usql = 'UPDATE ' . TABLE_PREFIX . 'features SET status = CASE id' . $usql1 . ' END WHERE id IN(' . substr($usql2, 1) . ')';
                        $status = $this->executeGenericDMLQuery($usql);
                    }
                    if ($status) {
                        $this->_request['type'] = 'updateFeature';
                        $this->getGeneralSetting();
                        //$this->allSettingsDetails(1);
                    } else {
                        $msg = array("status" => "failed");
                    }
                }
            } else {
                $msg = array("status" => "invalidkey");
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
     *Get All Admin Settings by Prtint id
     *
     *@param (String)apikey
     *@param (int)print_method_id
     *@param (int)type
     *@return json data
     *
     */
    public function getAllAdminSettings($type = 0)
    {
        try {
            $print_method_id = (isset($this->_request['print_method_id']) && $this->_request['print_method_id'])?$this->_request['print_method_id']:0;
            $allAdminSettings = array();
            $language_sql = "SELECT value FROM " . TABLE_PREFIX . "app_language WHERE status='1' LIMIT 1";
            $language = $this->executeFetchAssocQuery($language_sql);
            $allAdminSettings['language'] = (!empty($language)) ? $language[0]['value'] : '';
            // getting module limit data

            $allAdminSettings['moduleLimitData'] = $this->getItemsPerModule();
            $this->_request['returns'] = true;
            $allAdminSettings['disabledfeatures'] = $this->getFeatureSettings(); //status='0'    exit(0);
            $allAdminSettings['config'] = $this->getAdminSettings();
            $this->_request['customer'] = '1';
            $sql_category = "SELECT DISTINCT pc.id,pc.name FROM " . TABLE_PREFIX . "print_method pm
                   JOIN " . TABLE_PREFIX . "print_method_palette_category AS pmpc
                   ON pmpc.print_method_id=pm.pk_id
                   JOIN " . TABLE_PREFIX . "palette_category AS pc
                   ON pc.id=pmpc.palette_category_id
                   WHERE pm.pk_id='" . $print_method_id . "' AND pc.is_available=1";
            $rows1 = $this->executeGenericDQLQuery($sql_category);
            $categoryDetail = array();
            for ($j = 0; $j < sizeof($rows1); $j++) {
                $categoryDetail[$j]['id'] = $rows1[$j]['id'];
                $categoryDetail[$j]['category_name'] = $rows1[$j]['name'];
            }
            $allAdminSettings['palettecategories'] = $categoryDetail;

            $this->_request['srtIndex'] = 0;
            $this->_request['range'] = '';
            $this->_request['categoryId'] = '';
            $sql = "SELECT DISTINCT p.id, p.name, p.value, p.price, p.is_pattern
            FROM " . TABLE_PREFIX . "palettes p
            JOIN " . TABLE_PREFIX . "palette_category_rel pcl ON p.id = pcl.palette_id
            LEFT JOIN " . TABLE_PREFIX . "print_method_palette_category tcppr ON pcl.category_id = tcppr.palette_category_id
            WHERE 1 AND tcppr.print_method_id ='" . $print_method_id . "' ORDER BY p.id DESC";
            $colorArray = array();
            $i = 0;
            $colorsFromValue = mysqli_query($this->db, $sql);
            while ($row = mysqli_fetch_array($colorsFromValue)) {
                $colorArray[$i]['id'] = $row['id'];
                $colorArray[$i]['value'] = $row['value'];
                $colorArray[$i]['name'] = $row['name'];
                $colorArray[$i]['price'] = $row['price'];
                $colorArray[$i]['is_pattern'] = intval($row['is_pattern']);
                $sql_new = "SELECT DISTINCT category_id FROM " . TABLE_PREFIX . "palette_category_rel WHERE palette_id='" . $row['id'] . "'";
                $categoryIdsFromValue = mysqli_query($this->db, $sql_new);
                $categoryIdsArray = array();
                while ($rows = mysqli_fetch_array($categoryIdsFromValue)) {
                    array_push($categoryIdsArray, $rows['category_id']);
                }

                if ($this->_request['customer'] && $this->_request['customer'] == '1') {
                    if (sizeof($categoryIdsArray) == 0) {
                        array_push($categoryIdsArray, '0');
                    }

                }
                $colorArray[$i]['categoryIds'] = $categoryIdsArray;

                $i++;
            }

            $allAdminSettings['palettes'] = $colorArray; // fetching pallets info

            $sql = "SELECT symbol FROM " . TABLE_PREFIX . "tabs WHERE is_default = 1";
            $result = mysqli_query($this->db, $sql);
            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $allAdminSettings['default_tab'] = $row['symbol'];
            }

            $allAdminSettings['default_sub_tab'] = array();
            $sql = "SELECT symbol,default_subtab_id FROM " . TABLE_PREFIX . "tabs WHERE default_subtab_id <> 0";
            $result = mysqli_query($this->db, $sql);

            $i = 0;
            while ($row = mysqli_fetch_array($result)) {
                $tab_symbol = $row['symbol'];

                $sql = "SELECT type FROM " . TABLE_PREFIX . "features WHERE id = " . $row['default_subtab_id'];
                $subtab_result = mysqli_query($this->db, $sql);
                $subtab_row = mysqli_fetch_array($subtab_result);
                $subtab_type = $subtab_row['type'];
                $allAdminSettings['default_sub_tab'][$i][$tab_symbol] = $subtab_type;

                $i++;
            }

            $sql = "SELECT pmsr.print_method_id FROM " . TABLE_PREFIX . "print_method_setting_rel AS pmsr JOIN " . TABLE_PREFIX . "print_setting ps ON pmsr.print_setting_id=ps.pk_id WHERE ps.is_default='1' LIMIT 1";
            $rec = $this->executeFetchAssocQuery($sql);
            $allAdminSettings['print_method_id'] = (!empty($rec) && $rec[0]['print_method_id']) ? $rec[0]['print_method_id'] : 0;

            $fetch_sql = "SELECT MAX(id) AS n FROM " . TABLE_PREFIX . "fonts LIMIT 1";
            $res = $this->executeFetchAssocQuery($fetch_sql);
            $allAdminSettings['font_heighest_id'] = (!empty($res) && $res[0]['n']) ? $res[0]['n'] : 0;
            $sql = "SELECT * FROM " . TABLE_PREFIX . "preloaded_items";
            $res_item = $this->executeFetchAssocQuery($sql);
            $allAdminSettings['items_loaded_per_module'] = $res_item;
            $allAdminSettings['general_setting'] = $this->fetchGeneralSetting();
            $allAdminSettings['social_site_values'] = $this->getSocialImageDetails();
            if ($type == 1) {
                return $this->json($allAdminSettings);
            }

            $this->response($this->json($allAdminSettings), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *get item per module
     *
     *@param (String)apikey
     *@return json data
     *
     */
    public function getItemsPerModule()
    {
        try {
            $sql = "Select * from " . TABLE_PREFIX . "items_per_module";
            $result = $this->executeFetchAssocQuery($sql);
            if (!empty($result)) {
                $itemsPerModule = array();
                foreach ($result as $rows) {
                    $itemsPerModule[] = array(
                        "id" => $rows['id'],
                        "name" => $rows['name'],
                        "value" => $rows['value']);
                }
            }
            return $itemsPerModule;
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created 19-1-2015 (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get Feature settings
     *
     *@param (String)apikey
     *@param (Int)returns
     *@return json data
     *
     */
    public function getFeatureSettings()
    {
        try {
            $sql = "SELECT id,name,type,status FROM " . TABLE_PREFIX . "features";
            $sql .= (isset($this->_request['returns']) && $this->_request['returns']) ? " WHERE status='0'" : " WHERE mandatory_status='0'";
            $result = $this->executeFetchAssocQuery($sql . ' ORDER BY name');
            $featuresStatusData = array();
            if (!empty($result)) {
                foreach ($result as $v) {
                    $featuresStatusData[] = array("id" => $v['id'], "name" => $v['name'], "type" => $v['type'], "status" => $v['status']);
                }
            }
            return $featuresStatusData;
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created 19-1-2015 (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get admin settings
     *
     *@param (String)apikey
     *@return json data
     *
     */
    public function getAdminSettings()
    {
        try {
            $sql = "SELECT * FROM " . TABLE_PREFIX . "settings_config";
            $responseArray = array();
            $row = $this->executeFetchAssocQuery($sql);
            $responseArray['items_per_page'] = $row[0]['items_per_page'];
            //$responseArray['upload_active']= $row[0]['upload_active'];
            $responseArray['perInchPrice'] = $row[0]['price_per_unit'];
            if (isset($this->_request['returns']) && $this->_request['returns']) {
                $responseArray['isWhitebase'] = intval($row[0]['is_whitebase']);
                if ($row[0]['price_per_unit_calculation']) {
                    $responseArray['priceOption'] = 'print_area';
                } else {
                    $responseArray['priceOption'] = 'print_size';
                }

                return $responseArray;
            } else {
                $responseArray['id'] = $row[0]['id'];
                $responseArray['pricePerUnitCalc'] = $row[0]['price_per_unit_calculation'];
                $this->closeConnection();
                $this->response($this->json($responseArray), 200);
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
     *get all settings details
     *
     * @param (String)apikey
     * @param (int)type
     * @return string data
     *
     */
    public function allSettingsDetails($type = 0)
    {
        $admin = $this->getAllAdminSettings(1);

        $pos = strpos($admin, '{');
        $current_timestamp = strtotime("now");
        if ($pos !== false) {
            $admin = substr_replace($admin, '{"revision":"' . $current_timestamp . '",', $pos, 1);
        }
        $printProfile = Flight::printProfile();
        $print = $printProfile->getAllPrintSettings('', 1);
        $str = "RIAXEAPP.adminsettings=" . $admin . ";RIAXEAPP.printsettings=" . $print . ";";
        if ($type == 1) {
            $file_name = 'adminsettings.js';
            $url = $this->getCurrentUrl();
            $url = explode('/', $url);
            $new_file_name = str_ireplace('www.', '', $url[2]);
            $new_file_name = str_replace('.', '_', $new_file_name);
            $ds = DIRECTORY_SEPARATOR;
            $new_file_name = $new_file_name . $ds . $file_name;

            /*$path = __FILE__;
            $searchStr = 'designer-api'.$ds.'app-service'.$ds.'api_v4.php';
            $replaceStr = 'designer-app'.$ds.$new_file_name;
            $path = str_ireplace($searchStr,$replaceStr,$path);*/
            $path = '../designer-app' . $ds . $new_file_name;
            try {
                $h = fopen($path, 'w+');
                fwrite($h, $str);
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        } else {
            echo $str;
        }

    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *fetch general Settings
     *
     *@param (String)apikey
     *@return json data
     *
     */
    public function fetchGeneralSetting()
    {
        try {
            $rows = array();
            $sql = "SELECT * FROM " . TABLE_PREFIX . "general_setting";
            $rows = $this->executeFetchAssocQuery($sql);
            return $rows[0];
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Set Boundary general Settings
     *
     *@param (String)apikey
     *@param (int)bounds
     *@return json data
     *
     */
    public function setBoundsGeneralSetting()
    {
        $status = 0;
        $msg['status'] = 'Failed';
        try {
            if (!empty($this->_request) && $this->_request['bounds']) {
                $sql = "UPDATE " . TABLE_PREFIX . "general_setting SET bounds ='" . $this->_request['bounds'] . "'";
                $status = $this->executeGenericDMLQuery($sql);
            }
            if ($status) {
                $msg['status'] = 'success';
                $this->response($this->json($msg), 200);
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
     *Fetch general Settings
     *
     *@param (String)apikey
     *@return json data
     *
     */
    public function getGeneralSetting()
    {
        try {
            $apiKey = $this->_request['apikey'];
            if ($this->isValidCall($apiKey)) {
                $tabArray = $this->fetchTabDetails();
                $themeArr = $this->getThemes();
                $itemsPerModule = $this->getItemsPerModule();
                $featuresStatusData = $this->getFeatureSettings();
                $appCurrency = $this->fetchAppCurrency();
                $appUnit = $this->fetchAppUnit();
                $sql = "SELECT * FROM " . TABLE_PREFIX . "app_language";
                $res_language = $this->executeFetchAssocQuery($sql);
                $sql = "SELECT * FROM " . TABLE_PREFIX . "preloaded_items";
                $res_item = $this->executeFetchAssocQuery($sql);
                $response_arr = array();
                $response_arr['language'] = $res_language;
                $response_arr['items_loaded_per_module'] = $res_item;
                $allGeneralSetting = array();
                $allGeneralSetting = $response_arr;
                $allGeneralSetting['tab_details'] = $tabArray;
                $allGeneralSetting['themes'] = $themeArr;
                $allGeneralSetting['items_per_module'] = $itemsPerModule;
                $allGeneralSetting['features_status_data'] = $featuresStatusData;
				$allGeneralSetting['social_site_values'] =$this->getSocialImageDetails();
                //$allGeneralSetting['app_currency'] = $appCurrency;
                $allGeneralSetting['app_unit'] = $appUnit;
                $allGeneralSetting['general_setting'] = $this->fetchGeneralSetting();
				$allGeneralSetting['updated_css'] = $this->getStyleCss();
				if (isset($this->_request['type']) && $this->_request['type'] == 'updateFeature') {
                    $this->allSettingsDetails(1);
                }

                $this->response($this->json($allGeneralSetting), 200);
            } else {
                $msg = array("status" => "invalid");

            }
        } catch (Exception $e) {
            $msg = array('Caught exception:' => $e->getMessage());
        }
        $this->response($this->json($msg), 200);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Fetch App Units Currency
     *
     *@return json data
     *
     */
    public function fetchAppCurrency()
    {
        try {
            $sql = "SELECT id,name,code,is_default,symbol FROM " . TABLE_PREFIX . "app_currency";
            $appCurrency = array();
            $rows = $this->executeGenericDQLQuery($sql);
            for ($i = 0; $i < sizeof($rows); $i++) {
                $appCurrency[$i]['id'] = $rows[$i]['id'];
                $appCurrency[$i]['name'] = $rows[$i]['name'];
                $appCurrency[$i]['symbol'] = $rows[$i]['symbol'];
                $appCurrency[$i]['code'] = $rows[$i]['code'];
                $appCurrency[$i]['is_default'] = intval($rows[$i]['is_default']);
            }
            return $appCurrency;
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Fetch App Units
     *
     *@return json data
     *
     */
    public function fetchAppUnit()
    {
        try {
            $sql = "SELECT * FROM " . TABLE_PREFIX . "app_unit";
            $appUnit = array();
            $rows = $this->executeGenericDQLQuery($sql);
            for ($i = 0; $i < sizeof($rows); $i++) {
                $appUnit[$i]['id'] = $rows[$i]['id'];
                $appUnit[$i]['name'] = $rows[$i]['name'];
                $appUnit[$i]['is_default'] = intval($rows[$i]['is_default']);
            }
            return $appUnit;
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }

    }
    /**
     *
     *date created 19-1-2015 (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Update Admin Settings
     *
     *@param (String)apikey
     *@param (Int)itemsNo
     *@return json data
     *
     */
    public function updateAdminSettings()
    {
        try {
            $itemsNo = $this->_request['itemsNo'];
            $sql = "UPDATE " . TABLE_PREFIX . "settings_config SET items_per_page= $itemsNo ";
            $this->executeGenericDMLQuery($sql);
            $response = array();
            $response['status'] = "success";
            $response['message'] = "items per page updated ";
            $this->closeConnection();
            $this->response($this->json($response), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created 19-1-2015 (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Update Price Calculation Settings
     *
     *@param (String)apikey
     *@param (Int)pricePerUnitStatus
     *@param (Int)id
     *@return json data
     *
     */
    public function updatePriceCalculationSetting()
    {
        $apiKey = $this->_request['apikey'];
        if ($this->isValidCall($apiKey)) {
            try {
                $id = $this->_request['id'];
                $pricePerUnitStatus = $this->_request['pricePerUnitStatus'];
                $pricePerUnit = $this->_request['pricePerUnit'];
                $sql = "UPDATE " . TABLE_PREFIX . "settings_config SET price_per_unit=$pricePerUnit, price_per_unit_calculation=$pricePerUnitStatus";
                $status = $this->executeGenericDMLQuery($sql);

                $msg['status'] = ($status) ? "success" : "failed";

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
     *date created 19-1-2015 (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Update whitebase Configuration
     *
     *@param (String)apikey
     *@param (Int)wbConfig
     *@return json data
     *
     */
    public function updateWhitebaseConfig()
    {
        try {
            $apiKey = $this->_request['apikey'];
            if ($this->isValidCall($apiKey)) {
                $sql = "UPDATE " . TABLE_PREFIX . "settings_config SET is_whitebase=" . $this->_request['wbConfig'];
                $status = $this->executeGenericDMLQuery($sql);

                $msg['status'] = ($status) ? "success" : "failed";

                $this->closeConnection();
                $this->response($this->json($msg), 200);
            } else {
                $msg = array("status" => "invalid");
                $this->response($this->json($msg), 200);
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
     *grt themes colorsdetails
     *
     *@param (String)apikey
     *@param (int)ids
     *@return json data
     *
     */
    public function getThemeColorsDetails()
    {
        $id = $this->_request['id'];
        try {
            $sql = "select * from " . TABLE_PREFIX . "theme where id = $id";
            $rows = $this->executeGenericDQLQuery($sql);
            $themeArr['brand_primary'] = $rows[0]['brand_primary'];
            $themeArr['border_color'] = $rows[0]['border_color'];
            $themeArr['panel_color'] = $rows[0]['panel_color'];
            $themeArr['stage_color'] = $rows[0]['stage_color'];
            $themeArr['text_color'] = $rows[0]['text_color'];
            $this->response($this->json($themeArr), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *grt themes
     *
     *@param (String)apikey
     *@return json data
     *
     */
    public function getThemes()
    {
        try {
            $sql = "select * from " . TABLE_PREFIX . "theme";
            $rows = $this->executeGenericDQLQuery($sql);
            $themeArr = array();
            for ($i = 0; $i < sizeof($rows); $i++) {
                $themeArr[$i]['id'] = $rows[$i]['id'];
                $themeArr[$i]['file_name'] = $rows[$i]['file_name'];
                $themeArr[$i]['theme_name'] = $rows[$i]['theme_name'];
                $themeArr[$i]['brand_primary'] = $rows[$i]['brand_primary'];
                $themeArr[$i]['border_color'] = $rows[$i]['border_color'];
                $themeArr[$i]['panel_color'] = $rows[$i]['panel_color'];
                $themeArr[$i]['stage_color'] = $rows[$i]['stage_color'];
                $themeArr[$i]['text_color'] = $rows[$i]['text_color'];
                $themeArr[$i]['is_default'] = $rows[$i]['is_default'];
            }
            return $themeArr;
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Upadte Currency
     *
     *@param (String)apikey
     *@param (Object) currency
     *@param (int) id
     *@param (Enm)is_default
     *@return json data
     *
     */
    public function updateCurrency()
    {
        $status = 0;
        try {
            if (!empty($data['currency']) && !empty($data['currency']['id']) && !empty($data['currency']['is_default'])) {
                $sql_currency = "UPDATE " . TABLE_PREFIX . "app_currency SET is_default='0'";
                $this->executeGenericDMLQuery($sql_currency);
                $sql_currency = "UPDATE " . TABLE_PREFIX . "app_currency SET is_default='" . $data['currency']['is_default'] . "' WHERE id='" . $data['currency']['id'] . "'";
                $status = $this->executeGenericDMLQuery($sql_currency);
                $this->allSettingsDetails(1);
            }
            $status = ($status) ? 'Success' : 'Failed';
            $msg = array("status" => $status);
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
     *Upadte unit
     *
     *@param (String)apikey
     *@param (Object) unit
     *@param (int) id
     *@param (Enm)is_default
     *@return json data
     *
     */
    public function updateUnit()
    {
        try {
            $status = 0;
            if (!empty($data['unit']) && !empty($data['unit']['id']) && !empty($data['unit']['is_default'])) {
                $sql_unit = "UPDATE " . TABLE_PREFIX . "app_unit SET is_default='0'";
                $this->executeGenericDMLQuery($sql_unit);
                $sql_unit = "UPDATE " . TABLE_PREFIX . "app_unit SET is_default='" . $data['unit']['is_default'] . "' WHERE id='" . $data['unit']['id'] . "'";
                $status = $this->executeGenericDMLQuery($sql_unit);
            }
            $status = ($status) ? 'Success' : 'Failed';
            $msg = array("status" => $status);
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
     *Update App currency by currencyIds
     *
     *@param (String)apikey
     *@param (int)currencyIds
     *@return json data
     *
     */
    public function updateAppCurrency($currencyIds)
    {
        $status = 0;
        $sql = "UPDATE " . TABLE_PREFIX . "app_currency SET is_default='0'";
        $this->executeGenericDMLQuery($sql);
        $sql_currency = "UPDATE " . TABLE_PREFIX . "app_currency SET is_default='1'  WHERE id='" . $currencyIds . "'";
        $status .= $this->executeGenericDMLQuery($sql_currency);
        $this->allSettingsDetails(1);
        if ($status) {
            $msg = array("status" => "success");
        } else {
            throw new Exception('Error in currency update');
        }
        return $msg;
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Update App by unitIds id
     *
     *@param (String)apikey
     *@param (int)unitIds
     *@return json data
     *
     */
    public function updateAppUnit($unitIds)
    {
        $status = 0;
        $sql = "UPDATE " . TABLE_PREFIX . "app_unit SET is_default='0'";
        $this->executeGenericDMLQuery($sql);
        $sql_unit = "UPDATE " . TABLE_PREFIX . "app_unit SET is_default='1' WHERE id='" . $unitIds . "'";
        $status .= $this->executeGenericDMLQuery($sql_unit);
        if ($status) {
            $msg = array("status" => "success");
        } else {
            throw new Exception('Error in unit update');
        }
        return $msg;
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Update settings language by laguage id
     *
     *@param (String)apikey
     *@param (int)languageIds
     *@return json data
     *
     */
    public function updateSettingLanguage($languageIds)
    {
        $status = 0;
        $sql_language = "UPDATE " . TABLE_PREFIX . "app_language SET status='0'";
        $this->executeGenericDMLQuery($sql_language);
        $sql_language = "UPDATE " . TABLE_PREFIX . "app_language SET status='1' WHERE id='" . $languageIds . "'";
        $status .= $this->executeGenericDMLQuery($sql_language);
        if ($status) {
            $msg = array("status" => "success");
        } else {
            throw new Exception('Error in language status update');
        }
        return $msg;
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Update general settings language by feature id
     *
     *@param (String)apikey
     *@param (int)id
     *@return json data
     *
     */
    public function updateGeneralSettingLanguageByFeatureId($data = array())
    {
        $status = 0;
        if (!empty($data)) {
            if (!empty($data['language']) && !empty($data['language']['id']) && !empty($data['language']['status'])) {
                $sql_language = "UPDATE " . TABLE_PREFIX . "app_language SET status='0'";
                $this->executeGenericDMLQuery($sql_language);
                $sql_language = "UPDATE " . TABLE_PREFIX . "app_language SET status='" . $data['language']['status'] . "' WHERE id='" . $data['language']['id'] . "'";
                $status = $this->executeGenericDMLQuery($sql_language);
            }
            if (!empty($data['items'])) {
                $usql1 = '';
                $usql2 = '';
                foreach ($data['items'] as $v) {
                    $usql1 .= ' WHEN ' . $v['id'] . " THEN '" . $v['value'] . "'";
                    $usql2 .= ',' . $v['id'];
                }
                $usql = "UPDATE " . TABLE_PREFIX . "features SET general_setting_item_value = CASE id'.$usql1.' END WHERE id IN('.substr($usql2,1).')";
                $status = $this->executeGenericDMLQuery($usql);
            }
        }
        if ($status) {
            $msg = array("status" => "success");
        } else {
            throw new Exception('Error in language value update');
        }
        return $msg;

    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 14-4-2016(dd-mm-yy)
     *save save app language
     *
     * @param (String)apiKey
     * @param (String)name
     * @param (File)languageFile
     * @param (int)value
     * @return JSON data
     *
     */
    public function saveLanguage()
    {
        $apiKey = $this->_request['apikey'];
        $status = 0;
        $name = $this->_request['name'];
        $value = $this->_request['value'];
        $tmp = $_FILES['languageFile']['tmp_name'];
        if ($this->isValidCall($apiKey)) {
            try {
                $dir = $this->getLanguagePath();
                $type = 'json';
                $fname = 'locale' . '-' . $value . '.' . $type;
                $status = move_uploaded_file($tmp, $dir . $fname);
                if ($status) {
                    $sql0 = 'Select max(id) as id from  ' . TABLE_PREFIX . 'app_language';
                    $result0 = $this->getResult($sql0);
                    $maxId = $result0[0]['id'] + 1;
                    $sql_check = 'SELECT * FROM ' . TABLE_PREFIX . 'app_language WHERE name = "' . $name . '"';
                    $rows = $this->executeFetchAssocQuery($sql_check);
                    if (count($rows) == 0) {
                        $sql = 'INSERT INTO ' . TABLE_PREFIX . 'app_language(id,name,value) VALUES("' . $maxId . '","' . $name . '","' . $value . '")';
                        $status = $this->executeGenericDMLQuery($sql);
                    }
                    if ($status) {
                        $this->getGeneralSetting();
                    } else {
                        $msg = array("status" => "failed");
                    }

                }
            } catch (Exception $e) {
                $msg = array('Caught exception:' => $e->getMessage());
            }
        } else {
            $msg = array("status" => "invaliedkey");
        }

        $this->response($this->json($msg), 200);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 14-4-2016(dd-mm-yy)
     *save update app language by id
     *
     * @param (String)apikey
     * @param (int)id
     * @return JSON data
     *
     */
    public function editLanguage()
    {
        $status = 0;
        if (!empty($this->_request) && isset($this->_request['apikey']) && $this->isValidCall($this->_request['apikey'])) {
            $id = $this->_request['id'];
            try {
                $sql = "SELECT value FROM " . TABLE_PREFIX . "app_language WHERE  id='" . $id . "' LIMIT 1";
                $res = $this->executeFetchAssocQuery($sql);
                $dir = $this->getLanguagePath();
                $tmp = $_FILES['languageFile']['tmp_name'];
                $languagePath = $dir . 'locale-' . $res[0]['value'] . '.json';
                $status = move_uploaded_file($tmp, $languagePath);
                if ($status) {
                    $this->getGeneralSetting();
                } else {
                    $msg = array("status" => "failed");
                }

            } catch (Exception $e) {
                $msg = array('Caught exception:' => $e->getMessage());
            }
        } else {
            $msg = array('status' => 'invaliedkey');
        }
        $this->response($this->json($msg), 200);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 20-06-2016(dd-mm-yy)
     *Update language value in localsettings
     *
     */
    private function updateLocalSettingsLanguage()
    {
        try {
            $sqlLanguage = "select value from " . TABLE_PREFIX . "app_language where status = '1' LIMIT 1";
            $languageValue = $this->executeFetchAssocQuery($sqlLanguage);
            $localSettingsPath = DOC_ROOT.'/designer-tool/localsettings.js';
            $newData = file_get_contents($localSettingsPath);
            $pos = strpos($newData, '"');
            $newData = substr($newData, $pos);
            $newData = '{' . str_replace(';', '', $newData);
            $newData = $this->formatJSONToArray($newData);
            $newData['language'] = $languageValue[0]['value'];
            $updatedData = json_encode($newData);
            $updatedData = 'var RIAXEAPP ={};RIAXEAPP.localSettings = ' . $updatedData . ';';
            file_put_contents($localSettingsPath, $updatedData);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            return $result;
        }

    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *get labels
     *
     *@param (String)apikey
     *@return json data
     *
     */
    public function getLanguages()
    {
        try {
            $sql = "select * from " . TABLE_PREFIX . "app_language";
            $rows = $this->executeGenericDQLQuery($sql);
            $languageArr = array();
            for ($i = 0; $i < sizeof($rows); $i++) {
                $languageArr[$i]['id'] = $rows[$i]['id'];
                $languageArr[$i]['name'] = $rows[$i]['name'];
                $languageArr[$i]['status'] = $rows[$i]['status'];
            }
            $this->response($this->json($languageArr), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Set language
     *
     *@param (String)apikey
     *@param (int)id
     *@return json data
     *
     */
    public function setLanguages()
    {
        $id = $this->_request['id'];
        try {
            $sql = "UPDATE " . TABLE_PREFIX . "app_language SET status = 'false'";
            $status1 = $this->executeGenericDMLQuery($sql);
            $sql = "UPDATE " . TABLE_PREFIX . "app_language SET status = '" . true . "' WHERE id = " . $id;
            $status = $this->executeGenericDMLQuery($sql);
            $msg['status'] = ($status) ? 'success' : 'failed';
        } catch (Exception $e) {
            $msg = array('Caught exception:' => $e->getMessage());
        }
        $this->response($this->json($msg), 200);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *remove language
     *
     *@param (String)apikey
     *@param (int)id
     *@return json data
     *
     */
    public function removeLanguage()
    {
        $language_id = $this->_request['id'];
        try {
            $sql = "delete from " . TABLE_PREFIX . "app_language where id=$language_id";
            $this->executeGenericDMLQuery($sql);
            //deleting translated text from translate table by language id
            $sql = "delete from " . TABLE_PREFIX . "translate where language_id=$language_id";
            $this->executeGenericDMLQuery($sql);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
        $response = array();
        $response['status'] = "success";
        $response['message'] = "language deleted successfully !";
        $this->response($this->json($response), 200);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *get themes color
     *
     *@param (String)apikey
     *@return json data
     *
     */
    public function getThemeColors()
    {
        $apiKey = $this->_request['apikey'];
        if ($this->isValidCall($apiKey)) {
            try {
                $sql = "Select * from " . TABLE_PREFIX . "theme_color";
                $result = $this->executeGenericDQLQuery($sql);
                if (!empty($result)) {
                    $themeColors = array();
                    foreach ($result as $rows) {
                        $name = $rows['name'];
                        $id = $rows['id'];
                        $value = $rows['value'];
                        $data = array("id" => $id, "name" => $name, "value" => $value);
                        $themeColors[] = $data;
                    }
                    $this->closeConnection();
                    $this->response($this->json($themeColors), 200);
                }
            } catch (Exception $e) {
                $msg = array('Caught exception:' => $e->getMessage());
            }
        } else {
            $msg = array("status" => "invalid");
        }
        $this->response($this->json($msg), 200);
    }

    /**
     *
     *date created 19-1-2015 (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Get Tab details
     *
     *@param (String)apikey
     *@return json data
     *
     */
    public function fetchTabDetails()
    {
        try {
            $sql = "SELECT * FROM " . TABLE_PREFIX . "tabs";
            $tabsFromValue = mysqli_query($this->db, $sql);
            $tabArray['tabs'] = array();
            $i = 0;
            while ($row = mysqli_fetch_array($tabsFromValue)) {
                $tabArray['tabs'][$i]['id'] = $row['id'];
                $tabArray['tabs'][$i]['name'] = $row['name'];
                $tabArray['tabs'][$i]['is_default'] = $row['is_default'];
                $tabArray['tabs'][$i]['default_subtab_id'] = $row['default_subtab_id'];
                $tabArray['tabs'][$i]['subtabs'] = array();

                if ($row['default_subtab_id'] != 0) {
                    $sql = "SELECT id, name FROM " . TABLE_PREFIX . "features WHERE tab_id=" . $row['id'];
                    $subtabsFromValue = mysqli_query($this->db, $sql);
                    $j = 0;
                    while ($cRow = mysqli_fetch_array($subtabsFromValue)) {
                        $tabArray['tabs'][$i]['subtabs'][$j]['id'] = $cRow['id'];
                        $tabArray['tabs'][$i]['subtabs'][$j]['name'] = $cRow['name'];
                        $j++;
                    }
                }
                $i++;
            }
            return $tabArray;
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }
	
	/**
     *
     *date created 15-12-2016 (dd-mm-yy)
     *getSocialImageDetails
     *
     *@param (String)apikey
     *@return json data  or fetch social image data.
     *
     */
	public function getSocialImageDetails()
    {	
        try {
            $sql = "SELECT * FROM " . TABLE_PREFIX . "social_sites ";
            $result = $this->executeFetchAssocQuery($sql);
            if (!empty($result)) {
                $socialImageDetail = array();
				$resultArr = array();
                foreach ($result as $k=>$rows) {
					$socialImageDetail[$k]['site_id'] = $rows['id'];
					$socialImageDetail[$k]['name'] = $rows['name'];
					$sql_fetch = "SELECT key_index,key_value FROM " . TABLE_PREFIX . "social_site_values WHERE site_id =".$rows['id']." ";
					$data = $this->executeFetchAssocQuery($sql_fetch);
					foreach ($data as $k1=>$v) {
						$resultArr[$k1]['key_index'] = $v['key_index'];
						$resultArr[$k1]['key_value'] = $v['key_value'];
					}
					$socialImageDetail[$k]['keyArra'] = $resultArr;
                }
            }
			//$this->response($this->json($socialImageDetail), 200);
            return $socialImageDetail;
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }
	/**
     *
     *date created 15-12-2016 (dd-mm-yy)
     *addSocialImageDetails
     *
     *@param (String)apikey
     *@return json data
     *
     */
	public function addSocialImageDetails(){
		$social_sites_values_rel_sql = '';
		foreach($this->_request as $k1=>$v1){
			if(!empty($v1) && isset($v1['name'])){
				extract($this->_request);
				$sql="INSERT INTO ".TABLE_PREFIX."social_sites(name)VALUES('".$v1['name']."')";
				$social_sites_id = $this->executeGenericInsertQuery($sql);
			}
			foreach ($v1['keyArra'] as $k => $v) {
				$social_sites_values_rel_sql = "INSERT INTO " . TABLE_PREFIX . " social_site_values(site_id	,key_index,key_value)VALUES('".$social_sites_id."','".$v['key_index']."','".$v['key_value']."')";
				$status = $this->executeGenericInsertQuery($social_sites_values_rel_sql);
			} 
		}
		if ($status) {
			$this->_request['type'] = 'updateFeature';
			$this->getGeneralSetting();
		} else {
			$msg = array("status" => "failed");
		} 
	}
	/**
     *
     *date created 15-12-2016 (dd-mm-yy)
     *updateSocialImageDetails
     *
     *@param (String)apikey
     *@return json data
     *
     */
	public function updateSocialImageDetails(){
		if (!empty($this->_request) && isset($this->_request['apikey']) && $this->isValidCall($this->_request['apikey'])) {
			foreach($this->_request['socialIds'] as $k1=>$v1){
				foreach ($this->_request['socialKeyValues'] as $k => $v) {
					$social_sites_values_rel_sql = "UPDATE " . TABLE_PREFIX . " social_site_values SET key_value ='".$v."'  WHERE site_id = ".$v1." and key_index = '".$this->_request['socialKeyIndex'][0]."'";
					$status = $this->executeGenericDMLQuery($social_sites_values_rel_sql);
					//echo ($social_sites_values_rel_sql); exit;	
				} 
			}
			if ($status) {
				$this->_request['type'] = 'updateFeature';
				$this->getGeneralSetting();
			} else {
				$msg = array("status" => "failed");
			}
		} else {
            $msg = array("status" => "invalidkey");
        }
		$this->response($this->json($msg), 200);
	}

	/**
	*getStyleCss
	*
	*@return string
	*
	*/
	public function getStyleCss() {
		$file = $this->getBasePath() . LANGUAGE_PATH .'/'. FOLDER_NAME . '/style.css';
		if(file_exists($file)) {
			$data = @file_get_contents($file);
			return base64_encode($data);	  
		} else return '';
	}

	/**
	*updateStyleCss
	*
	*@param (String)apikey
	*@param (String)cssText
	*@return json data with status
	*
	*/
	public function updateStyleCss() {
		$status = 'failed';
		if (!empty($this->_request) && isset($this->_request['apikey']) && $this->isValidCall($this->_request['apikey'])) {
			extract($this->_request);
			$file = $this->getBasePath() . LANGUAGE_PATH .'/'. FOLDER_NAME . '/style.css';
			$data = base64_decode($cssText);
			if(file_put_contents($file, $data)){
				$this->_request['type'] = 'updateFeature';
			}
            $this->getGeneralSetting();
			$status = 'Success';
		}
		$msg['status'] = $status;
		$this->response($this->json($msg), 200);
	}
}
