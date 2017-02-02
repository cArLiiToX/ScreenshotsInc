<?php
namespace Html5design\Cedapi\Plugin;

class DownloadOrder
{
    public function afterGetButtonList(
        \Magento\Backend\Block\Widget\Context $subject,
        $buttonList
    ) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('Magento\Framework\App\Action\Context')->getRequest();
        if ($request->getFullActionName() == 'sales_order_view') {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
            $baseUrl = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB); //->getBaseUrl();
            $urlInterface = $objectManager->get('Magento\Framework\UrlInterface');
            $currentUrl = $urlInterface->getCurrentUrl();
            $arr = explode('order_id/', $currentUrl);
            $arr = explode('/', $arr[1]);
            $orderId = $arr[0];
            $reff_status = 0;
            $order = $objectManager->create('Magento\Sales\Model\Order')->load($orderId);
            $items = $order->getAllItems();
            $res = 0;
            foreach ($items as $item) {
                if (!$item->getParentItemId()) {
                    $ref_id = $item->getCustomDesign();
                    $res += $ref_id;
                    if ($reff_status == 0 && $ref_id != null && $ref_id != 0) {
                        $reff_status = 1;
                    }
                }
            }
            if ($reff_status) {
                $downloadUrl = $baseUrl .'designer-tool/designer-api/index.php?reqmethod=downloadOrderZipAdmin&order_id='. $orderId .'&increment_id='.$orderId;
                $buttonList->add(
                    'download',
                    [
                        'label' => __('Download'),
                        'onclick' => 'setLocation("' . $downloadUrl . '")',
                        'class' => 'ship',
                    ]
                );
            }
        }
        return $buttonList;
    }
}
