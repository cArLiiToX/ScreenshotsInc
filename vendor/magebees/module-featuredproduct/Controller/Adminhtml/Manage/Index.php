<?php
/***************************************************************************
 Extension Name	: Featured Products 
 Extension URL	: https://www.magebees.com/featured-products-extension-for-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
namespace Magebees\Featuredproduct\Controller\Adminhtml\Manage;

class Index extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Customer\Model\Session $session
    ) {
    
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->session = $session;
    }
    public function execute()
    {
        
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magebees_Featuredproduct::grid');
        $resultPage->getConfig()->getTitle()->prepend(__('MageBees Featured Products'));

       
        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }
         $store=(int)$this->getRequest()->getParam('store', 0);
         $this->session->setTestKey($store);
         return $resultPage;
    }
    
    protected function _isAllowed()
    {
        return true;
    }
}
