<?php
/***************************************************************************
 Extension Name	: Featured Products 
 Extension URL	: https://www.magebees.com/featured-products-extension-for-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
namespace Magebees\Featuredproduct\Block\Adminhtml;

class Product extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_product';
        $this->_blockGroup = 'Magebees_Featuredproduct';
        $this->_headerText = __('Product');
        $this->_addButtonLabel = __('Add Featured Product');
        parent::_construct();
    }
}
