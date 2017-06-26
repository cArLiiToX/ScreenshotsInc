<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Test\Unit\Controller\Adminhtml\Slide;

use Aheadworks\Rbslider\Controller\Adminhtml\Slide\MassAddToBanner;
use Magento\Framework\Message\ManagerInterface;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Backend\Model\View\Result\Redirect;
use Aheadworks\Rbslider\Api\Data\SlideInterface;
use Magento\Backend\App\Action\Context;
use Aheadworks\Rbslider\Api\SlideRepositoryInterface;
use Aheadworks\Rbslider\Model\ResourceModel\Slide\CollectionFactory;
use Aheadworks\Rbslider\Model\ResourceModel\Slide\Collection;
use Aheadworks\Rbslider\Model\Slide;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\App\RequestInterface;

/**
 * Test for \Aheadworks\Rbslider\Controller\Adminhtml\Slide\MassStatus
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MassAddToBannerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MassAddToBanner
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
     * @var SlideRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $slideRepositoryMock;

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
        $this->slideRepositoryMock = $this->getMockForAbstractClass(SlideRepositoryInterface::class);
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
            MassAddToBanner::class,
            [
                'context' => $contextMock,
                'collectionFactory' => $this->collectionFactoryMock,
                'filter' => $this->filterMock,
                'slideRepository' => $this->slideRepositoryMock
            ]
        );
    }

    /**
     * Testing of execute method
     */
    public function testExecute()
    {
        $slideData = [
            'id' => 1,
            'banner_ids' => [
                0 => 1,
                1 => 2
            ]
        ];
        $bannerId = 3;
        $count = 1;

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('banner_id')
            ->willReturn($bannerId);
        $slideModelMock = $this->getMock(Slide::class, ['getId'], [], '', false);
        $slideModelMock->expects($this->once())
            ->method('getId')
            ->willReturn($slideData['id']);
        $collectionMock = $this->getMock(Collection::class, ['getItems'], [], '', false);
        $collectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$slideModelMock]);
        $this->collectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($collectionMock);
        $this->filterMock->expects($this->once())
            ->method('getCollection')
            ->willReturn($collectionMock);

        $slideMock = $this->getMockForAbstractClass(SlideInterface::class);
        $slideMock->expects($this->once())
            ->method('getBannerIds')
            ->willReturn($slideData['banner_ids']);
        $slideMock->expects($this->once())
            ->method('setBannerIds')
            ->willReturn([0 => 1, 1 => 2, 2 => 3]);
        $this->slideRepositoryMock->expects($this->once())
            ->method('get')
            ->with($slideData['id'])
            ->willReturn($slideMock);
        $this->slideRepositoryMock->expects($this->once())
            ->method('save')
            ->with($slideMock)
            ->willReturn($slideMock);

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
