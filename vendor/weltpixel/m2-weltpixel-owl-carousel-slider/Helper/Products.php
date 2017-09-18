<?php

namespace WeltPixel\OwlCarouselSlider\Helper;

/**
 * Helper Products Slider
 * @category WeltPixel
 * @package  WeltPixel_OwlCarouselSlider
 * @module   OwlCarouselSlider
 * @author   WeltPixel Developer
 */
class Products extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_scopeConfig;

    const SYS_PATH = 'weltpixel_owl_carousel_config/';

    /**
     * @param \Magento\Framework\App\Helper\Context  $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);

        $this->_scopeConfig = $context->getScopeConfig();
    }

    /**
     * Retrieve the slider config options.
     *
     * @param $type
     * @return array
     */
    public function getSliderConfigOptions($type)
    {
        $configFields = [
            'status',
            'title',
            'show_price',
            'show_addto',
            'show_wishlist',
            'show_compare',
            'nav',
            'dots',
            'center',
            'items',
            'loop',
            'margin',
            'merge',
            'URLhashListener',
            'stagePadding',
            'lazyLoad',
            'autoplay',
            'autoplayTimeout',
            'autoplayHoverPause',

            'nav_brk1',
            'dots_brk1',
            'items_brk1',
            'center_brk1',
            'stagePadding_brk1',
            
            'nav_brk2',
            'dots_brk2',
            'items_brk2',
            'center_brk2',
            'stagePadding_brk2',

            'nav_brk3',
            'dots_brk3',
            'items_brk3',
            'center_brk3',
            'stagePadding_brk3',
            
            'nav_brk4',
            'dots_brk4',
            'items_brk4',
            'center_brk4',
            'stagePadding_brk4',
        ];

        $sliderConfig = [];
        $sysPath = self::SYS_PATH . $type;

        foreach ($configFields as $field) {
            $configPath = $sysPath . '/' . $field;
            $sliderConfig[$field] = $this->_getConfigValue($configPath);
        }

        return $sliderConfig;
    }

    /**
     * Retrieve the config value.
     *
     * @param string $configPath
     * @return mixed
     */
    private function _getConfigValue($configPath)
    {
        return $this->_scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve the product limit config value.
     *
     * @param $string type
     * @return int
     */
    public function getProductLimit($type)
    {
        $configPath = self::SYS_PATH . $type . '/max_items';
        
        return (int)$this->_getConfigValue($configPath);
    }

    /**
     * Retrieve the random sort config value.
     *
     * @param $string type
     * @return int
     */
    public function getRandomSort($type)
    {
        $configPath = self::SYS_PATH . $type . '/random_sort';

        return $this->_getConfigValue($configPath);
    }

    /**
     * Retrieve the slider configuration.
     *
     * @param $string type
     * @return array
     */
    public function getSliderConfiguration($type)
    {
        switch($type){
            case 'related':
                $type = 'related_products';
                break;
            case 'upsell':
                $type = 'upsell_products';
                break;
            case 'crosssell':
                $type = 'crosssell_products';
                break;
            default:
                $type = 'related_products';
        }

        return $this->getSliderConfigOptions($type);
    }
}
