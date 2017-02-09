<?php
/* Check Un-authorize Access */
if (!defined('accessUser')) {
    die("Error");
}

class Authentication extends UTIL
{
	public function __construct()
    {
        parent::__construct();
        session_start();
    }
    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Authenticate user 
     *
     *@param (String)token optional
     *@return boolean 0/1
     *
     */
    public function authenticateUser()
    {		
		$token = isset($this->_request['token'])?$this->_request['token']:'';
		if ($token=='') {
			if (isset($_SESSION['user']) && $_SESSION['user']!='') {
				$user = $this->xorIt(base64_decode($_SESSION['user']), 's9k7a8l4j', 1);
				$decodeString = explode("#~_", $user);
				$userId = $decodeString[1];
				$userSql = "SELECT name FROM " . TABLE_PREFIX . "user where id='$userId' LIMIT 1";
				$userToken = $this->executeFetchAssocQuery($userSql);
				if (!empty($userToken)) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			} 
		} else {
			$userSql = "SELECT id FROM " . TABLE_PREFIX . "user where token='$token' LIMIT 1";
			$userToken = $this->executeFetchAssocQuery($userSql);
			if (!empty($userToken)) {
				return true;
			} else{
				return false;
			}
		}
    } 
	/**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Authorize user
     *
     *@param (String)module
     *@param (String)token optional
     *@return boolean true/false
     *
     */
    public function authorizeUser($module)
    {
		$token = isset($this->_request['token'])?$this->_request['token']:'';
		if ($token!='') {
			$userSql = "SELECT id,userType FROM " . TABLE_PREFIX . "user where token='$token' LIMIT 1";
		} else {
			$user = $this->xorIt(base64_decode($_SESSION['user']), 's9k7a8l4j', 1);
			$decodeString = explode("#~_", $user);
			$userId = $decodeString[1];
			$userSql = "SELECT userType FROM " . TABLE_PREFIX . "user where id='$userId' LIMIT 1";
		}
		$userToken = $this->executeFetchAssocQuery($userSql);
		if (!empty($userToken))	{
			$type = $userToken[0]['userType'];
			if ($type==1) {
				return true;
			} else {
				$sqlModule = "select privilege from " . TABLE_PREFIX . "user_privileges up," . TABLE_PREFIX . "user_privilege_rel upr where up.id =upr.p_id and upr.u_id = '" . $userId . "'";
				$rows = $this->executeFetchAssocQuery($sqlModule);
				if (!empty($rows)) {
					foreach ($rows as $v) {						
						$result[] = ($v['privilege']=="Graphics")?"design":str_replace(" ","",lcfirst($v['privilege']));
					}
				}
				if (in_array($module,$result)) {
					return true;
				} else {
					return false;
				}
			}
		} else {
			return false;
		}
				
    }
}
