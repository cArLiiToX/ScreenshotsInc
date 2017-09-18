<?php
namespace Mageplaza\Seo\Helper;

use Mageplaza\Core\Helper\AbstractData as CoreHelper;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;

class Data extends CoreHelper
{
    const XML_PATH_GENERAL = 'seo/general/';
    const XML_PATH_META = 'seo/meta/';
    const XML_PATH_RICHSNIPPETS = 'seo/richsnippets/';
    const XML_PATH_HTACCESS = 'seo/htaccess/';
    const XML_PATH_ROBOTS = 'seo/robots/';
    const XML_PATH_HTML_SITEMAP = 'seo/htmlsitemap/';
    const XML_PATH_HREFLANG_TAG = 'seo/hreflang/';
    const XML_PATH_VERIFICATION = 'seo/verification/';
    /**
     * @type \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @type \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;
    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        StoreManagerInterface $storeManager,
        ObjectManagerInterface $objectManager
    ) {
    
        $this->objectManager = $objectManager;
        $this->_storeManager = $storeManager;
        $this->_moduleManager = $context->getModuleManager();

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @param      $field
     * @param null $storeId
     *
     * @return mixed
     */
    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param      $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getGeneralConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_GENERAL . $code, $storeId);
    }

    /**
     * @param      $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getMetaConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_META . $code, $storeId);
    }


    /**
     * @param $code
     * @param null $storeId
     * @return mixed
     */
    public function getVerficationConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_VERIFICATION . $code, $storeId);
    }

    /**
     * @param      $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getRichsnippetsConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_RICHSNIPPETS . $code, $storeId);
    }

    /**
     * @param      $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getHtaccessConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_HTACCESS . $code, $storeId);
    }

    /**
     * @param      $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getRobotsConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_ROBOTS . $code, $storeId);
    }




    /**
     * @param      $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getHtmlsitemapConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_HTML_SITEMAP . $code, $storeId);
    }


    /**
     * info config
     * @param null $code
     * @return mixed
     */
    public function getInfoConfig($code = null)
    {
        return $this->getConfigValue('seo/info/' . $code);
    }

    /**
     * get social share config
     * @param null $code
     * @return mixed
     */
    public function getSocialShares($code = null)
    {
        return $this->getConfigValue('seo/social_shares/' . $code);
    }


    /**
     * social profiles config
     * @param null $code
     * @return mixed
     */
    public function getSocialProfiles($code = null)
    {
        return $this->getConfigValue('seo/social_profiles/' . $code);
    }

    /**
     * get duplicate content config
     * @param null $code
     * @return mixed
     */
    public function getDuplicateConfig($code = null)
    {
        return $this->getConfigValue('seo/duplicate/' . $code);
    }


    /**
     * convert array to options
     *
     * @access public
     *
     * @param $options
     *
     * @return array
     * @author Sam
     */
    public function convertOptions($options)
    {
        $converted = [];
        foreach ($options as $option) {
            if (isset($option['value']) && !is_array($option['value'])
                && isset($option['label'])
                && !is_array($option['label'])
            ) {
                $converted[$option['value']] = $option['label'];
            }
        }

        return $converted;
    }


    /**
     * get html sitemap
     * @return string
     */
    public function getHtmlSitemapUrl()
    {
        return $this->_getUrl('mageplaza_seo/sitemap');
    }

    /**
     * @param null $code
     * @return mixed
     */
    public function getHreflang($code = null)
    {
        return $this->getConfigValue(self::XML_PATH_HREFLANG_TAG . $code);
    }

    /**
     * Whether a module is enabled in the configuration or not
     *
     * @param string $moduleName Fully-qualified module name
     * @return boolean
     */
    public function isModuleEnabled($moduleName)
    {
        return $this->_moduleManager->isEnabled($moduleName);
    }

    /**
     * Whether a module output is permitted by the configuration or not
     *
     * @param string $moduleName Fully-qualified module name
     * @return boolean
     */
    public function isOutputEnabled($moduleName)
    {
        return $this->_moduleManager->isOutputEnabled($moduleName);
    }


    /**
     * check module active
     * @param $moduleName
     * @return bool
     */
    public function checkModuleActive($moduleName)
    {
        return ($this->isModuleEnabled($moduleName) && $this->isOutputEnabled($moduleName));
    }
}
