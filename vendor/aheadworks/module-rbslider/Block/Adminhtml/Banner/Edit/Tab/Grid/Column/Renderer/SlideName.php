<?php
namespace Aheadworks\Rbslider\Block\Adminhtml\Banner\Edit\Tab\Grid\Column\Renderer;

/**
 * Class SlideName
 * @package Aheadworks\Rbslider\Block\Adminhtml\Banner\Edit\Tab\Grid\Column\Renderer
 */
class SlideName extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
{
    /**
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $url = $this->getUrl(
            'aw_rbslider_admin/slide/edit',
            ['id' => $row->getId()]
        );
        return '<a href="' . $url . '" target="_blank">' . $row->getName() . '</a>';
    }
}
