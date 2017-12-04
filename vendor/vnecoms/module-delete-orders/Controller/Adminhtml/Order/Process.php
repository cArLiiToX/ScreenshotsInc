<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\DeleteOrders\Controller\Adminhtml\Order;

use Magento\Backend\App\Action\Context;
use Magento\Sales\Model\OrderFactory;
use Magento\Framework\Controller\ResultFactory;

class Process extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order
     */
    protected $orderResource;
    
    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        OrderFactory $orderFactory
    ) {
        parent::__construct($context);
        $this->orderResource = $orderFactory->create()->getResource();
    }
    
    /**
     * Prepare Sql
     * @param array $ids
     * @return array
     */
    public function prepareSql($ids = []){
        $ids = implode(",", $ids);
        $sql = [];
        $resource = $this->orderResource;
        
        /*DELETE All Related Invoice Item*/
        $sql[] = "DELETE FROM {$resource->getTable('sales_invoice_item')}
            WHERE parent_id IN (SELECT entity_id FROM
            {$resource->getTable('sales_invoice')}
            WHERE order_id IN({$ids}));";
        
        /*DELETE all invoice comment*/
        $sql[] = "DELETE FROM {$resource->getTable('sales_invoice_comment')}
            WHERE parent_id IN
            (SELECT entity_id FROM {$resource->getTable('sales_invoice')}
            WHERE order_id IN({$ids}));";
        
        /*Delete All invoice in invoice grid*/
        $sql[] = "DELETE FROM {$resource->getTable('sales_invoice_grid')}
            WHERE order_id IN({$ids});";
        
        /*Delete All invoices*/
        $sql[] = "DELETE FROM {$resource->getTable('sales_invoice')}
            WHERE order_id IN({$ids});";
        
        
        /*DELETE All Related Shipment Item*/
        $sql[] = "DELETE FROM {$resource->getTable('sales_shipment_item')}
            WHERE parent_id IN (SELECT entity_id FROM
            {$resource->getTable('sales_shipment')}
            WHERE order_id IN({$ids}));";
        
        /*DELETE all shipment comment*/
        $sql[] = "DELETE FROM {$resource->getTable('sales_shipment_comment')}
            WHERE parent_id IN
            (SELECT entity_id FROM {$resource->getTable('sales_shipment')}
            WHERE order_id IN({$ids}));";
        
        /*DELETE all shipment tracks*/
        $sql[] = "DELETE FROM {$resource->getTable('sales_shipment_track')}
            WHERE parent_id IN
            (SELECT entity_id FROM {$resource->getTable('sales_shipment')}
            WHERE order_id IN({$ids}));";
        
         /*Delete All shipments in shipment grid*/
         $sql[] = "DELETE FROM {$resource->getTable('sales_shipment_grid')}
            WHERE order_id IN({$ids});";

        /*Delete All shipments*/
        $sql[] = "DELETE FROM {$resource->getTable('sales_shipment')}
            WHERE order_id IN({$ids});";
        
        
        
        /*DELETE All Related Creditmemo Item*/
        $sql[] = "DELETE FROM {$resource->getTable('sales_creditmemo_item')}
            WHERE parent_id IN (SELECT entity_id FROM
            {$resource->getTable('sales_creditmemo')}
            WHERE order_id IN({$ids}));";
        
        /*DELETE all creditmemo comment*/
        $sql[] = "DELETE FROM {$resource->getTable('sales_creditmemo_comment')}
            WHERE parent_id IN
            (SELECT entity_id FROM {$resource->getTable('sales_creditmemo')}
            WHERE order_id IN({$ids}));";
        
        /*Delete All creditmemos in creditmemo grid*/
        $sql[] = "DELETE FROM {$resource->getTable('sales_creditmemo_grid')}
            WHERE order_id IN({$ids});";
        
        /*Delete All shipments*/
        $sql[] = "DELETE FROM {$resource->getTable('sales_creditmemo')}
            WHERE order_id IN({$ids});";
        
        
        /*DELETE all order tax item*/
        $sql[] = "DELETE FROM {$resource->getTable('sales_order_tax_item')}
            WHERE tax_id IN
            (SELECT tax_id FROM {$resource->getTable('sales_order_tax')}
            WHERE order_id IN({$ids}));";
        
        /*DELETE all order tax*/
        $sql[] = "DELETE FROM {$resource->getTable('sales_order_tax')}
            WHERE order_id IN({$ids});";
        
        /*DELETE All Related order Item*/
        $sql[] = "DELETE FROM {$resource->getTable('sales_order_item')}
            WHERE order_id IN({$ids});";
        
        /*DELETE all order payment*/
        $sql[] = "DELETE FROM {$resource->getTable('sales_order_payment')}
            WHERE parent_id IN({$ids});";
        
        /*DELETE all order status history*/
        $sql[] = "DELETE FROM {$resource->getTable('sales_order_status_history')}
            WHERE parent_id IN({$ids});";
        
        /*DELETE all order address*/
        $sql[] = "DELETE FROM {$resource->getTable('sales_order_address')}
            WHERE parent_id IN({$ids});";
        
        /*Delete All order in order grid*/
        $sql[] = "DELETE FROM {$resource->getTable('sales_order_grid')}
            WHERE entity_id IN({$ids});";
        
        /*Delete All orders*/
        $sql[] = "DELETE FROM {$resource->getTable('sales_order')}
            WHERE entity_id IN({$ids});";
        
        return $sql;
    }
    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        try {
            $ids = $this->getRequest()->getParam('selected');
            if(!$ids || !is_array($ids) || !sizeof($ids))
                throw new \Exception(__("Please select an item to process."));
            
            $sqls = $this->prepareSql($ids);
            foreach($sqls as $sql){
                $this->orderResource->getConnection()->query($sql);
            }
            $this->messageManager->addSuccess(
                __('We deleted %1 order(s).', sizeof($ids))
            );
            
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('sales/order');
    }
}
