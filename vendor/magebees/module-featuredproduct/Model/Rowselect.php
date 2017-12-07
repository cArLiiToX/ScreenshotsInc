<?php
/***************************************************************************
 Extension Name	: Featured Products 
 Extension URL	: https://www.magebees.com/featured-products-extension-for-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
namespace Magebees\Featuredproduct\Model;

class Rowselect implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [['value' => 1, 'label' => __('1')],['value' => 2, 'label' => __('2')], ['value' =>3, 'label' => __('3')],['value' =>4, 'label' => __('4')],['value' =>5, 'label' => __('5')],['value' =>6, 'label' => __('6')]];
    }
    
    public function toArray()
    {
        return [6 => __('6'),5 => __('5'),4 => __('4'),3 => __('3'), 2 => __('2'),1=>__('1')];
    }
}
