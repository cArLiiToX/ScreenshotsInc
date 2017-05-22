<?php
namespace Aheadworks\Rbslider\Model;

use Aheadworks\Rbslider\Api\Data\BannerInterface;

/**
 * Class BannerRegistry
 * @package Aheadworks\Rbslider\Model
 */
class BannerRegistry
{
    /**
     * @var array
     */
    private $bannerRegistry = [];

    /**
     * Retrieve Banner from registry by ID
     *
     * @param int $bannerId
     * @return BannerInterface|null
     */
    public function retrieve($bannerId)
    {
        if (!isset($this->bannerRegistry[$bannerId])) {
            return null;
        }
        return $this->bannerRegistry[$bannerId];
    }

    /**
     * Remove instance of the Banner from registry by ID
     *
     * @param int $bannerId
     * @return void
     */
    public function remove($bannerId)
    {
        if (isset($this->bannerRegistry[$bannerId])) {
            unset($this->bannerRegistry[$bannerId]);
        }
    }

    /**
     * Replace existing Banner with a new one
     *
     * @param BannerInterface $banner
     * @return $this
     */
    public function push(BannerInterface $banner)
    {
        $this->bannerRegistry[$banner->getId()] = $banner;
        return $this;
    }
}
