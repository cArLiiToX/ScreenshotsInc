<?php
namespace Aheadworks\Rbslider\Model;

use Aheadworks\Rbslider\Model\ResourceModel\Statistic as ResourceStatistic;

/**
 * Class Statistic
 * @package Aheadworks\Rbslider\Model
 */
class Statistic extends \Magento\Framework\Model\AbstractModel
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ResourceStatistic::class);
    }
}
