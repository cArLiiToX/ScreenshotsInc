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
namespace Bss\MinQtyCP\Plugin\Model;

class Cart
{
    private $registry;

	public function __construct(
        \Magento\Framework\Registry $registry
    ) {
        $this->registry = $registry;
    }

    public function beforeAddProduct($productInfo, $requestInfo = null)
    {
        if (!$this->registry->registry('change_cart')) {
            $this->registry->register('change_cart', true);
        }
    }

    public function beforeUpdateItems($data)
    {
        if (!$this->registry->registry('change_cart')) {
            $this->registry->register('change_cart', true);
        }
    }

    public function beforeUpdateItem($itemId, $requestInfo = null, $updatingParams = null)
    {
        if (!$this->registry->registry('change_cart')) {
            $this->registry->register('change_cart', true);
        }
    }

    public function beforeSave()
    {
        if (!$this->registry->registry('change_cart')) {
            $this->registry->register('change_cart', true);
        }
    }
}
