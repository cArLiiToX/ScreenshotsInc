<?php
use Magento\Framework\App\Bootstrap;
include '../app/bootstrap.php';
$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();
$mversion = '';
try {
    echo $mversion = $objectManager->get('\Magento\Framework\App\ProductMetadata')->getVersion();
} catch (Exception $e) {
    $msg = '' . $e->getMessage();die();
}
