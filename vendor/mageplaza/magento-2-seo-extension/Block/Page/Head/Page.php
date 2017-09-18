<?php

namespace Mageplaza\Seo\Block\Page\Head;

use Mageplaza\Seo\Block\AbstractSeo;

class Page extends AbstractSeo
{
    public function getDefaultContent()
    {
        return null;
    }

    /**
     * get store description
     * @return mixed
     */
    public function getStoreDescription()
    {
        return $this->helperData->getInfoConfig('business_description');
    }

    public function entityEnable()
    {
        return $this->hreflang->hasEnableForEntity('enable_page');
    }
}
