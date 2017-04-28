<?php
namespace Aheadworks\Rbslider\Controller\Adminhtml\Slide;

use Magento\Backend\App\Action\Context;
use Aheadworks\Rbslider\Api\Data\SlideInterfaceFactory;
use Aheadworks\Rbslider\Api\Data\SlideInterface;
use Aheadworks\Rbslider\Api\SlideRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Stdlib\DateTime;
use Aheadworks\Rbslider\Model\Source\ImageType;

/**
 * Class Save
 * @package Aheadworks\Rbslider\Controller\Adminhtml\Slide
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aheadworks_Rbslider::slides';

    /**
     * @var SlideRepositoryInterface
     */
    private $slideRepository;

    /**
     * @var SlideInterfaceFactory
     */
    private $slideDataFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param Context $context
     * @param SlideRepositoryInterface $slideRepository
     * @param SlideInterfaceFactory $slideDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataPersistorInterface $dataPersistor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        SlideRepositoryInterface $slideRepository,
        SlideInterfaceFactory $slideDataFactory,
        DataObjectHelper $dataObjectHelper,
        DataPersistorInterface $dataPersistor,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->slideRepository = $slideRepository;
        $this->slideDataFactory = $slideDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPersistor = $dataPersistor;
        $this->storeManager = $storeManager;
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $data = $this->prepareData($data);
            $id = isset($data['id']) ? $data['id'] : false;
            try {
                $slideDataObject = $id
                    ? $this->slideRepository->get($id)
                    : $this->slideDataFactory->create();
                $this->dataObjectHelper->populateWithArray(
                    $slideDataObject,
                    $data,
                    SlideInterface::class
                );
                if (!$slideDataObject->getId()) {
                    $slideDataObject->setId(null);
                }
                $slide = $this->slideRepository->save($slideDataObject);
                $this->dataPersistor->clear('aw_rbslider_slide');
                $this->messageManager->addSuccessMessage(__('Slide was successfully saved'));
                if ($this->getRequest()->getParam('back') == 'edit') {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $slide->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the slide'));
            }
            $this->dataPersistor->set('aw_rbslider_slide', $data);
            if ($id) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $id, '_current' => true]);
            }
            return $resultRedirect->setPath('*/*/new', ['_current' => true]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Prepare data after save
     *
     * @param array $data
     * @return array
     */
    private function prepareData(array $data)
    {
        if (!isset($data['banner_ids'])) {
            $data['banner_ids'] = [];
        }
        if ($this->storeManager->isSingleStoreMode()) {
            $data['store_ids'] = 0;
        }
        if (empty($data['display_from'])) {
            $data['display_from'] = null;
        } else {
            $data['display_from'] = $this->convertDate($data['display_from']);
        }
        if (empty($data['display_to'])) {
            $data['display_to'] = null;
        } else {
            $data['display_to'] = $this->convertDate($data['display_to']);
        }
        if ($data['img_type'] == ImageType::TYPE_FILE) {
            $data['img_file'] = $data['img_file'][0]['file'];
            $data['img_url'] = '';
        } else {
            $data['img_file'] = '';
        }
        return $data;
    }

    /**
     * Convert date
     *
     * @param string $dateFromForm
     * @return string
     */
    private function convertDate($dateFromForm)
    {
        $locale = new \Zend_Locale($this->_localeResolver->getLocale());
        $date = new \Zend_Date(null, null, $locale);
        $date->setDate($dateFromForm, $locale->getTranslation(null, 'date', $locale));

        return $date->toString(DateTime::DATE_INTERNAL_FORMAT);
    }
}
