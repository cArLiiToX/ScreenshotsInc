<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Test\Unit\Block\Adminhtml\Slide\Edit\Button;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Rbslider\Block\Adminhtml\Slide\Edit\Button\Save;

/**
 * Test for \Aheadworks\Rbslider\Block\Adminhtml\Slide\Edit\Button\Save
 */
class SaveTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Save
     */
    private $button;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->button = $objectManager->getObject(
            Save::class,
            []
        );
    }

    /**
     * Testing of return value of getButtonData method
     */
    public function testGetButtonData()
    {
        $this->assertTrue(is_array($this->button->getButtonData()));
    }
}
