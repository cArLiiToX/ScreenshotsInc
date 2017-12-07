<?php
/***************************************************************************
 Extension Name	: Featured Products 
 Extension URL	: https://www.magebees.com/featured-products-extension-for-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
namespace Magebees\Featuredproduct\Controller\Adminhtml\Manage;

class NewAction extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Customer\Model\Session $session
    ) {
    
        $this->resultPageFactory = $resultPageFactory;
        $this->session = $session;
        parent::__construct($context);
    }
    public function execute()
    {
    
        /* Get store value from url and save in session then get in save.php*/
        $store=(int)$this->getRequest()->getParam('store', 0);
        $this->session->setTestKey($store);
         
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magebees_Featuredproduct::grid');
        $resultPage->getConfig()->getTitle()->prepend(__('MageBees Featured Products'));
        return $resultPage;
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Featuredproduct::featuredproduct');
    }
}
