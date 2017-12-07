<?php
/***************************************************************************
 Extension Name	: Featured Products 
 Extension URL	: https://www.magebees.com/featured-products-extension-for-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
namespace Magebees\Featuredproduct\Model;

class Showproduct implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [['value' => 2, 'label' => __('Display Category Wise')],['value' => 1, 'label' => __('Display All Products')]];
    }

    public function toArray()
    {
        return [1 => __('Display All Products'),2=>__('Display Category Wise')];
    }
}
