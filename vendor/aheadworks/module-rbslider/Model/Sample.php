<?php
namespace Aheadworks\Rbslider\Model;

/**
 * class Sample
 * @package Aheadworks\Rbslider\Model
 */
class Sample extends \Magento\Framework\Config\Data
{
    /**
     * @param \Aheadworks\Rbslider\Model\Sample\Reader\Xml $reader
     * @param \Magento\Framework\Config\CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        \Aheadworks\Rbslider\Model\Sample\Reader\Xml $reader,
        \Magento\Framework\Config\CacheInterface $cache,
        $cacheId = 'aheadworks_rbslider_sample_data_cache'
    ) {
        parent::__construct($reader, $cache, $cacheId);
    }
}
