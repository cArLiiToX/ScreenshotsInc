<?php
	use Magento\Framework\App\Bootstrap;	
	require_once('app/bootstrap.php');
	$bootstrap = Bootstrap::create(BP, $_SERVER);
	$objectManager = $bootstrap->getObjectManager();
	


	
	$state = $objectManager->get('Magento\Framework\App\State');
	$state->setAreaCode('frontend');

	
	$configId = 388;
	$productModel = $objectManager->create('Magento\Catalog\Model\Product');
	$productModel = $productModel->load($configId)
                    ->setDescription('Gildan Classic T-Shirt')
                    ->setShortDescription('short_description')->save();