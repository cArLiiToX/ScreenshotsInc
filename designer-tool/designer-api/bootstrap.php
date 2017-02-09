<?php
if (!defined('accessUser')) {
    die("Error");
}

/* Debug ON / OFF */
error_reporting(-1);
define("DEBUG", false); // set false for live mode //

/* Load vendor files */
require_once 'vendor/image.php';
require_once 'vendor/simple_html_dom.php';
require_once 'vendor/ttfInfo.class.php';
require_once 'vendor/BarcodeGenerator.php';
require_once 'vendor/BarcodeGeneratorPNG.php';

/* Load core files and initiate the API call */
require_once "../../xeconfig.php";
//echo STORE_TYPE.'/'.STORE_VERSION; exit;
require_once 'flight/Flight.php';
require_once 'lib/define.php';

if (file_exists('modules/store/' . STORE_TYPE . '/' . STORE_VERSION . '/component/componentStore.php')) {
    require_once 'modules/store/' . STORE_TYPE . '/' . STORE_VERSION . '/component/componentStore.php';
}
require_once 'modules/component/component.php';
require_once 'lib/loadUtility.php';
require_once 'lib/registerModules.php';
require_once 'lib/route.php';
