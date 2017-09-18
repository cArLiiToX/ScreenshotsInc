<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Test\Unit\Controller\Adminhtml\Banner\Widget;

use Magento\Backend\App\Action\Context;
use Aheadworks\Rbslider\Controller\Adminhtml\Banner\Widget\Chooser;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\View\Layout;
use Aheadworks\Rbslider\Block\Adminhtml\Banner\Widget\Chooser as BlockWidgetChooser;
use Magento\Framework\App\RequestInterface;

/**
 * Test for \Aheadworks\Rbslider\Controller\Adminhtml\Banner\Widget\Chooser
 */
class ChooserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Chooser
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

        $this->layoutFactoryMock = $this->getMock(LayoutFactory::class, ['create'], [], '', false);
        $this->resultRawFactoryMock = $this->getMock(RawFactory::class, ['create'], [], '', false);
        $this->requestMock = $this->getMockForAbstractClass(RequestInterface::class);
        $contextMock = $objectManager->getObject(
            Context::class,
            ['request' => $this->requestMock]
        );

        $this->controller = $objectManager->getObject(
            Chooser::class,
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
        $blockHtml = 'html content';
        $uniqId = 1;

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('uniq_id')
            ->willReturn($uniqId);
        $blockWidgetChooserMock = $this->getMock(BlockWidgetChooser::class, ['toHtml'], [], '', false);
        $blockWidgetChooserMock->expects($this->once())
            ->method('toHtml')
            ->willReturn($blockHtml);
        $layoutMock = $this->getMock(Layout::class, ['createBlock'], [], '', false);
        $layoutMock->expects($this->once())
            ->method('createBlock')
            ->with(BlockWidgetChooser::class, '', ['data' => ['id' => $uniqId]])
            ->willReturn($blockWidgetChooserMock);
        $this->layoutFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($layoutMock);

        $resultRawMock = $this->getMock(Raw::class, ['setContents'], [], '', false);
        $resultRawMock->expects($this->any())
            ->method('setContents')
            ->with($blockHtml)
            ->willReturnSelf();
        $this->resultRawFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultRawMock);

        $this->assertSame($resultRawMock, $this->controller->execute());
    }
}
