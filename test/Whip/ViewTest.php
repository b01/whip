<?php namespace Whip\Tests;

/**
 * Please see the included LICENSE.txt with this source code. If no
 * LICENSE.txt was provided, then all rights for the source code in
 * this file are reserved by Khalifah Khalil Shabazz
 */

use Whip\Renderer;
use Whip\View;
use PHPUnit\Framework\TestCase;

/**
 * Class ViewTest
 *
 * @package \Whip\Tests
 * @coversDefaultClass \Whip\View
 */
class ViewTest extends TestCase
{
    /** @var \Whip\Renderer|\PHPUnit_Framework_MockObject_MockObject */
    private $mockRenderer;

    /** @var \Whip\View|\PHPUnit_Framework_MockObject_MockObject */
    private $view;

    public function setUp()
    {
        $this->mockRenderer = $this->createMock(Renderer::class);
        $this->view = $this->getMockForAbstractClass(View::class, [$this->mockRenderer]);
    }

    /**
     * @covers ::__construct
     */
    public function testCanInitialize()
    {
        $view = $this->getMockForAbstractClass(View::class, [$this->mockRenderer]);

        $this->assertInstanceOf(View::class, $view);
    }

    /**
     * @covers ::render
     */
    public function testCanRender()
    {
        $this->setupView(__FUNCTION__, $this->view);

        $actual = $this->view->render();

        $this->assertEquals(__FUNCTION__, $actual);
    }

    /**
     * @covers ::addData
     * @uses \Whip\View::render
     */
    public function testCanPassDataRenderer()
    {
        $this->setupView(__FUNCTION__, $this->view);

        $this->mockRenderer->expects($this->once())
            ->method('render')
            ->with($this->equalTo(['test' => 1234]));

        $this->view->addData('test', 1234);

        $actual = $this->view->render();

        $this->assertEquals(__FUNCTION__, $actual);
    }

    /**
     * @covers ::__toString
     */
    public function testWillCallRenderWhenCastToString()
    {
        $this->setupView(__FUNCTION__, $this->view);

        $actual = (string) $this->view;
    }

    private function setupView($fixture, View $view)
    {
        $fixtureTemplateFile = $fixture;

        $this->mockRenderer->expects($this->once())
            ->method('withTemplate')
            ->with($fixtureTemplateFile);

        $this->mockRenderer->expects($this->once())
            ->method('render')
            ->willReturn($fixtureTemplateFile);

        $view->expects($this->once())
            ->method('build');

        $view->expects($this->once())
            ->method('getTemplateFile')
            ->willReturn($fixtureTemplateFile);

        return $view;
    }
}
