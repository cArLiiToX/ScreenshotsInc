<?php
namespace Html5design\Cedapi\Observer;
use Magento\Framework\Event\ObserverInterface;

class AddQuoteAfter implements ObserverInterface{
    protected $_objectManager;
	
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $_objectManager,
		\Psr\Log\LoggerInterface $_logger
    ) {
        $this->_objectManager = $_objectManager;
		$this->_logger = $_logger;
    }
	
    public function execute(\Magento\Framework\Event\Observer $observer){ 
		$quote = $observer->getEvent()->getQuote();
		$expire = time() + 60 * 60 * 24 * 30;
		$quoteId = $quote->getId();
		if($quoteId > 0) setcookie("quoteId", $quoteId, $expire, "/");
	}
}