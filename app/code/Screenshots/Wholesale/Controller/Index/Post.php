<?php

namespace Screenshots\Wholesale\Controller\Index;

class Post extends \Magento\Framework\App\Action\Action
{
    public function __construct(
        \Magento\Framework\App\Action\Context $context
    )
    {
        parent::__construct($context);
    }

    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
        if (!$post) {
            $this->_redirect('Wholesale/Index');
            return;
        }

        $application     = new \Zend_Mail();
        $firstName       = $post['firstName'];
        $lastName        = $post['lastName'];
        $userEmail       = $post['userEmail'];
        $userPhone       = $post['userPhone'];
        $userTitle       = $post['userTitle'];
        $companyName     = $post['companyName'];
        $companyDesc     = $post['companyDesc'];
        $companyProducts = $post['companyProducts'];
        $companyAddr     = $post['companyAddr'];
        $companyCity     = $post['companyCity'];
        $companyState    = $post['companyState'];
        $companyCountry  = $post['companyCountry'];
        $companyTaxId    = $post['companyTaxId'];
        $companyReferal  = $post['companyReferal'];
        $recipient       = "info@screenshotsinc.com";
        $subject         = "Wholesaler Application";

        $emailTemplate = "<HTML>
                            <h2>Company: $companyName</h2>
                            <p>Contact: $firstName $lastName</p>
                            <p>Title: $userTitle</p>
                            <p>Email: $userEmail</p>
                            <p>Phone: $userPhone</p>

                            <p>Description: $companyDesc</p>
                            <p>Products: $companyProducts</p>
                            <p>Tax Id: $companyTaxId</p>
                            <p>Referal: $companyReferal</p>

                            <p>Street: $companyAddr</p>
                            <p>City: $companyCity</p>
                            <p>State: $companyState</p>
                            <p>Country: $companyCountry</p>
                          </HTML>";

        $application->setBodyText($emailTemplate);

        $application->setFrom($userEmail);

        $application->addTo($recipient);

        $application->setSubject($subject);

        try {
            $application->send();

            $this->_redirect('Wholesale/Index');
            return;
        }
        catch(Exception $ex) {
            $this->_redirect('Wholesale/Index');
            return;
        }
    }
}
