<?php namespace Whip\Controllers\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Whip\Controllers\Html;
use Whip\Tests\Mocks\MockHtmlController;
use Whip\View;

/**
 * Class HtmlTest
 * @package Whip\Controllers\Tests
 * @coversDefaultClass \Whip\Controllers\Html
 */
class HtmlTest extends TestCase
{
    /** @var \Whip\Controllers\Html|\PHPUnit_Framework_MockObject_MockObject */
    private $htmlController;

    /** @var \Psr\Http\Message\ServerRequestInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $mockRequest;

    /** @var \Psr\Http\Message\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $mockResponse;

    /** @var \Whip\View|\PHPUnit_Framework_MockObject_MockObject */
    private $mockView;

    public function setUp()
    {
        $this->mockRequest = $this->createMock(ServerRequestInterface::class);
        $this->mockResponse = $this->createMock(ResponseInterface::class);
        $this->mockView = $this->createMock(View::class);

        $this->htmlController = new MockHtmlController(
            $this->mockRequest,
            $this->mockResponse,
            $this->mockView
        );
    }

    /**
     * @covers ::__construct
     */
    public function testCanInitialize()
    {
        $this->assertInstanceOf(Html::class, $this->htmlController);
    }
}
