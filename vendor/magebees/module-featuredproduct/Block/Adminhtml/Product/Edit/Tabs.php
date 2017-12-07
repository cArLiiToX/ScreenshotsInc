<?php
/***************************************************************************
 Extension Name	: Featured Products 
 Extension URL	: https://www.magebees.com/featured-products-extension-for-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
namespace Magebees\Featuredproduct\Block\Adminhtml\Product\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('product_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Add Featuredproduct'));
    }
    protected function _prepareLayout()
    {
            $this->addTab(
                'featured',
                [
                    'label' => __('Add Featured Products'),
                    'url' => $this->getUrl('featuredproduct/manage/productinfo', ['_current' => true]),
                    'class' => 'ajax'
                ]
            );
            return parent::_prepareLayout();
    }
}
