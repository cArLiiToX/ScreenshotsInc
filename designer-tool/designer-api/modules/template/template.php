<?php
/* Check Un-authorize Access */
if (!defined('accessUser')) {
    die("Error");
}

class Template extends UTIL
{

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get Template category by print id
     *
     *@param (String)apikey
     *@param (int)printId
     *@return json data
     *
     */
    public function getTemplateCategory()
    {
        try {
            $catagoryArray = array();
            if (isset($this->_request['printId']) && $this->_request['printId'] != 0) {
                $sql = "SELECT tc.id,tc.name FROM " . TABLE_PREFIX . "template_category tc join " . TABLE_PREFIX . "template_category_printmethod_rel tcppr on tcppr.temp_category_id =tc.id where tcppr.print_method_id='" . $this->_request['printId'] . "' ORDER BY tc.name DESC";
            } else {
                $sql = "SELECT id,name FROM " . TABLE_PREFIX . "template_category ORDER BY name DESC";
            }
            $categoryDetail = array();
            $rows = $this->executeGenericDQLQuery($sql);
            for ($i = 0; $i < sizeof($rows); $i++) {
                $categoryDetail[$i]['id'] = $rows[$i]['id'];
                $categoryDetail[$i]['name'] = $rows[$i]['name'];
            }
            $this->response($this->json($categoryDetail,1), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Get Template sub category
     *
     *@param (String)apikey
     *@param (int)id
     *@return json data
     *
     */
    public function getTemplateSubCategory()
    {
        $cat_id = $this->_request['id'];
        try {
            $sql = "Select * from  " . TABLE_PREFIX . "template_subcategory where cat_id =" . $cat_id;
            $result = $this->executeGenericDQLQuery($sql);
            if (!empty($result)) {
                $tmpltSubCategory = array();
                foreach ($result as $rows) {
                    $name = $rows['name'];
                    $id = $rows['id'];
                    $data = array("id" => $id, "name" => $name);
                    $tmpltSubCategory[] = $data;
                    //array_push($images, $data);
                }
                $this->closeConnection();
                $this->response($this->json($tmpltSubCategory), 200);
            } else {
                $msg = array("status" => "nodata");
                $this->closeConnection();
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
     *date modified 15-4-2016 (dd-mm-yy)
     *Get Template
     *
     *@param (String)apikey
     *@param (int)tempId
     *@param (int)categoryId
     *@param (int)subCategoryId
     *@param (String)searchVal
     *@param (int)lastLoaded
     *@param (int)loadCount
     *@return json data
     *
     */
    public function getTemplateList()
    {
        try {
            $tempId = isset($this->_request['tempId'])?$this->_request['tempId']:0;
            $cat_id = $this->_request['categoryId'];
            $subCat_id = isset($this->_request['subCategoryId'])?$this->_request['subCategoryId']:0;
            $print_method = $this->_request['printId'];
            $searchString = $this->_request['searchVal'];
            $start = $this->_request['lastLoaded'];
            $range = $this->_request['loadCount'];
            $searchByCategory = ($cat_id != '') ? " and cat_id='" . $cat_id . "'" : "";
            $searchByString = ($searchString != '') ? " and   or name LIKE '" . $searchString . "%'" : "";
			$sql = "Select ts.id,ts.name,ts.json_data,ts.status,ts.template_image from  " . TABLE_PREFIX . "template_state as ts ";
            if ($cat_id >= 1 && $searchString == '' && $print_method != '') {
                $sql .= "LEFT JOIN " . TABLE_PREFIX . "template_category_printmethod_rel tcppr ON tcppr.temp_category_id = ts.cat_id
                WHERE cat_id =$cat_id and tcppr.print_method_id='" . $print_method . "' ORDER BY ts.id DESC";
            }if ($cat_id >= 1 && $searchString != '' && $print_method != '') {
                $sql .= "LEFT JOIN " . TABLE_PREFIX . "template_category_printmethod_rel tcppr ON tcppr.temp_category_id = ts.cat_id WHERE cat_id =$cat_id and (name LIKE '" . $searchString . "%') and tcppr.print_method_id='" . $print_method . "' ORDER BY ts.id DESC";
            }if ($cat_id == 0 && $searchString != '' && $print_method != '') {
                $sql .= "LEFT JOIN " . TABLE_PREFIX . "template_category_printmethod_rel tcppr ON tcppr.temp_category_id = ts.cat_id WHERE 1 and tcppr.print_method_id='" . $print_method . "' and (name LIKE '" . $searchString . "%')  ORDER BY ts.id DESC";
            }
            if ($cat_id == 0 && $searchString == '' && $print_method != '') {
                $sql .= "LEFT JOIN " . TABLE_PREFIX . "template_category_printmethod_rel tcppr ON tcppr.temp_category_id = ts.cat_id and tcppr.print_method_id='" . $print_method . "' ORDER BY ts.id DESC ";
            }
            if ($cat_id == 0 && $print_method == '') {
                $sql .= "ORDER BY ts.id DESC";
            }
            if ($cat_id != 0 && $print_method == '' && $tempId == '') {
                $sql .= "where ts.cat_id = " . $cat_id . " ORDER BY ts.id DESC";
            }
            if ($tempId != '' && $print_method == '' && $cat_id == 0) {
                $sql .= "where ts.id='" . $tempId . "' ORDER BY ts.id DESC";
            }
            if ($start != '' && $range != '') {
                $sql .= "  limit $start,$range";
            }

            $result = $this->executeFetchAssocQuery($sql);
            if (!empty($result)) {
                $images = array();
                foreach ($result as $rows) {
                    $name = $rows['name'];
                    $template_image = $rows['template_image'];
                    $savePath = $this->getTemplateImageURL();
                    $imageURL = $savePath . $template_image;
                    $productURL = $savePath . 'products/' . $template_image;
					$data = json_decode($rows['json_data'],true);
					$captureImage = $data['templateConfig']['captureImage'];
                    $id = $rows['id'];
                    $status = $rows['status'];
                    $data = array("captureImage" => $captureImage, "template_image" => $template_image, "template_imagePath" => $imageURL, "product_image" => $productURL, "id" => $id, "name" => $name, "status" => $status);
                    $images[] = $data;
                    //array_push($images, $data);
                }
                $this->closeConnection();
                $this->response($this->json($images,1), 200);
            } else {
                $msg = array("status" => "nodata");
                $this->closeConnection();
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
     *date modified 15-4-2016 (dd-mm-yy)
     *Save Template category
     *
     *@param (String)apikey
     *@param (int)category
     *@return json data
     *
     */
    public function saveTemplateCategory()
    {
        $status = 0;
        try {
            if (!empty($this->_request) && isset($this->_request['category'])) {
                $category = addslashes($this->_request['category']);
                $chk_duplicate = "SELECT COUNT(*) AS duplicate FROM " . TABLE_PREFIX . "template_category WHERE name='" . $category . "'";
                $res = $this->executeFetchAssocQuery($chk_duplicate);
                if ($res[0]['duplicate']) {
                    $msg['msg'] = 'Duplicate entry';
                } else {
                    $sql = "INSERT INTO " . TABLE_PREFIX . "template_category (name) VALUES ('$category')";
                    $status = $this->executeGenericDMLQuery($sql);
                    $msg['templtCategory'] = $category;
                }
                $msg['status'] = ($status) ? 'success' : 'failed';
            } else {
                $msg['status'] = 'No Data';
            }

            $this->closeConnection();
            $this->response($this->json($msg,1), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *upadte Template category name
     *
     * @param (String)apikey
     * @param (int)id
     * @param (String)name
     * @return json data
     *
     */
    public function updateTemplateCategory()
    {
        $status = 0;
        if (!empty($this->_request) && $this->_request['id'] && isset($this->_request['name'])) {
            extract($this->_request);
            try {
                $name = addslashes($name);
                $chk_duplicate = "SELECT COUNT(*) AS duplicate FROM " . TABLE_PREFIX . "template_category WHERE name='" . $name . "' AND id !='" . $id . "'";
                $res = $this->executeFetchAssocQuery($chk_duplicate);
                if ($res[0]['duplicate']) {
                    $msg['msg'] = 'Duplicate Entry';
                } else {
                    $sql = "UPDATE " . TABLE_PREFIX . "template_category SET name='" . $name . "' WHERE id='" . $id . "'";
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
     *Remove Template category by print id
     *
     *@param (String)apikey
     *@param (int)categoryId
     *@return json data
     *
     */
    public function removeTemplateCategory()
    {
        try {
            $apiKey = $this->_request['apikey'];
            $categoryId = $this->_request['categoryId'];
            $sql = "delete from  " . TABLE_PREFIX . "template_category where id =$categoryId ";
            $result1 = $this->executeGenericDMLQuery($sql);
            $sql = "delete from  " . TABLE_PREFIX . "template_subcategory where cat_id =$categoryId ";
            $result2 = $this->executeGenericDMLQuery($sql);
            $msg['status'] = ($result1) ? 'success' : 'failed';
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
     *Get Template
     *
     *@param (String)apikey
     *@param (int)templtId
     *@return json data
     *
     */
    public function getTemplate()
    {
        try {
            $apiKey = $this->_request['apikey'];
            $id = $this->_request['templtId'];
            $sql = "Select json_data,status from  " . TABLE_PREFIX . "template_state where id=" . $id;
            $rows = $this->executeGenericDQLQuery($sql);
            if (!empty($rows)) {
                $jsonData = $rows[0]['json_data'];
                $status = $rows[0]['status'];
                $json_data = (!strpos($jsonData, '\\\"') ? $jsonData : stripslashes($jsonData));
                $this->response($json_data, 200);
            } else {
                $msg = array("status" => "nodata", "sql" => $sql);
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
     *Save upadte Template
     *
     * @param (String)apikey
     * @param (String)name
     * @param (int)id
     * @param (int)cate_id
     * @return json data
     *
     */
    public function updateTemplate()
    {
        $status = 0;
        if (!empty($this->_request) && isset($this->_request['id']) && isset($this->_request['name']) && isset($this->_request['cate_id'])) {
            extract($this->_request);
            $id_str = implode(',', $id);
            try {
                $sql_fetch = "Select id, json_data from  " . TABLE_PREFIX . "template_state WHERE id IN(" . $id_str . ")";
                $rows = $this->executeGenericDQLQuery($sql_fetch);
                for ($j = 0; $j < sizeof($rows); $j++) {
                    $jsonData = str_replace('\/', '/', $rows[$j]['json_data']);
                    $jsonData = $this->formatJSONToArray($jsonData, false);
                    $jsonData->templateConfig->name = $name;
                    $name = addslashes($name);
                    $jsonData->templateConfig->category = $cate_id;
                    $jsonData = json_encode($jsonData);
                    $jsonData = $this->executeEscapeStringQuery($jsonData);
                    $sql = "UPDATE " . TABLE_PREFIX . "template_state SET name = '" . $name . "',cat_id='" . $cate_id . "',json_data='" . $jsonData . "' WHERE id = '" . $rows[$j]['id'] . "'";
                    $status = $this->executeGenericDMLQuery($sql);
                }
                $msg['status'] = ($status) ? 'success' : 'failed';
            } catch (Exception $e) {
                $msg = array('Caught exception:' => $e->getMessage());
            }
        } else {
            $msg = array("status" => "nodata");
        }
        $this->response($this->json($msg), 200);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Remove Template
     *
     *@param (String)apikey
     *@param (int)templtId
     *@return json data
     *
     */
    public function removeTemplate()
    {
        try {
            $apiKey = $this->_request['apikey'];
            $id = $this->_request['templtId'];
            $id = explode(",", $id); // gives array if ids
            $dir = $this->getTemplateImagePath();
            for ($i = 0; $i < count($id); $i++) {
                $fileName = $id[$i] . '.svg';
                $filePath = $dir . $fileName;
                $templProPath = $dir . 'products/' . $fileName;
                $msg = '';
                if (!$dir) {
                    $this->response('', 204); //204 - immediately termiante this request
                }
                $sql = "delete from " . TABLE_PREFIX . "template_state where id=" . $id[$i];
                $result = $this->executeGenericDMLQuery($sql);
                if ($result > 0) {
                    $msg = array('status' => "success");
                    if (file_exists($filePath) && file_exists($templProPath)) {
                        if (is_file($filePath) && is_file($templProPath)) {
                            unlink($filePath); // removing template svg
                            unlink($templProPath); // removing template product
                        }
                        if (file_exists($filePath)) {
                            //file couldn't be deleted
                            $msg = array('status' => "image removed failed");
                        } else {
                            $msg = array('status' => "success");
                        }
                    } else {
                        $msg = array('status' => "success", "image removed" => "failed", "error" => "File doesn't exist", "imageFilePath" => $filePath);
                    }
                }
            }
            $this->closeConnection();
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
     *Save Template
     *
     *@param (String)apikey
     *@param (int)refid
     *@param (Array)jsonData
     *@return json data
     *
     */
    public function saveTemplate()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('!POST', 406);
        }
        try {
            $apiKey = $this->_request['apikey'];
            $refid = $this->_request['refid'];
            $jsonData = json_decode(urldecode($this->_request['data']), true);
            if (is_array($jsonData)) {
                $dataArray = $jsonData;
            } else {
                $dataArray = $this->formatJSONToArray($jsonData);
            }
            $tmpltName = addslashes($dataArray['templateConfig']['name']);
            $tmpltCategory = $dataArray['templateConfig']['category'];
            $tmpltSubCategory = $dataArray['templateConfig']['subCategory'];
            $productId = $dataArray['templateConfig']['productId'];
            $variantId = $dataArray['templateConfig']['variantId'];
            $productURL = $dataArray['templateConfig']['productURL']; //to be add in clientapp
            $captureImage = $dataArray['templateConfig']['captureImage']; //to be add in clientapp
            $svgData = $dataArray['objects'];

            $checkExistSql = "SELECT count(*) AS nos FROM ".TABLE_PREFIX."template_state WHERE name = '".$tmpltName."'";
            $exist = $this->executeFetchAssocQuery($checkExistSql);
            if(!empty($exist) && $exist[0]['nos']){
                $msg = array("status" => "failed", "sql" => "Duplicate Template Name.");
            }else{
                $productImgContent = file_get_contents($productURL);
                $base64ProductImgData = base64_encode($productImgContent);
                $cartObj = Flight::carts();
                $svgPreviewData = $cartObj->parsePrintSVG($svgData);
                $templatePreviewData = "<svg xmlns='http://www.w3.org/2000/svg' id='svgroot' xlinkns='http://www.w3.org/1999/xlink' width='500' height='500' x='0' y='0' overflow='visible'>" . stripslashes($svgPreviewData['svgStringwithImageURL']) . "</svg>";
                $templateProductPreviewData = "<svg xmlns='http://www.w3.org/2000/svg' id='svgroot' xlinkns='http://www.w3.org/1999/xlink' width='500' height='500' x='0' y='0' overflow='visible'><image x='0' y='0' width='500' height='500' id='svg_1' xmlns:xlink='http://www.w3.org/1999/xlink' xlink:href='data:image/png;base64," . stripslashes($base64ProductImgData) . "'></image>" . stripslashes($svgPreviewData['svgStringwithImageURL']) . "</svg>";
                $sql0 = "Select max(id) as id from " . TABLE_PREFIX . "template_state";
                $result0 = $this->getResult($sql0);
                $maxId = $result0[0]['id'] + 1;
                $ext = 'svg';
                $tmpltImgFileName = $maxId . '.' . $ext;
                $tmpltImgSavePath = $this->getTemplateImagePath();
                if (!file_exists($tmpltImgSavePath)) {
                    mkdir($tmpltImgSavePath, 0777, true);
                }
                $tmpltImgFilePath = $tmpltImgSavePath . $tmpltImgFileName;
                $tmpltImgFileURL = $this->getTemplateImageURL() . $tmpltImgFileName;
                $tmpltImgStatus = file_put_contents($tmpltImgFilePath, $templatePreviewData);

                $tmpltProductImgSavePath = $this->getTemplateImagePath() . 'products/';
                if (!file_exists($tmpltProductImgSavePath)) {
                    mkdir($tmpltProductImgSavePath, 0777, true);
                }
                $tmpltProductImgFilePath = $tmpltProductImgSavePath . $tmpltImgFileName;
                $tmpltProductImgFileURL = $this->getTemplateImageURL() . 'products/' . $tmpltImgFileName;
                $tmpltProductImgStatus = file_put_contents($tmpltProductImgFilePath, $templateProductPreviewData);
                $dataArray['templateConfig']['templateImage'] = $tmpltImgFileURL;
                $dataArray['templateConfig']['productImage'] = $tmpltProductImgFileURL;
                $dataArray = json_encode($dataArray);
                $dataArray = mysqli_real_escape_string($this->db, $dataArray);
                $sql = "INSERT INTO " . TABLE_PREFIX . "template_state (id, name, json_data, product_image, template_image, cat_id, sub_id, pid, pvid, date_created) VALUES ($maxId, '$tmpltName', '$dataArray', '$tmpltImgFileName', '$tmpltImgFileName', $tmpltCategory, $tmpltSubCategory, $productId, $variantId, now())";
                $status = $this->executeGenericDMLQuery($sql);
                if ($status) {
                    $msg = array("status" => "success", "tmplid" => $maxId, "captureImage" => $captureImage, "tmplImage" => $tmpltImgFileURL, "tmplThumbImage" => $tmpltProductImgFileURL);
                } else {
                    $msg = array("status" => "failed", "sql" => $sql);
                }
            }
            $this->response($this->json($msg), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }
}
