<?php
/***************************************************************************
 Extension Name	: Featured Products 
 Extension URL	: https://www.magebees.com/featured-products-extension-for-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
namespace  Magebees\Featuredproduct\Controller\Adminhtml\Manage;

class MassDelete extends \Magento\Backend\App\Action
{
 
    public function execute()
    {
        /*Store value get from session whish is saved in newaction.php*/
        $session = $this->_objectManager->get('Magento\Customer\Model\Session');
        $storeId=$session->getTestKey();
        $entityIds = $this->getRequest()->getParam('product');
        
        if (!is_array($entityIds) || empty($entityIds)) {
            $this->messageManager->addError(__('Please select product(s).'));
        } else {
            try {
                $count=0;
                 $count=count($entityIds);
                foreach ($entityIds as $entityId) {
                    $customcollection= $this->_objectManager->get('Magebees\Featuredproduct\Model\Customcollection')->getCollection()->getData();
                    foreach ($customcollection as $custom) {
                        if ($storeId!=0) {
                            if ($custom['store_id']==$storeId) {
                                $id=$custom['id'];
                                $entity_arr=explode(",", $custom['entity_id']);
                                if (in_array($entityId, $entity_arr)) {
                                    foreach (array_keys($entity_arr, $entityId) as $key) {
                                        unset($entity_arr[$key]);
                                        $new_entity=implode(",", $entity_arr);
                                        $data=$this->_objectManager->create('Magebees\Featuredproduct\Model\Customcollection');
                                        $data->setData('entity_id', $new_entity);
                                        $data->setData('store_id', $storeId);
                                        $data->setData('id', $id);
                                        $data->save();
                                    }
                                }
                            }
                        } else {
                            $all_store=$this->_objectManager->create('\Magento\Store\Api\StoreRepositoryInterface')->getList();
                            
                            foreach ($all_store as $store) {
                                $store_id=$store->getId();
                                foreach ($customcollection as $custom) {
                                    if ($custom['store_id']==$store_id) {
                                        $id=$custom['id'];
                                        $entity_arr=explode(",", $custom['entity_id']);
                                        if (in_array($entityId, $entity_arr)) {
                                            foreach (array_keys($entity_arr, $entityId) as $key) {
                                                unset($entity_arr[$key]);
                                                $new_entity=implode(",", $entity_arr);
                                                $data=$this->_objectManager->create('Magebees\Featuredproduct\Model\Customcollection');
                                                $data->setData('entity_id', $new_entity);
                                                $data->setData('store_id', $store_id);
                                                $data->setData('id', $id);
                                                $data->save();
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                
                $this->messageManager->addSuccess(
                    __('A total of   '.$count .'  record(s) have been deleted.', count($entityId))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
         $this->_redirect('*/*/');
    }
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Featuredproduct::featuredproduct');
    }
}
