<?php
namespace WeltPixel\GoogleCards\Block;

class RichSnippet extends GoogleCards {

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getDescription($product) {
        if ($this->_helper->getRichSnippetDescriptionType()) {
            return nl2br($product->getData('description'));
        } else {
            return nl2br($product->getData('short_description'));
        }
    }

    /**
     * @return bool
     */
    public function wrapWithDiv() {
        return $this->_helper->wrapRichSnippet();
    }

}
