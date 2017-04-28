<?php
namespace Aheadworks\Rbslider\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;
use Aheadworks\Rbslider\Api\Data\BlockInterface;

/**
 * Interface for Rbslider block search results
 *
 * @api
 */
interface BlockSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get blocks list
     *
     * @return BlockInterface[]
     */
    public function getItems();

    /**
     * Set blocks list
     *
     * @param BlockInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
