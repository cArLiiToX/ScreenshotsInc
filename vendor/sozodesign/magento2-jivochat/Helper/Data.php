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

namespace Sozo\JivoChat\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const CFG_JIVOCHAT_ENABLED = 'sozo_jivochat/general/enabled';
    const CFG_JIVOCHAT_WIDGET_ID = 'sozo_jivochat/general/widget_id';

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    protected $_moduleList;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context         $context
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     */
    public function __construct(
        Context $context,
        ModuleListInterface $moduleList
    )
    {
        $this->_moduleList = $moduleList;

        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::CFG_JIVOCHAT_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getWidgetId()
    {
        return $this->scopeConfig->getValue(self::CFG_JIVOCHAT_WIDGET_ID, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getExtensionVersion()
    {
        $moduleCode = 'Sozo_JivoChat';
        $moduleInfo = $this->_moduleList->getOne($moduleCode);
        return $moduleInfo['setup_version'];
    }
}
