<?php
namespace Aheadworks\Rbslider\Controller\Adminhtml\Banner;

/**
 * Class MassDelete
 * @package Aheadworks\Rbslider\Controller\Adminhtml\Banner
 */
class MassDelete extends AbstractMassAction
{
    /**
     * {@inheritdoc}
     */
    protected function massAction($collection)
    {
        $count = 0;
        foreach ($collection->getItems() as $item) {
            $this->bannerRepository->deleteById($item->getId());
            $count++;
        }
        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) were deleted', $count));
    }
}
