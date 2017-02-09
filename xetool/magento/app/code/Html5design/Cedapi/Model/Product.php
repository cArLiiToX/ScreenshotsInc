<?php

namespace Html5design\Cedapi\Model;

use Html5design\Cedapi\Api\ProductInterface;

class Product extends \Magento\Framework\Model\AbstractModel implements ProductInterface
{
    public function __construct(
        \Psr\Log\LoggerInterface $_logger,
        \Magento\Eav\Model\Config $_eavConfig,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $_productCollectionFactory,
        \Magento\Catalog\Model\Product $_productModel,
        \Magento\Store\Model\StoreManagerInterface $_storeManager,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $_categoryCollectionFactory,
        \Magento\Catalog\Model\Category $_categoryFactory,
        \Magento\Catalog\Model\CategoryFactory $_categoryProductFactory,
        \Magento\CatalogInventory\Api\StockStateInterface $_stockInterface,
        \Magento\Eav\Setup\EavSetupFactory $_eavSetupFactory,
        \Magento\Tax\Api\TaxCalculationInterface $_taxCalculationService,
        \Magento\Tax\Api\Data\QuoteDetailsInterfaceFactory $_quoteDetailsFactory,
        \Magento\Framework\Module\Manager $_moduleManager,
        \Magento\Eav\Model\Entity\Attribute\Set $_attributeSet,
        \Magento\Framework\ObjectManagerInterface $_objectManager,
        \Magento\Framework\App\ProductMetadata $_version,
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $_stockItemRepository,
        \Magento\Indexer\Model\IndexerFactory $_indexerFactory,
        \Magento\Indexer\Model\Indexer\CollectionFactory $_indexerCollectionFactory
    ) {
        $this->_logger = $_logger;
        $this->_eavConfig = $_eavConfig;
        $this->_productCollectionFactory = $_productCollectionFactory;
        $this->_productModel = $_productModel;
        $this->_stockInterface = $_stockInterface;
        $this->_categoryCollectionFactory = $_categoryCollectionFactory;
        $this->_storeManager = $_storeManager;
        $this->_categoryFactory = $_categoryFactory;
        $this->_categoryProductFactory = $_categoryProductFactory;
        $this->_eavSetupFactory = $_eavSetupFactory;
        $this->_taxCalculationService = $_taxCalculationService;
        $this->_quoteDetailsFactory = $_quoteDetailsFactory;
        $this->_moduleManager = $_moduleManager;
        $this->_attributeSet = $_attributeSet;
        $this->_objectManager = $_objectManager;
        $this->_version = $_version;
        $this->_stockItemRepository = $_stockItemRepository;
        $this->_indexerFactory = $_indexerFactory;
        $this->_indexerCollectionFactory = $_indexerCollectionFactory;
    }

    /**
     *
     * @return string $vesrion.
     */
    public function storeVersion()
    {
        return $this->_version->getVersion();
    }

    /**
     *
     * @param int $storeId
     * @return string of refIdCartArr and duration
     *
     */
    public function getLiveQuoteRefIds($storeId)
    {
        //$res = array('refIdCartArr' => array(),'duration' => 2592000);//30 days in sec
        return array();
    }

    /**
     *
     * @api
     * @param int $store.
     * @return string enabled/disabled.
     */
    public function checkDesignerTool($store)
    {
        $moduleName = "Html5design_Cedapi";
        $module = $this->_moduleManager->isEnabled($moduleName);
        return ($module) ? 'Enabled' : 'Disabled';
    }

    /**
     *
     * @api
     * @param int $store.
     * @return string The all category in a json format.
     */
    public function getCategories($store)
    {
        $store = 1;
        $categories = array();
        $rootCategoryId = $this->_storeManager->getStore($store)->getRootCategoryId();
        $collection = $this->_categoryCollectionFactory->create();
        $collection->addAttributeToSelect('name', 'id')
            ->addIsActiveFilter(true)
            ->addAttributeToFilter('path', array('like' => "1/{$rootCategoryId}/%"))
            ->addLevelFilter(2);
        foreach ($collection as $category) {
            $categories[] = array(
                'id' => $category->getId(),
                'name' => $category->getName(),
            );
        }
        return json_encode(array('categories' => $categories));
    }

    /**
     * Get sub categories
     *
     * @api
     * @param int $catId.
     * @return string sub category in a json format.
     */
    public function getsubCategories($catId)
    {
        $cat = $this->_categoryFactory->load($catId);
        $subcats = $cat->getChildren();
        $subcategories = array();
        foreach (explode(',', $subcats) as $subCatid) {
            $_subCategory = $this->_categoryFactory->load($subCatid);
            if ($_subCategory->getIsActive()) {
                $subcategories[] = array('id' => $_subCategory->getId(), 'name' => $_subCategory->getName());
            }
            $subCat = $this->_categoryFactory->load($_subCategory->getId());
            $subSubcats = $subCat->getChildren();
            if (count($subSubcats) > 0) {
                foreach (explode(',', $subSubcats) as $subSubcatid) {
                    $_subSubCategory = $this->_categoryFactory->load($subSubcatid);
                    if ($_subSubCategory->getIsActive()) {
                        $subcategories[] = array('id' => $_subSubCategory->getId(), 'name' => $_subSubCategory->getName());
                    }
                }
            }
            $subsubCat = $this->_categoryFactory->load($_subSubCategory->getId());
            $subSubSubcats = $subsubCat->getChildren();
            if (count($subSubSubcats) > 0) {
                foreach (explode(',', $subSubSubcats) as $subSubSubcatid) {
                    $_subSubSubCategory = $this->_categoryFactory->load($subSubSubcatid);
                    if ($_subSubSubCategory->getIsActive()) {
                        $subcategories[] = array('id' => $_subSubSubCategory->getId(), 'name' => $_subSubSubCategory->getName());
                    }
                }
            }
        }
        return json_encode($subcategories);
    }

    /**
     * Get size array.
     *
     * @api
     * @param int $store.
     * @return string all size in a json format.
     */
    public function getSizeArr($store)
    {
        $attribute = $this->_eavConfig->getAttribute('catalog_product', 'xe_size');
        $optarr = array();
        if ($attribute->usesSource()) {
            $optarr = $attribute->getSource()->getAllOptions(); //array(9=>'L',10=>'XL',11=>'XXL');
            array_shift($optarr);
        }
        $res = json_encode($optarr);
        return $res;
    }

    /**
     * Get color array
     *
     * @api
     * @param string|null $lastLoaded.
     * @param string $loadCount.
     * @param string $oldConfId.
     * @return string color in a json format.
     */
    public function getColorArr($lastLoaded, $loadCount, $oldConfId)
    {
        if ($oldConfId == 0) {
            $attribute = $this->_eavConfig->getAttribute('catalog_product', 'xe_color');
            $optarr = array();
            if ($attribute->usesSource()) {
                $optarr = $attribute->getSource()->getAllOptions();
                array_shift($optarr);
                rsort($optarr);
            }
        } else {
            $product = $this->_productModel->load($oldConfId);
            $simpleCollection = $product->getTypeInstance()->getUsedProducts($product);
            //Get all attribute of old configure product
            if (!empty($simpleCollection)) {
                $k = 0;
                $temp = array();
                foreach ($simpleCollection as $simple) {
                    $attr = $simple->getResource()->getAttribute("xe_color");
                    if ($attr->usesSource()) {
                        $attrId = $color_id = $attr->getSource()->getOptionId($simple->getAttributeText('xe_color'));
                        if (!in_array($attrId, $temp)) {
                            $optarr[$k]['value'] = $attrId;
                            $optarr[$k]['label'] = $attr->getSource()->getOptionText($attrId);
                            $k++;
                        }
                        $temp[] = $attrId;
                    }
                }
            }
        }
        $res = $optarr;
        if ($loadCount) {
            $res = array_slice($optarr, (int) $lastLoaded, $loadCount);
        }
        return json_encode($res);
    }

    /**
     *
     * @api
     * @param int $productId.
     * @param int $store.
     * @return string category id in a json format.
     */
    public function getCategoriesByProduct($productId, $store)
    {
        $product = $this->_productModel->load($productId);
        $currentCatIds = $product->getCategoryIds();
        return json_encode($currentCatIds);
    }

    /**
     *
     * @api
     * @param int $filters.
     * @param int $categoryid.
     * @param string $searchstring.
     * @param int $store.
     * @param int $range.
     * @param int $loadVariants.
     * @param int $offset.
     * @param int $limit.
     * @param int $preDecorated.
     * @return string The all products in a json format.
     */
    public function getAllProducts($filters, $categoryid, $searchstring, $store, $range, $loadVariants, $offset, $limit, $preDecorated)
    {
        $xe_colorId = $this->_eavConfig->getAttribute('catalog_product', 'xe_color')->getId();
        $xe_sizeId = $this->_eavConfig->getAttribute('catalog_product', 'xe_size')->getId();
        $category = $this->_categoryProductFactory->create()->load($categoryid);
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        if (!$preDecorated) {
            if ($categoryid && $categoryid != '') {
                $collection = $this->_productCollectionFactory->create()
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter('type_id', 'configurable')
                    ->addAttributeToFilter('xe_is_designer', 1)
                    ->addStoreFilter($store)
                    ->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
                    ->setPageSize($limit)
                    ->addCategoryFilter($category)
                    ->addAttributeToFilter('name', array('like' => '%' . $searchstring . '%'))
                    ->addAttributeToFilter('xe_is_template', array(array('null' => true), array('neq' => 1)), 'left')
                    ->setCurPage($offset)
                    ->load();
            }else{
                $collection = $this->_productCollectionFactory->create()
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter('type_id', 'configurable')
                    ->addAttributeToFilter('xe_is_designer', 1)
                    ->addStoreFilter($store)
                    ->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
                    ->setPageSize($limit)
                    ->addAttributeToFilter('name', array('like' => '%' . $searchstring . '%'))
                    ->addAttributeToFilter('xe_is_template', array(array('null' => true), array('neq' => 1)), 'left')
                    ->setCurPage($offset)
                    ->load();
            }
        } else{
            if ($categoryid && $categoryid != '') {
                $collection = $this->_productCollectionFactory->create()
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter('type_id', 'configurable')
                    ->addAttributeToFilter('xe_is_designer', 1)
                    ->addStoreFilter($store)
                    ->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
                    ->setPageSize($limit)
                    ->addCategoryFilter($category)
                    ->addAttributeToFilter('name', array('like' => '%' . $searchstring . '%'))
                    ->setCurPage($offset)
                    ->load();
            }else{
                $collection = $this->_productCollectionFactory->create()
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter('type_id', 'configurable')
                    ->addAttributeToFilter('xe_is_designer', 1)
                    ->addStoreFilter($store)
                    ->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
                    ->setPageSize($limit)
                    ->addAttributeToFilter('name', array('like' => '%' . $searchstring . '%'))
                    ->setCurPage($offset)
                    ->load();
            }
        }
        $pages = $collection->getLastPageNumber();
        $length = 0;
        $products = array();
        if (!empty($collection) && $offset <= $pages) {
            $length = $collection->getSize();
            $counter = 0;
            foreach ($collection as $productData) {
                $product1 = $productData->getId();
                $product = $this->_productModel->load($product1);
                $simpleProductColl = $productData->getTypeInstance()->getUsedProducts($productData);
                foreach ($simpleProductColl as $productColl) {
                    $price = $productColl->getPrice();
                }
                $img = (string) $baseUrl . 'pub/media/catalog/product/cache/1/thumbnail/88x110/beff4985b56e3afdbeabfc89641a4582' . $productData->getImage();
                $products[$counter] = array(
                    'id' => $productData->getId(),
                    'name' => $productData->getName(),
                    'description' => strip_tags($productData->getDescription()),
                    'price' => $price,
                    'thumbnail' => (string) $img,
                    'category' => $productData->getCategoryIds(),
                    'store' => $productData->getStoreIds(),
                );
                $counter++;
            }
        }
        return json_encode(array('product' => $products, 'count' => $length));
    }

    /**
     *
     * @api
     * @param int $productId.
     * @param int $store.
     * @param int $attributes.
     * @param int $configPid.
     * @return string The simple products in a json format.
     */
    public function getSimpleProduct($productId, $store, $attributes, $configPid)
    {
        $configPname = '';
        $simplePid = '';
        $images = array();
        $thumbs = array();
        $labels = array();
        $result = array();
        $product = $this->_productModel->load($productId);
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        if ($product->getTypeId() == 'configurable') {
            $configPname = $product->getName();
            $configPid = $product->getId();
            $collection = $product->getTypeInstance()->getUsedProducts($product);
            foreach ($collection as $productColl) {
                $simplePid[] = $productColl->getId();
            }
            $simpleProduct = $this->_productModel->load($simplePid[0]);
        } else {
            if ($configPid <= 0) {
                $simplePid = $product->getId();
                $configPids = $this->_productCollectionFactory->create('Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable')->getParentIdsByChild($simplePid);
                $configPid = $configPids[0];
            }
            $confProduct = $this->_productModel->load($configPid);
            $configPname = $confProduct->getName();
            $simpleProduct = $this->_productModel->load($productId);
        }
        $attr = $this->_eavConfig->getAttribute('catalog_product', 'xe_is_template');
        if (null === $attr->getId()) {
            $preDecorated = 0;
        } else {
            $preDecorated = boolval($product->getData("xe_is_template"));
        }
        if ($simpleProduct) {
            $qty = $this->_stockInterface->getStockQty($simpleProduct->getId(), $simpleProduct->getStore()->getWebsiteId());
            $productImages = $simpleProduct->getMediaGalleryImages()->setOrder('position', 'ASC');
            $productImagesLength = $productImages->getSize();
            if ($productImagesLength > 0) {
                foreach ($productImages as $productImage) {
                    $curImage = $productImage->getUrl();
                    $curThumb = (string) $baseUrl . 'pub/media/catalog/product/cache/1/thumbnail/88x110/beff4985b56e3afdbeabfc89641a4582' . $productImage->getFile();
                    array_push($images, $curImage);
                    array_push($thumbs, $curThumb);
                    array_push($labels, $productImage->getLabel());
                }
            }
            $quoteDetailsObject = $this->_quoteDetailsFactory->create();
            $taxDetails = $this->_taxCalculationService
                ->calculateTax($quoteDetailsObject, $product->getStoreId());
            if ($taxDetails->getTaxAmount() > 0 && $taxDetails->getSubtotal() > 0) {
                $percent = ($taxDetails->getTaxAmount() / $taxDetails->getSubtotal()) * 100;
            } else {
                $taxClassId = $product->getTaxClassId();
                $percent = 0;
            }
            $attr = $simpleProduct->getResource()->getAttribute("xe_color");
            $attr1 = $simpleProduct->getResource()->getAttribute("xe_size");
            $productFinalPrice = $simpleProduct->getFinalPrice();
            $tierPrices = $simpleProduct->getPriceInfo()->getPrice('tier_price')->getTierPriceList();
            $tier = array();
            if (is_array($tierPrices)) {
                foreach ($tierPrices as $k => $price) {
                    $tier[$k]['tierQty'] = (int) $price['price_qty'];
                    $tier[$k]['percentage'] = round(100 - $price['website_price'] / $productFinalPrice * 100);
                    $tier[$k]['tierPrice'] = number_format($price['website_price'], 2);
                }
            }
            $attributes = $simpleProduct->getAttributes();
            $result = array(
                'pid' => $configPid,
                'pidtype' => $simpleProduct->getTypeId(),
                'pname' => $configPname,
                'shortdescription' => $simpleProduct->getShortDescription(),
                'category' => $simpleProduct->getCategoryIds(),
                'pvid' => $simpleProduct->getId(),
                'pvname' => $simpleProduct->getName(),
                'xecolor' => $simpleProduct->getAttributeText('xe_color'),
                'xesize' => $simpleProduct->getAttributeText('xe_size'),
                'xe_color_id' => $attr->getSource()->getOptionId($simpleProduct->getAttributeText('xe_color')),
                'xe_size_id' => $attr1->getSource()->getOptionId($simpleProduct->getAttributeText('xe_size')),
                'quanntity' => (int) $qty,
                'price' => $simpleProduct->getFinalPrice(),
                'tierPrices' => $tier,
                'taxrate' => $percent,
                'thumbsides' => $thumbs,
                'sides' => $images,
                'isPreDecorated' => $preDecorated,
                'labels' => $labels,
            );
            foreach ($attributes as $attribute) {
                $attrCode = $attribute->getAttributeCode();
                $attrData = $attribute->getData();
                if ($attribute->getIsVisibleOnFront()) {
                    $attr = $simpleProduct->getResource()->getAttribute($attrCode);
                    $attrText = $simpleProduct->getAttributeText($attrCode);
                    $attrId = $attr->getSource()->getOptionId($simpleProduct->getAttributeText($attrCode));
                    if ($attrText) {
                        $result['attributes'][$attrCode] = $attrText;
                        $result['attributes'][$attrCode . "_id"] = $attrId;
                    }
                }
            }
        }
        return json_encode($result);
    }

    /**
     *
     * @api
     * @param int $productId.
     * @param int $store.
     * @param int $simpleProductId.
     * @return string size and quantity in a json format.
     */
    public function getSizeAndQuantity($productId, $store, $simpleProductId)
    {
        $variant = array();
        $optarr = array();
        $product = $this->_productModel->load($productId);
        $collection = $product->getTypeInstance()->getUsedProducts($product);
        $childProduct = $this->_productModel->load($simpleProductId);
        $variantColor = $childProduct->getAttributeText('xe_color');
        $checkSizeId = array();
        foreach ($collection as $productColl) {
            if ($productColl->getAttributeText('xe_color') == $variantColor) {
                $color = $productColl->getAttributeText('xe_color');
                $size = $productColl->getAttributeText('xe_size');
                $attr = $productColl->getResource()->getAttribute("xe_color");
                $attr1 = $productColl->getResource()->getAttribute("xe_size");
                if ($attr->usesSource()) {
                    $color_id = $attr->getSource()->getOptionId($color);
                    $size_id = $attr1->getSource()->getOptionId($size);
                }
                $productFinalPrice = $productColl->getFinalPrice();
                $tierPrices = $productColl->getPriceInfo()->getPrice('tier_price')->getTierPriceList();
                $tier = array();
                if (is_array($tierPrices)) {
                    foreach ($tierPrices as $k => $price) {
                        $tier[$k]['tierQty'] = (int) $price['price_qty'];
                        $tier[$k]['percentage'] = round(100 - $price['website_price'] / $productFinalPrice * 100);
                        $tier[$k]['tierPrice'] = number_format($price['website_price'], 2);
                    }
                }
                if (!in_array($size_id, $checkSizeId)) {
                    $attributes = $productColl->getAttributes();
                    $extraAttr = array();
                    foreach ($attributes as $attribute) {
                        $attrCode = $attribute->getAttributeCode();
                        $attrData = $attribute->getData();
                        if ($attribute->getIsVisibleOnFront()) {
                            $attr = $productColl->getResource()->getAttribute($attrCode);
                            $attrText = $productColl->getAttributeText($attrCode);
                            $attrId = $attr->getSource()->getOptionId($productColl->getAttributeText($attrCode));
                            if ($attrText) {
                                $extraAttr[$attrCode] = $attrText;
                                $extraAttr[$attrCode . "_id"] = $attrId;
                            }
                        }
                    }
                    $productStock = $this->getStockItem($productColl->getId());
                    $qty = $productStock->getQty();
                    $minimumQuantity = $productStock->getMinSaleQty();
                    $variant[] = array(
                        'simpleProductId' => $productColl->getId(),
                        'xe_color' => $color,
                        'xe_size' => $size,
                        'xe_color_id' => $color_id,
                        'xe_size_id' => $size_id,
                        'quantity' => (int) $qty,
                        'minQuantity' => (int) $minimumQuantity,
                        'price' => $productColl->getPrice(),
                        'tierPrices' => $tier,
                        'attributes' => $extraAttr,
                    );
                    $checkSizeId[] = $size_id;
                }
            }
        }
        $result = $variant;
        return json_encode(array('quantities' => $result));
    }

    /**
     *
     * @api
     * @param int $productId.
     * @param int $store.
     * @param int $simpleProductId.
     * @return string size and quantity in a json format.
     */
    public function getSizeVariants($productId, $store, $simpleProductId)
    {
        $variant = array();
        $optarr = array();
        $product = $this->_productModel->load($productId);
        $collection = $product->getTypeInstance()->getUsedProducts($product);
        $childProduct = $this->_productModel->load($simpleProductId);
        $checkSizeId = array();
        foreach ($collection as $productColl) {
            $color = $productColl->getAttributeText('xe_color');
            $size = $productColl->getAttributeText('xe_size');
            $attr = $productColl->getResource()->getAttribute("xe_color");
            $attr1 = $productColl->getResource()->getAttribute("xe_size");
            if ($attr->usesSource()) {
                $color_id = $attr->getSource()->getOptionId($color);
                $size_id = $attr1->getSource()->getOptionId($size);
            }
            if (!in_array($size_id, $checkSizeId)) {
                $productFinalPrice = $productColl->getFinalPrice();
                $tierPrices = $productColl->getPriceInfo()->getPrice('tier_price')->getTierPriceList();
                $tier = array();
                if (is_array($tierPrices)) {
                    foreach ($tierPrices as $k => $price) {
                        $tier[$k]['tierQty'] = (int) $price['price_qty'];
                        $tier[$k]['percentage'] = round(100 - $price['website_price'] / $productFinalPrice * 100);
                        $tier[$k]['tierPrice'] = number_format($price['website_price'], 2);
                    }
                }
                $attributes = $productColl->getAttributes();
                $extraAttr = array();
                foreach ($attributes as $attribute) {
                    $attrCode = $attribute->getAttributeCode();
                    $attrData = $attribute->getData();
                    if ($attribute->getIsVisibleOnFront()) {
                        $attr = $productColl->getResource()->getAttribute($attrCode);
                        $attrText = $productColl->getAttributeText($attrCode);
                        $attrId = $attr->getSource()->getOptionId($productColl->getAttributeText($attrCode));
                        if ($attrText) {
                            $extraAttr[$attrCode] = $attrText;
                            $extraAttr[$attrCode . "_id"] = $attrId;
                        }
                    }
                }
                $productStock = $this->getStockItem($productColl->getId());
                $qty = $productStock->getQty();
                $minimumQuantity = $productStock->getMinSaleQty();
                $variant[] = array(
                    'simpleProductId' => $productColl->getId(),
                    'xe_color' => $color,
                    'xe_size' => $size,
                    'xe_color_id' => $color_id,
                    'xe_size_id' => $size_id,
                    'quantity' => (int) $qty,
                    'minQuantity' => (int) $minimumQuantity,
                    'price' => $productColl->getPrice(),
                    'tierPrices' => $tier,
                    'attributes' => $extraAttr,
                );
                $checkSizeId[] = $size_id;
            }
        }
        $result = $variant;
        return json_encode(array('quantities' => $result));
    }

    /**
     *
     * @api
     * @param int $confId.
     * @param int $start.
     * @param int $limit.
     * @param int $store.
     * @param int $offset.
     * @return string variants in a json format.
     */
    public function getVariants($confId, $start, $limit, $store, $offset)
    {
        $product = $this->_productModel->load($confId);
        $simpleProducts = $product->getTypeInstance()->getUsedProducts($product);
        foreach ($simpleProducts as $child) {
            $ids[] = $child->getId();
        }
        $simpleCollection = $this->_productCollectionFactory->create()
            ->addIdFilter($ids)
            ->addAttributeToSelect('*')
            ->addStoreFilter($store)
            ->groupByAttribute('xe_color')
            ->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
            ->setPageSize($limit)
            ->setCurPage($offset);
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        $categoryIds = $product->getCategoryIds();
        $curVariant = array();
        $temp = array();
        $pages = $simpleCollection->getLastPageNumber();
        $productCount = 0;
        if (!empty($simpleCollection) && $offset <= $pages) {
            $productCount = $simpleCollection->getSize();
            foreach ($simpleCollection as $child) {
                $qty = $this->_stockInterface->getStockQty($child->getId(), $child->getStore()->getWebsiteId());
                if ($qty > 0) {
                    $productFinalPrice = $child->getFinalPrice();
                    $productPrice = $child->getPriceInfo()->getPrice('final_price')->getAmount()->getBaseAmount();
                    $tax = $productPrice - $productFinalPrice;
                    $colorId = $child->getXe_color();
                    if (!in_array($colorId, $temp)) {
                        $curVariant[] = array(
                            'id' => $child->getId(),
                            'name' => $child->getName(),
                            'description' => strip_tags($child->getDescription()),
                            'thumbnail' => (string) $baseUrl . 'pub/media/catalog/product/cache/1/thumbnail/88x110/beff4985b56e3afdbeabfc89641a4582' . $child->getImage(),
                            'price' => $productFinalPrice,
                            'tax' => $tax,
                            'xeColor' => $child->getAttributeText('xe_color'),
                            'xe_color_id' => $colorId,
                            'xe_size_id' => $child->getXe_size(),
                            'colorUrl' => $colorId . ".png",
                            'ConfcatIds' => $categoryIds,
                        );
                    }
                    $temp[] = $colorId;
                }
            }
        }
        return json_encode(array('variants' => $curVariant, 'count' => $productCount));
    }

    /**
     *
     * @api
     * @param string $colorname.
     * @param int $store.
     * @return string options in a json format.
     */
    public function addAttributeColorOptionValue($colorname, $store)
    {
        $argValue = $colorname;
        $attrId = $this->_eavConfig->getAttribute('catalog_product', 'xe_color')->getId();
        $option['attribute_id'] = $attrId;
        $option['value']['attribute_value'][0] = $argValue;
        $eavSetup = $this->_eavSetupFactory->create();
        $eavSetup->addAttributeOption($option);
        $attribute = $this->_eavConfig->getAttribute('catalog_product', 'xe_color');
        $source = $attribute->getSource();
        $options = $source->getAllOptions();
        foreach ($options as $optionValue) {
            if ($argValue == $optionValue["label"]) {
                $value = $optionValue["value"];
            }
        }
        $optionss['attribute_id'] = $value;
        $optionss['attribute_value'] = $argValue;
        $optionss['status'] = 'success';
        return json_encode($optionss);
    }

    /**
     *
     * @api
     * @param int $optionId.
     * @param string $colorname.
     * @param int $store.
     * @return string options in a json format.
     */
    public function editAttributeColorOptionValue($optionId, $colorname, $store)
    {
        $argValue = $colorname;
        $attrId = $this->_eavConfig->getAttribute('catalog_product', 'xe_color')->getId();
        $attribute = $this->_eavConfig->getAttribute('catalog_product', 'xe_color');
        $attribute->load($attrId);
        $data = array();
        $values = array(
            $optionId => array(
                0 => $colorname, //0 is current store id, Apple is the new label for the option
            ),
        );
        $data['option']['value'] = $values;
        try {
            $attribute->addData($data);
            $attribute->save();
            $source = $attribute->getSource();
            $options = $source->getAllOptions();
            foreach ($options as $optionValue) {
                if ($argValue == $optionValue["label"]) {
                    $label = $optionValue["label"];
                }
            }
            $optionss['attribute_id'] = $optionId;
            $optionss['attribute_value'] = $label;
            $optionss['status'] = 'success';
            return json_encode($optionss);
        } catch (Exception $e) {
            $session->addError($e->getMessage());
            $session->setAttributeData($data);
            return json_encode(array('isFault' => 1, 'faultMessage' => $e->getMessage()));
        }
    }

    /**
     *
     *date created 27-08-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *Add Pre-decorated product as product
     * @param int $store.
     * @param string $data.
     * @param string $configFile.
     * @param int $oldConfId.
     * @param int $varColor.
     * @param string $varSize.
     * @return string response in a json format.
     */
    public function addTemplateProducts($store, $data, $configFile, $oldConfId, $varColor, $varSize)
    {
        $data = json_decode($data, true);
        $configFile = json_decode($configFile, true);
        $varSize = json_decode($varSize, true);
        $attribute_set_name = 'inkXE';
        $data['attribute_set_name'] = $attribute_set_name;
        $attributeColorCode = 'xe_color';
        $data['attributeColorCode'] = $attributeColorCode;
        $attributeSizeCode = 'xe_size';
        $data['attributeSizeCode'] = $attributeSizeCode;
        $attribute_set_id = $this->_attributeSet->load($attribute_set_name, 'attribute_set_name')->getAttributeSetId();
        $data['attribute_set_id'] = $attribute_set_id;
        $attribute_colorid = $this->_eavConfig->getAttribute('catalog_product', $attributeColorCode)->getData('attribute_id');
        $data['attribute_colorid'] = $attribute_colorid;
        $attribute_sizeid = $this->_eavConfig->getAttribute('catalog_product', $attributeSizeCode)->getData('attribute_id');
        $data['attribute_sizeid'] = $attribute_sizeid;
        $mediaAttribute = array('thumbnail', 'small_image', 'image', ' swatch_image');
        $data['mediaAttribute'] = $mediaAttribute;
        $filepath = $this->_storeManager->getStore()->getBaseUrl('media') . 'import';
        $data['filepath'] = $filepath;
        $data['storeId'] = $store;
        $simpleProductName = $data['product_name'];
        $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        $data['websiteId'] = array($websiteId);
        if (empty($data['cat_id'])) {
            $rootcatId = $this->_storeManager->getStore($store)->getRootCategoryId();
            $data['cat_id'] = array($rootcatId);
        }
        //Create new configure product
        $product1 = $this->_productModel->load($oldConfId);
        $data['weight'] = $product1->getWeight();
        $confId = ($data['conf_id'] == 0) ? $this->createTemplateConfigurableProduct($data, $configFile) : $data['conf_id'];
        $simpleCollection = $product1->getTypeInstance()->getUsedProducts($product1);
        if (!empty($simpleCollection)) {
            $childIds = array();
            $temp = array();
            foreach ($simpleCollection as $simple) {
                //Get simple product data
                $prodName = $simple->getData();
                $rand = rand(1, 9999);
                $weight = $simple->getWeight();
                $description = $simple->getDescription();
                $short_description = $simple->getShortDescription();
                $oldSKU = $simple->getSku();
                // $qty = $this->_stockInterface->getStockQty($simple->getId(), $simple->getStore()->getWebsiteId());
                $attr = $simple->getResource()->getAttribute("xe_color");
                $attr1 = $simple->getResource()->getAttribute("xe_size");
                $color_id = $attr->getSource()->getOptionId($simple->getAttributeText('xe_color'));
                $xe_size_id = $attr1->getSource()->getOptionId($simple->getAttributeText('xe_size'));
                $color = $simple->getAttributeText('xe_color');
                $data['weight'] = $weight;
                $data['description'] = $description;
                $data['short_description'] = $short_description;
                $data['color_id'] = $color_id;
                //get product ID
                $product = $this->_productModel->load($simple->getId());
                if (!in_array($color_id, $temp)) {
                    if ($varColor == $color_id) {
                        foreach ($varSize as $size_id) {
                            $size = $attr1->getSource()->getOptionText($size_id);
                            $oldSKU = $oldSKU . $rand;
                            $data['sku'] = $oldSKU;
                            $data['size_id'] = $size_id;
                            $data['product_name'] = $simpleProductName . '-' . $size . '-' . $color;
                            $childIds[] = $this->createTemplateSimpleProduct($data, $product);
                        }
                    }
                }
                $temp[] = $color_id;
            }
        }
        $response = array();
        $variants = array();
        if ($confId && !empty($childIds)) {
            $assigned_splist = $this->fetchSimpleProductOfConfigurable($confId);
            $assigned_splist = json_decode($assigned_splist);
            if (is_array($assigned_splist) && !empty($assigned_splist)) {
                $childIds = array_values(array_merge($assigned_splist, $childIds));
            }
            //associate new simple product with new configure product
            $req = $this->associateSimpleToConfigurableProduct($confId, $childIds, $attributeColorCode, $attributeSizeCode, $attribute_colorid, $attribute_sizeid, $attribute_set_id);
            $res = array();
            foreach ($req['variants'] as $v) {
                $x = array_search($v['color_id'], $req['colors']);
                if ($x) {
                    $res[$x]['color_id'] = $v['color_id'];
                    $res[$x]['size_id'][] = $v['size_id'];
                } else {
                    $res[$x]['color_id'] = $v['color_id'];
                    $res[$x]['size_id'][] = $v['size_id'];
                }
            }
            $response['conf_id'] = $confId;
            $response['old_conf_id'] = $oldConfId;
            $response['variants'] = array_values($res);
        }
        $indexerCollection = $this->_indexerCollectionFactory->create();
        $indexerIds = $indexerCollection->getAllIds();
        foreach ($indexerIds as $indexerId) {
            $indexer = $this->_indexerFactory->create();
            $indexer->load($indexerId);
            $indexer->reindexAll();
        }
        return json_encode($response);
    }

    /**
     *
     *date created 23-07-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *Create configurable product
     * @param string $data
     * @param string $configFile
     * @return int created config id.
     */
    public function createTemplateConfigurableProduct($data, $configFile)
    {
        extract($data);
        $c_product = $this->_productModel->getIdBySku($data['sku']);
        if (empty($c_product)) {
            $configProduct = $this->_objectManager->create('Magento\Catalog\Model\Product');
            try {
                $configProduct
                    ->setStoreId($data['storeId']) //you can set data in store scope
                    ->setWebsiteIds($data['websiteId']) //website ID the product is assigned to, as an array
                    ->setAttributeSetId($data['attribute_set_id']) //ID of a attribute set named 'default'
                    ->setTypeId('configurable') //product type
                    ->setCreatedAt(strtotime('now')) //product creation time
                    ->setSku($data['sku']) //SKU
                    ->setName($data['product_name']) //product name
                    ->setWeight($data['weight'])
                    ->setStatus(1) //product status (1 - enabled, 2 - disabled)
                    ->setTaxClassId(0) //tax class (0 - none, 1 - default, 2 - taxable, 4 - shipping)
                    ->setVisibility(4) //catalog and search visibility
                    ->setXeIsDesigner($data['is_customized']) //manufacturer id
                    ->setNewsFromDate('') //product set as new from
                    ->setNewsToDate('') //product set as new to
                    ->setPrice($data['price']) //price in form 11.22
                    ->setSpecialPrice('') //special price in form 11.22
                    ->setSpecialFromDate('') //special price from (MM-DD-YYYY)
                    ->setSpecialToDate('') //special price to (MM-DD-YYYY)
                    ->setMetaTitle($data['product_name'])
                    ->setMetaKeyword('metakeyword')
                    ->setMetaDescription('metadescription')
                    ->setDescription($data['description'])
                    ->setShortDescription($data['short_description'])
                    ->setXeIsTemplate(1);
                if (!empty($configFile)) {
                    $count = 0;
                    foreach ($configFile as $image):
                        $imgUrl = $this->saveImage($image, $this->_objectManager);
                        if ($count == 0):
                            $configProduct->addImageToMediaGallery($imgUrl, $mediaAttribute, true, false);
                        else:
                            $configProduct->addImageToMediaGallery($imgUrl, null, true, false);
                        endif;
                        $count++;
                    endforeach;
                }
                $configProduct->setStockData(array(
                    'use_config_manage_stock' => 0, //'Use config settings' checkbox
                    'manage_stock' => 1, //manage stock
                    //'min_sale_qty' => 1, //Minimum Qty Allowed in Shopping Cart
                    //'max_sale_qty' => 20, //Maximum Qty Allowed in Shopping Cart
                    'is_in_stock' => 1, //Stock Availability
                    'qty' => $data['qty'], //qty
                )
                )
                    ->setCategoryIds($data['cat_id']); //assign product to categories
                $configProduct->save();
                $conf_id1 = $configProduct->getId();
                $product = $configProduct->load($conf_id1);
                $this->_objectManager->create('Magento\ConfigurableProduct\Model\Product\Type\Configurable')->setUsedProductAttributeIds(array($data['attribute_colorid'], $data['attribute_sizeid']), $product);
                $product->setCanSaveConfigurableAttributes(true);
                $product->save();
                $conf_id = $product->getId();
            } catch (Exception $e) {
                $this->_logger->info($e->getMessage());
                $this->_logger->debug($e->getMessage());
            }
        } else {echo 'Duplicate sku';}
        return $conf_id;
    }

    /**
     *
     *date created 23-07-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *Create simple product
     * @param string $data
     * @param int $oldSimpleId
     * @return int created simple id.
     */
    public function createTemplateSimpleProduct($data, $oldSimpleId)
    {
        extract($data);
        $simpleProductArr = array();
        if (isset($data['color_id'])) {
            $simpleProduct = $this->_objectManager->create('Magento\Catalog\Model\Product');
            try {
                $simpleProduct
                //Set all simple product data
                ->setStoreId($data['storeId'])
                    ->setWebsiteIds($data['websiteId'])
                    ->setAttributeSetId($data['attribute_set_id'])
                    ->setTypeId('simple')
                    ->setCreatedAt(strtotime('now'))
                    ->setSku($data['sku'])
                    ->setName($data['product_name'])
                    ->setWeight($data['weight'])
                    ->setStatus(1)
                    ->setTaxClassId(0)
                    ->setVisibility(1)
                    ->setNewsFromDate('')
                    ->setNewsToDate('')
                    ->setPrice($data['price'])
                    ->setSpecialPrice('')
                    ->setSpecialFromDate('')
                    ->setSpecialToDate('')
                    ->setMetaTitle('metatitle')
                    ->setMetaKeyword('metakeyword')
                    ->setMetaDescription('metadescription')
                    ->setDescription($data['description'])
                    ->setShortDescription($data['short_description'])
                    ->setXeColor($data['color_id'])
                    ->setXeSize($data['size_id']);
                $simpleProduct->setStockData(array(
                    'use_config_manage_stock' => 0, //'Use config settings' checkbox
                    'manage_stock' => 1, //manage stock
                    // 'min_sale_qty' => $data['mini_qty'], //Minimum Qty Allowed in Shopping Cart
                    //'max_sale_qty' => 30, //Maximum Qty Allowed in Shopping Cart
                    'is_in_stock' => 1, //Stock Availability
                    'qty' => $data['qty'], //qty
                )
                )
                    ->setCategoryIds($data['cat_id']); //assign product to categories
                if (!empty($oldSimpleId)) {
                    $img = array();
                    foreach ($oldSimpleId->getMediaGalleryImages() as $image) {
                        $imgData = $image->getUrl();
                        $parts = parse_url($imgData);
                        $str = $parts['path'];

                        $str1 = explode('media', $str);
                        $dir = $this->_storeManager->getStore()->getBaseUrl('media');
                        $img[] = $dir . $str1[1];
                    }
                    $count = 0;
                    foreach ($img as $image):
                        $imgUrl = $this->saveImage($image, $this->_objectManager);
                        if ($count == 0):
                            $simpleProduct->addImageToMediaGallery($imgUrl, $mediaAttribute, true, false);
                        else:
                            $simpleProduct->addImageToMediaGallery($imgUrl, null, true, false);
                        endif;
                        $count++;
                    endforeach;
                }
                $simpleProduct->save();
                $simpleProductArr = (int) $simpleProduct->getId();
            } catch (Exception $e) {
                $this->_logger->info($e->getMessage());
                $this->_logger->debug($e->getMessage());
            }
        }
        return $simpleProductArr;
    }

    /**
     *
     *date created 23-07-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *Get simple product of configurable product
     * @param int $confId
     * @return int already associated id.
     */
    public function fetchSimpleProductOfConfigurable($confId)
    {
        $parent = $this->_productModel->load($confId);
        $childProducts = $parent->getTypeInstance()->getChildrenIds($confId, true);
        foreach ($childProducts as $key => $value) {
            $childProduct = $value;
        }
        $childProduct = array_values($childProduct);
        $childProduct = json_encode($childProduct);
        $childProduct = preg_replace('/["]/', '', $childProduct);
        return $childProduct;
    }

    /**
     *
     *date created 23-07-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *Associate simple product to configurable
     * @param int $confId
     * @param int $childIds
     * @param int $attributeColorCode
     * @param int $attributeSizeCode
     * @param int $attribute_colorid
     * @param int $attribute_sizeid
     * @return int $already associated id.
     */
    public function associateSimpleToConfigurableProduct($confId, $childIds, $attributeColorCode, $attributeSizeCode, $attribute_colorid, $attribute_sizeid, $attribute_set_id)
    {
        $configProduct = $this->_productModel->load($confId);
        $simpleProducts = $this->_productCollectionFactory->create()
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
        /* Associate simple product to configurable*/
        $attributeModel = $this->_objectManager->create('Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute');
        $position = 0;
        $attributes = array($attribute_colorid, $attribute_sizeid); // Super Attribute Ids Used To Create Configurable Product
        foreach ($attributes as $attributeId) {
            $data = array('attribute_id' => $attributeId, 'product_id' => $confId, 'position' => $position);
            $position++;
            $attributeModel->setData($data);
        }
        $configProduct->setAffectConfigurableProductAttributes($attribute_set_id);
        $this->_objectManager->create('Magento\ConfigurableProduct\Model\Product\Type\Configurable')->setUsedProductAttributeIds($attributes, $configProduct);
        $configProduct->setNewVariationsAttributeSetId($attribute_set_id);
        $configProduct->setAssociatedProductIds($childIds); // Setting Associated Products
        $configProduct->setCanSaveConfigurableAttributes(true);
        $configProduct->save();
        return array('colors' => array_unique($colors), 'variants' => array_values($variants));
    }

    /**
     *
     *date created 23-07-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *Associate simple product to configurable
     * @param string $confId
     * @return string $filepath.
     */
    public function saveImage($img, $objectManager)
    {
        $imageFilename = basename($img);
        $image_type = substr(strrchr($imageFilename, "."), 1); //find the image extension
        $filename = md5($img . strtotime('now')) . '.' . $image_type; //give a new name, you can modify as per your requirement
        $mediaDir = $objectManager->get('Magento\Framework\App\Filesystem\DirectoryList')->getPath('media'); //E:/xampp/htdocs/magento20_0407/pub/mediaaa4b3115617ae70c17e01bc353c20bee.png
        if (!file_exists($mediaDir)) {
            mkdir($mediaDir, 0777, true);
        } else {
            chmod($mediaDir, 0777);
        }
        $filepath = $mediaDir . '/' . $filename; //path for temp storage folder: pub/media
        file_put_contents($filepath, file_get_contents(trim($img))); //store the image from external url to the temp storage folder
        return $filepath;
    }

    /**
     *
     *date created 17-08-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *Check Sku exist or not
     * @param string $confId
     * @param int $store
     * @return string sku in json format.
     */
    public function checkDuplicateSku($sku_arr, $store)
    {
        $data = $this->_productCollectionFactory->create()
            ->addAttributeToSelect('*')->addFieldToFilter('sku', array('in' => $sku_arr))->getData();
        $exists = array();
        if (!empty($data)) {
            foreach ($data as $v) {
                $exists[] = $v['sku'];
            }
        }
        return json_encode($exists);
    }

    /**
     *
     *date created 23-08-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *Get Product data of a simple product
     * @param string $configId
     * @param string $colorId
     * @param string $sizeId
     * @param string $qty
     * @return string sku in json format.
     */
    public function getProductInfo($configId, $colorId, $sizeId, $qty)
    {
        $nextarr = array();
        $productData = array();
        $productData['qty'] = $qty;
        $productData['id'] = $configId;
        $product = $this->_productModel->load($configId);
        $simpleCollection = $product->getTypeInstance()->getUsedProducts($product);
        if (!empty($simpleCollection)) {
            $data = array();
            foreach ($simpleCollection as $simple) {
                $attributes = $simple->getAttributes();
                $prodName = $simple->getData();
                $product_name = $simple->getName();
                $price = $simple->getPrice();
                $oldSKU = $simple->getSku();
                $attr = $simple->getResource()->getAttribute("xe_color");
                $attr1 = $simple->getResource()->getAttribute("xe_size");
                $simpleColorId = $attr->getSource()->getOptionId($simple->getAttributeText('xe_color'));
                $simpleSizeId = $attr1->getSource()->getOptionId($simple->getAttributeText('xe_size'));
                if ($simpleColorId == $colorId && $simpleSizeId == $sizeId) {
                    // $data['xe_color'] = $simple->getAttributeText('xe_color');
                    // $data['xe_size'] = $simple->getAttributeText('xe_size');
                    // $data['xe_size_id'] = $simpleSizeId;
                    $data['simpleProductId'] = $simple->getId();
                    foreach ($attributes as $attribute) {
                        $attrCode = $attribute->getAttributeCode();
                        $attrData = $attribute->getData();
                        if ($attribute->getIsVisibleOnFront()) {
                            $attr = $simple->getResource()->getAttribute($attrCode);
                            $attrText = $simple->getAttributeText($attrCode);
                            $attrId = $attr->getSource()->getOptionId($simple->getAttributeText($attrCode));
                            if ($attrText) {
                                $data[$attrCode] = $attrText;
                                $data[$attrCode . "_id"] = $attrId;
                            }
                        }
                    }
                }
            }
        }
        $productData['simple_product'] = $data;
        $result[] = $productData;
        return json_encode($result);
    }

    /**
     *
     *date created 23-08-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *Get Product data of a simple product
     * @param string $configId
     * @param string $sizeId
     * @param string $colorId
     * @return string id in json format.
     */
    public function getSimpleProductId($configId, $sizeId, $colorId)
    {
        $product = $this->_productModel->load($configId);
        $simpleCollection = $product->getTypeInstance()->getUsedProducts($product);
        if (!empty($simpleCollection)) {
            $data = array();
            foreach ($simpleCollection as $simple) {
                $attr = $simple->getResource()->getAttribute("xe_color");
                $attr1 = $simple->getResource()->getAttribute("xe_size");
                $simpleColorId = $attr->getSource()->getOptionId($simple->getAttributeText('xe_color'));
                $simpleSizeId = $attr1->getSource()->getOptionId($simple->getAttributeText('xe_size'));
                if ($simpleSizeId == $sizeId && $simpleColorId == $colorId) {
                    $data['simpleProductId'] = $simple->getId();
                }
            }

        }
        return json_encode($data);
    }

    /**
     *
     *date created 07-12-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *Get Stock Item instance
     * @param string $productId
     * @return Instance.
     */
    public function getStockItem($productId)
    {
        return $this->_stockItemRepository->get($productId);
    }
}
