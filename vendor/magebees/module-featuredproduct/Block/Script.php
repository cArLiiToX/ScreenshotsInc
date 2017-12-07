<?php
/***************************************************************************
 Extension Name : Featured Products 
 Extension URL  : https://www.magebees.com/featured-products-extension-for-magento-2.html
 Copyright      : Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email  : support@magebees.com 
 ***************************************************************************/
namespace Magebees\Featuredproduct\Block;

class Script extends \Magento\Framework\View\Element\Template
{
    public function manageHeaderContent()
    {
        
        $this->pageConfig->addPageAsset('Magebees_Featuredproduct::css/featuredproduct.css');
    }
}
