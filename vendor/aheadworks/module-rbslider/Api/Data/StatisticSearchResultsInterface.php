<?php
namespace Aheadworks\Rbslider\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for statistic search results.
 * @api
 */
interface StatisticSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get banners list.
     *
     * @return \Aheadworks\Rbslider\Api\Data\StatisticInterface[]
     */
    public function getItems();

    /**
     * Set banners list.
     *
     * @param \Aheadworks\Rbslider\Api\Data\StatisticInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
