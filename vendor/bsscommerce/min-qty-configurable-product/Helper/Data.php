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
namespace Bss\MinQtyCP\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	private $stockItemRepository;
	private $productRepository;

	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository,
        \Magento\Catalog\Model\ProductRepository $productRepository
	) {
		parent::__construct($context);
		$this->stockItemRepository = $stockItemRepository;
		$this->productRepository = $productRepository;
	}

	public function isEnabled()
	{
		return $this->scopeConfig->isSetFlag(
			'min_qty_cp/qty_cp/enable',
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE
		);
	}

	public function getDefaultMinQty()
	{
		return $this->scopeConfig->getValue(
			'min_qty_cp/qty_cp/min_qty',
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE
		);
	}

	public function getQuoteQtyList($quote)
    {
    	$items = $quote->getAllItems();
    	$result = [];
        foreach ($items as $item) {
            if($item->getProductType() == 'configurable') {
                if(!isset($result[$item->getProductId()])) {
                    $result[$item->getProductId()] = $item->getQty();
                } else {
                	$result[$item->getProductId()] += $item->getQty();
                }
            }
        }

        return $result;
    }
}
