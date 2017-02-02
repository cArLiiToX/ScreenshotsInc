<?php
use Magento\Framework\App\Bootstrap;
include '../app/bootstrap.php';
$bootstrap = Bootstrap::create(BP, $_SERVER);


$mversion = '';
try {
	$objectManager = $bootstrap->getObjectManager();
    echo $mversion = $objectManager->get('\Magento\Framework\App\ProductMetadata')->getVersion();
} catch (Exception $e) {
    echo $msg = 'Exception : ' . $e->getMessage();die();
}
