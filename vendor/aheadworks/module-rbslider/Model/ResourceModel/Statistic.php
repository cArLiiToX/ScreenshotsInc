<?php
namespace Aheadworks\Rbslider\Model\ResourceModel;

/**
 * Class Statistic
 * @package Aheadworks\Rbslider\Model\ResourceModel
 */
class Statistic extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_rbslider_statistic', 'id');
    }
}
