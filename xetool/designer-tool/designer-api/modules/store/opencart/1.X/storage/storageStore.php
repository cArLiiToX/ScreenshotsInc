<?php
/* Check Un-authorize Access */
if (!defined('accessUser')) {
    die("Error");
}

class StorageStore extends UTIL
{

    /**
     *
     * @Purpose: fetch refIds of all the quotes which are not abandoned
     * @return array of refId objects
     *
     */
    public function getLiveQuoteRefIds()
    {
        //$res = array('refIdCartArr' => array(),'duration' => 2592000);//30 days in sec
        /* $result = $this->storeApiLogin();
        if(!empty($this->_request) && $result==true){
        $key = $GLOBALS['params']['apisessId'];

        $filters = array(
        'store'=>$this->getDefaultStoreId()
        );
        try {
        $quotes    = $this->proxy->call($key, 'cedapi_product.getLiveQuoteRefIds', $filters);
        if(!empty($quotes)){
        foreach($quotes as $quote){
        $refIdsArr[] = $quote['refId'];
        }
        }
        } catch (Exception $e) {
        //$result = json_encode(array('isFault: ' => 1, 'faultMessage'=>$e->getMessage()));
        return $refIdsArr;
        }
        } */
        return array();
    }

}
