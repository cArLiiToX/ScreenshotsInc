<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Test\Unit\Controller\Adminhtml\Statistic;

use Magento\Backend\App\Action\Context;
use Aheadworks\Rbslider\Controller\Adminhtml\Statistic\Index;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Title;

/**
 * Test for \Aheadworks\Rbslider\Controller\Adminhtml\Statistic\Index
 */
class IndexTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Index
     */
    private $controller;

    /**
     * @var PageFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultPageFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->resultPageFactoryMock = $this->getMock(PageFactory::class, ['create'], [], '', false);
        $contextMock = $objectManager->getObject(
            Context::class,
            []
        );

        $this->controller = $objectManager->getObject(
            Index::class,
            [
                'context' => $contextMock,
                'resultPageFactory' => $this->resultPageFactoryMock
            ]
        );
    }

    /**
     * Testing of execute method
     */
    public function testExecute()
    {
        $titleMock = $this->getMock(Title::class, ['prepend'], [], '', false);
        $pageConfigMock = $this->getMock(Config::class, ['getTitle'], [], '', false);
        $pageConfigMock->expects($this->once())
            ->method('getTitle')
            ->willReturn($titleMock);
        $resultPageMock = $this->getMock(Page::class, ['setActiveMenu', 'getConfig'], [], '', false);
        $resultPageMock->expects($this->any())
            ->method('setActiveMenu')
            ->willReturnSelf();
        $resultPageMock->expects($this->any())
            ->method('getConfig')
            ->willReturn($pageConfigMock);
        $this->resultPageFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultPageMock);

        $this->assertSame($resultPageMock, $this->controller->execute());
    }
}
