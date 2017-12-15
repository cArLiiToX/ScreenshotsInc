<?php
namespace Bss\MinQtyCP\Plugin\Model\Quote\Item;

class QuantityValidator
{
    /**
     * @var \Bss\MinQtyCP\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;
    private $request;
    /**
     * QuantityValidator constructor.
     * @param \Bss\MinQtyCP\Helper\Data $helper
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Bss\MinQtyCP\Helper\Data $helper,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->helper = $helper;
        $this->stockRegistry = $stockRegistry;
        $this->registry = $registry;
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function aroundValidate(\Magento\CatalogInventory\Model\Quote\Item\QuantityValidator $subject, \Closure $proceed, $observer)
    {
        if ($this->request->getActionName() == 'index') {
            $proceed($observer);

            if (!$this->helper->isEnabled()) {
                return;
            }

            $item = $observer->getEvent()->getItem();
            $quote = $item->getQuote();

            if ($this->registry->registry('change_cart')) {
                $quote->setHasError(false);
                return;
            }

            if (!$this->registry->registry('min_qty_configurale_product')) {
                $this->registry->register('min_qty_configurale_product', $this->helper->getQuoteQtyList($quote));
            }
            
            $qtyList = $this->registry->registry('min_qty_configurale_product');
            
            if (isset($qtyList[$item->getProductId()])) {
                $sumQty = (float) $qtyList[$item->getProductId()];
                $product = $item->getProduct();
                
                $stockItem = $this->stockRegistry->getStockItem(
                    $product->getId(),
                    $product->getStore()->getWebsiteId()
                );

                $currentWebsiteId = $product->getStore()->getWebsiteId();
                $valueMinimumQty = $stockItem->getData('bss_minimum_qty_configurable');
                $valueMinimumQty = explode(',', $valueMinimumQty);
                $minimumQty = '';
                $defaultMinimumQty = '';
                foreach ($valueMinimumQty as $key => $value) {
                    if ((int)$key%2 == 0) {
                        $nextKey = (string)((int)$key + 1);
                        if ($value == $currentWebsiteId) {
                            $minimumQty = $valueMinimumQty[$nextKey];
                        }
                        if ($value == '0') {
                            $defaultMinimumQty = $valueMinimumQty[$nextKey];
                        }
                    }
                }
                $minimumQty = (int)$minimumQty;
                $defaultMinimumQty = (int)$defaultMinimumQty;


                $useDefaultConfig = $this->getSavedConfig($stockItem, $currentWebsiteId);

                $minQty = $this->getMinQty($minimumQty, $defaultMinimumQty, $useDefaultConfig);

                if ($minQty > 0 && $sumQty < $minQty) {
                    $item->addErrorInfo(
                        'cataloginventory',
                        \Magento\CatalogInventory\Helper\Data::ERROR_QTY,
                        __('The minimum quantity allowed for purchase of %1 is %2. It is %3 currently.', $product->getName(), (float) $minQty, $sumQty)
                    );
                    $quote->addErrorInfo(
                        'stock',
                        'cataloginventory',
                        \Magento\CatalogInventory\Helper\Data::ERROR_QTY,
                        __('Some configurable products do not meet minimum quantity requirement!')
                    );
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getMinQty($minimumQty, $defaultMinimumQty, $useDefaultConfig)
    {
        if ($minimumQty == '' && $defaultMinimumQty == '') {
                $minQty = $this->helper->getDefaultMinQty();
        } else {
            if ($useDefaultConfig == '1') {
                $minQty = $this->helper->getDefaultMinQty();
            } else {
                if ($minimumQty == null || $minimumQty == '') {
                    $minQty = $defaultMinimumQty;
                } else {
                    $minQty = $minimumQty;
                }
            }
        }
        return $minQty;
    }

    /**
     * {@inheritdoc}
     */
    protected function getSavedConfig($productStock, $currentWebsiteId)
    {

        $useConfig = $productStock->getData('use_config_bss_minimum_qty_configurable');

        if ($useConfig != "" || $useConfig != null) {
            $useConfig = explode(',', $useConfig);
            $savedConfig = '1';
            foreach ($useConfig as $key => $value) {
                if ((int)$key%2 == 0) {
                    $nextKey = (string)((int)$key + 1);
                    if ($value == $currentWebsiteId) {
                        $savedConfig = $useConfig[$nextKey];
                    }
                }
            }
        } else {
            $savedConfig = '1';
        }

        return $savedConfig;
    }
}
