<?php
/***************************************************************************
 Extension Name	: Featured Products 
 Extension URL	: https://www.magebees.com/featured-products-extension-for-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
namespace Magebees\Featuredproduct\Controller\Adminhtml\Manage;

use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Magento\Backend\App\Action
{
    protected $_collection;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $collection,
        array $data = []
    ) {
    
        $this->_collection = $collection;
        parent::__construct($context);
    }
    
    public function execute()
    {
        /*Store value get from session whish is saved in newaction.php*/
        
        $session = $this->_objectManager->get('Magento\Customer\Model\Session');
        $storeId=$session->getTestKey();
        $jsHelper = $this->_objectManager->get('Magento\Backend\Helper\Js');
        
        $data=$this->getRequest()->getPost()->toarray();
        $entityIds=$jsHelper->decodeGridSerializedInput($data['links']['featuredproduct']);
        
        if (!is_array($entityIds) || empty($entityIds)) {
            $this->messageManager->addError(__('Please select product(s).'));
             $this->_redirect('*/*/new');
        } else {
            /**display the selected product and save it in database in custom_product_collection table **/
            $records=$this->_collection->getData();
            $entityId_str= implode(",", $entityIds);
            $all_store=$this->_objectManager->create('\Magento\Store\Api\StoreRepositoryInterface')->getList();
             
            $customcollection= $this->_objectManager->get('Magebees\Featuredproduct\Model\Customcollection')->getCollection()->getData();
            foreach ($all_store as $store) {
                $store_id=$store->getId();
                $store_ids[]=$store_id;
            }
               
            if ($customcollection) {
                foreach ($customcollection as $custom) {
                    $custom_storeids[]=$custom['store_id'];
                }
                if (in_array($storeId, $custom_storeids)) {
                    if ($storeId==0) {
                        foreach ($all_store as $store) {
                            $store_id=$store->getId();
                            foreach ($customcollection as $custom) {
                                if ($custom['store_id']==$store_id) {
                                    $id=$custom['id'];
                                    $entity= explode(",", $entityId_str);
                                    $custom_entity= explode(",", $custom['entity_id']);
                                    $result = array_unique(array_merge($entity, $custom_entity));
                                    $new_entityId= implode(",", $result);
                                    $data=$this->_objectManager->create('Magebees\Featuredproduct\Model\Customcollection');
                                    $data->setData('entity_id', $new_entityId);
                                    $data->setData('store_id', $store_id);
                                    $data->setData('id', $id);
                                    $data->save();
                                }
                            }
                            if (!in_array($store_id, $custom_storeids)) {
                                $data=$this->_objectManager->create('Magebees\Featuredproduct\Model\Customcollection');
                                $data->setData('entity_id', $entityId_str);
                                $data->setData('store_id', $store_id);
                                $data->save();
                            }
                        }
                    } else {
                        $store_arr=[0,$storeId];
                        foreach ($store_arr as $store) {
                            foreach ($customcollection as $custom) {
                                if ($custom['store_id']==$store) {
                                    $id=$custom['id'];
                                    if ($store==0) {
                                        $entity= explode(",", $entityId_str);
                                        $custom_entity= explode(",", $custom['entity_id']);
                                        $result = array_unique(array_merge($entity, $custom_entity));
                                        $new_entityId= implode(",", $result);
                                        $data=$this->_objectManager->create('Magebees\Featuredproduct\Model\Customcollection');
                                        $data->setData('entity_id', $new_entityId);
                                        $data->setData('store_id', $store);
                                        $data->setData('id', $id);
                                        $data->save();
                                    } else {
                                        $new_entityId=$entityId_str.",".$custom['entity_id'];
                                        $data=$this->_objectManager->create('Magebees\Featuredproduct\Model\Customcollection');
                                        $data->setData('entity_id', $new_entityId);
                                        $data->setData('store_id', $store);
                                        $data->setData('id', $id);
                                        $data->save();
                                    }
                                }
                            }
                        }
                    }
                } else {
                    if ($storeId==0) {
                        foreach ($all_store as $store) {
                            $store_id=$store->getId();
                            $data=$this->_objectManager->create('Magebees\Featuredproduct\Model\Customcollection');
                            $data->setData('entity_id', $entityId_str);
                            $data->setData('store_id', $store_id);
                            $data->save();
                        }
                    } else {
                        $store_arr=[0,$storeId];
                        foreach ($store_arr as $store) {
                            foreach ($customcollection as $custom) {
                                if ($custom['store_id']==$store) {
                                    $id=$custom['id'];
                                    if ($store==0) {
                                        $entity= explode(",", $entityId_str);
                                        $custom_entity= explode(",", $custom['entity_id']);
                                        $result = array_unique(array_merge($entity, $custom_entity));
                                        $new_entityId= implode(",", $result);
                                        $data=$this->_objectManager->create('Magebees\Featuredproduct\Model\Customcollection');
                                        $data->setData('entity_id', $new_entityId);
                                        $data->setData('store_id', $store);
                                        $data->setData('id', $id);
                                        $data->save();
                                    }
                                }
                            }
                            if (!in_array($store, $custom_storeids)) {
                                $data=$this->_objectManager->create('Magebees\Featuredproduct\Model\Customcollection');
                                $data->setData('entity_id', $entityId_str);
                                $data->setData('store_id', $store);
                                $data->save();
                            }
                        }
                    }
                }
            } else {
                if ($storeId==0) {
                    foreach ($all_store as $store) {
                        $store_id=$store->getId();
                        $data=$this->_objectManager->create('Magebees\Featuredproduct\Model\Customcollection');
                        $data->setData('entity_id', $entityId_str);
                        $data->setData('store_id', $store_id);
                        $data->save();
                    }
                } else {
                    $store_arr=[0,$storeId];
                    foreach ($store_arr as $store) {
                        $data=$this->_objectManager->create('Magebees\Featuredproduct\Model\Customcollection');
                        $data->setData('entity_id', $entityId_str);
                        $data->setData('store_id', $store);
                        $data->save();
                    }
                }
            }
           
            $this->_redirect('*/*/');
        }
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Featuredproduct::featuredproduct');
    }
}
