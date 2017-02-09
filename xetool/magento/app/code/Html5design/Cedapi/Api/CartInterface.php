<?php

namespace Html5design\Cedapi\Api;

interface CartInterface
{
    /**
     *
     * @api
     * @param int $from.
     * @param int $to.
     * @param int $store.
     * @return string The all products in a json format.
     */
    public function getOrdersGraph($from, $to, $store);

    /**
     *
     * @api
     * @param int $lastOrderId.
     * @param int $store.
     * @param int $range.
     * @param int $fromDate.
     * @param int $toDate.
     * @return string The all products in a json format.
     */
    public function getOrders($lastOrderId, $store, $range, $fromDate, $toDate);

    /**
     *
     * @api
     * @param int $orderId,
     * @param int $store.
     * @return string The all products in a json format.
     */
    public function updateItemStatus($orderId, $store);

    /**
     *
     * @api
     * @param int $orderId.
     * @param int $store.
     * @return string The all products in a json format.
     */
    public function getOrderDetails($orderId, $store);

    /**
     *
     * @api
     * @param int $quoteId.
     * @param int $store.
     * @param string $productsData.
     * @param string $action.
     * @return string The all products in a json format.
     */
    public function addToCart($quoteId, $store, $productsData, $action);

    /**
     * @api
     * @param int $quoteId
     * @param int $store
     * @param int $customerId
     * @return string No of cart qty.
     */
    public function getTotalCartItem($quoteId, $store, $customerId);
}
