<?php

namespace Screenshots\CustomArt\Controller\Index;

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
            $this->_redirect('CustomArt/Index');
            return;
        }

        $application     = new \Zend_Mail();
        $firstName       = $post['firstName'];
        $lastName        = $post['lastName'];
        $userEmail       = $post['userEmail'];
        $userPhone       = $post['userPhone'];
        $artDesc         = $post['artDesc'];
        $userFiles       = $post['userFiles'];
        $recipient       = "cody@screenshotsinc.com";
        $subject         = "Custom Artwork Form Submission";

        $emailTemplate = "<HTML>
                            <p>Contact: $firstName $lastName</p>
                            <p>Email: $userEmail</p>
                            <p>Phone: $userPhone</p>

                            <p>Description: $artDesc</p>
                          </HTML>";

        $application->setBodyText($emailTemplate);
        $application->setFrom($userEmail);
        $application->addTo($recipient);
        $application->setSubject($subject);

        try {
            $application->send();

            $this->_redirect('CustomArt/Index');
            return;
        }
        catch(Exception $ex) {
            $this->_redirect('CustomArt/Index');
            return;
        }
    }
}
