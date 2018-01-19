<?php

namespace WeltPixel\GoogleCards\Helper;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var array
     */
    protected $_cardsOptions;

    /**
     * @var array
     */
    protected $_schemasOptions;


    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        
        $this->_cardsOptions = $this->scopeConfig->getValue('weltpixel_google_cards', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $this->_schemasOptions = $this->scopeConfig->getValue('weltpixel_schemas', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getDescriptionType($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/general/description', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_cardsOptions['general']['description'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getBrand($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/general/brand', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_cardsOptions['general']['brand'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getRichSnippetDescriptionType($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/rich_snippet/description', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_cardsOptions['rich_snippet']['description'];
        }
    }


    /**
     * @param int $storeId
     * @return boolean
     */
    public function wrapRichSnippet($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/rich_snippet/wrap_with_div', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_cardsOptions['rich_snippet']['wrap_with_div'];
        }
    }


    /**
     * @param int $storeId
     * @return mixed
     */
    public function getTwitterCardDescriptionType($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_schemas/twitter_cards/description', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_schemasOptions['twitter_cards']['description'];
        }
    }


    /**
     * @param int $storeId
     * @return string
     */
    public function getTwitterCardType($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_schemas/twitter_cards/card_type', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_schemasOptions['twitter_cards']['card_type'];
        }
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getTwitterCreator($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_schemas/twitter_cards/creator', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_schemasOptions['twitter_cards']['creator'];
        }
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getTwitterSite($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_schemas/twitter_cards/site', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_schemasOptions['twitter_cards']['site'];
        }
    }


    /**
     * @return string
     */
    public function getTwitterShippingCountry() {
        return $this->scopeConfig->getValue('shipping/origin/country_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }


    /**
     * @param int $storeId
     * @return string
     */
    public function getFacebookDescriptionType($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_schemas/facebook_opengraph/description', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_schemasOptions['facebook_opengraph']['description'];
        }
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getFacebookSiteName($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_schemas/facebook_opengraph/site_name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_schemasOptions['facebook_opengraph']['site_name'];
        }
    }

}
