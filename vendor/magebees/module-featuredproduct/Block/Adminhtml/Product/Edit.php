<?php
/***************************************************************************
 Extension Name	: Featured Products 
 Extension URL	: https://www.magebees.com/featured-products-extension-for-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
namespace Magebees\Featuredproduct\Block\Adminhtml\Product;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_product';
        $this->_blockGroup = 'Magebees_Featuredproduct';
        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save Products'));
        $this->buttonList->update('delete', 'label', __('Delete Block'));
    }
}
