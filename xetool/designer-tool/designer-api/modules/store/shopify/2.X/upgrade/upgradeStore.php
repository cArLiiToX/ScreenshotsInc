<?php
/* Check Un-authorize Access */
if (!defined('accessUser')) {
    die("Error");
}

class UpgradeStore extends UTIL
{
    /**
     * Check Shopify version
     *
     * @param   nothing
     * @return  string $version
     */
    public function storeVersion()
    {
        $result = $this->storeApiLogin();
        if ($this->storeApiLogin == true) {
            $key = $GLOBALS['params']['apisessId'];
            $result = "2.0";
            return $version = (!empty($result)) ? strchr($result, '.', true) : 1;
        } else {
            $msg = array('status' => 'apiLoginFailed', 'error' => json_decode($result));
            $this->response($this->json($msg), 200);
        }
    }
    /**
     *
     * @Purpose: fetch refIds of all the quotes which are not abandoned
     * @return array of refId objects
     *
     */

}
