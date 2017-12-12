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
namespace Bss\MinQtyCP\Plugin\Ui\DataProvider\Product\Form\Modifier;
 
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;

class StockData 
{
    protected $locator;
    protected $stockItemRepository;

    public function __construct(
        LocatorInterface $locator,
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository
    ) {
        $this->locator = $locator;
        $this->stockItemRepository = $stockItemRepository;
    }

    public function afterModifyMeta(
        \Magento\ConfigurableProduct\Ui\DataProvider\Product\Form\Modifier\StockData $subject,
        $meta
    ) {
        try {
            $productStock  = $this->stockItemRepository->get($this->locator->getProduct()->getId());
            $useConfig = $productStock->getData('use_config_bss_minimum_qty_configurable');

            if ($this->locator->getProduct()->getTypeId() === ConfigurableType::TYPE_CODE) {
                $config['children']['use_config_bss_minimum_qty_configurable']['arguments']['data']['config'] = [
                    'value' => $useConfig
                ];

                $config['arguments']['data']['config'] = [
                    'visible' => '1',
                ];

                $meta['advanced_inventory_modal'] = [
                    'children' => [
                        'stock_data' => [
                            'children' => [
                                'container_minimum_qty_cp' => $config
                            ],
                        ],
                    ],
                ];
            }
        } catch (\Exception $e) {

        }

        return $meta;
    }
}
