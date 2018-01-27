<?php namespace Whip\Test;

/**
 * Please see the included LICENSE.txt with this source code. If no
 * LICENSE.txt was provided, then all rights for the source code in
 * this file are reserved by Khalifah Khalil Shabazz
 */

use PHPUnit\Framework\TestCase;
use Whip\Renderer;
use Whip\HtmlView;

/**
 * Class HtmlViewTest
 *
 * @package \Whip\Test
 * @coversDefaultClass \Whip\HtmlView
 */
class HtmlViewTest extends TestCase
{
    /** @var \Whip\Renderer|\PHPUnit_Framework_MockObject_MockObject */
    protected $mockRenderer;

    public function setUp()
    {
        $this->mockRenderer = $this->createMock(Renderer::class);
    }

    /**
     * @covers ::__construct
     */
    public function testCanInitializeTheView()
    {
        $htmlView = $this->createMock(HtmlView::class);

        $htmlView->expects($this->once())
            ->method('addData')
            ->with($this->equalTo('title'), $this->equalTo('test'));

        $htmlView->__construct($this->mockRenderer, 'test');
    }
}
