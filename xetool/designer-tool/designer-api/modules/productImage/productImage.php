<?php
/* Check Un-authorize Access */
if (!defined('accessUser')) {
    die("Error");
}

class ProductImage extends UTIL
{
    /**
     *
     *date created 3-6-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *add template side
     *
     *@param (String)apikey
     *@param (Array)product_templist
     *@return json data
     *
     */

    public function setProductTempList()
    {
        $status = 0;
        if (!empty($this->_request) && !empty($this->_request['product_templist'])) {
            extract($this->_request);
            $dir = $this->setProductTemplatePath();
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }

            $type = 'png';
            $insertSql = "INSERT INTO " . TABLE_PREFIX . "product_template (name,date_created,date_modified,is_default) VALUES('" . $product_templist['name'] . "',NOW(),NULL,'" . $product_templist['is_default'] . "');";
            $temp_id = $this->executeGenericInsertQuery($insertSql);

            foreach ($product_templist['temp_side_list'] as $k => $v) {
                $sql_side = "INSERT INTO " . TABLE_PREFIX . "product_temp_side (product_temp_id,side_name,sort_order,image,date_created,date_modified)
                VALUES('" . $temp_id . "','" . $v['side_name'] . "','" . $v['sort_order'] . "','" . $type . "',NOW(),NULL);";
                $side_id[$k] = $this->executeGenericInsertQuery($sql_side);

                if (strpos($v['url'], ';base64') != false) {
                    if (!file_exists($dir . $temp_id)) {
                        mkdir($dir . $temp_id, 0777, true);
                    }

                    $fname = $dir . $temp_id . '/' . $side_id[$k] . '.' . $type;
                    $base64 = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $v['url']));

                    $status = file_put_contents($fname, $base64);
                }
            }
        }
        $msg['status'] = ($status) ? $this->getProductTempList() : 'failed';
        $this->response($this->json($msg), 200);
    }

    /**
     *
     *date created 3-6-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *upadte template side
     *
     *@param (String)apikey
     *@param (Array)product_templist
     *@return json data
     *
     */
    public function updateProductTemp()
    {
        $status = 0;
        if (!empty($this->_request) && !empty($this->_request['product_templist'])) {
            extract($this->_request['product_templist']);
            $checkExistSql = "SELECT count(*) AS nos FROM " . TABLE_PREFIX . "product_template WHERE name = '" . $name . "' AND pk_id != '" . $product_temp_id . "'";
            $exist = $this->executeFetchAssocQuery($checkExistSql);
            if (!empty($exist) && $exist['nos']) {
                $msg['msg'] = 'Duplicate template name.';
            } else {
                $dir = $this->setProductTemplatePath();
                $type = 'png';

                $updateSql = "UPDATE " . TABLE_PREFIX . "product_template SET name = '" . $name . "',date_modified = NOW() WHERE pk_id='" . $product_temp_id . "';";
                $status = $this->executeGenericDMLQuery($updateSql);

                $Sql = "DELETE FROM " . TABLE_PREFIX . "product_temp_side WHERE product_temp_id='" . $product_temp_id . "';";
                $status = $this->executeGenericDMLQuery($Sql);
                if (!empty($temp_side_list)) {
                    if (!file_exists($dir)) {
                        mkdir($dir, 0777, true);
                    }

                    foreach ($temp_side_list as $k => $v) {
                        $sql_side = "INSERT INTO " . TABLE_PREFIX . "product_temp_side (product_temp_id,side_name,sort_order,image,date_modified)
                        VALUES('" . $product_temp_id . "','" . $v['side_name'] . "','" . $v['sort_order'] . "','" . $type . "',NOW());";
                        $side_id[$k] = $this->executeGenericInsertQuery($sql_side);
                        if ($v['side_id']) {
                            if (!file_exists($dir . $product_temp_id)) {
                                mkdir($dir . $product_temp_id, 0777, true);
                            }

                            $fnam = $dir . $product_temp_id . '/' . $side_id[$k] . '.' . $type;
                            $imageData = file_get_contents($v['url']);
                            $status = file_put_contents($fnam, $imageData);
                            unlink($dir . $product_temp_id . '/' . $v['side_id'] . '.' . $type);

                        }
                        if (strpos($v['url'], ';base64') != false) {
                            if (!file_exists($dir . $product_temp_id)) {
                                mkdir($dir . $product_temp_id, 0777, true);
                            }

                            $fname = $dir . $product_temp_id . '/' . $side_id[$k] . '.' . $type;
                            $base64 = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $v['url']));
                            $status = file_put_contents($fname, $base64);
                        }
                    }
                }
            }
        }
        $msg['status'] = ($status) ? $this->getProductTempList() : 'failed';
        $this->response($this->json($msg), 200);
    }

    /**
     *
     *date created 3-6-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *Remove product template by template id
     *
     *@param (String)apikey
     *@param (int)templateId
     *@return json data
     *
     */
    public function removeProductTemplate()
    {
        $templateId = $this->_request['templateId'];
        if (isset($templateId) && $templateId != '') {
            $status = 0;
            $imageurl = $this->setProductTemplatePath();
            $sql_temp = "DELETE FROM " . TABLE_PREFIX . "product_template WHERE  pk_id = '" . $templateId . "'";
            $status = $this->executeGenericDMLQuery($sql_temp);
            $sql_data = "DELETE FROM " . TABLE_PREFIX . "product_temp_rel WHERE  temp_id = '" . $templateId . "'";
            $status = $this->executeGenericDMLQuery($sql_data);
            $sql_pro_temp = "DELETE FROM " . TABLE_PREFIX . "product_temp_side WHERE  product_temp_id = '" . $templateId . "'";
            $status = $this->executeGenericDMLQuery($sql_pro_temp);
            if ($status) {
                $this->deleteZipFileFolder($imageurl . $templateId);
            }

            $msg['status'] = ($status) ? $this->getProductTempList() : 'failed';
        } else {
            $msg['status'] = 'no templateid';
        }
        $this->response($this->json($msg), 200);
    }

    /**
     *
     *date created 3-6-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *Get product template list
     *
     *@param (String)apikey
     *@param (int)start
     *@param (int)count
     *@return json data
     *
     */
    public function getProductTempList()
    {
        $start = (isset($this->_request['start'])) ? $this->_request['start'] : 0;
        $range = (isset($this->_request['count'])) ? $this->_request['count'] : 0;
        if ($range == 0) {
                $sql="SELECT pk_id,name,is_default FROM ".TABLE_PREFIX."product_template";
            }else{
                $sql="SELECT pk_id,name,is_default FROM ".TABLE_PREFIX."product_template LIMIT $start, $range";
            }
        $rows = $this->executeFetchAssocQuery($sql);
        $result = array();
        $i = 0;
        foreach ($rows as $value) {
            $result[$i]['product_temp_id'] = $value['pk_id'];
            $result[$i]['name'] = $value['name'];
            $result[$i]['is_default'] = $value['is_default'];
            $side_arr = array();
            $side_sql = "SELECT distinct pts.pk_id,pts.sort_order,pts.side_name,pts.image
                        FROM " . TABLE_PREFIX . "product_template AS pt ," . TABLE_PREFIX . "product_temp_side AS pts
                        WHERE pts.product_temp_id ='" . $value['pk_id'] . "' ORDER BY sort_order";
            $row = $this->executeFetchAssocQuery($side_sql);
            if (!empty($row)) {
                $imageurl = $this->getProductTemplatePath();
                foreach ($row as $key => $v) {
                    $side_arr[$key]['side_id'] = $v['pk_id'];
                    $side_arr[$key]['side_name'] = $v['side_name'];
                    $side_arr[$key]['sort_order'] = $v['sort_order'];
                    $side_arr[$key]['url'] = $imageurl . $value['pk_id'] . '/' . $v['pk_id'] . '.' . $v['image'];
                }
                $result[$i]['temp_side_list'] = $side_arr;
            } else {
                $result[$i]['temp_side_list'] = [];
            }
            $i++;
        }
        $resultArr['product_templist'] = $result;
        $response = (empty($result)) ? array() : $resultArr['product_templist'];
        $this->response($this->json($resultArr), 200);
    }

    /**
     *
     *date created 3-6-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *set default template to product
     *
     *@param (String)apikey
     *@param (int)templateId
     *@return json data
     *
     */
    public function setDefaultProductTemp()
    {
        $status = 0;
        if (isset($this->_request['templateId']) && $this->_request['templateId']) {
            $sql = "UPDATE " . TABLE_PREFIX . "product_template SET is_default='0'";
            $status = $this->executeGenericDMLQuery($sql);
            $sql = "UPDATE " . TABLE_PREFIX . "product_template SET is_default='1' WHERE pk_id='" . $this->_request['templateId'] . "' LIMIT 1";
            $status = $this->executeGenericDMLQuery($sql);
        }
        $msg['status'] = ($status) ? $this->getProductTempList() : 'failed';
        $this->response($this->json($msg), 200);
    }

}
