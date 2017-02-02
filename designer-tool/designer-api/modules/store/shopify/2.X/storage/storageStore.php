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
        $refIdsArr = array();
        /*$result = $this->storeApiLogin();
        if(!empty($this->_request) && $this->storeApiLogin==true){
        $key = $GLOBALS['params']['apisessId'];

        $filters = array(
        'store'=>$this->getDefaultStoreId()
        );
        try {
        $quotes    = $this->proxy->call($key, 'cedapi_product.getLiveQuoteRefIds', $filters);
        if(!empty($quotes)){
        foreach($quotes['refIds'] as $quote){
        $refIdsArr['refIdCartArr'][] = $quote['refId'];
        }
        $refIdsArr['duration'] = $quotes['duration'];
        }
        } catch (Exception $e) {
        return $refIdsArr;
        }
        }*/
        return $refIdsArr;
    }

}
