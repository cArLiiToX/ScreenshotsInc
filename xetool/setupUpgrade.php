<?php
use Magento\Framework\App\Bootstrap;
include '../app/bootstrap.php';
$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();
$state = $objectManager->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');

$modules = array('Html5design_Cedapi');
try {
    /* Code to enable a module [ php bin/magento module:enable VENDORNAME_MODULENAME ] */
    $moduleStatus = $objectManager->create('Magento\Framework\Module\Status')->setIsEnabled(true, $modules);

    /* Code to run setup upgrade [ php bin/magento setup:upgrade ] */
    $installerFactory = $objectManager->create('Magento\Setup\Test\Unit\Console\Command\UpgradeCommandTest')->testExecute();

    /* Code to clean cache [ php bin/magento:cache:clean ] */
    try {
        $_cacheTypeList = $objectManager->create('Magento\Framework\App\Cache\TypeListInterface');
        $_cacheFrontendPool = $objectManager->create('Magento\Framework\App\Cache\Frontend\Pool');
        $_indexerFactory = $objectManager->create('Magento\Indexer\Model\IndexerFactory');
        $_indexerCollectionFactory = $objectManager->create('Magento\Indexer\Model\Indexer\CollectionFactory');
        $types = array('config', 'layout', 'block_html', 'collections', 'reflection', 'db_ddl', 'eav', 'config_integration', 'config_integration_api', 'full_page', 'translate', 'config_webservice');
        foreach ($types as $type) {
            $_cacheTypeList->cleanType($type);
        }
        foreach ($_cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }
        $indexerCollection = $_indexerCollectionFactory->create();
        $indexerIds = $indexerCollection->getAllIds();
        foreach ($indexerIds as $indexerId) {
            $indexer = $_indexerFactory->create();
            $indexer->load($indexerId);
            $indexer->reindexAll();
        }
    } catch (Exception $e) {
        echo $msg = 'Error during cache clean: ' . $e->getMessage();die();
    }
} catch (Exception $e) {
    echo $msg = 'Error during module enabling : ' . $e->getMessage();die();
}

/* Code to flush cache */
/*public function __construct(\Magento\Framework\App\Cache\ManagerFactory $cacheManagerFactory){
$this->cacheManagerFactory = $cacheManagerFactory;
}

public function clearCache() {
$cacheManager = $this->cacheManagerFactory->create();
$types = $cacheManager->getAvailableTypes();
$cacheManager->clean($types);
}*/

/*
use Magento\Framework\App\Bootstrap;
include('../app/bootstrap.php');
$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();

try{
$_cacheTypeList = $objectManager->create('Magento\Framework\App\Cache\TypeListInterface');
$_cacheFrontendPool = $objectManager->create('Magento\Framework\App\Cache\Frontend\Pool');
$types = array('config','layout','block_html','collections','reflection','db_ddl','eav','config_integration','config_integration_api','full_page','translate','config_webservice');
foreach ($types as $type) {
$_cacheTypeList->cleanType($type);
}
foreach ($_cacheFrontendPool as $cacheFrontend) {
$cacheFrontend->getBackend()->clean();
}
}catch(Exception $e){
echo $msg = 'Error : '.$e->getMessage();die();
}*/

/*$command = 'php bin/magento setup:upgrade && php bin/magento cache:clean && php bin/magento cache:flush';
echo '<pre>' . shell_exec($command) . '</pre>';
//For windows you have to make sure you have added the php.exe to your PATH in the Environment Variables. Please see http://willj.co/2012/10/run-wamp-php-windows-7-command-line/ */
