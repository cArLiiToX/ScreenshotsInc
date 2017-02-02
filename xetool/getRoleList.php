<?php
use Magento\Framework\App\Bootstrap;
include '../app/bootstrap.php';
$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();

$integrationList = array();
try {
    $integrationList = $objectManager->get('Magento\Integration\Model\IntegrationFactory')->create()->getCollection()
        ->addFieldToSelect(array('integration_id', 'name'))->getData();
} catch (Exception $e) {
    $msg = '' . $e->getMessage();die();
}
//print_r($integrationList);
