<?php

namespace Html5design\Cedapi\Api;

interface ProductInterface
{

    /**
     *
     * @api
     * @param int $store.
     * @return string The all category in a json format.
     */
    public function getCategories($store);
    /**
     * Get sub categories
     *
     * @api
     * @param int $catId.
     * @return string sub category in a json format.
     */
    public function getsubCategories($catId);
    /**
     * Get size array.
     *
     * @api
     * @param int $store.
     * @param string $size.
     * @return string all size in a json format.
     */
    public function getSizeArr($store, $size);
    /**
     * Get color array
     *
     * @api
     * @param string|null $lastLoaded.
     * @param string $loadCount.
     * @param string $oldConfId.
     * @param string $color.
     * @return string color in a json format.
     */
    public function getColorArr($lastLoaded, $loadCount, $oldConfId, $color);
    /**
     *
     * @api
     * @param int $productId.
     * @param int $store.
     * @return string category id in a json format.
     */
    public function getCategoriesByProduct($productId, $store);

    /**
     *
     * @return string $version.
     */
    public function storeVersion();

    /**
     *
     * @param int $storeId
     * @return string of refIdCartArr and duration
     *
     */
    public function getLiveQuoteRefIds($storeId);

    /**
     *
     * @api
     * @param int $store.
     * @return string enabled/disabled.
     */
    public function checkDesignerTool($store);
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
     * @param string $color.
     * @param string $size.
     * @return string The all products in a json format.
     */
    public function getAllProducts($filters, $categoryid, $searchstring, $store, $range, $loadVariants, $offset, $limit, $preDecorated, $color, $size);
    /**
     *
     * @api
     * @param int $productId.
     * @param int $store.
     * @param int $attributes.
     * @param int $configPid.
     * @param string $color.
     * @param string $size.
     * @return string The simple products in a json format.
     */
    public function getSimpleProduct($productId, $store, $attributes, $configPid, $color, $size);
    /**
     *
     * @api
     * @param int $productId.
     * @param int $store.
     * @param int $simpleProductId.
     * @param string $color.
     * @param string $size.
     * @return string size and quantity in a json format.
     */
    public function getSizeAndQuantity($productId, $store, $simpleProductId, $color, $size);
    /**
     *
     * @api
     * @param int $productId.
     * @param int $store.
     * @param int $simpleProductId.
     * @param string $color.
     * @param string $size.
     * @return string size and quantity in a json format.
     */
    public function getSizeVariants($productId, $store, $simpleProductId, $color, $size);
    /**
     *
     * @api
     * @param int $confId.
     * @param int $start.
     * @param int $limit.
     * @param int $store.
     * @param int $offset.
     * @param string $color.
     * @param string $size.
     * @return string variants in a json format.
     */
    public function getVariants($confId, $start, $limit, $store, $offset, $color, $size);
    /**
     *
     * @api
     * @param string $colorname.
     * @param int $store.
     * @param string $color.
     * @return string options in a json format.
     */
    public function addAttributeColorOptionValue($colorname, $store, $color);
    /**
     *
     * @api
     * @param int $optionId.
     * @param string $colorname.
     * @param int $store.
     * @param string $color.
     * @return string options in a json format.
     */
    public function editAttributeColorOptionValue($optionId, $colorname, $store, $color);
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
     * @param string $color.
     * @param string $size.
     * @param string $attrSet.
     * @return string response in a json format.
     */
    public function addTemplateProducts($store, $data, $configFile, $oldConfId, $varColor, $varSize, $color, $size, $attrSet);
    /**
     *
     *date created 17-08-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *Check Sku exist or not
     * @param string $confId
     * @param int $store
     * @return string sku in json format.
     */
    public function checkDuplicateSku($sku_arr, $store);
    /**
     *
     *date created 23-08-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *Get Product data of a simple product
     * @param string $configId.
     * @param string $colorId.
     * @param string $sizeId.
     * @param string $qty.
     * @param string $color.
     * @param string $size.
     * @return string sku in json format.
     */
    public function getProductInfo($configId, $colorId, $sizeId, $qty, $color, $size);
    /**
     *
     *date created 23-08-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *Get Product data of a simple product
     * @param string $configId.
     * @param string $sizeId.
     * @param string $colorId.
     * @param string $color.
     * @param string $size.
     * @return string id in json format.
     */
    public function getSimpleProductId($configId, $sizeId, $colorId, $color, $size);
}
