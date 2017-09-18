<?php
namespace Screenshots\CustomArt\Block;

use Magento\Framework\View\Element\Template;

class Form extends Template
{
    public function getFormAction()
    {
        return $this->getUrl('CustomArt/Index/Post', ['_secure' => true]);
    }
}
