<?php
namespace WeltPixel\GoogleCards\Block;

class FacebookOpenGraph extends GoogleCards {

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getDescription($product) {
        if ($this->_helper->getFacebookDescriptionType()) {
            return nl2br($product->getData('description'));
        } else {
            return nl2br($product->getData('short_description'));
        }
    }

    public function getSiteName() {
        return $this->_helper->getFacebookSiteName();
    }
}
