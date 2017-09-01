<?php
/**
 * SOZO Design
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    SOZO Design
 * @package     Sozo_JivoChat
 * @copyright   Copyright (c) 2017 SOZO Design (https://sozodesign.co.uk)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 */

namespace Sozo\JivoChat\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Sozo\JivoChat\Helper\Data;

class Display extends Template
{
    /**
     * @var \Sozo\JivoChat\Helper\Data
     */
    protected $_chatHelper;

    /**
     * Display constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Sozo\JivoChat\Helper\Data                       $chatHelper
     * @param array                                            $data
     */
    public function __construct(Context $context, Data $chatHelper, array $data = [])
    {
        $this->_chatHelper = $chatHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get the Widget ID
     *
     * @return mixed
     */
    public function getWidgetId()
    {
        return $this->_chatHelper->getWidgetId();
    }

    /**
     * Generate the JivoChat output
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->_chatHelper->isEnabled()) {
            return '';
        }

        return parent::_toHtml();
    }
}
