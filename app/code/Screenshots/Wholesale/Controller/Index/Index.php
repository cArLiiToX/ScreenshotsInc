<?php

namespace Screenshots\Wholesale\Controller\Index;
use Magento\Framework\App\Action\Context;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_resultPageFactory;

    public function __construct(Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory)
    {
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        return $resultPage;
    }

    public function submitForm()
    {
        $post = $this->getRequest()->getPost();

        if(!$post) exit;

        $application     = new Zend_Mail();
        $firstname       = $post['firstName'];
        $lastname        = $post['lastName'];
        $useremail       = $post['userEmail'];
        $userphone       = $post['userPhone'];
        $userTitle       = $post['userTitle'];
        $companyName     = $post['companyName'];
        $companyDesc     = $post['companyDesc'];
        $companyProducts = $post['companyProducts'];
        $companyAddr     = $post['companyAddr'];
        $companyCity     = $post['companyCity'];
        $companyCountry  = $post['companyCountry'];
        $companyTaxId    = $post['companyTaxId'];
        $companyReferal  = $post['companyReferal'];
        $recipient       = "cody@screenshotsinc.com";
        $subject         = "Wholesaler Application";
        $body            = "This is Test Email!";

        $application->setBodyText($body);

        $application->setFrom($useremail);

        $application->addTo($recipient);

        $application->setSubject($subject);

        try {
            $application->send();

            $successMessage = $this->__('Application submitted succesfully.');
            Mage::getSingleton('core/session')->addSuccess($successMessage);
        }
        catch(Exception $ex) {
            $errorMessage = $this->__('Application was not submitted.');
            Mage::getSingleton('core/session')->addError($errorMessage);
        }

        $this->renderLayout();
    }
}
