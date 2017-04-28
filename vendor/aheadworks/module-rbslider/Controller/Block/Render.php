<?php
namespace Aheadworks\Rbslider\Controller\Block;

use Magento\Framework\Translate\InlineInterface;
use Magento\Framework\App\Action\Context;
use Aheadworks\Rbslider\Block\Widget\Banner as WidgetBanner;

/**
 * Class Render
 * @package Aheadworks\Rbslider\Controller\Block
 */
class Render extends \Magento\Framework\App\Action\Action
{
    /**
     * @var InlineInterface
     */
    private $translateInline;

    /**
     * @param Context $context
     * @param InlineInterface $translateInline
     */
    public function __construct(
        Context $context,
        InlineInterface $translateInline
    ) {
        parent::__construct($context);
        $this->translateInline = $translateInline;
    }

    /**
     * Returns block content depends on ajax request
     *
     * @return \Magento\Framework\Controller\Result\Redirect|void
     */
    public function execute()
    {
        if (!$this->getRequest()->isAjax()) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setRefererOrBaseUrl();
        }

        $blocks = $this->getRequest()->getParam('blocks');
        $handles = $this->getRequest()->getParam('handles');
        $data = $this->getBlocks($blocks, $handles);

        $this->translateInline->processResponseBody($data);
        $this->getResponse()->appendBody(json_encode($data));
    }

    /**
     * Get blocks from layout by handles
     *
     * @param string $blocks
     * @param string $handles
     * @return string[]
     */
    private function getBlocks($blocks, $handles)
    {
        if (!$handles || !$blocks) {
            return [];
        }
        $blocks = json_decode($blocks);
        $handles = json_decode($handles);

        $data = [];
        $this->_view->loadLayout($handles, true, true, false);
        $layout = $this->_view->getLayout();
        foreach ($blocks as $blockName) {
            if (strpos($blockName, WidgetBanner::WIDGET_NAME_PREFIX, 0) === false) {
                $blockInstance = $layout->getBlock($blockName);
                if (is_object($blockInstance)) {
                    $data[$blockName] = $blockInstance->toHtml();
                }
            } else {
                $html = '';
                $bannerId = (int)substr($blockName, strlen(WidgetBanner::WIDGET_NAME_PREFIX), strlen($blockName));

                if ($bannerId) {
                    // Define widget block and check the type is instance of Widget Interface
                    $widget = $layout->createBlock(
                        WidgetBanner::class,
                        '',
                        ['data' => ['banner_id' => $bannerId]]
                    );
                    if ($widget instanceof \Magento\Widget\Block\BlockInterface) {
                        $html = $widget->toHtml();
                    }
                }
                $data[$blockName] = $html;
            }
        }

        return $data;
    }
}
