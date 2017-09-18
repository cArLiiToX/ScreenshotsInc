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
namespace Mageplaza\Blog\Block\Adminhtml\Category;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{

    /**
     * prepare the form
     */
    protected function _construct()
    {
        $this->_objectId = 'category_id';
        $this->_blockGroup = 'Mageplaza_Blog';
        $this->_controller = 'adminhtml_category';
        $this->_mode = 'edit';
        parent::_construct();
        $this->buttonList->remove('back');
        $this->buttonList->remove('reset');
        $this->buttonList->remove('save');
    }
}
