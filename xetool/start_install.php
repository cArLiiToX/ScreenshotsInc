<?php
require_once "function.php";

##########################################################
## App name fetched from the url & setting xml updated ###
##########################################################
use Magento\Framework\App\Bootstrap;
require_once "../app/bootstrap.php";
$bootstrap = Bootstrap::create(BP, $_SERVER);

$objectManager = $bootstrap->getObjectManager();
$storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface');
$currentStore = $storeManager->getStore();
$storeId = $storeManager->getStore()->getId();
$base_url = $currentStore->getBaseUrl();

$dom = new DomDocument();
$dom->load('xeconfig.xml') or die("error");
$dom->getElementsByTagName('base_url')->item(0)->nodeValue = $base_url;

$path = $_SERVER['PHP_SELF'];
$path = str_ireplace('/xetool/start_install.php', '', $path);
$appname = substr($path, 1);
$dom->getElementsByTagName('appname')->item(0)->nodeValue = $appname;
$dom->save('xeconfig.xml');

############################################################
########### Disable Cache & Re-index Models ################
############################################################
/*try{
$cache_arr = Mage::app()->useCache();
$new_status_arr = array_fill(0,count($cache_arr),0);
$new_cache_arr = array_combine(array_keys($cache_arr),$new_status_arr);
Mage::app()->saveUseCache($new_cache_arr);
}catch(Exception $e){
$msg = "Cache disable failed.";
xe_log("\n".date("Y-m-d H:i:s").': Error in 1st Step : '.$conn->error.' : '.$msg."\n");
header('Location: '.$base_url.'xetool/index.php?action=setdb&msg='.$msg);die();
}
require_once "reindex.php";
 */

###########################################################
## Modify path & add lines dynamically into xeconfig.php ##
###########################################################
@chmod('xeconfig.php', 0777);
$str = @file_get_contents('xeconfig.php');
$ds = DIRECTORY_SEPARATOR;
$p = getcwd();

$replaceLine = 'if (file_exists($file)) {';
$addLine = '$file = str_replace("xeconfig.xml",$file_folder_name."_xeconfig.xml",$base_path."app".$ds.$file);if(file_exists($file)){';
$str = str_replace(array("getcwd()", $replaceLine), array("'$p'", $addLine), $str);
@file_put_contents('../xeconfig.php', $str);

############################################################
########### Update base url in all the settings ############
############################################################
$local = 'designer-tool/localsettings.js';
if (file_exists($local)) {
    @chmod($local, 0777);
    $settingStr = @file_get_contents($local);
    $settingStr = str_replace("XEPATH", $base_url, $settingStr);
    @file_put_contents($local, $settingStr);
}

###########################################################
## Copy files and paste to the corresponding directories ##
###########################################################
@copy("frontendlc.php", "../frontendlc.php");
@copy("store_detail.json", "../store_detail.json");

if (!file_exists("../designer-tool")) {
    mkdir('../designer-tool', 0777, true);
}

recurse_copy("designer-tool", "../designer-tool");

if (!file_exists("../app/code")) {
    mkdir('../app/code', 0777, true);
}

recurse_copy("magento/app/code/Html5design", "../app/code/Html5design");
try{
	$upgradeUrl = $base_url . 'xetool/setupUpgrade.php';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $upgradeUrl);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	$x = curl_exec($ch);
	curl_close($ch);
}catch(Exception $e){
	$msg = "setupUpgrade.php failed to run.";
	xe_log("\n".date("Y-m-d H:i:s").': Error in 1st Step : '.$conn->error.' : '.$msg."\n");
}

header('Location: ' . $base_url . 'xetool/index.php?action=attr');
