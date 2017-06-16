<?php
namespace Html5design\Cedapi\Block\Product\View;

use Magento\Catalog\Block\Product\AbstractProduct;

class Customize extends AbstractProduct
{

    public $superAttributes = array();

    public function isCustomizeButton()
    {
        $product = $this->getProduct();
        if ($product->getTypeId() == 'simple') {
            $productId = $product->getId();
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $productModel = $objectManager->get('Magento\Catalog\Model\Product')->load($productId);
            $xeIsDesigner = $productModel->getResource()->getAttribute("xe_is_designer")->getFrontend()->getValue($productModel);
            if ($xeIsDesigner = 'yes') {
                return true;
            }
        }

        if ($product->getTypeId() == 'configurable') {
            $productId = $product->getId();
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $productModel = $objectManager->get('Magento\Catalog\Model\Product')->load($productId);
            $xeIsDesigner = $productModel->getResource()->getAttribute("xe_is_designer")->getFrontend()->getValue($productModel);
            $productAttributeOptions = $productModel->getTypeInstance(true)->getConfigurableAttributesAsArray($productModel);
            $attributeOptions = array();

            foreach ($productAttributeOptions as $productAttribute) {
                $attributeOptions[] = $productAttribute['attribute_code'];
            }

            if ($xeIsDesigner = 'yes') {
                return true;
            }
        }
        return false;
    }

    public function getSuperAttributes()
    {
        $product = $this->getProduct();

        $configurableAttributeCollection = $product->getTypeInstance()->getConfigurableAttributes();
        $super_attrs_code = array();

        foreach ($configurableAttributeCollection as $attribute) {
            $super_attrs[$attribute->getProductAttribute()->getAttributeCode()] = $attribute->getProductAttribute()->getId();
            $super_attrs_code[] = $attribute->getProductAttribute()->getAttributeCode();
        }
        $this->superAttributes = $super_attrs_code;
    }

}
