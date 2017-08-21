<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Blog
 * @copyright   Copyright (c) 2016 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\Blog\Model\Config\Source\Comments\Facebook;

class Colorscheme implements \Magento\Framework\Option\ArrayInterface
{
    const LIGHT = 'light';
    const DARK  = 'dark';

    public function toOptionArray()
    {
        return [['value' => self::LIGHT, 'label' => __('Light')], ['value' => self::DARK, 'label' => __('Dark')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [self::LIGHT => __('Light'), self::DARK => __('Dark')];
    }

    public function getAllOptions()
    {
        return $this->toOptionArray();
    }
}
