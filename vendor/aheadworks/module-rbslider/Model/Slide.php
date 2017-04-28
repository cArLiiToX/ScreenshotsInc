<?php
namespace Aheadworks\Rbslider\Model;

use Aheadworks\Rbslider\Model\ResourceModel\Slide as ResourceSlide;

/**
 * Class Slide
 * @package Aheadworks\Rbslider\Model
 */
class Slide extends \Magento\Framework\Model\AbstractModel
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ResourceSlide::class);
    }
}
