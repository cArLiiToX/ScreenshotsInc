<?php

namespace Html5design\Cedapi\Model;

use Html5design\Cedapi\Api\CartInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Cart extends \Magento\Framework\Model\AbstractModel implements CartInterface
{

    public function __construct(
        \Psr\Log\LoggerInterface $_logger,
        \Magento\Sales\Model\Order $_orderModel,
        \Magento\Catalog\Model\Product $_productModel,
        \Magento\Quote\Model\QuoteFactory $_quote,
        \Magento\Store\Model\StoreManagerInterface $_storeManager,
        \Magento\Customer\Model\CustomerFactory $_customerFactory,
        \Magento\Customer\Model\Session $_customerSession,
        \Magento\Customer\Model\Customer $_customerData,
        \Magento\Customer\Model\Address $_customerAddress,
        \Magento\Checkout\Model\Cart $_cartModel,
        \Magento\Quote\Api\CartRepositoryInterface $_quoteRepository,
        \Magento\Quote\Model\Quote $_quoteModel,
        ProductRepositoryInterface $_productRepository,
        \Magento\GiftMessage\Model\Message $_giftMessageModel,
        \Magento\Checkout\Helper\Cart $_cartHelper,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $_timeZone

    ) {
        $this->_logger = $_logger;
        $this->_orderModel = $_orderModel;
        $this->_productModel = $_productModel;
        $this->_quote = $_quote;
        $this->_storeManager = $_storeManager;
        $this->_customerFactory = $_customerFactory;
        $this->_customerSession = $_customerSession;
        $this->_customerData = $_customerData;
        $this->_customerAddress = $_customerAddress;
        $this->_cartModel = $_cartModel;
        $this->_quoteRepository = $_quoteRepository;
        $this->_quoteModel = $_quoteModel;
        $this->_productRepository = $_productRepository;
        $this->_giftMessageModel = $_giftMessageModel;
        $this->_cartHelper = $_cartHelper;
        $this->_timeZone = $_timeZone;

    }

    /**
     * Get product object based on requested product information
     *
     * @param   Product|int|string $productInfo
     * @return  Product
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getProduct($productInfo)
    {
        $product = null;
        if (is_int($productInfo) || is_string($productInfo)) {
            $storeId = $this->_storeManager->getStore()->getId();
            try {
                $product = $this->_productRepository->getById($productInfo, false, $storeId);
            } catch (NoSuchEntityException $e) {
                throw new \Magento\Framework\Exception\LocalizedException(__('We can\'t find the product.'), $e);
            }
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(__('We can\'t find the product.'));
        }
        $currentWebsiteId = $this->_storeManager->getStore()->getWebsiteId();
        if (!is_array($product->getWebsiteIds()) || !in_array($currentWebsiteId, $product->getWebsiteIds())) {
            throw new \Magento\Framework\Exception\LocalizedException(__('We can\'t find the product.'));
        }
        return $product;
    }

    /**
     * Base preparation of product data
     *
     * @param mixed $data
     * @return null|array
     */
    protected function _prepareProductsData($data)
    {
        return is_array($data) ? $data : null;
    }

    /**
     * Create new quote for shopping cart
     *
     * @param int|string $store
     * @return int
     */

    public function create($store)
    {
        //$storeId = $this->_storeManager->getStore()->getId();
        try {
            /*@var $quote Mage_Sales_Model_Quote*/
            $quote = $this->_quote->create(); //Create object of quote
            $quote->setStore($store)->setIsActive(false); //set store for which you create quote
            $quote->save();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            json_encode(array('is_Fault' => 1, 'create_quote_fault' => $e->getMessage()));
        }
        return $quoteId = $quote->getId();
    }

    /**
     *
     * @api
     * @param int $quoteId.
     * @param int $store.
     * @param string $productsData.
     * @param string $action.
     * @return string The all products in a json format.
     */
    public function addToCart($quoteId, $store, $productsData, $action)
    {
        $productsData = json_decode($productsData, true);
        $store = $this->_storeManager->getStore($store);
        if (!$store) {
            return json_encode(array('is_Fault' => 1, 'faultMessage' => 'Invalid Store'));
        }
        if (!$quoteId) {
            $quoteId = $this->create($store);
            if (!$quoteId || $quoteId == 0) {
                return json_encode(array('is_Fault' => 1, 'create_quote_fault' => 'Invalid Quote Id'));
            }
        }
        $websiteId = $store->getWebsiteId();
        foreach ($productsData as $key => $value) {
            $sepArrProduct = $productsData[$key];
            $result = $this->add($quoteId, $sepArrProduct, $store);
        }
        $url = $this->_cartHelper->getCartUrl();
        return json_encode(array('is_Fault' => 0, 'quoteId' => $quoteId, 'checkoutURL' => $url, 'quoteInfo' => $result));
    }

    /**
     * @param  $quoteId
     * @param  $productsData
     * @param  $store
     * @return bool
     */

    public function add($quoteId, $productsData, $store)
    {
        $quote = $this->_quote->create()->load($quoteId);
        if (empty($store)) {
            $store = $quote->getStoreId();
        }
        $productsData = $this->_prepareProductsData($productsData);
        if (empty($productsData)) {
            return json_encode(array('is_Fault' => 1, 'faultMessage' => 'invalid_product_data'));
        }
        $errors = array();
        if (isset($productsData['product_id'])) {
            $productByItem = $this->getProduct($productsData['product_id']);
        } else {
            $errors[] = "One item of products do not have identifier or sku";
            continue;
        }
        if ($productByItem->getData('type_id') == 'configurable') {
            $configProd = $this->_productModel->load($productByItem->getData('entity_id'));
            $super_attrs = array();
            $super_attrs_code = array();
            $configurableAttributeCollection = $configProd->getTypeInstance()->getConfigurableAttributes($configProd);
            foreach ($configurableAttributeCollection as $attribute) {
                $super_attrs[$attribute->getProductAttribute()->getAttributeCode()] = $attribute->getProductAttribute()->getId();
                $super_attrs_code[] = $attribute->getProductAttribute()->getAttributeCode();
            }
            $super_attribute_values = array();
            foreach ($super_attrs as $supercode => $superid) {
                $supervalue = $this->setOrAddOptionAttribute('product', $supercode, $productsData['options'][$supercode]);
                if (!$supervalue) {
                    return json_encode(array('is_Fault' => 1, 'faultMessage' => 'Please specify correct options of product. The option ' . $supercode . ' with value ' . $productsData['options'][$supercode] . ' not exist.'));
                }
                $super_attribute_values[$superid] = $supervalue;
            }
            if (count($super_attribute_values) != count($super_attrs)) {
                return json_encode(array('is_Fault' => 1, 'faultMessage' => 'Please specify correct options.22'));
            }
            $productsData["super_attribute"] = $super_attribute_values;
        }
        unset($productsData['options']);
        $productRequest = new \Magento\Framework\DataObject($productsData);
        $productRequest->setItem($productRequest);
        try {
            $result = $quote->addProduct($productByItem, $productRequest);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $errors[] = $e->getMessage();
        }
        $quoteItem = $quote->getItemByProduct($productByItem);
        $quoteItem->setCustomDesign($productsData['custom_design']);
        $product = $quoteItem->getProduct();
        $data['microtime'] = microtime(true);
        $product->addCustomOption('do_not_merge', serialize($data));
        $quoteItem->addOption($product->getCustomOption('do_not_merge'));
        $tierPrice = 0;
        $tierPrices = array();
        $quoteProduct = $this->_productModel->load($productsData['simpleproduct_id']);
        $tierPrices = $quoteProduct->getPriceInfo()->getPrice('tier_price')->getTierPriceList();
        if (is_array($tierPrices)) {
            foreach ($tierPrices as $price) {
                if ($productsData['qty'] >= (int) $price['price_qty']) {
                    $tierPrice = number_format($price['website_price'], 2);
                }
            }
        }
        if (!empty($quoteProduct)) {
            if ($tierPrice == 0) {
                $customPrice = $quoteProduct->getPrice() + $productsData['custom_price'];
            } else {
                $customPrice = $tierPrice + $productsData['custom_price'];
            }
            $quoteItem->setCustomPrice($customPrice);
            $quoteItem->setOriginalCustomPrice($customPrice);
        }
        if ($productsData['qty'] > 0) {
            $quoteItem->setQty($productsData['qty']);
        }
        if (!empty($errors)) {
            return json_encode(array('is_Fault' => 1, 'add_product_fault' => implode(PHP_EOL, $errors)));
        }
        try {
            $quote->collectTotals()->save();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return json_encode(array('is_Fault' => 1, 'add_product_quote_save_fault' => $e->getMessage()));
        }
        return true;
    }

    /**
     * Retrieve attribute option value
     *
     * @param integer $product
     * @param array $arg_attribute
     * @param string $arg_value
     * @return array
     */
    public function setOrAddOptionAttribute($product, $arg_attribute, $arg_value)
    {
        $options = $this->_productModel->getResource()
            ->getAttribute($arg_attribute)
            ->getSource()
            ->getAllOptions(false);
        // determine if this option exists
        $value_exists = false;
        foreach ($options as $option) {
            if ($option['label'] == $arg_value) {
                $value_exists = true;
                return $option['value'];
                break;
            }
        }
        return false;
    }

    /**
     *
     * @api
     * @param int $from.
     * @param int $to.
     * @param int $store.
     * @return string The all products in a json format.
     */
    public function getOrdersGraph($from, $to, $store)
    {
        $lastweek = date('Y-m-d', strtotime("-1 month"));
        $orders = $this->_orderModel->getCollection();
        $orders->join(array('item' => 'sales_order_item'), 'main_table.entity_id = item.order_id AND item.custom_design>0')
            ->addAttributeToFilter('main_table.created_at', array('from' => $lastweek))
            ->addFieldToFilter('main_table.store_id', $store);
        $orders->getSelect()->group('main_table.entity_id');
        $order_data = array();
        foreach ($orders as $key => $order) {
            $order_data[$key] = array(
                'created_date' => date('Y-m-d', strtotime($order->getCreatedAt())),
                'order_id' => $order->getIncrementId(),
            );
        }
        $res = array();
        $tempId = 0;
        $count = -1;
        foreach ($order_data as $k => $v) {
            if ($tempId != $v['created_date']) {
                $i = 0;
                $count++;
                $tempId = $v['created_date'];
                $res[$count]['date'] = $v['created_date'];
                $res[$count]['sales'] = $i + 1;
            } else {
                $i++;
                $res[$count]['date'] = $v['created_date'];
                $res[$count]['sales'] = $i + 1;
            }
        }
        return json_encode($res);
    }

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
    public function getOrders($lastOrderId, $store, $range, $fromDate, $toDate)
    {
        $orders = $this->_orderModel->getCollection();
        $orders->join(array('item' => 'sales_order_item'), 'main_table.entity_id = item.order_id AND item.custom_design>0 AND main_table.store_id=' . $store . ' ');
        $orders->getSelect()->group('main_table.entity_id');
        $orders->getSelect()->order('main_table.created_at DESC');
        if ((int) $lastOrderId) {
            $orders->addAttributeToFilter('entity_id', array('lt' => $lastOrderId));
        } else {
            $orders->addAttributeToFilter('entity_id', array('gt' => $lastOrderId));
        }
        $range = intval($range);
        $orders->getSelect()->limit($range);
        $order_array = array();
        $i = 0;
        if ($range) {
            foreach ($orders as $order) {
                $i = ($i < 0) ? 0 : $i;
                $timeZone = $this->_timeZone->getConfigTimezone('store', $order->getStore());
                $date = $this->_timeZone->formatDateTime(new \DateTime($order->getCreatedAt()), \IntlDateFormatter::MEDIUM, \IntlDateFormatter::SHORT, null, $timeZone);
                $date = date('Y-m-d H:i:s', strtotime($date));

                $order_array[$i] = array(
                    'order_id' => $order->getId(),
                    'order_incremental_id' => $order->getIncrementId(),
                    'order_status' => $order->getStatusLabel(),
                    'order_date' => $date, //$order->getCreatedAt(),
                    'customer_name' => $order->getCustomerName(),
                );
                $i++;
                $tempId = $order->getId();
                $range--;
            }
        } else {
            foreach ($orders as $order) {
                $i = ($i < 0) ? 0 : $i;
                $timeZone = $this->_timeZone->getConfigTimezone('store', $order->getStore());
                $date = $this->_timeZone->formatDateTime(new \DateTime($order->getCreatedAt()), \IntlDateFormatter::MEDIUM, \IntlDateFormatter::SHORT, null, $timeZone);
                $date = date('Y-m-d H:i:s', strtotime($date));

                $order_array[$i] = array(
                    'order_id' => $order->getId(),
                    'order_incremental_id' => $order->getIncrementId(),
                    'order_status' => $order->getStatusLabel(),
                    'order_date' => $date, //$order->getCreatedAt(),
                    'customer_name' => $order->getCustomerName(),
                );
                $i++;
                $tempId = $order->getId();
            }
        }
        return json_encode(array('is_Fault' => 0, 'order_list' => $order_array));
    }

    /**
     *
     * @api
     * @param int $orderId,
     * @param int $store.
     * @return string The all products in a json format.
     */
    public function updateItemStatus($orderId, $store)
    {
        return 1;
    }
    /**
     *
     * @api
     * @param int $orderId.
     * @param int $store.
     * @return string The all products in a json format.
     */
    public function getOrderDetails($orderId, $store)
    {
        $order = $this->_orderModel->load($orderId);
        $orderItems = $order->getItemsCollection();
        $shippingAddress = $order->getShippingAddress();
        $orderDetails = array();
        $shippingStreet = $shippingAddress->getStreet();
        $orderDetails['shipping_address']['first_name'] = $shippingAddress->getFirstname();
        $orderDetails['shipping_address']['last_name'] = $shippingAddress->getLastname();
        $orderDetails['shipping_address']['fax'] = $shippingAddress->getFax();
        $orderDetails['shipping_address']['region'] = $shippingAddress->getRegion();
        $orderDetails['shipping_address']['postcode'] = $shippingAddress->getPostcode();
        $orderDetails['shipping_address']['telephone'] = $shippingAddress->getTelephone();
        $orderDetails['shipping_address']['city'] = $shippingAddress->getCity();
        if (isset($shippingStreet[0])) {
            $orderDetails['shipping_address']['address_1'] = $shippingStreet[0];
        }
        if (isset($shippingStreet[1])) {
            $orderDetails['shipping_address']['address_2'] = $shippingStreet[1];
        }
        $orderDetails['shipping_address']['state'] = $shippingAddress->getRegion();
        $orderDetails['shipping_address']['company'] = $shippingAddress->getCompany();
        $orderDetails['shipping_address']['email'] = $shippingAddress->getEmail();
        $orderDetails['shipping_address']['country'] = $shippingAddress->getCountry();
        $billingAddress = $order->getBillingAddress();
        $billingStreet = $billingAddress->getStreet();
        $orderDetails['billing_address']['first_name'] = $billingAddress->getFirstname();
        $orderDetails['billing_address']['last_name'] = $billingAddress->getLastname();
        $orderDetails['billing_address']['fax'] = $billingAddress->getFax();
        $orderDetails['billing_address']['region'] = $billingAddress->getRegion();
        $orderDetails['billing_address']['postcode'] = $billingAddress->getPostcode();
        $orderDetails['billing_address']['telephone'] = $billingAddress->getTelephone();
        $orderDetails['billing_address']['state'] = $billingAddress->getRegion();
        $orderDetails['billing_address']['city'] = $billingAddress->getCity();
        if (isset($billingStreet[0])) {
            $orderDetails['billing_address']['address_1'] = $billingStreet[0];
        }
        if (isset($billingStreet[1])) {
            $orderDetails['billing_address']['address_2'] = $billingStreet[1];
        }
        $orderDetails['billing_address']['company'] = $billingAddress->getCompany();
        $orderDetails['billing_address']['email'] = $billingAddress->getEmail();
        $orderDetails['billing_address']['telephone'] = $billingAddress->getTelephone();
        $orderDetails['billing_address']['country'] = $billingAddress->getCountry();
        $orderDetails['order_id'] = $order->getId();
        $orderDetails['order_incremental_id'] = $order->getIncrementId();
        $orderDetails['order_status'] = $order->getStatusLabel();
        $orderDetails['order_date'] = $order->getCreatedAt();
        $orderDetails['customer_name'] = $order->getCustomerName();
        $orderDetails['customer_email'] = $order->getCustomerEmail();
        $orderDetails['shipping_method'] = $order->getShippingMethod();
        $orderDetails['order_items'] = array();
        $index = 0;
        $simpindex = 0;
        foreach ($orderItems as $item) {
            $product = $this->_productModel->load($item->getProductId());
            $attributes = $product->getAttributes();
            if ($item->getParentItemId()) {
                $orderDetails['order_items'][$simpindex]['product_id'] = $item->getProductId();
                $orderDetails['order_items'][$simpindex]['product_sku'] = $item->getSku();
                $orderDetails['order_items'][$simpindex]['product_name'] = $item->getName();
                $orderDetails['order_items'][$simpindex]['quantity'] = $item->getQtyOrdered();
                $attindex = 0;
                $orderDetail = array();
                foreach ($attributes as $attribute) {
                    $attributeCode = $attribute->getAttributeCode();
                    $xesize = 'xe_size';
                    $xecolor = 'xe_color';
                    if ($attributeCode == $xesize) {
                        $value = $attribute->getFrontend()->getValue($product);
                        $orderDetails['order_items'][$simpindex]['xe_size'] = $value;
                    } else if ($attributeCode == $xecolor) {
                        $value = $attribute->getFrontend()->getValue($product);
                        $orderDetails['order_items'][$simpindex]['xe_color'] = $value;
                    }
                    if ($attribute->getIsVisibleOnFront()) {
                        $orderDetail[$attindex]['attributeCode'] = $attributeCode;
                        $orderDetail[$attindex]['label'] = $attribute->getFrontend()->getLabel();
                        $orderDetail[$attindex]['value'] = $attribute->getFrontend()->getValue($product);
                        $attindex++;
                    }
                }
                $orderDetails['order_items'][$simpindex]['attribute'] = $orderDetail;
                $simpindex++;
            } else {
                $orderDetails['order_items'][$index]['itemStatus'] = $item->getStatus();
                $orderDetails['order_items'][$index]['ref_id'] = $item->getCustom_design();
                $orderDetails['order_items'][$simpindex]['item_id'] = $item->getId();
                $orderDetails['order_items'][$index]['print_status'] = $item->getItem_printed();
                $orderDetails['order_items'][$index]['product_price'] = $item->getPrice();
                $orderDetails['order_items'][$simpindex]['config_product_id'] = $item->getProductId();
                $index++;
            }
        }
        return json_encode(array('is_Fault' => 0, 'order_details' => $orderDetails));
    }
    /**
     * @api
     * @param int $quoteId
     * @param int $store
     * @param int $customerId
     * @return string No of cart qty.
     */

    public function getTotalCartItem($quoteId, $store, $customerId)
    {
        if ($customerId && $customerId > 0) {
            $quoteCollection = $this->_quote->create()->getCollection()
                ->addFieldToFilter('customer_id', $customerId)
                ->addFieldToFilter('store_id', $store)
                ->addOrder('updated_at');
            $quote = $quoteCollection->getFirstItem();
            $quoteId = (int) $quote->getId();
        }
        if ($quoteId > 0) {
            $quote = $this->_quote->create()->load($quoteId);
            $itemQty = (int) $quote->getItemsQty();
        } else {
            $itemQty = 0;
        }
        $url = $this->_cartHelper->getCartUrl();
		$url= ($quoteId > 0)? $url.'?quoteId='.$quoteId : $url;
        return json_encode(array('is_Fault' => 0, 'totalCartItem' => $itemQty, 'checkoutURL' => $url), JSON_UNESCAPED_SLASHES);
    }
}
