<?php
/* Check Un-authorize Access */
if (!defined('accessUser')) {
    die("Error");
}

class User extends UTIL
{

    public function userAccess()
    {
        try {
            if (isset($this->_request['printId']) && ($this->_request['printId']) != '') {
                $catagoryArray = array();
                $sql = "SELECT dc.id,dc.category_name FROM " . TABLE_PREFIX . "des_cat dc join " . TABLE_PREFIX . "design_category_printmethod_rel dcppr
                 on dcppr.design_category_id =dc.id where dcppr.print_method_id='" . $this->_request['printId'] . "' ";
            } else {
                $sql = "SELECT id,category_name FROM " . TABLE_PREFIX . "des_cat";
            }
            $categoryDetail = array();
            $rows = $this->executeGenericDQLQuery($sql);
            for ($i = 0; $i < sizeof($rows); $i++) {
                $categoryDetail[$i]['id'] = $rows[$i]['id'];
                $categoryDetail[$i]['category_name'] = $rows[$i]['category_name'];
            }
            $this->response($this->json($categoryDetail), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *To check user authontication by email and password
     *
     *@param (String)apikey
     *@param (String)email
     *@param (String)password
     *@return json data
     *
     */
    public function userAuthentication()
    {
		session_start();
		$random = substr(sha1(rand()), 0, 15);
		$timestamp = time();
        $products = Flight::products();
        if (!empty($this->_request) && $this->_request['apikey'] && $this->isValidCall($this->_request['apikey'])) {
            try {
                $errorMsg = '';
                extract($this->_request);
                if ($email == '') {
                    $errorMsg = 'Please enter your email';
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errorMsg = 'Please enter valid email';
                } elseif ($password == '') {
                    $errorMsg = 'Please enter your password';
                }

                if ($errorMsg == '') {
                    $sql = "select id,email,userType,name from " . TABLE_PREFIX . "user where email='$email' AND password='" . md5($password) . "' LIMIT 1";
                    $numRows = $this->executeFetchAssocQuery($sql);
                    if (!empty($numRows)) {
                        $length = 10;
                        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        $charactersLength = strlen($characters);
                        $randomString = '';
                        for ($i = 0; $i < $length; $i++) {
                            $randomString .= $characters[rand(0, $charactersLength - 1)];
                        }

                        $tempArray = array();
                        $tempArray['tool_status'] = $products->checkDesignerTool(1);
                        $tempArray['products'] = base64_encode('SUCCESS@' . $randomString);
                        $tempArray['designs'] = base64_encode($numRows[0]['email']);
                        $tempArray['type'] = $numRows[0]['userType'];
                        $tempArray['name'] = $numRows[0]['name'];
                        $tempArray['id'] = $numRows[0]['id'];
                        $user_id = $numRows[0]['id'];
                        $result = array();
                        $sql_module = "select privilege from " . TABLE_PREFIX . "user_privileges up," . TABLE_PREFIX . "user_privilege_rel upr where up.id =upr.p_id and upr.u_id = '" . $user_id . "'";
                        $rows = $this->executeFetchAssocQuery($sql_module);
                        if (!empty($rows)) {
                            foreach ($rows as $v) {
                                $result[] = $v['privilege'];
                            }
                        }
                        $tempArray['moduleAllow'] = $result;
						$encodeString = $random."#~_".$user_id."#~_".$timestamp."#~_".$numRows[0]['userType'];
						$xorString = base64_encode($this->xorIt($encodeString, 's9k7a8l4j'));						
						if (isset($this->_request['orderapp']) && $this->_request['orderapp'] == 1) {
							$updateUser = "UPDATE " . TABLE_PREFIX . "user SET token = '" . $xorString . "' WHERE id='" . $user_id . "'";
							$status = $this->executeGenericDMLQuery($updateUser);
							if ($status) {
								$tempArray['token'] = $xorString;
							}
						} else {
							$_SESSION['user'] = $xorString;
						}
                        $msg = array("status" => $tempArray);
                    } else {
                        $msg = array("status" => 'Email or Password is incorrect');
                    }
                } else {
                    $msg = array("status" => $errorMsg);
                }

                $this->closeConnection();
                $this->response($this->json($msg), 200);
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        } else {
            $msg = array("status" => "Invalid");
            $this->response($this->json($msg), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Check valid user
     *
     *@param (String)apikey
     *@param (String)email
     *@param (String)license_key
     *@return json data
     *
     */
    public function validateUser()
    {
        try {
            $email = $this->_request['email'];
            $apiKey = $this->_request['license_key'];
            /* $security_question = $this->_request['security_question'];
            $security_answer = $this->_request['security_answer']; */
            $result = array();
            if ($this->isValidCall($apiKey) && $this->validEmail($email)) // && $this->validSecurity($security_question,$security_answer))
            {
                $sql = "SELECT question from " . TABLE_PREFIX . "user where email='" . $email . "'";
                $rows = $this->executeFetchAssocQuery($sql);
                $result['status'] = 'success';
                $result['question'] = $rows[0]['question'];
            } else {
                $result['status'] = 'failed';
            }
            $this->closeConnection();
            $this->response($this->json($result), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Check valid Security
     *
     *@param (String)apikey
     *@param (String)security_question
     *@param (String)security_answer
     *@param (String)email
     *@return json data
     *
     */
    public function validSecurity()
    {
        try {
            $security_question = addslashes($this->_request['security_question']);
            $security_answer = addslashes($this->_request['security_answer']);
            $email = $this->_request['email'];
            $flag = false;
            $sql = "SELECT * from " . TABLE_PREFIX . "user where email='" . $email . "' AND question='" . $security_question . "' AND BINARY answer='" . $security_answer . "'";
            $res = $this->executeFetchAssocQuery($sql);
            if (!empty($res)) {
                $result['status'] = 'success';
            } else {
                $result['status'] = 'failed';
            }
            $this->closeConnection();
            $this->response($this->json($result), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Update pasword
     *
     *@param (String)apikey
     *@param (String)email
     *@param (String)password
     *@return json data
     *
     */
    public function updatePassword()
    {
        try {
            $email = $this->_request['email'];
            $password = $this->_request['password'];
            $sql = "UPDATE " . TABLE_PREFIX . "user SET password = '" . md5($password) . "' WHERE email='" . $email . "'";
            $status = $this->executeGenericDMLQuery($sql);
            if ($status) {
                $this->response($this->json(array('status' => 'success')), 200);
            } else {
                $this->response($this->json(array('status' => 'failed')), 200);
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
     *Check valid Email
     *
     *@param (String)apikey
     *@param (String)email
     *@return json data
     *
     */
    public function validEmail($email)
    {
        $flag = false;
        try {
            $sql = $this->executeGenericCountQuery("SELECT email from " . TABLE_PREFIX . "user where email='" . $email . "'");
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
        if (!empty($sql)) {
            $flag = true;
        }
        return $flag;
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Fetch All users
     *
     * @param (String)apikey
     * @return json data
     *
     */
    public function getUsers()
    {
        try {
            $query = "SELECT id,name,email FROM " . TABLE_PREFIX . "user where userType='0'";
            $rows = $this->executeFetchAssocQuery($query);
            $result = array();
            if (!empty($rows)) {
                foreach ($rows as $k => $v) {
                    $result[$k]['id'] = $v['id'];
                    $result[$k]['name'] = $v['name'];
                    $result[$k]['email'] = $v['email'];
                }
            } else {
                $result['status'] = 'nodata';
            }

        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
        }
        $this->response($this->json($result), 200);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Get All users privileges
     *
     * @param (String)apikey
     * @return json data
     *
     */
    public function getPrivileges()
    {
        try {
            $sql = "select * from " . TABLE_PREFIX . "user_privileges";
            $rows = $this->executeFetchAssocQuery($sql);
            $previlege = array();
            if (!empty($rows)) {
                foreach ($rows as $k => $v) {
                    $previlege[$k]['id'] = $v['id'];
                    $previlege[$k]['name'] = $v['privilege'];
                }
            }
        } catch (Exception $e) {
            $previlege = array('Caught exception:' => $e->getMessage());
        }
        $this->response($this->json($previlege), 200);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Remove users by user id
     *
     * @param (String)apikey
     * @param (int)user_id
     * @return json data
     *
     */
    public function deleteUser()
    {
        $user_id = $this->_request['user_id'];
        $status = 0;
        try {
            $sqld = "DELETE FROM " . TABLE_PREFIX . "user WHERE id='" . $user_id . "'";
            $this->executeGenericDMLQuery($sqld);
            $sql = "DELETE FROM " . TABLE_PREFIX . "user_privilege_rel WHERE u_id='" . $user_id . "'";
            $status = $this->executeGenericDMLQuery($sql);
            if ($status) {
                $this->getUsers();
            } else {
                $msg['status'] = 'failed';
            }

        } catch (Exception $e) {
            $msg = array('Caught exception:' => $e->getMessage());
        }
        $this->response($this->json($msg), 200);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Add user privilege
     *
     * @param (String)apikey
     * @param (String)name
     * @param (String)password
     * @return json data
     *
     */
    public function addUser()
    {
        $name = $this->_request['name'];
        $email = $this->_request['email'];
        $password = $this->_request['password'];
        $status = 0;
        try {
            $sql = "insert into " . TABLE_PREFIX . "user(name,email,password) values('" . $name . "','" . $email . "','" . md5($password) . "')";
            $user_id = $this->executeGenericInsertQuery($sql);
            $new_priv_id = $this->_request['privilegeId'];
            if (!empty($new_priv_id)) {
                foreach ($new_priv_id as $privileges) {
                    $sql_privileges = "INSERT INTO " . TABLE_PREFIX . "user_privilege_rel(u_id, p_id) VALUES ('" . $user_id . "', '" . $privileges . "')";
                    $status = $this->executeGenericDMLQuery($sql_privileges);
                }
            }
            if ($status) {
                $this->getUsers();
            } else {
                $msg['status'] = 'failed';
            }

        } catch (Exception $e) {
            $msg = array('Caught exception:' => $e->getMessage());
        }
        $this->response($this->json($msg), 200);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Update user privilege by user id
     *
     * @param (String)apikey
     * @param (String)privileges
     * @param (int)user_id
     * @return json data
     *
     */
    public function editUser_privileges()
    {
        $status = 0;
        $user_id = $this->_request['user_id'];
        $new_priv_id = $this->_request['privileges'];
        $delete_sql = "DELETE from " . TABLE_PREFIX . "user_privilege_rel WHERE u_id = '" . $user_id . "'";
        $status = $this->executeGenericDMLQuery($delete_sql);
        if ($status) {
            if (!empty($this->_request)) {
                extract($this->_request);
                try {
                    $sql_fetch = "select password from " . TABLE_PREFIX . "user WHERE id =$user_id";
                    $row = $this->executeGenericDQLQuery($sql_fetch);
                    if ($password == $row[0]['password']) {
                        $sql_user = "UPDATE " . TABLE_PREFIX . "user SET name = '" . $name . "',email='" . $email . "' WHERE id =$user_id";
                        $status = $this->executeGenericDMLQuery($sql_user);
                    } else {
                        $sql_new = "UPDATE " . TABLE_PREFIX . "user SET name = '" . $name . "',password='" . md5($password) . "',email='" . $email . "' WHERE id =$user_id";
                        $status = $this->executeGenericDMLQuery($sql_new);
                    }
                    if ($status) {
                        foreach ($new_priv_id as $privileges) {
                            $sql = "INSERT INTO " . TABLE_PREFIX . "user_privilege_rel(u_id, p_id) VALUES ('" . $user_id . "', '" . $privileges . "')";
                            $status = $this->executeGenericDMLQuery($sql);
                        }
                    }
                    if ($status) {
                        $this->getUsers();
                    } else {
                        $msg['status'] = 'failed';
                    }

                } catch (Exception $e) {
                    $msg = array('Caught exception:' => $e->getMessage());
                }
                $this->response($this->json($msg), 200);
            }
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Get users privileges by user id
     *
     * @param (String)apikey
     * @param (int)user_id
     * @return json data
     *
     */
    public function getUser_privileges()
    {
        $user_id = $this->_request['user_id'];
        $result = array();
        try {
            $user_sql = "SELECT name,email,password FROM " . TABLE_PREFIX . "user WHERE id='" . $user_id . "'";
            $row = $this->executeGenericDQLQuery($user_sql);
            $result['name'] = $row[0]['name'];
            $result['email'] = $row[0]['email'];
            $result['password'] = $row[0]['password'];
            $sql = "select p_id from " . TABLE_PREFIX . "user_privilege_rel where u_id = '" . $user_id . "'";
            $rows = $this->executeFetchAssocQuery($sql);
            if (!empty($rows)) {
                foreach ($rows as $v) {
                    $result['p_id'][] = $v['p_id'];
                }
            }
            $result_arr = array();
            $result_arr['user_details'] = $result;
        } catch (Exception $e) {
            $result_arr = array('Caught exception:' => $e->getMessage());
        }
        $this->response($this->json($result_arr), 200);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Get security questions
     *
     *@param (String)apikey
     *@return json data
     *
     */
    public function getSecurityQuestions()
    {
        try {
            $sql = "SELECT id, name FROM " . TABLE_PREFIX . "security_questions";
            $rows = $this->executeFetchAssocQuery($sql);
            $security = array();
            if (!empty($rows)) {
                foreach ($rows as $k => $v) {
                    $security[$k]['id'] = $v['id'];
                    $security[$k]['name'] = $v['name'];
                }
            }
            $this->closeConnection();
            $this->response($this->json($security), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Forgot password
     *
     *@param (String)apikey
     *@param (String)email
     *@param (String)hostURL
     *@param (String)resetPasswordURL
     *@return json data
     *
     */
    public function forgotPassword()
    {
        $apiKey = $this->_request['apikey'];
        if ($this->isValidCall($apiKey)) {
            try {
                $errorMsg = '';
                $email = $this->_request['email'];
                $hostURL = $this->_request['hostURL'];
                $resetPasswordURL = $this->_request['resetPasswordURL'];
                if ($email == '') {
                    $errorMsg = 'Please enter your email';
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errorMsg = 'Please enter valid email';
                }

                if ($errorMsg == '') {
                    $sql = "select * from " . TABLE_PREFIX . "user where email = '$email'";
                    $numRows = $this->executeGenericCountQuery($sql);
                    if ($numRows == 1) {
                        $currentTime = time();
                        $sql = "update " . TABLE_PREFIX . "user set resetPasswordKey = '$currentTime' where email = '$email'";
                        $resetKeyQueryResult = $this->executeGenericDMLQuery($sql);
                        $resetPasswordURL = $resetPasswordURL . $currentTime;

                        $toMail = $email;
                        $fromMail = '';
                        $subjectMail = "Reset Password at the Product Designer Admin Tool";
                        $txtMail = "<html><body>This email is in response to a request to reset the password for the Admin at <a href='" . $hostURL . "'>" . $hostURL . "</a><br><br>
                        If you wish to reset your password please either click on the link below or copy and <br>
                        paste it into your browser to be directed to the secure password reset form:<br><a href='" . $resetPasswordURL . "'>" .
                            $resetPasswordURL . "</a><br><br>

                        If it is not your intention to change your password, please simply ignore this message.</body></html>";
                        //echo $txtMail;
                        $headersMail = "From:" . $fromMail;
                        $headersMail .= "MIME-Version: 1.0\r\n";
                        $headersMail .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                        if (mail($toMail, $subjectMail, $txtMail, $headersMail)) {
                            $msg = array("status" => 'An email is sent to you to reset password');
                        } else {
                            //echo 'false';
                        }
                    } else {
                        $msg = array("status" => 'Email not found in our records');
                    }
                } else {
                    $msg = array("status" => $errorMsg);
                }
                $this->closeConnection();
                $this->response($this->json($msg), 200);
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        } else {
            $msg = array("status" => "Invalid");
            $this->response($this->json($msg), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Change password
     *
     *@param (String)apikey
     *@param (String)email
     *@param (String)newPassword
     *@param (String)confirmPassword
     *@param (String)resetPassKey
     *@return json data
     *
     */
    public function changePassword()
    {
        $apiKey = $this->_request['apikey'];
        if ($this->isValidCall($apiKey)) {
            try {
                $errorMsg = '';
                $email = $this->_request['email'];
                $newPassword = $this->_request['newPassword'];
                $confirmPassword = $this->_request['confirmPassword'];
                $resetPassKey = $this->_request['resetPassKey'];
                if ($email == '') {
                    $errorMsg = 'Please enter your email';
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errorMsg = 'Please enter valid email';
                }

                if ($errorMsg == '') {
                    $sql = "select email from " . TABLE_PREFIX . "user where email = '$email'";
                    $verifyEmailResult = mysqli_query($this->db, $sql);
                    $verifyEmailRows = mysqli_num_rows($verifyEmailResult);
                    $sql = "select * from " . TABLE_PREFIX . "user where email = '$email' &&  resetPasswordKey = '$resetPassKey'";
                    $verifyKeyResult = mysqli_query($this->db, $sql);
                    $verifyKeyRows = mysqli_num_rows($verifyKeyResult);

                    if ($verifyEmailRows != 1) {
                        $errorMsg = 'Email not found in our records';
                    } else if ($newPassword == '') {
                        $errorMsg = 'Enter New Password';
                    } else if ($confirmPassword == '') {
                        $errorMsg = 'Confirm Password';
                    } else if ($confirmPassword != $newPassword) {
                        $errorMsg = 'Both Passwords are not same';
                    } else if ($verifyKeyRows != 1) {
                        $errorMsg = 'To reset go to forgot password section';
                    }

                }
                if ($errorMsg == '') {
                    $encodedPassword = md5($newPassword);
                    $sql = "update " . TABLE_PREFIX . "user set password = '$encodedPassword' where email = '$email' &&  resetPasswordKey = '$resetPassKey'";
                    $chngPassQueryResult = $this->executeGenericDMLQuery($sql);
                    $sql = "update " . TABLE_PREFIX . "user set resetPasswordKey = '' where email = '$email'";
                    $changeKeyQueryResult = $this->executeGenericDMLQuery($sql);
                    $msg = array("status" => 'Password successfully reset.');
                } else {
                    $msg = array("status" => $errorMsg);
                }
                $this->closeConnection();
                $this->response($this->json($msg), 200);
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        } else {
            $msg = array("status" => "Invalid");
            $this->response($this->json($msg), 200);
        }
    }
	
	/**
	*
	*date created (dd-mm-yy)
	*date modified 15-4-2016 (dd-mm-yy)
	*Logout User
	*
	*@param none
	*@return json status
	*
	*/
	public function userLogout(){		
		$token = isset($this->_request['token'])?$this->_request['token']:'';		
		if($token!='') {
			$updateUser = "UPDATE " . TABLE_PREFIX . "user SET token = '' WHERE token='" . $token . "'";
			$status = $this->executeGenericDMLQuery($updateUser);
		} else {
			session_start();
			unset($_SESSION['user']);
		}
		$this->response($this->json(array("status" => "success")), 200);
	}
}
