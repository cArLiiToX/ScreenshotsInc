<?php
namespace Html5design\Cedapi\Observer;

use Magento\Framework\Event\ObserverInterface;

class PredispatchCheckoutCart implements ObserverInterface
{
    protected $_objectManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Psr\Log\LoggerInterface $_logger,
        \Magento\Checkout\Model\Session $_checkoutSessionModel,
        \Magento\Framework\App\Request\Http $_request,
        \Magento\Framework\Message\ManagerInterface $_messangeManager,
        \Magento\Checkout\Helper\Cart $_cartHelper,
		\Magento\Quote\Model\QuoteFactory $_quote
    ) {
        $this->_objectManager = $objectManager;
        $this->_logger = $_logger;
        $this->_checkoutSessionModel = $_checkoutSessionModel;
        $this->_request = $_request;
        $this->_messangeManager = $_messangeManager;
        $this->_cartHelper = $_cartHelper;
		$this->_quote = $_quote;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $quoteId = intval($this->_request->getParam('quoteId'));
        if ($quoteId == '' || $quoteId <= 0) {
            $quoteId == 0;
        }
        if ($quoteId && $quoteId > 0) {
            try {
                $quoteId = intval($this->_request->getParam('quoteId'));
                $cartsess = $this->_objectManager->get('Magento\Checkout\Model\Session');
                $cartsess->setQuoteId($quoteId);
                $cartsess->setLoadInactive(true);
                $quote = $this->_quote->create()->load($quoteId);
                $quote->setIsActive(true);
                $quote->save();
                $this->_messangeManager->addSuccess('Product is successfully added in to cart.');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->_messangeManager->addError($e->getMessage());
            }
            $redirectUrl = $this->_cartHelper->getCartUrl();
            $observer->getControllerAction()->getResponse()->setRedirect($redirectUrl);

        }
    }
}
