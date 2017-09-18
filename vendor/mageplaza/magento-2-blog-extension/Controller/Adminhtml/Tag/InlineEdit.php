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
namespace Mageplaza\Blog\Controller\Adminhtml\Tag;

abstract class InlineEdit extends \Magento\Backend\App\Action
{
    /**
     * JSON Factory
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
	public $jsonFactory;

    /**
     * Tag Factory
     *
     * @var \Mageplaza\Blog\Model\TagFactory
     */
	public $tagFactory;

    /**
     * constructor
     *
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Mageplaza\Blog\Model\TagFactory $tagFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Mageplaza\Blog\Model\TagFactory $tagFactory,
        \Magento\Backend\App\Action\Context $context
    ) {
    
        $this->jsonFactory = $jsonFactory;
        $this->tagFactory  = $tagFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];
        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && !empty($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

		$key = array_keys($postItems);
		$tagId = !empty($key) ? (int) $key[0] : '';
		/** @var \Mageplaza\Blog\Model\Tag $tag */
		$tag = $this->tagFactory->create()->load($tagId);
		try {
			$tagData = $postItems[$tagId];
			$tag->addData($tagData);
			$tag->save();
		} catch (\Magento\Framework\Exception\LocalizedException $e) {
			$messages[] = $this->getErrorWithTagId($tag, $e->getMessage());
			$error = true;
		} catch (\RuntimeException $e) {
			$messages[] = $this->getErrorWithTagId($tag, $e->getMessage());
			$error = true;
		} catch (\Exception $e) {
			$messages[] = $this->getErrorWithTagId(
				$tag,
				__('Something went wrong while saving the Tag.')
			);
			$error = true;
		}

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add Tag id to error message
     *
     * @param \Mageplaza\Blog\Model\Tag $tag
     * @param string $errorText
     * @return string
     */
	public function getErrorWithTagId(\Mageplaza\Blog\Model\Tag $tag, $errorText)
    {
        return '[Tag ID: ' . $tag->getId() . '] ' . $errorText;
    }
}
