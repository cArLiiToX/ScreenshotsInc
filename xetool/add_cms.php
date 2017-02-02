<?php
require_once 'function.php';
require_once 'xeconfig.php';
$redirect_path = XEPATH . 'xetool/index.php';
$fpath = 'wizard/images/install_image/';
$mediaAttribute = array('thumbnail', 'small_image', 'image');

use Magento\Framework\App\Bootstrap;
include '../app/bootstrap.php';
$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();

$state = $objectManager->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');
#########################################################
################# Code to create the CMS page ###########
#########################################################
$identifier = 'product-designer';
$page = $objectManager->create('Magento\Cms\Model\Page');
$exist = $page->getCollection()->addFieldToFilter('identifier', $identifier)->getData();
if (empty($exist)) {
    $content = '<p>{{block type="core/template" name="myDesignerSesId" template="cedapi/productdesigner.phtml"}}<iframe id="tshirtIFrame" src="{{config path="web/unsecure/base_url"}}designer-tool/designer-app/index.html" height="770" width="100%"></iframe></p>';
    $page->setTitle('Product Designer Tool')
        ->setIdentifier($identifier)
        ->setIsActive(true)
        ->setPageLayout('1column')
        ->setStores(array(0))
        ->setContent($content)
        ->save();
} else {
    xe_log("\n" . date("Y-m-d H:i:s") . ': You have already created this CMS Page.' . "\n");
    $msg = 'You have already created this CMS Page.';
    //header('Location: '.$redirect_path.'?action=cms&msg='.$msg);
}
$attributeSetName = 'inkXE';
$attributeSetId = $objectManager->create('Magento\Eav\Model\Entity\Attribute\Set')->load($attributeSetName, 'attribute_set_name')->getAttributeSetId();
$attributeColorCode = 'xe_color';
$attributeSizeCode = 'xe_size';
$attributeObj = $objectManager->create('Magento\Eav\Model\ResourceModel\Entity\Attribute');
$attributeColorId = $attributeObj->getIdByCode('catalog_product', $attributeColorCode);
$attributeSizeId = $attributeObj->getIdByCode('catalog_product', $attributeSizeCode);
$storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface');
$rootCatId = $storeManager->getStore()->getRootCategoryId();
$sProductName = 'InkXE test product purple';
$sSku = 'ssku';
$cProductName = 'InkXE test product';
$cSku = 'csku';
$configProductId = 0;
$productModel = $objectManager->create('Magento\Catalog\Model\Product');
$existProduct = $productModel->loadByAttribute('name', $sProductName);
$attr = $productModel->getResource()->getAttribute($attributeColorCode);
$optarr = array();
if ($attr->usesSource()) {
    $optarr = $attr->getSource()->getAllOptions();
    $optionColorId = $optarr[1]['value'];
}
$attr = $productModel->getResource()->getAttribute($attributeSizeCode);
if ($attr->usesSource()) {
    $optarr = $attr->getSource()->getAllOptions();
    $optionSizeId = $optarr[1]['value'];
}

/* Simple Product Insert Section */
if (empty($existProduct)) {
    $simpleProduct = $objectManager->create('Magento\Catalog\Model\Product');
    $simpleProduct->setWebsiteIds(array(1)) //website ID the product is assigned to, as an array
        ->setAttributeSetId($attributeSetId)
        ->setTypeId('simple')
        ->setSku($sSku)
        ->setName($sProductName)
        ->setWeight(2)
        ->setStatus(1) //product status (1 - enabled, 2 - disabled)
        ->setTaxClassId(0) //tax class (0 - none, 1 - default, 2 - taxable, 4 - shipping)
        ->setVisibility(1) //VISIBILITY_NOT_VISIBLE Not visible individually
        ->setColor($optionColorId)
        ->setSize($optionSizeId)
        ->setPrice(500)
        ->setXeIsDesigner(1)
        ->setXeIsTemplate(0)
        ->setDescription('InkXE test product. Not for sold.')
        ->setXeColor($optionColorId)
        ->setXeSize($optionSizeId);
    // We set up a $count variable - the first image gets used as small, thumbnail and base
    $count = 0;
    $imgArray = array($fpath . 'simple.png');
    foreach ($imgArray as $image):
        $imgUrl = _save_image($image, $objectManager);
        if ($count == 0) {
            //$smediaAttribute = array_push($mediaAttribute,'swatch_image');
            $simpleProduct->addImageToMediaGallery($imgUrl, $mediaAttribute, true, false);
        } else {
            $simpleProduct->addImageToMediaGallery($imgUrl, null, true, false);
        }
        $count++;
    endforeach;
    $simpleProduct->setStockData(array(
        'use_config_manage_stock' => 0, //'Use config settings' checkbox
        'manage_stock' => 1, //manage stock
        'min_sale_qty' => 1, //Minimum Qty Allowed in Shopping Cart
        'max_sale_qty' => 30, //Maximum Qty Allowed in Shopping Cart
        'is_in_stock' => 1, //Stock Availability
        'qty' => 100, //qty
    )
    )
        ->setCategoryIds(array($rootCatId));
    $simpleProduct->save();
    $simplProductId = $simpleProduct->getId();
} else {
    $spid = $existProduct->getData();
    $simplProductId = $spid['entity_id'];
}

/* Configurable Product Insert Section */
if (isset($simplProductId) && $simplProductId) {
    $existConfigProduct = $productModel->loadByAttribute('name', $cProductName);
    if (empty($existConfigProduct)) {
        $configProduct = $objectManager->create('Magento\Catalog\Model\Product');
        $configProduct->setStoreId(1)
            ->setWebsiteIds(array(1)) //website ID the product is assigned to, as an array
            ->setAttributeSetId($attributeSetId)
            ->setTypeId('configurable')
            ->setSku($cSku)
            ->setName($cProductName)
            ->setWeight(213)
            ->setStatus(1) //product status (1 - enabled, 2 - disabled)
            ->setTaxClassId(0) //tax class (0 - none, 1 - default, 2 - taxable, 4 - shipping)
            ->setVisibility(4) //catalog and search visibility = 4 Visibility::VISIBILITY_BOTH
            ->setXeIsDesigner(1)
            ->setXeIsTemplate(0)
            ->setPrice(500000.00)
            ->setDescription('InkXE test product. Not for sold.');
        $configProduct->setStockData(array(
            'use_config_manage_stock' => 0, //'Use config settings' checkbox
            'manage_stock' => 1, //manage stock
            'min_sale_qty' => 1, //Minimum Qty Allowed in Shopping Cart
            'max_sale_qty' => 30, //Maximum Qty Allowed in Shopping Cart
            'is_in_stock' => 1, //Stock Availability
            'qty' => 100, //qty
        )
        )
            ->setCategoryIds(array($rootCatId)); //assign product to categories
        $count = 0;
        $imgArray = array($fpath . 'configurable.png');
        foreach ($imgArray as $image) {
            $imgUrl = _save_image($image, $objectManager);
            if ($count == 0) {
                $configProduct->addImageToMediaGallery($imgUrl, $mediaAttribute, true, false);
            } else {
                $configProduct->addImageToMediaGallery($imgUrl, null, true, false);
            }
            $count++;
        }
        $configProduct->save();
        $conf_id1 = $configProduct->getId();
        $product = $configProduct->load($conf_id1);

        $objectManager->create('Magento\ConfigurableProduct\Model\Product\Type\Configurable')->setUsedProductAttributeIds(array($attributeColorId, $attributeSizeId), $product);
        $product->setCanSaveConfigurableAttributes(true);
        $product->save();
        try {
            $configProductId = $product->getId();
        } catch (Exception $e) {
            $msg = 'exception: ' . $e->getMessage();
            xe_log("\n" . date("Y-m-d H:i:s") . $msg . "\n");
        }
    } else {
        $cpid = $existConfigProduct->getData();
        $configProductId = $cpid['entity_id'];
    }
}

/* Associate simple product to configurable*/
if (isset($simplProductId) && isset($configProductId)) {
    associateSimpleToConfigurableProduct($configProductId, $simplProductId, $attributeColorCode, $attributeSizeCode, $attributeColorId, $attributeSizeId, $attributeSetId);
}

function associateSimpleToConfigurableProduct($confId, $childIds, $attributeColorCode, $attributeSizeCode, $attribute_colorid, $attribute_sizeid, $attribute_set_id)
{
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    $configProduct = $objectManager->create('Magento\Catalog\Model\Product')->load($confId);
    $productCollectionFactory = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
    $simpleProducts = $productCollectionFactory->create()
        ->addIdFilter($childIds)
        ->addAttributeToSelect($attributeColorCode)
        ->addAttributeToSelect($attributeSizeCode)
        ->addAttributeToSelect('price');
    $configProduct->setCanSaveConfigurableAttributes(true);
    $configProduct->setCanSaveCustomOptions(true);
    $configProduct->getTypeInstance()->setUsedProductAttributeIds(array($attribute_colorid, $attribute_sizeid), $configProduct);
    $configurableAttributesData = $configProduct->getTypeInstance()->getConfigurableAttributesAsArray($configProduct);
    $configProduct->setCanSaveConfigurableAttributes(true);
    $configProduct->setConfigurableAttributesData($configurableAttributesData);
    $configurableProductsData = array();
    $variants = array();
    foreach ($simpleProducts as $i => $simple) {
        $variants[$i]['color_id'] = (int) $simple->getXeColor();
        $variants[$i]['size_id'] = (int) $simple->getXeSize();
        $colors[] = (int) $simple->getXeColor();
        $productData = array(
            'label' => $simple->getAttributeText($attributeColorCode),
            'attribute_id' => $attribute_colorid,
            'value_index' => (int) $simple->getColor(),
            'is_percent' => 0,
            'pricing_value' => $simple->getPrice(),
        );
        $configurableProductsData[$simple->getId()] = $productData;
        $configurableAttributesData[0]['values'][] = $productData;
        $productData = array(
            'label' => $simple->getAttributeText($attributeSizeCode),
            'attribute_id' => $attribute_sizeid,
            'value_index' => (int) $simple->getSize(),
            'is_percent' => 0,
            'pricing_value' => $simple->getPrice(),
        );
        $configurableProductsData[$simple->getId()] = $productData;
        $configurableAttributesData[1]['values'][] = $productData;
    }
    $configProduct->setConfigurableProductsData($configurableProductsData);
    $attributeModel = $objectManager->create('Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute');
    $position = 0;
    $attributes = array($attribute_colorid, $attribute_sizeid);
    foreach ($attributes as $attributeId) {
        $data = array('attribute_id' => $attributeId, 'product_id' => $confId, 'position' => $position);
        $position++;
        $attributeModel->setData($data);
    }
    $configProduct->setAffectConfigurableProductAttributes($attribute_set_id);
    $objectManager->create('Magento\ConfigurableProduct\Model\Product\Type\Configurable')->setUsedProductAttributeIds($attributes, $configProduct);
    $configProduct->setNewVariationsAttributeSetId($attribute_set_id);
    $configProduct->setAssociatedProductIds(array($childIds));
    $configProduct->setCanSaveConfigurableAttributes(true);
    $configProduct->save();
}
function _save_image($img, $objectManager)
{
    $imageFilename = basename($img);
    $image_type = substr(strrchr($imageFilename, "."), 1);
    $filename = md5($img . strtotime('now')) . '.' . $image_type;
    $mediaDir = $objectManager->get('Magento\Framework\App\Filesystem\DirectoryList')->getPath('media');
    if (!file_exists($mediaDir)) {
        mkdir($mediaDir, 0777, true);
    } else {
        chmod($mediaDir, 0777);
    }

    $filepath = $mediaDir . '/' . $filename;
    file_put_contents($filepath, file_get_contents(trim($img)));
    return $filepath;
}

header('Location: ' . $redirect_path . '?action=soap');
