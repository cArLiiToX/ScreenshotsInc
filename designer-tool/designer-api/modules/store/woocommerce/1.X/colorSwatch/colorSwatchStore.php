<?php
/* Check Un-authorize Access */
if (!defined('accessUser')) {
    die("Error");
}

class ColorSwatchStore extends UTIL
{

    /**
     * Add color swatch to the color
     *
     * @param   color name, image
     * @return  boolean true/false
     */
    public function addColorSwatch()
    {
        header('HTTP/1.1 200 OK');
        global $wpdb;
        $error = '';
        $colortexonomy = "pa_".$this->getStoreAttributes("xe_color");

        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];

            if (isset($this->_request['colorname']) && trim($this->_request['colorname']) != '') {
                $colorname = $this->_request['colorname'];
            }
            if (isset($this->_request['imagename']) && trim($this->_request['imagename']) != '') {
                $imagename = trim($this->_request['imagename']);
            }
            if (isset($this->_request['imagetype']) && trim($this->_request['imagetype']) != '') {
                $imagetype = trim($this->_request['imagetype']);
            }
            try {
                $table_name = $wpdb->prefix . "terms";
                $wpdb->insert($table_name, array('name' => $colorname, 'slug' => strtolower($colorname), 'term_group' => 0));
                $lastid = $wpdb->insert_id;

                $table_name = $wpdb->prefix . "term_taxonomy";
                $wpdb->insert($table_name, array('term_id' => $lastid, 'taxonomy' => $colortexonomy, 'description' => '', 'parent' => '', 'count' => 0));

                $rsultrsponse = array();
                $rsultrsponse['attribute_id'] = $lastid;
                $rsultrsponse['attribute_value'] = $colorname;
                $rsultrsponse['status'] = 'success';
                if (isset($imagename) && $imagename != '') {
                    $swatchObj = Flight::colorSwatch();
                    $swatchObj->customRequest(array('value' => $rsultrsponse['attribute_id'], 'imgData' => $imagename, 'imagetype' => $imagetype, 'swatchWidth' => 45, 'swatchHeight' => 45, 'base64Data' => base64_decode($imagename)));
                    $saveSucss = $swatchObj->saveColorSwatch('add');
                    $rsultrsponse['swatchImage'] = $saveSucss['swatchImage'];
                    $rsultrsponse['hexCode'] = $saveSucss['hexCode'];

                }
                $result = json_encode($rsultrsponse);

            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $error = true;
            }
            if (!$error) {
                $this->response($result, 200);
            } else {
                $this->response(json_decode($result), 200);
            }
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
            $this->response($this->json($msg), 200);
        }
    }

    /**
     * Edit color swatch of a color
     *
     * @param   option_id, color name, image
     * @return  boolean true/false
     */
    public function editSwachColor()
    {
        header('HTTP/1.1 200 OK');
        global $wpdb;
        $error = '';
        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];

            if (isset($this->_request['option_id']) && trim($this->_request['option_id']) != '') {
                $option_id = $this->_request['option_id'];
            }
            if (isset($this->_request['colorname']) && trim($this->_request['colorname']) != '') {
                $colorname = $this->_request['colorname'];
            }
            if (isset($this->_request['imagename']) && trim($this->_request['imagename']) != '') {
                $imagename = trim($this->_request['imagename']);
            }
            if (isset($this->_request['imagetype']) && trim($this->_request['imagetype']) != '') {
                $imagetype = trim($this->_request['imagetype']);
            }
            try {
                $filters = array(
                    'option_id' => $option_id,
                    'colorname' => $colorname,
                );
                $table_name = $wpdb->prefix . 'terms';
                $data = array(
                    'name' => $colorname,
                    'slug' => strtolower($colorname),
                );
                $where = array('term_id' => $option_id);
                $format = array('%s', '%d');
                $where_format = array('%d');
                //$wpdb->update( $table_name, $data, $where, $format, $where_format );
                $rsultrsponse = array();
                $rsultrsponse['attribute_id'] = $option_id;
                $rsultrsponse['attribute_value'] = $colorname;
                $rsultrsponse['status'] = 'success';
                if (isset($imagename) && $imagename != '') {
                    $swatchObj = Flight::colorSwatch();
                    $swatchObj->customRequest(array('value' => $option_id, 'imgData' => $imagename, 'imagetype' => $imagetype, 'swatchWidth' => 45, 'swatchHeight' => 45, 'base64Data' => base64_decode($imagename)));
                    $saveSucss = $swatchObj->saveColorSwatch('add');
                    $rsultrsponse['swatchImage'] = $saveSucss['swatchImage'];
                    $rsultrsponse['hexCode'] = $saveSucss['hexCode'];
                }
                $result = json_encode($rsultrsponse);

            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
                $error = true;
            }
            if (!$error) {
                $this->response($result, 200);
            } else {
                $this->response(json_decode($result), 200);
            }
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
            $this->response($this->json($msg), 200);
        }
    }
}