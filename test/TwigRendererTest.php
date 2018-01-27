<?php namespace Whip\Test;

/**
 * Please see the included LICENSE.txt with this source code. If no
 * LICENSE.txt was provided, then all rights for the source code in
 * this file are reserved by Khalifah Khalil Shabazz
 */

use PHPUnit\Framework\TestCase;
use Twig_Environment;
use Twig_Template;
use Whip\TwigRenderer;

/**
 * Class TwigRendererTest
 *
 * @package \Whip\Tests
 * @coversDefaultClass \Whip\TwigRenderer
 */
class TwigRendererTest extends TestCase
{
    /** @var \Twig_Environment|\PHPUnit_Framework_MockObject_MockObject */
    private $mockRenderer;

    private $mockTemplate;

    public function setUp()
    {
        $this->mockRenderer = $this->getMockBuilder(Twig_Environment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockTemplate = $this->getMockBuilder(Twig_Template::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @covers ::__construct
     */
    public function testCanInitialize()
    {
        $tr = new TwigRenderer($this->mockRenderer);

        $this->assertInstanceOf(TwigRenderer::class, $tr);
    }

    /**
     * @covers ::withTemplate
     * @uses \Whip\TwigRenderer::__construct
     * @uses \Whip\TwigRenderer::render
     */
    public function testCanSetATemplate()
    {
        $fixture = 'test';
        $data = [];

        $this->mockRenderer->expects($this->once())
            ->method('load')
            ->with($fixture)
            ->willReturn($this->mockTemplate);

        $this->mockTemplate->expects($this->once())
            ->method('render')
            ->willReturn('');

        $tr = new TwigRenderer($this->mockRenderer);

        $tr->withTemplate($fixture)
            ->render($data);
    }

    /**
     * @covers ::render
     * @uses \Whip\TwigRenderer::__construct
     * @uses \Whip\TwigRenderer::withTemplate
     */
    public function testCanRenderATemplate()
    {
        $fixture = 'test';
        $data = [];

        $this->mockRenderer->expects($this->once())
            ->method('load')
            ->with($fixture)
            ->willReturn($this->mockTemplate);

        $this->mockTemplate->expects($this->once())
            ->method('render')
            ->with($data)
            ->willReturn('1234');

        $tr = new TwigRenderer($this->mockRenderer);

        $actual = $tr->withTemplate($fixture)
            ->render($data);

        $this->assertEquals('1234', $actual);
    }

    /**
     * @covers ::addData
     * @uses \Whip\TwigRenderer::__construct
     */
    public function testAddDataToRenderer()
    {
        $fixture = 'test';
        $data = ['test' => 1234];

        $this->mockRenderer->expects($this->once())
            ->method('addGlobal')
            ->with($fixture);

        $tr = new TwigRenderer($this->mockRenderer);

        $tr->addData($data);
    }
}
