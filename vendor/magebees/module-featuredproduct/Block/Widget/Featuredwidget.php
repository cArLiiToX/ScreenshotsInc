<?php
/***************************************************************************
 Extension Name	: Featured Products 
 Extension URL	: https://www.magebees.com/featured-products-extension-for-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
namespace Magebees\Featuredproduct\Block\Widget;

class Featuredwidget extends \Magebees\Featuredproduct\Block\Featuredproduct implements \Magento\Widget\Block\BlockInterface
{
    
    
    public function addData(array $arr)
    {
        
        $this->_data = array_merge($this->_data, $arr);
    }

    public function setData($key, $value = null)
    {
        
        $this->_data[$key] = $value;
    }
 
    public function _toHtml()
    {
        if ($this->getData('template')) {
            $this->setTemplate($this->getData('template'));
        }
        return parent::_toHtml();
    }
}
