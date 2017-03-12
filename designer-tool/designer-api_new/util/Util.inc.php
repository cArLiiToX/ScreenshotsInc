<?php
class UTIL extends REST
{
    //Designer Tool DATABASE DETAILS
    const DB_SERVER = SERVER;
    const DB_USER = USER;
    const DB_PASSWORD = PASSWORD;
    const DB_NAME = DBNAME;
    const APIURL = APIURL;
    const APIUSER = APIUSER;
    const APIPASS = APIPASS;
    const TABLE_PREFIX = TABLE_PREFIX;

    public $db = null;
    public $proxy = null;
    public $storeApiLogin = false;

    public function __construct()
    {
        parent::__construct();
        if (DBNAME != '') {
            $this->dbConnect();
        }
    }

    ####################################################
    ############## syncOrdersZip ###################
    ####################################################
    public function dbConnect()
    {
        $db = mysqli_connect(self::DB_SERVER, self::DB_USER, self::DB_PASSWORD);
        if ($db) {
            mysqli_select_db($db, self::DB_NAME) or die('ERRROR:' . mysqli_error());
            $this->db = $db;
        }
    }

    ####################################################
    ############## syncOrdersZip ###################
    ####################################################
    public function executeFetchAssocQuery($query)
    {
        try {
            if (!$this->db) {
                $this->db = mysqli_connect(self::DB_SERVER, self::DB_USER, self::DB_PASSWORD);
            }

            mysqli_set_charset($this->db, 'utf8');
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            $result = mysqli_query($this->db, $query);
            $rows = array();
            if ($result) {
                while ($row = mysqli_fetch_assoc($result)) {
                    array_push($rows, $row);
                }
            }
            return $rows;
        } catch (Exception $e) {
            $response = array();
            $response['Caught exception'] = $e->getMessage();
            $this->response($this->json($response), 200);
        }
    }

    ####################################################
    ############## syncOrdersZip ###################
    ####################################################
    public function executeGenericDQLQuery($query)
    {
        try {
            if (!$this->db) {
                $this->db = mysqli_connect(self::DB_SERVER, self::DB_USER, self::DB_PASSWORD);
            }

            mysqli_set_charset($this->db, 'utf8');
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            $result = mysqli_query($this->db, $query);
            /* if(mysqli_errno($con) != 0){
            throw new Exception("Error   :".mysqli_errno($con)."   :  ".mysqli_error($con));
            } */
            $rows = array();
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_array($result)) {
                    array_push($rows, $row);
                }
                return $rows;
            } else {
                return array();
            }

        } catch (Exception $e) {
            $response = array();
            $response['Caught exception'] = $e->getMessage();
            $this->response($this->json($response), 200);
        }
    }

    ####################################################
    ############## syncOrdersZip ###################
    ####################################################
    public function executeGenericDMLQuery($query)
    {
// Delete, Update
        try {
            if (!$this->db) {
                $this->db = mysqli_connect(self::DB_SERVER, self::DB_USER, self::DB_PASSWORD);
            }

            mysqli_set_charset($this->db, 'utf8');
            $result = mysqli_query($this->db, $query);
            if (mysqli_errno($this->db) != 0) {
                throw new Exception("Error   :" . mysqli_errno($this->db) . "   :  " . mysqli_error($this->db));
            } else {
                return $result;
            }
        } catch (Exception $e) {
            $response = array();
            $response['Caught exception'] = $e->getMessage();
            //echo json_encode($response);
            $this->response($this->json($response), 200);
        }
    }

    ####################################################
    ############## syncOrdersZip ###################
    ####################################################
    public function executeGenericInsertQuery($query)
    {
        try {
            if (!$this->db) {
                $this->db = mysqli_connect(self::DB_SERVER, self::DB_USER, self::DB_PASSWORD);
            }

            mysqli_set_charset($this->db, 'utf8');
            $result = mysqli_query($this->db, $query);
            if (mysqli_errno($this->db) != 0) {
                throw new Exception("Error   :" . mysqli_errno($this->db) . "   :  " . mysqli_error($this->db));
            }
            return mysqli_insert_id($this->db);
        } catch (Exception $e) {
            $response = array();
            $response['Caught exception'] = $e->getMessage();
            //echo json_encode($response);
            $this->response($this->json($response), 200);
        }
    }

    ####################################################
    ############## syncOrdersZip ###################
    ####################################################
    public function executeGenericCountQuery($query)
    {
        try {
            if (!$this->db) {
                $this->db = mysqli_connect(self::DB_SERVER, self::DB_USER, self::DB_PASSWORD);
            }

            $result = mysqli_query($this->db, $query);
            $count = mysqli_num_rows($result);
            if (mysqli_errno($this->db) != 0) {
                throw new Exception("Error   :" . mysqli_errno($this->db) . "   :  " . mysqli_error($this->db));
            }
            return $count;
        } catch (Exception $e) {
            $response = array();
            $response['Caught exception'] = $e->getMessage();
            //echo json_encode($response);
            $this->response($this->json($response), 200);
        }
    }

    ####################################################
    ############## syncOrdersZip ###################
    ####################################################
    public function executeEscapeStringQuery($str)
    {
        try {
            if (!$this->db) {
                $this->db = mysqli_connect(self::DB_SERVER, self::DB_USER, self::DB_PASSWORD);
            }

            $result = mysqli_real_escape_string($this->db, $str);
            if (mysqli_errno($this->db) != 0) {
                throw new Exception("Error   :" . mysqli_errno($this->db) . "   :  " . mysqli_error($this->db));
            }
            return $result;
        } catch (Exception $e) {
            $response = array();
            $response['Caught exception'] = $e->getMessage();
            //echo json_encode($response);
            $this->response($this->json($response), 200);
        }
    }
    ####################################################
    ############## syncOrdersZip ###################
    ####################################################
    public function closeConnection()
    {
        if ($this->db) {
            mysqli_close($this->db);
        }

    }

    ####################################################
    ############## syncOrdersZip ###################
    ####################################################
    public function formatJson($jsonData)
    {
        $formatted = $jsonData;
        $formatted = str_replace('"{', '{', $formatted);
        $formatted = str_replace('}"', '}', $formatted);
        $formatted = str_replace('\n', '<br/>', $formatted);
        $formatted = str_replace('\\', '', $formatted);
        return $formatted;
    }

    ####################################################
    ############## syncOrdersZip ###################
    ####################################################
    public function json($data)
    {
        if (is_array($data)) {
            $formatted = json_encode($data, JSON_UNESCAPED_UNICODE);
            return $this->formatJson($formatted);
        }
    }

    ####################################################
    ############## syncOrdersZip ###################
    ####################################################
    public function clearArray($arr)
    {
        unset($arr);
        $arr = array();
        return $arr;
    }

    ####################################################
    ############## syncOrdersZip ###################
    ####################################################
    public function processApi()
    {
        $func = '';
        if (isset($_REQUEST['service'])) {
            $func = strtolower(trim(str_replace("/", "", $_REQUEST['service'])));
        } else if (isset($_REQUEST['reqmethod'])) {
            $func = strtolower(trim(str_replace("/", "", $_REQUEST['reqmethod'])));
        }

        if ($func) {
            //if(function_exists($func))
            if (method_exists($this, $func)) {
                $this->$func();
            } else {
                $this->log('invalid service:' . $func, true, 'log_invalid.txt');
                $this->response('invalid service', 406);
            }
        }
    }

    ####################################################
    ############## syncOrdersZip ###################
    ####################################################
    public function isValidCall($apiKey)
    {
        $flag = false;
        $apiKey = mysqli_real_escape_string($this->db, $apiKey);

        $sql = "SELECT api_key FROM " . TABLE_PREFIX . "api_data WHERE api_key ='$apiKey' ";
        $result = mysqli_query($this->db, $sql);
        if (mysqli_num_rows($result) > 0) {
            $rows = mysqli_fetch_array($result, MYSQLI_ASSOC);
            $apiKeyDB = $rows['api_key'];
            $flag = true;
        }
        return $flag;
    }

    ####################################################
    ############## syncOrdersZip ###################
    ####################################################
    public function log($text, $append = true, $fileName = '')
    {
        $file = 'log.log';
        if ($fileName) {
            $file = $fileName;
        }

        // Write the contents to the file,
        // using the FILE_APPEND flag to append the content to the end of the file
        // and the LOCK_EX flag to prevent anyone else writing to the file at the same time
        //file_put_contents($file, $text, FILE_APPEND | LOCK_EX);

        if ($append) {
            file_put_contents($file, $text . PHP_EOL, FILE_APPEND | LOCK_EX);
        } else {
            file_put_contents($file, $text);
        }

    }

    ####################################################
    ############## syncOrdersZip ###################
    ####################################################
    public function getCurrentUrl($full = false)
    {
        $s = &$_SERVER;
        $ssl = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on') ? true : false;
        $sp = strtolower($s['SERVER_PROTOCOL']);
        $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
        $port = $s['SERVER_PORT'];
        $port = ((!$ssl && $port == '80') || ($ssl && $port == '443')) ? '' : ':' . $port;
        $host = isset($s['HTTP_X_FORWARDED_HOST']) ? $s['HTTP_X_FORWARDED_HOST'] : isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : $s['SERVER_NAME'];
        $uri = $protocol . '://' . $host;
        if ($full) {
            $uri = $protocol . '://' . $host . $port . $s['REQUEST_URI'];
        }

        $segments = explode('?', $uri, 2);
        $url = $segments[0];
        $url .= TOOL_CONTAINER_DIR;
        return $url;
    }

    ####################################################
    ############## syncOrdersZip ###################
    ####################################################
    public function getDefaultStoreId()
    {
        $url = $this->getCurrentUrl();
        $url = explode('/', $url);
        $result = $url[2];
        $result = preg_replace('/^www\./', '', $result);
        $sql = "Select store_id FROM " . TABLE_PREFIX . "domain_store_rel Where domain_name='" . $result . "' limit 1";
        $data = $this->executeFetchAssocQuery($sql);
        $domain = $data[0]['store_id'];
        return (!empty($data) && $domain) ? $domain : 1;
    }

    ####################################################
    ############## syncOrdersZip ###################
    ####################################################
    public function getFileContents($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $data = curl_exec($curl);
        curl_close($curl);
        return $data;
    }

    //@ All the services which defines path @//

}
