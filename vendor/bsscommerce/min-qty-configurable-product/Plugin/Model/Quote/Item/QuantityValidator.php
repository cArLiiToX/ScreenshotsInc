<?php
namespace Bss\MinQtyCP\Plugin\Model\Quote\Item;

class QuantityValidator
{
	private $helper; 
    private $stockRegistry;
    private $registry;
    private $request;

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

    public function aroundValidate($subject, $proceed, $observer)
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

                if ($stockItem->getData('use_config_bss_minimum_qty_configurable')) {
                    $minQty = $this->helper->getDefaultMinQty();
                } else {
                    $minQty = $stockItem->getData('bss_minimum_qty_configurable');
                }

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
}
