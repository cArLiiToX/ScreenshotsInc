<?php
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
    const HTML5_THEME_DIR = "../../designer-app/"; // THIS IS THE CSS-FILE PATH FOR THEME COLOR IMPORTANT
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
     * Check soap connection to magento
     *
     * @param   nothing
     * @return  true/ false with error message
     */
    public function storeApiLogin()
    {
        /*$this->storeApiLogin = false;
        $result='';
        $data = array("username" => APIUSER, "password" => APIPASS);
        $data_string = json_encode($data);
        try{
        $ch = curl_init(APIURL);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string))
        );
        $token = curl_exec($ch);
        $token = json_decode($token);
        if(isset($token->message)){
        $result = json_encode(array('isFault' => 1, 'loginFaultMessage'=>'Authentication Failed: '.$token->message));
        }else{
        $GLOBALS['params']['apisessId'] = html_session_set('apisessId',$token);
        $key = $GLOBALS['params']['apisessId'];*/
        $this->storeApiLogin = true;
        /*}
        } catch(Exception $e) {
        $result = json_encode(array('isFault' => 1, 'initFaultMessage'=>$e->getMessage()));
        }*/
        return '57567567567567fghgf565'; //$result;
    }

    public function apiCall($model, $service, $param)
    {
        $path = $_SERVER['DOCUMENT_ROOT'] . '/' . TOOL_CONTAINER_DIR . '/';
        require_once $path . 'vendor/zendframework/zend-server/src/Client.php';
        require_once $path . 'vendor/zendframework/zend-soap/src/Client.php';
        require_once $path . 'vendor/zendframework/zend-soap/src/Client/Common.php';

        $url = 'html5designCedapi' . $model . 'V1';
        $wsdlUrl = XEPATH . 'soap?wsdl&services=' . $url;
        $callUrl = $url . ucfirst($service);
        $opts = ['http' => ['header' => "Authorization: Bearer " . ACCESSTOKEN]];

        try {
            $context = stream_context_create($opts);
            $soapClient = new \Zend\Soap\Client($wsdlUrl);
            $soapClient->setSoapVersion(SOAP_1_2);
            $soapClient->setStreamContext($context);

            return $soapResponse = $soapClient->$callUrl($param);
        } catch (Exception $e) {
            echo 'Error1 : ' . $e->getMessage();
        }
    }

    /**
     * Check whether xetool is enabled or disabled
     *
     * @param   nothing
     * @return  true/false
     */
    public function checkDesignerTool($t = 0)
    {
        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];
            $filters = array(
                'store' => $this->getDefaultStoreId(),
            );
            try {
                $result = $this->apiCall('Product', 'checkDesignerTool', $filters);
                $result = $result->result;

            } catch (Exception $e) {
                $result = json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
            }
            if ($t) {
                return $result;
            } else {
                print_r($result);
            }

        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
            $this->response($this->json($msg), 200);
        }
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

                if ($uid != "" || $uid != '0') {
                    $sql = "Select image,thumbnail,type,customer_id,uid from  " . TABLE_PREFIX . "image_data where uid='" . $uid . "'";
                }

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
        $arr = json_decode($data, $returnArr);
        return $arr;
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 02-09-2017 (dd-mm-yy)
     *get Format Json Array
     *
     *@return boolean
     */
    public function checkSendQuote()
    {
        //if(APPNAME != '')$url = $this->getCurrentUrl().'/designer-tool/localsettings.js';
        $url = $this->getCurrentUrl() . '/designer-tool/localsettings.js';
        $tarray = array(" ", "\n", "\r");
        $contents = $this->getFileContents($url);
        $contents = trim(str_replace($tarray, "", $contents));
        $contents = substr($contents, 0, -1);
        $contents = explode("localSettings=", $contents);
        $contents = json_decode($contents['1'], true);
        $isQuote = $contents['is_send_a_quote'];
        return $isQuote;
    }
}
