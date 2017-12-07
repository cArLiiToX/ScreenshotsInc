<?php
/***************************************************************************
 Extension Name	: Featured Products 
 Extension URL	: https://www.magebees.com/featured-products-extension-for-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
namespace Magebees\Featuredproduct\Model;

class Categories implements \Magento\Framework\Option\ArrayInterface
{
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Catalog\Model\ResourceModel\Category\Tree $categorytree,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_categorytree = $categorytree;
        $this->_categoryFactory = $categoryFactory;
    }
    public function buildCategoriesMultiselectValues($node, $values, $level = 0)
    {
        $nonEscapableNbspChar = html_entity_decode('&#160;', ENT_NOQUOTES, 'UTF-8');

        $level++;
        if ($level > 2) {
            $values[$node->getId()]['value'] = $node->getId();
            $values[$node->getId()]['label'] = str_repeat($nonEscapableNbspChar, ($level - 3) * 5).$node->getName();
        }

        foreach ($node->getChildren() as $child) {
            $values = $this->buildCategoriesMultiselectValues($child, $values, $level);
        }

        return $values;
    }

    public function toOptionArray()
    {
        $tree = $this->_categorytree->load();

        $parentId = 1;

        $root = $tree->getNodeById($parentId);

        if ($root && $root->getId() == 1) {
            $root->setName('Root');
        }

        $collection = $this->_categoryFactory->create()->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('is_active');

        $tree->addCollectionData($collection, true);

        $values['---'] = [
            'value' => '',
            'label' => '',
        ];
        return $this->buildCategoriesMultiselectValues($root, $values);
    }
}
