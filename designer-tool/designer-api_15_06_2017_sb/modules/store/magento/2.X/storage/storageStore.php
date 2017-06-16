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
        return array();
    }

}
