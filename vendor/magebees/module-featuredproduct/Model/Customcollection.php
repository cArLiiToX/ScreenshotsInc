<?php
/***************************************************************************
 Extension Name	: Featured Products 
 Extension URL	: https://www.magebees.com/featured-products-extension-for-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
namespace Magebees\Featuredproduct\Model;

class Customcollection extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('Magebees\Featuredproduct\Model\ResourceModel\Customcollection');
    }
}
