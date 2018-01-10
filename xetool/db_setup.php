<?php
use Magento\Framework\App\Bootstrap;
require_once 'function.php';
require_once 'xeconfig.php';

if (!empty($_POST) && !empty($_POST['db']) && !empty($_POST['user']) && !empty($_POST['folder_name'])) {
    extract($_POST);

    $d = '../app/' . $folder_name . '_xeconfig.xml';
    if (file_exists($d)) {
        echo 2;die();
    } else {
        if (!filter_var($user['uname'], FILTER_VALIDATE_EMAIL)) {
            $msg = "Please enter a valid email for admin login id.";
            echo $msg;die();
        }

        // Create connection
        mysqli_report(MYSQLI_REPORT_STRICT);
        try {
            $conn = new mysqli($db['host'], $db['uid'], $db['pwd']);
        } catch (Exception $e) {
            $msg = "Database connection failed. Please provide correct database connection info.";
            xe_log("\n" . date("Y-m-d H:i:s") . ': Database Connection failed: ' . $e->getMessage() . "\n");
            echo $msg;die();
        }

        // Check connection
        if ($conn->connect_error) {
            $msg = "Database connection failed. Please provide correct database connection info.";
            xe_log("\n" . date("Y-m-d H:i:s") . ': Error in 5th Step : Database Connection failed: ' . $conn->error . ' : ' . $conn->connect_error . "\n");
            echo $msg;die();
        } else {
            if ($conn->select_db($db['dbname'])) {
                ########################################################
                ########## Import database for designer tool ###########
                ########################################################
                $sqlFile = 'sql/basic_database.sql';
                run_sql_file($sqlFile, $conn);

                ########################################################
                #### Update stores and ids in domain_store_rel table ###
                ########################################################
                //$chk_duplicate = "SELECT COUNT(*) AS nos FROM ".TABLE_PREFIX."domain_store_rel";
                //$handler = $conn->query($chk_duplicate);
                //$row = $handler->fetch_assoc();
                //if(empty($row)){
                try {
                    include '../app/bootstrap.php';
                    $bootstrap = Bootstrap::create(BP, $_SERVER);

                    $objectManager = $bootstrap->getObjectManager();
                    $storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface');
                    $websites = $storeManager->getWebsites();
                    $val = '';
                    foreach ($websites as $website) {
                        foreach ($website->getStores() as $pk_id => $store) {
                            $pk_id += 1;
                            $wedId = $website->getId();
                            $storeObj = $storeManager->getStore($store);
                            $storeId = $storeObj->getId();
                            $url = $storeObj->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
                            $url = explode('/', $url);
                            $url = $url[2];
                            $url = str_ireplace('www.', '', $url);
                            $val .= ",(" . $pk_id . ",'" . $url . "','" . $storeObj->getId() . "')";
                        }
                    }
                    if (strlen($val)) {
                        $domainStoreSql = "INSERT INTO " . TABLE_PREFIX . "domain_store_rel(pk_id,domain_name,store_id) VALUES" . substr($val, 1) . ";";
                        $status = $conn->query($domainStoreSql);
                    }
                } catch (Exception $e) {
                    $msg = "Update your domain_store_rel table in your inkXE database.";
                    xe_log("\n" . date("Y-m-d H:i:s") . ': Error in 5th Step : domain_store_rel update failed: ' . $conn->error . ' : ' . $msg . "\n");
                    echo $msg;die();
                }
                //}

                ##########################################################
                ######## Insert user access Info in user table ###########
                ##########################################################
                //$chk_duplicate = "SELECT COUNT(*) AS nos FROM ".TABLE_PREFIX."user";
                //$handler = $conn->query($chk_duplicate);
                //$row = $handler->fetch_assoc();
                //if(empty($row)){
                $sql = "INSERT INTO " . TABLE_PREFIX . "user(email,password,question,answer,name,userType) VALUES('" . $user['uname'] . "','" . md5($user['upwd']) . "','" . $security['question'] . "','" . $security['answer'] . "','Super Admin','1')";
                $status = $conn->query($sql);
                //}

                #########################################################
                ### Update installation date in version_manage table ####
                #########################################################
                $sql_vm = "UPDATE " . TABLE_PREFIX . "version_manage SET installed_on=curdate()";
                $status = $conn->query($sql_vm);
                $conn->close();

                ##########################################################
                ##### Update Database related Info in xeconfig.xml #######
                ##########################################################
                $dom = new DomDocument();
                $s = 'xeconfig.xml';
                $dom->load($s) or die("Unable to load xml");
                $dom->getElementsByTagName('host')->item(0)->nodeValue = $db['host'];
                $dom->getElementsByTagName('dbuser')->item(0)->nodeValue = $db['uid'];
                if (isset($db['pwd']) && $db['pwd']) {
                    $dom->getElementsByTagName('dbpass')->item(0)->nodeValue = $db['pwd'];
                }

                $dom->getElementsByTagName('dbname')->item(0)->nodeValue = $db['dbname'];
                $dom->save($s);
                //if (file_exists('store_details.json'))
                @copy($s, $d);

                $upgradeUrl = XEPATH . 'xetool/setupUpgrade.php';
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $upgradeUrl);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_exec($ch);
                curl_close($ch);

                echo 1;die();
            } else {
                $msg = "Please provide correct database connection info."; //"Enter an existing database name.";
                xe_log("\n" . date("Y-m-d H:i:s") . ': Error in 5th Step : Error creating tables: ' . $conn->error . ' : ' . $msg . "\n");
                echo $msg;die();
            }
        }
    }
} else {
    $msg = 'Please fill up all the fields.';
    echo $msg;die();
}
