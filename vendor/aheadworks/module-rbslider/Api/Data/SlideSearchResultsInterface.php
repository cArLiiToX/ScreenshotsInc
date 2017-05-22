<?php
namespace Aheadworks\Rbslider\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for slide search results.
 * @api
 */
interface SlideSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get slides list.
     *
     * @return \Aheadworks\Rbslider\Api\Data\SlideInterface[]
     */
    public function getItems();

    /**
     * Set slides list.
     *
     * @param \Aheadworks\Rbslider\Api\Data\SlideInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
