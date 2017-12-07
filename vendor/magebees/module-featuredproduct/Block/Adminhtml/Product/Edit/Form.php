<?php
/***************************************************************************
 Extension Name	: Featured Products 
 Extension URL	: https://www.magebees.com/featured-products-extension-for-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
namespace Magebees\Featuredproduct\Block\Adminhtml\Product\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected $_customerAccountService;
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl('*/*/save'),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data'
                ]
            ]
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
