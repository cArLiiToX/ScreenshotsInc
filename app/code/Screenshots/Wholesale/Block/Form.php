<?php
namespace Screenshots\Wholesale\Block;

use Magento\Framework\View\Element\Template;

class Form extends Template
{
    public function getFormAction()
    {
        return $this->getUrl('Wholesale/Index/Post', ['_secure' => true]);
    }
}
