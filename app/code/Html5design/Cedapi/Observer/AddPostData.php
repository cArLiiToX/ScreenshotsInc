<?php
namespace Html5design\Cedapi\Observer;
use Magento\Framework\Event\ObserverInterface;

class AddPostData implements ObserverInterface{
    protected $_objectManager;
	
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $_objectManager,
		\Psr\Log\LoggerInterface $_logger,
		\Magento\Framework\App\Request\Http $_request
    ) {
        $this->_objectManager = $_objectManager;
		$this->_logger = $_logger;
		$this->_request = $_request;
    }
	
    public function execute(\Magento\Framework\Event\Observer $observer){ 
		$handle = $this->_request->getFullActionName();
		if ($handle == 'checkout_cart_add') {
			$item = $observer->getProduct();
			$data['microtime'] = microtime(true);
            $item->addCustomOption('do_not_merge', serialize($data));	
		}
	}
}