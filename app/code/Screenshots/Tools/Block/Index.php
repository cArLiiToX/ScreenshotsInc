<?php
namespace Screenshots\Tools\Block;

use Magento\Framework\View\Element\Template;

class Index extends Template
{
  public function getFormAction()
  {
      return $this->getUrl('Tools/Index/Index', ['_secure' => true]);
  }
}
