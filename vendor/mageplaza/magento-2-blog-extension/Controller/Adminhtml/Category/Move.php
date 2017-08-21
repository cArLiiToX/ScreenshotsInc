<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Blog
 * @copyright   Copyright (c) 2016 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\Blog\Controller\Adminhtml\Category;

class Move extends \Mageplaza\Blog\Controller\Adminhtml\Category
{
    /**
     * JSON Result Factory
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
	public $resultJsonFactory;

    /**
     * Layout Factory
     *
     * @var \Magento\Framework\View\LayoutFactory
     */
	public $layoutFactory;

    /**
     * Logger instance
     *
     * @var \Psr\Log\LoggerInterface
     */
	public $logger;

    /**
     * constructor
     *
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Mageplaza\Blog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Psr\Log\LoggerInterface $logger,
        \Mageplaza\Blog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\App\Action\Context $context
    ) {
    
        $this->resultJsonFactory = $resultJsonFactory;
        $this->layoutFactory     = $layoutFactory;
        $this->logger            = $logger;
        parent::__construct($categoryFactory, $coreRegistry, $context);
    }

    /**
     * Move Blog category action
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        /**
         * New parent Blog category identifier
         */
        $parentNodeId = $this->getRequest()->getPost('pid', false);
        /**
         * Blog category id after which we have put our Blog category
         */
        $prevNodeId = $this->getRequest()->getPost('aid', false);

        /** @var $block \Magento\Framework\View\Element\Messages */
        $block = $this->layoutFactory->create()->getMessagesBlock();
        $error = false;

        try {
            $category = $this->initCategory();
            if ($category === false) {
				throw new \Magento\Framework\Exception\LocalizedException(__('Blog category is not available.'));
            }
            $category->move($parentNodeId, $prevNodeId);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $error = true;
            $this->messageManager->addError(__('There was a Blog category move error.'));
        } catch (\Magento\Framework\Exception\AlreadyExistsException $e) {
            $error = true;
            $this->messageManager->addError(__('There was a Blog category move error. %1', $e->getMessage()));
        } catch (\Exception $e) {
            $error = true;
            $this->messageManager->addError(__('There was a Blog category move error.'));
            $this->logger->critical($e);
        }

        if (!$error) {
            $this->messageManager->addSuccess(__('You moved the Blog category'));
        }

        $block->setMessages($this->messageManager->getMessages(true));
        $resultJson = $this->resultJsonFactory->create();

        return $resultJson->setData([
            'messages' => $block->getGroupedHtml(),
            'error' => $error
        ]);
    }
}
