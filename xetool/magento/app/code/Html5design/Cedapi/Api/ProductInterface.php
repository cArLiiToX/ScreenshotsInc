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
     * @return string all size in a json format.
     */
    public function getSizeArr($store);
    /**
     * Get color array
     *
     * @api
     * @param string|null $lastLoaded.
     * @param string $loadCount.
     * @param string $oldConfId.
     * @return string color in a json format.
     */
    public function getColorArr($lastLoaded, $loadCount, $oldConfId);
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
     * @return string The all products in a json format.
     */
    public function getAllProducts($filters, $categoryid, $searchstring, $store, $range, $loadVariants, $offset, $limit, $preDecorated);
    /**
     *
     * @api
     * @param int $productId.
     * @param int $store.
     * @param int $attributes.
     * @param int $configPid.
     * @return string The simple products in a json format.
     */
    public function getSimpleProduct($productId, $store, $attributes, $configPid);
    /**
     *
     * @api
     * @param int $productId.
     * @param int $store.
     * @param int $simpleProductId.
     * @return string size and quantity in a json format.
     */
    public function getSizeAndQuantity($productId, $store, $simpleProductId);
    /**
     *
     * @api
     * @param int $productId.
     * @param int $store.
     * @param int $simpleProductId.
     * @return string size and quantity in a json format.
     */
    public function getSizeVariants($productId, $store, $simpleProductId);
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
    public function getVariants($confId, $start, $limit, $store, $offset);
    /**
     *
     * @api
     * @param string $colorname.
     * @param int $store.
     * @return string options in a json format.
     */
    public function addAttributeColorOptionValue($colorname, $store);
    /**
     *
     * @api
     * @param int $optionId.
     * @param string $colorname.
     * @param int $store.
     * @return string options in a json format.
     */
    public function editAttributeColorOptionValue($optionId, $colorname, $store);
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
    public function addTemplateProducts($store, $data, $configFile, $oldConfId, $varColor, $varSize);
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
     * @param string $configId
     * @param string $colorId
     * @param string $sizeId
     * @param string $qty
     * @return string sku in json format.
     */
    public function getProductInfo($configId, $colorId, $sizeId, $qty);
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
    public function getSimpleProductId($configId, $sizeId, $colorId);
}
