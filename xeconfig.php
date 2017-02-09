<?php
$ds = DIRECTORY_SEPARATOR;
$base_path = '/var/www/html/xetool' . $ds;
$file = 'xeconfig.xml';
$base_path = str_ireplace('xetool', '', $base_path);
$app_path = $base_path . 'app' . $ds;
$domain = getDomain();
$file_folder_name = str_replace(array('www.', '.'), array('', '_'), $domain);
$base_path = substr($base_path, 0, -1);

$file = str_replace("xeconfig.xml",$file_folder_name."_xeconfig.xml",$base_path."app".$ds.$file);if(file_exists($file)){
    $dom = new DomDocument();
    $dom->load($file);
    define('APPNAME', $dom->getElementsByTagName('appname')->item(0)->nodeValue);
    define('XEPATH', $dom->getElementsByTagName('base_url')->item(0)->nodeValue);

    define('ACCESSTOKEN', $dom->getElementsByTagName('accessToken')->item(0)->nodeValue);
    define('APIURL', XEPATH . 'soap?wsdl&services=');

    define('SERVER', $dom->getElementsByTagName('host')->item(0)->nodeValue);
    define('USER', $dom->getElementsByTagName('dbuser')->item(0)->nodeValue);
    define('PASSWORD', $dom->getElementsByTagName('dbpass')->item(0)->nodeValue);
    define('DBNAME', $dom->getElementsByTagName('dbname')->item(0)->nodeValue);

    define('FOLDER_NAME', $file_folder_name);
    define('ASSET_PATH', '/designer-assets/' . FOLDER_NAME);
    define('TABLE_PREFIX', '');
    define('STORE_TYPE', 'magento');
    define('STORE_VERSION', '2.X');
    //echo '<hr />XEPATH:'.XEPATH.' APIUSER:'.APIUSER.' APIPASS: '.APIPASS.' USER:'.USER.' PASSWORD:'.PASSWORD.' DBNAME:'.DBNAME;exit(0);
}

function getDomain()
{
    //global $app_path;
    //require_once $app_path.'Mage.php';
    //Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
    //umask(0);Mage::app($mageRunCode);
    //$path = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
    $path = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']; //$_SERVER['HTTP_REFERER'];
    $protocol = strchr($path, '//', true);
    $path = str_replace($protocol . '//', '', $path);
    return strchr($path, '/', true);
}
