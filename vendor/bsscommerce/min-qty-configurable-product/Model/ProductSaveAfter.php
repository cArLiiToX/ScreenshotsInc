<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * BSS Commerce does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BSS Commerce does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   BSS
 * @package    Bss_MinQtyCP
 * @author     Extension Team
 * @copyright  Copyright (c) 2014-2105 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\MinQtyCP\Model;

use Magento\Framework\Event\ObserverInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Api\StockItemRepositoryInterface;
use Magento\CatalogInventory\Api\StockConfigurationInterface;

class ProductSaveAfter implements ObserverInterface
{
	private $request;
	private $stockRegistry;
	private $stockItemRepository;
	private $stockConfiguration;

	public function __construct(
		\Magento\Framework\App\Request\Http $request,
		StockRegistryInterface $stockRegistry,
		StockConfigurationInterface $stockConfiguration,
		StockItemRepositoryInterface $stockItemRepository
	) {
		$this->request = $request;
		$this->stockRegistry = $stockRegistry;
		$this->stockItemRepository = $stockItemRepository;
		$this->stockConfiguration = $stockConfiguration;
	}

	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$params = $this->request->getParams();
		$minQtyCp = $params['product']['qty_data']['bss_minimum_qty_configurable'];
		$useConfigMinQtyCp = $params['product']['qty_data']['use_config_bss_minimum_qty_configurable'];
		$product = $observer->getEvent()->getProduct();

		if ($product->getStockData() === null) {
			return;
		}

		$stockItemData = $product->getStockData();
		$stockItemData['product_id'] = $product->getId();

		if (!isset($stockItemData['website_id'])) {
			$stockItemData['website_id'] = $this->stockConfiguration->getDefaultScopeId();
		}

		$stockItem = $this->stockRegistry->getStockItem($stockItemData['product_id'], $stockItemData['website_id']);
		$stockItemData['bss_minimum_qty_configurable'] = $minQtyCp;
		$stockItemData['use_config_bss_minimum_qty_configurable'] = $useConfigMinQtyCp;
		$stockItem->addData($stockItemData);
		$this->stockItemRepository->save($stockItem);
	}
}
