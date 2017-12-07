<?php
/***************************************************************************
 Extension Name	: Featured Products 
 Extension URL	: https://www.magebees.com/featured-products-extension-for-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/

namespace Magebees\Featuredproduct\Block\Adminhtml;

class DefaultFrontend extends \Magento\Config\Block\System\Config\Form\Field
{
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
    
        return '<div style="background:#efefef;border:1px solid #d8d8d8;padding:10px;margin-bottom:10px;"><span>&lt?php echo 
$this->getLayout()->createBlock("Magebees\Featuredproduct\Block\Featuredproduct")->toHtml(); ?&gt;</span></div>';
    }
}
