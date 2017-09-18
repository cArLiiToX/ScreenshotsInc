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
namespace Mageplaza\Blog\Block\Adminhtml\Topic\Edit\Tab;

class Post extends \Magento\Backend\Block\Widget\Grid\Extended implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Post collection factory
     *
     * @var \Mageplaza\Blog\Model\ResourceModel\Post\CollectionFactory
     */
    public $postCollectionFactory;

    /**
     * Registry
     *
     * @var \Magento\Framework\Registry
     */
	public $coreRegistry;

    /**
     * Post factory
     *
     * @var \Mageplaza\Blog\Model\PostFactory
     */
	public $postFactory;

    /**
     * constructor
     *
     * @param \Mageplaza\Blog\Model\ResourceModel\Post\CollectionFactory $postCollectionFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Mageplaza\Blog\Model\PostFactory $postFactory
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param array $data
     */
    public function __construct(
        \Mageplaza\Blog\Model\ResourceModel\Post\CollectionFactory $postCollectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Mageplaza\Blog\Model\PostFactory $postFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    ) {
    
        $this->postCollectionFactory = $postCollectionFactory;
        $this->coreRegistry          = $coreRegistry;
        $this->postFactory           = $postFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Set grid params
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('post_grid');
        $this->setDefaultSort('position');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        if ($this->getTopic()->getId()) {
            $this->setDefaultFilter(['in_posts'=>1]);
        }
    }

    /**
     * prepare the collection

     * @return $this
     */
    protected function _prepareCollection()
    {
        /** @var \Mageplaza\Blog\Model\ResourceModel\Post\Collection $collection */
        $collection = $this->postCollectionFactory->create();
        if ($this->getTopic()->getId()) {
            $constraint = 'related.topic_id='.$this->getTopic()->getId();
        } else {
            $constraint = 'related.topic_id=0';
        }
        $collection->getSelect()->joinLeft(
            ['related' => $collection->getTable('mageplaza_blog_post_topic')],
            'related.post_id=main_table.post_id AND '.$constraint,
            ['position']
        );
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_posts',
            [
                'header_css_class'  => 'a-center',
                'type'   => 'checkbox',
                'name'   => 'in_post',
                'values' => $this->_getSelectedPosts(),
                'align'  => 'center',
                'index'  => 'post_id'
            ]
        );
        $this->addColumn(
            'post_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'post_id',
                'type' => 'number',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'title',
            [
                'header' => __('Name'),
                'index' => 'name',
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name'
            ]
        );

        $this->addColumn(
            'position',
            [
                'header' => __('Position'),
                'name'   => 'position',
                'width'  => 60,
                'type'   => 'number',
                'validate_class' => 'validate-number',
                'index' => 'position',
                'editable'  => true,
            ]
        );
        return $this;
    }

    /**
     * Retrieve selected Posts

     * @return array
     */
    protected function _getSelectedPosts()
    {
        $posts = $this->getTopicPosts();
        if (!is_array($posts)) {
            $posts = $this->getTopic()->getPostsPosition();
            return array_keys($posts);
        }
        return $posts;
    }

    /**
     * Retrieve selected Posts

     * @return array
     */
    public function getSelectedPosts()
    {
        $selected = $this->getTopic()->getPostsPosition();
        if (!is_array($selected)) {
            $selected = [];
        } else {
            foreach ($selected as $key => $value) {
                $selected[$key] = ['position' => $value];
            }
        }
        return $selected;
    }

    /**
     * @param \Mageplaza\Blog\Model\Post|\Magento\Framework\Object $item
     * @return string
     */
    public function getRowUrl($item)
    {
        return '#';
    }

    /**
     * get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            '*/*/postsGrid',
            [
                'topic_id' => $this->getTopic()->getId()
            ]
        );
    }

    /**
     * @return \Mageplaza\Blog\Model\Topic
     */
    public function getTopic()
    {
        return $this->coreRegistry->registry('mageplaza_blog_topic');
    }

    /**
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_posts') {
            $postIds = $this->_getSelectedPosts();
            if (empty($postIds)) {
                $postIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('main_table.post_id', ['in'=>$postIds]);
            } else {
                if ($postIds) {
                    $this->getCollection()->addFieldToFilter('main_table.post_id', ['nin'=>$postIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getTabLabel()
    {
        return __('Posts');
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('mageplaza_blog/topic/posts', ['_current' => true]);
    }

    /**
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax only';
    }
}
