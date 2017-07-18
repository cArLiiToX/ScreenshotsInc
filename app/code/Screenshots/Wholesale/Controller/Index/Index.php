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
}

class ProcessApplication extends \Magento\Framework\App\Action\Action
{
    /**
     * Submit application action
     *
     * @return void
     */
    public function execute()
    {
        $post = $this->getRequest()->getPost();

        if ($post) {
            // Retrieve your form data
            $firstname   = $post['firstName'];
            $lastname    = $post['lastName'];
            $useremail   = $post['userEmail'];
            $userphone   = $post['userPhone'];
            $userTitle   = $post['userTitle'];
            $companyname = $post['companyName'];
            $companydesc = $post['companyDesc'];
            $billingaddr = $post['billingAddr'];
            $billingcity = $post['billingCity'];
            $companyref =  $post['companyReference'];

            $recipient = "cody@screenshotsinc.com";
            $subject = "Wholesaler Application";
            $body = "This is Test Email!"; // body text

            $mail = new Zend_Mail();

            $mail->setBodyText($body);

            $mail->setFrom($fromEmail, $fromName);

            $mail->addTo($recipient, $toName);

            $mail->setSubject($subject);

            try {
                $mail->send();
            }
            catch(Exception $ex) {
                // I assume you have your custom module.
                // If not, you may keep 'customer' instead of 'yourmodule'.
                Mage::getSingleton('core/session')
                    ->addError(Mage::helper('Wholesale')
                    ->__('Unable to process application.'));
            }

            // Display the succes form validation message
            $this->messageManager->addSuccessMessage('Application submitted succesfully.');
        }
    }
}
