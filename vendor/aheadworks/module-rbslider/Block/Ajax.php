<?php
namespace Aheadworks\Rbslider\Block;

/**
 * Class Ajax
 * @package Aheadworks\Rbslider\Block
 */
class Ajax extends \Magento\Framework\View\Element\Template
{
    /**
     * Retrieve script options encoded to json
     *
     * @return string
     */
    public function getScriptOptions()
    {
        $params = [
            'url' => $this->getUrl(
                'aw_rbslider/block/render/',
                [
                    '_current' => true,
                    '_secure' => $this->templateContext->getRequest()->isSecure()
                ]
            ),
            'handles' => $this->_layout->getUpdate()->getHandles()
        ];
        return json_encode($params);
    }
}
