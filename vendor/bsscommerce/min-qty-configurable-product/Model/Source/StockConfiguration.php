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
namespace Bss\MinQtyCP\Model\Source;

use Magento\Framework\Data\ValueSourceInterface;
use Magento\CatalogInventory\Api\StockConfigurationInterface;

class StockConfiguration implements ValueSourceInterface
{
    private $request;
    private $stockConfiguration;
    private $helper;
    private $coreRegistry;
    private $stockRegistry;

    public function __construct(
        StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\App\Request\Http $request,
        \Bss\MinQtyCP\Helper\Data $helper,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
    ) {
        $this->stockConfiguration = $stockConfiguration;
        $this->request = $request;
        $this->helper = $helper;
        $this->coreRegistry = $coreRegistry;
        $this->stockRegistry = $stockRegistry;
    }

    public function getValue($name)
    {
        try {
            $productStock = $this->getStockItem();
            $useConfig = $productStock->getData('use_config_bss_minimum_qty_configurable');

            if ($useConfig) {
                $value = $this->helper->getDefaultMinQty();
            } else {
                $value = $productStock->getData($name);
            }
        } catch (\Exception $e) {
            $value = 0;
        }
        
        return is_numeric($value) ? (float) $value : 0;
    }

    protected function getProduct()
    {
        return $this->coreRegistry->registry('product');
    }

    protected function getStockItem()
    {
        return $this->stockRegistry->getStockItem(
            $this->getProduct()->getId(),
            $this->getProduct()->getStore()->getWebsiteId()
        );
    }
}
