<?php
header('Access-Control-Allow-Origin: *');

	require_once("Rest.inc.php");
	
	class API extends REST {
	
		public $data = "";
		
		const DB_SERVER = "localhost";
		const DB_USER = "magentod_tshirtv";
		const DB_PASSWORD = "MvCtG]xQQ&93";
		const DB = "magentod_html5tshirtappv";
		
                const TABLE_CLIENT_INFO = "client";
                const TABLE_FEATURE = "features";
                const TABLE_PLANS = "plans";
                const TABLE_LOG = "licence_log";
                const TABLE_CLIENT_PLAN = " r_client_plan";
                const TABLE_FEATURE_CLIENT = "r_feature_client";
                const TABLE_FEATURE_PLAN = "r_feature_plan";
                
		private $db = NULL;
	
		public function __construct(){
			parent::__construct();
			$this->dbConnect();
		}
		
		private function dbConnect(){
			$this->db = mysql_connect(self::DB_SERVER,self::DB_USER,self::DB_PASSWORD);
			if($this->db)
                            mysql_select_db(self::DB, $this->db);
		}
		
		public function processApi(){
			$func = strtolower(trim(str_replace("/","",$_REQUEST['reqmethod'])));
                      // echo $func;
			//if(function_exists($func))
			if(method_exists($this, $func))
			{
                            $this->$func();
                        }
			else
				$this->response('',406);
		}
		
		private function isValidCall($apiKey)
		{
			$flag=false;
			$apiKey = mysql_real_escape_string($apiKey);
			
			$sql="SELECT api_key  FROM ".self::TABLE_API_DATA." WHERE api_key ='$apiKey' ";
			$result = mysql_query($sql, $this->db);
			if(mysql_num_rows($result) > 0)
                        {
                            $rows =  mysql_fetch_array($result,MYSQL_ASSOC);
                            $apiKeyDB=$rows['api_key'];
                            $flag =true;
			}
			return $flag;
		}
///////////////////////////////////////////////////////////////////////////////

    public function savedProductHistoryList()
    {
      /*if($this->get_request_method() != "POST"){
        $this->response('',406);
      }*/
     // $this->response('$msg', 200);
	 	$str= $this->_request['str'];	 //error_log("str=".$str, 3, "D:/errors.log");	
		$localUrl= $this->_request['date'];
		$encodedData = str_replace(' ','+',$str);
		$decocedData = base64_decode($encodedData);	
		$encodedlocalUrl = str_replace(' ','+',$localUrl);
		$decocedlocalUrl = base64_decode($encodedlocalUrl);
		$get_localUrl = explode("...",$decocedlocalUrl);
		//$xorData=xor_this($decocedData,"saHf3#2x-99Y!=4456sd");
		$get_data = explode("...",$decocedData);
		$decrypt_data = explode(":",$get_data[0]);
		$decrypt_data = explode("'}",$decrypt_data[1]);
		$decrypt_data = explode("'",$decrypt_data[0]);
		$arr = explode("|",$decrypt_data[1]);
		$hosturl = $arr[0];
		$expiry = date("Y-m-d",strtotime($arr[1]));
		$features = explode(".",$arr[2]);
		$licensekey=$arr[3];
		$url = rawUrlEncode("http://www.riaxe.com/?InvalidLicense=1");
		$time = date("Y-m-d h:m:s",time());
		if(strpos($hosturl, $get_localUrl[0]) !== FALSE)
		{
			$sql="Select * from ".self::TABLE_CLIENT_INFO;
			$exe = mysql_query($sql, $this->db);
			$res = mysql_fetch_array($exe,MYSQL_ASSOC);
			if(mysql_num_rows($exe)>0)
			{
				$clientId = $res['clientId'];
				$licenceKey = $res['licenceKey'];
				$domainName = $res['domainName'];
				if($res['licenceKey']==$licensekey && $res['expiryDate']>=$expiry)
				{
					//echo base64_encode(json_encode('success...location.href..."'.$url.'"'));
					$sql="INSERT INTO ".self::TABLE_LOG." (clientId, domainName, licenceKey, logDate, status) VALUES ('".$res['clientId']."', '".$res['domainName']."', '".$res['licenceKey']."', '$time', 'success')";
					$status = mysql_query($sql,$this->db);
					$msg = base64_encode(json_encode('success...location.href..."'.$url.'"'));
					$this->response($msg, 200);
				}
				else if($res['licenceKey']==$licensekey && $res['expiryDate']<$expiry)
				{
					//echo base64_encode(json_encode('Licence Expired!...location.href..."'.$url.'"'));
					$sql="INSERT INTO ".self::TABLE_LOG." (clientId, domainName, licenceKey, logDate, status) VALUES ('".$res['clientId']."', '".$res['domainName']."', '".$res['licenceKey']."', '$time', 'failed')";
					$status = mysql_query($sql,$this->db);
					$msg = base64_encode(json_encode('Licence Expired!...location.href..."'.$url.'"'));
					$this->response($msg, 200);
				}
				else if( $res['licenceKey']!=$licensekey && $res['expiryDate']>=$expiry)
				{
					//echo base64_encode(json_encode('404 Error...location.href..."'.$url.'"'));
					$sql="INSERT INTO ".self::TABLE_LOG." (clientId, domainName, licenceKey, logDate, status) VALUES ('".$res['clientId']."', '".$res['domainName']."', '".$res['licenceKey']."', '$time', 'failed')";
					$status = mysql_query($sql,$this->db);
					$msg = base64_encode(json_encode('404 Error...location.href..."'.$url.'"'));
					$this->response($msg, 200);
				}
			}
			else
			{
				//echo base64_encode(json_encode('XML_ERROR_SYNTAX...location.href..."'.$url.'"'));
				$sql="INSERT INTO ".self::TABLE_LOG." (clientId, domainName, licenceKey, logDate, status) VALUES ('', '$hosturl', '$licensekey', '$time', 'failed')";
				$status = mysql_query($sql,$this->db);
				$msg = base64_encode(json_encode('XML_ERROR_SYNTAX...location.href..."'.$url.'"'));
				$this->response($msg, 200);
			}			
		}
		else
		{
			//echo base64_encode(json_encode('CODE MALLFUNCTION...location.href..."'.$url.'"'));
			$sql="INSERT INTO ".self::TABLE_LOG." (clientId, domainName, licenceKey, logDate, status) VALUES ('', '$hosturl', '$licensekey', '$time', 'failed')";
			$status = mysql_query($sql,$this->db);
			$msg = base64_encode(json_encode('CODE MALLFUNCTION...location.href..."'.$url.'"'));
			$this->response($msg, 200);
		}

    }

    public function saveProductHistory()
    {
      /* if($this->get_request_method() != "POST"){
        $this->response('',406);
      } */
		$str= $this->_request['str'];	 //error_log("str=".$str, 3, "D:/errors.log");	
		$localUrl= $this->_request['date'];
		$encodedData = str_replace(' ','+',$str);
		$decocedData = base64_decode($encodedData);	
		$encodedlocalUrl = str_replace(' ','+',$localUrl);
		$decocedlocalUrl = base64_decode($encodedlocalUrl);
		$get_localUrl = explode("...",$decocedData);
		//$xorData=xor_this($decocedData,"saHf3#2x-99Y!=4456sd");
		$get_data = explode("...",$decocedData);
		$decrypt_data = explode(":",$get_data[0]);
		$decrypt_data = explode("'}",$decrypt_data[1]);
		$decrypt_data = explode("'",$decrypt_data[0]);
		$arr = explode("|",$decrypt_data[1]);
		$hosturl = $arr[0];
		$expiry = date("Y-m-d",strtotime($arr[1]));
		$features = explode(".",$arr[2]);
		$licensekey=$arr[3];
		$today = date("Y-m-d", strtotime('now'));
		 $sql="INSERT INTO ".self::TABLE_CLIENT_INFO." (domainName, licenceKey, planType, startDate, expiryDate) VALUES ('$hosturl', '$licensekey', 'basic', '$today', '$expiry')";
		$status = mysql_query($sql,$this->db);
		if($status)
		{
			$id = mysql_insert_id($this->db);
			foreach($features as $feature)
			{
				$sql="Select * from ".self::TABLE_FEATURE."where featureName=".$feature;
				$exe = mysql_query($sql, $this->db);
				$res = mysql_fetch_array($result,MYSQL_ASSOC);
				if(mysqli_num_rows($exe)>0)
				{	
					$featureId = $res['featureId'];
					$sql="INSERT INTO ".self::TABLE_FEATURE_CLIENT." (clientId, featureId) VALUES ('$id', '$featureId')";
					$status = mysql_query($sql,$this->db);
				}
			}
			$msg = array("status"=>"success");
		    $this->response($this->json($msg), 200);
		}
		else
		{
			$msg=array("status"=>"failed");
			$this->response($this->json($msg), 200);
		}        
	}
}
	$api = new API;
	$api->processApi();
	
	
	
	
?>