<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Test\Unit\Controller\Adminhtml\Banner;

use Aheadworks\Rbslider\Controller\Adminhtml\Banner\MassStatus;
use Magento\Framework\Message\ManagerInterface;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Backend\Model\View\Result\Redirect;
use Aheadworks\Rbslider\Api\Data\BannerInterface;
use Magento\Backend\App\Action\Context;
use Aheadworks\Rbslider\Api\BannerRepositoryInterface;
use Aheadworks\Rbslider\Model\ResourceModel\Banner\CollectionFactory;
use Aheadworks\Rbslider\Model\ResourceModel\Banner\Collection;
use Aheadworks\Rbslider\Model\Banner;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\App\RequestInterface;

/**
 * Test for \Aheadworks\Rbslider\Controller\Adminhtml\Banner\MassStatus
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MassStatusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MassStatus
     */
    private $controller;

    /**
     * @var RedirectFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultRedirectFactoryMock;

    /**
     * @var ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $messageManagerMock;

    /**
     * @var CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $collectionFactoryMock;

    /**
     * @var Filter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterMock;

    /**
     * @var BannerRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $bannerRepositoryMock;

    /**
     * @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->resultRedirectFactoryMock = $this->getMock(RedirectFactory::class, ['create'], [], '', false);
        $this->bannerRepositoryMock = $this->getMockForAbstractClass(BannerRepositoryInterface::class);
        $this->messageManagerMock = $this->getMockForAbstractClass(ManagerInterface::class);
        $this->collectionFactoryMock = $this->getMock(CollectionFactory::class, ['create'], [], '', false);
        $this->filterMock = $this->getMock(Filter::class, ['getCollection'], [], '', false);
        $this->requestMock = $this->getMock(RequestInterface::class);
        $contextMock = $objectManager->getObject(
            Context::class,
            [
                'request' => $this->requestMock,
                'messageManager' => $this->messageManagerMock,
                'resultRedirectFactory' => $this->resultRedirectFactoryMock
            ]
        );

        $this->controller = $objectManager->getObject(
            MassStatus::class,
            [
                'context' => $contextMock,
                'collectionFactory' => $this->collectionFactoryMock,
                'filter' => $this->filterMock,
                'bannerRepository' => $this->bannerRepositoryMock
            ]
        );
    }

    /**
     * Testing of execute method
     */
    public function testExecute()
    {
        $bannerId = 1;
        $status = 1;
        $count = 1;

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('status')
            ->willReturn($status);
        $bannerModelMock = $this->getMock(Banner::class, ['getId'], [], '', false);
        $bannerModelMock->expects($this->once())
            ->method('getId')
            ->willReturn($bannerId);
        $collectionMock = $this->getMock(Collection::class, ['getItems'], [], '', false);
        $collectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$bannerModelMock]);
        $this->collectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($collectionMock);
        $this->filterMock->expects($this->once())
            ->method('getCollection')
            ->willReturn($collectionMock);

        $bannerMock = $this->getMockForAbstractClass(BannerInterface::class);
        $this->bannerRepositoryMock->expects($this->once())
            ->method('get')
            ->with($bannerId)
            ->willReturn($bannerMock);
        $this->bannerRepositoryMock->expects($this->once())
            ->method('save')
            ->with($bannerMock)
            ->willReturn($bannerMock);

        $this->messageManagerMock->expects($this->once())
            ->method('addSuccessMessage')
            ->with(__('A total of %1 record(s) have been updated', $count))
            ->willReturnSelf();

        $resultRedirectMock = $this->getMock(Redirect::class, ['setPath'], [], '', false);
        $resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/index')
            ->willReturnSelf();
        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultRedirectMock);

        $this->assertSame($resultRedirectMock, $this->controller->execute());
    }
}
