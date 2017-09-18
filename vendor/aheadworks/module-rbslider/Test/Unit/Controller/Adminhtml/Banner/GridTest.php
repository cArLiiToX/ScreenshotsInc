<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Test\Unit\Controller\Adminhtml\Banner;

use Magento\Backend\App\Action\Context;
use Aheadworks\Rbslider\Controller\Adminhtml\Banner\Grid;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\View\Layout;
use Aheadworks\Rbslider\Block\Adminhtml\Banner\Edit\Tab\Grid\Slide;

/**
 * Test for \Aheadworks\Rbslider\Controller\Adminhtml\Banner\Grid
 */
class GridTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Grid
     */
    private $controller;

    /**
     * @var LayoutFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $layoutFactoryMock;

    /**
     * @var RawFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultRawFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->layoutFactoryMock = $this->getMock(LayoutFactory::class, ['create'], [], '', false);
        $this->resultRawFactoryMock = $this->getMock(RawFactory::class, ['create'], [], '', false);
        $contextMock = $objectManager->getObject(
            Context::class,
            []
        );

        $this->controller = $objectManager->getObject(
            Grid::class,
            [
                'context' => $contextMock,
                'layoutFactory' => $this->layoutFactoryMock,
                'resultRawFactory' => $this->resultRawFactoryMock
            ]
        );
    }

    /**
     * Testing of execute method
     */
    public function testExecute()
    {
        $slideGridHtml = 'html content';

        $slideMock = $this->getMock(Slide::class, ['toHtml'], [], '', false);
        $slideMock->expects($this->once())
            ->method('toHtml')
            ->willReturn($slideGridHtml);
        $layoutMock = $this->getMock(Layout::class, ['createBlock'], [], '', false);
        $layoutMock->expects($this->once())
            ->method('createBlock')
            ->with(Slide::class, 'slide.banner.grid')
            ->willReturn($slideMock);
        $this->layoutFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($layoutMock);

        $resultRawMock = $this->getMock(Raw::class, ['setContents'], [], '', false);
        $resultRawMock->expects($this->any())
            ->method('setContents')
            ->with($slideGridHtml)
            ->willReturnSelf();
        $this->resultRawFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultRawMock);

        $this->assertSame($resultRawMock, $this->controller->execute());
    }
}
