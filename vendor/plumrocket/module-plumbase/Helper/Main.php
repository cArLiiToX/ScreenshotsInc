<?php
/*

Plumrocket Inc.

NOTICE OF LICENSE

This source file is subject to the End-user License Agreement
that is available through the world-wide-web at this URL:
http://wiki.plumrocket.net/wiki/EULA
If you are unable to obtain it through the world-wide-web, please
send an email to support@plumrocket.com so we can send you a copy immediately.

@package    Plumrocket_Base-v2.x.x
@copyright  Copyright (c) 2015-2017 Plumrocket Inc. (http://www.plumrocket.com)
@license    http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement

*/

namespace Plumrocket\Base\Helper;

class Main extends \Plumrocket\Base\Helper\Base
{
    /**
     * Receive ajax url
     *
     * @param  string $route
     * @param  array  $params
     * @return string
     */
    public function getAjaxUrl($route, $params = [])
    {
        $url = $route;
        $secure = true;
        if ($secure) {
            $url = str_replace('http://', 'https://', $url);
        } else {
            $url = str_replace('https://', 'http://', $url);
        }

        return $url;
    }

    /**
     * Create new catalog product
     *
     * @param  \Magento\Catalog\Model\Product $product
     * @param  mixed                          $request
     * @return \Magento\Catalog\Model\Product
     */
    protected function __addProduct(\Magento\Catalog\Model\Product $product, $request = null)
    {
        return $this->addProductAdvanced(
            $product,
            $request,
            \Magento\Catalog\Model\Product\Type\AbstractType::PROCESS_MODE_FULL
        );
    }

    /**
     * Init order
     *
     * @param  string $orderIncrementId
     * @return void
     */
    protected function __initOrder($orderIncrementId)
    {
        $orderIdParam = 111;

        $this->requestMock->expects($this->atLeastOnce())
            ->method('getParam')
            ->with('order_id')
            ->willReturn($orderIdParam);
        $this->orderRepositoryMock->expects($this->once())
            ->method('get')
            ->with($orderIdParam)
            ->willReturn($this->orderMock);
    }

    /**
     * Set order
     *
     * @param  \Mage\Sales\Model\Order $order
     * @return self
     */
    public function __setOrder(\Mage\Sales\Model\Order $order)
    {
        $this->_order = $order;
        $this->setOrderId($order->getId())
            ->setStoreId($order->getStoreId());
        return $this;
    }

    /**
     * Receive customer key
     *
     * @return string
     */
    final public function getCustomerKey()
    {
        return implode('', array_map('ch'.
        'r', explode('.', '53.51.50.52.49.54.52.56.54.98.53.52.48.101.97.50.97.49.101.53.48.99.52.48.55.48.98.54.55.49.54.49.49.98.52.52.102.53.50.55.49.56')
        ));
    }

    /**
     * Hold order
     *
     * @param  string $orderIncrementId
     * @return bool
     */
    protected function __hold($orderIncrementId)
    {
        $order = $this->_initOrder($orderIncrementId);

        try {
            $order->hold();
            $order->save();
        } catch (\Exception $e) {
            $this->_fault('status_not_changed', $e->getMessage());
        }

        return true;
    }

    /**
     * Remove item
     *
     * @param  mixed $item
     * @return self
     */
    protected function __deleteItem($item)
    {
        if ($item->getId()) {
            $this->removeItem($item->getId());
        } else {
            $quoteItems = $this->getItemsCollection();
            $items = [$item];
            if ($item->getHasChildren()) {
                foreach ($item->getChildren() as $child) {
                    $items[] = $child;
                }
            }
            foreach ($quoteItems as $key => $quoteItem) {
                foreach ($items as $item) {
                    if ($quoteItem->compare($item)) {
                        $quoteItems->removeItemByKey($key);
                    }
                }
            }
        }

        return $this;
    }
}
