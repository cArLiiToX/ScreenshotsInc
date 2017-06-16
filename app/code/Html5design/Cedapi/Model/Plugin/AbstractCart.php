<?php
namespace Html5design\Cedapi\Model\Plugin;

class AbstractCart
{
    /**
     * @param \Magento\Checkout\Block\Cart\AbstractCart $subject
     * @return $result
     */
    public function afterGetItemRenderer(\Magento\Checkout\Block\Cart\AbstractCart $subject, $result)
    {
	    //$result->setTemplate('Html5design_Cedapi::cart/item/renderer/actions/edit.phtml');
		$result->setTemplate('Html5design_Cedapi::cart/item/default.phtml');
		return $result;
    }

}
