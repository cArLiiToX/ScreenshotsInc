<?php
error_reporting(E_ALL & ~E_NOTICE);
require_once 'modules/store/woocommerce/1.X/library/class-wc-api-client.php';
require_once dirname(__FILE__) . '/../../../../../../../wp-blog-header.php';

class ComponentStore
{
    public $data = "";

    //Paths
    const ROOT_PATH = "/../"; //THIS INDICATES PATH TO THE ROOT(or BASEPATH)-DIRECTORY WHICH CONTAINS ALL API, ASSETS, APP & ADMIN etc; refer getBasePath() IMPORTANT
    const ASSETS_CONTAINER_DIR = "/designer-tool"; //"/product_designer/"// THIS IS THE PARENT DIRECTORY OF 'assets' DIRECTORY IMPORTANT
    const TOOL_CONTAINER_DIR = TOOL_CONTAINER_DIR; //"/magento";

    const TEMPLATE_IMAGE_DIR = TEMPLATE_IMAGE_DIR;
    const USER_IMAGE_DIR = USER_IMAGE_DIR;
    const PREVIEW_IMAGE_DIR = PREVIEW_IMAGE_DIR;
    const HTML5_DESIGN_DIR = HTML5_DESIGN_DIR;
    const HTML5_PRINT_METHOD_DIR = HTML5_PRINT_METHOD_DIR;
    const HTML5_SHAPE_DIR = HTML5_SHAPE_DIR;
    const HTML5_DISTRESS_DIR = HTML5_DISTRESS_DIR;
    const HTML5_PALETTE_DIR = HTML5_PALETTE_DIR;
    const HTML5_WORDCLOUD_DIR = HTML5_WORDCLOUD_DIR;
    const HTML5_TEXTFX_DIR = HTML5_TEXTFX_DIR;
    const HTML5_WEBFONTS_DIR = HTML5_WEBFONTS_DIR;
    const HTML5_THEME_DIR = "../designer-app/"; // THIS IS THE CSS-FILE PATH FOR THEME COLOR IMPORTANT
    const HTML5_USERSLOTS_DIR = HTML5_USERSLOTS_DIR;
    const HTML5_MASK_IMAGE_DIR = HTML5_MASK_IMAGE_DIR;
    const HTML5_TEXTONPATH_DIR = HTML5_TEXTONPATH_DIR;
    const ORDER_PATH_DIR = ORDER_PATH_DIR;
    const SWATCH_PATH_DIR = SWATCH_PATH_DIR;
    const LANGUAGE_DIR = LANGUAGE_DIR;
    const HTML5_PRODUCTTEMPLATE_DIR = HTML5_PRODUCTTEMPLATE_DIR;
    const CAPTURED_IMAGE_DIR = CAPTURED_IMAGE_DIR;
    const USER_WORD_CLOUD_SVG = USER_WORD_CLOUD_SVG;
    const HTML5_DESIGN_BACKGROUND_DIR = HTML5_DESIGN_BACKGROUND_DIR;
    const CLEAR_ZIP_DURATION = 1; //1 Day
    const CLEAR_USERSLOT_DURATION = 2; //In Day(s)
    const HTML5_BACKGROUND_PATTERN_DIR = HTML5_BACKGROUND_PATTERN_DIR;
    const HTML5_MULTIPLE_BOUNDARY_DIR = HTML5_MULTIPLE_BOUNDARY_DIR;
	const ADMIN_LANGUAGE_DIR = ADMIN_LANGUAGE_DIR;

    /**
     * check store shop user login
     *
     * @param   nothing
     * @return  boolean
     */
    public function storeApiLogin()
    {
        $this->storeApiLogin = true;
        return true;
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Get User Images
     *
     *@param (String)apikey
     *@param (Int)customerId
     *@param (Int)uid
     *@return json data
     *
     */
    public function getUserImages()
    {
        $apiKey = $this->_request['apikey'];
        $refid = 0;
        $customerId = 0;
        $uid = 0;
        $result = array();
        if (isset($this->_request['customerId'])) {
            $customerId = $this->_request['customerId'];
        }

        if (isset($this->_request['uid'])) {
            $uid = $this->_request['uid'];
        }

        $baseImageURL = $this->getUserImageURL();
        if ($this->isValidCall($apiKey)) {
            try {
                if ($refid && $refid > 0) {
                    $sql = "Select image,thumbnail,type,customer_id from  " . TABLE_PREFIX . "image_data where refid=" . $refid;
                }

                if($uid!="" || $uid!='0')
                    $sql = "Select image,thumbnail,type,customer_id,uid from  " . TABLE_PREFIX."image_data where uid='". $uid."'"; 
                if ($customerId && $customerId > 0) {
                    $sql = "Select image,thumbnail,type,customer_id,uid from  " . TABLE_PREFIX . "image_data where customer_id=" . $customerId;
                }

                if (!empty($sql)) {
                    $result = $this->executeGenericDQLQuery($sql);
                }

                if (!empty($result)) {
                    $images = array();
                    foreach ($result as $rows) {
                        $customerId = $rows['customer_id'];
                        $uid = $rows['uid'];
                        if ($customerId && $customerId > 0) {
                            $imageURL = $baseImageURL . $customerId . '/';
                        } else {
                            $imageURL = $baseImageURL . $uid . '/';
                        }

                        $data = array(
                            "filename" => $rows['image'],
                            "thumbnail" => $rows['thumbnail'],
                            "filepath" => $imageURL,
                            "type" => $rows['type'],
                        );
                        $images[] = $data;
                    }
                    $this->response($this->json($images), 200);
                } else {
                    $msg = array("status" => "nodata");
                    $this->response($this->json($msg), 200);
                }
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Fetch user slot
     *
     *@param (String)apikey
     *@param (Int)uid
     *@param (Int)userId
     *@return json data
     *
     */
    public function getUserSlotList()
    {
        $user_id = (isset($this->_request['userId'])) ? $this->_request['userId'] : 0;
        $uid = (isset($this->_request['uid'])) ? $this->_request['uid'] : 0;
        $result = array();
        try {
            if ($user_id && $user_id > 0) {
                $sql = "Select slot_id, user_id, json_data, status, slot_image, uid from " . TABLE_PREFIX . "user_slot where user_id=" . $user_id;
                //$sql .= ($user_id && $user_id>0)?"where user_id=".$user_id:"where uid='". $uid."'";
                $result = $this->executeFetchAssocQuery($sql);
            }
            $responseData = array();
            if (!empty($result)) {
                $slotBasePath = $this->getSlotsPreviewURL();
                foreach ($result as $rows) {
		    $CapturedImageUrl=$this->getCapturedImageUrl();
                    $imageURL = ($rows['user_id'] && $rows['user_id'] > 0) ? $slotBasePath . $rows['user_id'] . '/' . $rows['slot_image'] : $slotBasePath . $rows['uid'] . '/' . $rows['slot_image'];
					if (preg_match('|^http(s)?://|i', $this->formatJSONToArray($rows['json_data'], false)->captureSlot)) {
					    $responseData[] = array(
							"slotImage" => $rows['slot_image'],
							"slotImageUrl" => $imageURL,
							"slotId" => $rows['slot_id'],
							"userId" => $rows['user_id'],
							"status" => $rows['status'],
							"uid" => $rows['uid'],
							"captureSlot" => $this->formatJSONToArray($rows['json_data'], false)->captureSlot
						);
					} else {
						$responseData[] = array(
							"slotImage" => $rows['slot_image'],
							"slotImageUrl" => $imageURL,
							"slotId" => $rows['slot_id'],
							"userId" => $rows['user_id'],
							"status" => $rows['status'],
							"uid" => $rows['uid'],
							"captureSlot" => $CapturedImageUrl.$this->formatJSONToArray($rows['json_data'], false)->captureSlot
						);
					}	
		        $CapturedImageUrl=null;
                }
            } else {
                $responseData = array("status" => "nodata");
            }
            $this->response($this->json($responseData), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *get Format Json Array
     *
     *@param (String)apikey
     *@return json decode
     *
     */
    public function formatJSONToArray($data, $returnArr = true)
    {
        $arr = json_decode(stripslashes($data), $returnArr);
        return $arr;
    }
}
