<?php namespace Whip\Test\Controllers;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Whip\Controllers\TextHtml;
use Whip\FormService;
use Whip\View;

/**
 * Class TextHtmlTest
 *
 * @package \Whip\Controllers\Tests
 * @coversDefaultClass \Whip\Controllers\TextHtml
 */
final class TextHtmlTest extends TestCase
{
    /** @var \Whip\Controllers\TextHtml|\PHPUnit_Framework_MockObject_MockObject */
    private $sut;

    /** @var \Psr\Http\Message\ServerRequestInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $mockFormService;

    /** @var \Psr\Http\Message\ServerRequestInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $mockRequest;

    /** @var \Psr\Http\Message\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $mockResponse;

    /** @var \Whip\View|\PHPUnit_Framework_MockObject_MockObject */
    private $mockView;

    public function setUp()
    {
        $this->mockFormService = $this->createMock(FormService::class);
        $this->mockRequest = $this->createMock(ServerRequestInterface::class);
        $this->mockResponse = $this->createMock(ResponseInterface::class);
        $this->mockView = $this->createMock(View::class);

        $this->sut = $this->getMockForAbstractClass(
            TextHtml::class,
            [
                $this->mockRequest,
                $this->mockResponse,
                $this->mockFormService,
            ]
        );
    }

    /**
     * @covers ::__construct
     */
    public function testCanInitialize()
    {
        $this->assertInstanceOf(TextHtml::class, $this->sut);
    }

    /**
     * @covers ::render
     * @uses \Whip\Controllers\TextHtml::__construct
     */
    public function testCanRender()
    {
        $this->mockRequest->expects($this->once())
            ->method('getParsedBody')
            ->willReturn([]);

        $this->mockRequest->expects($this->once())
            ->method('getQueryParams')
            ->willReturn([]);

        $this->mockView->expects($this->any())
            ->method('addData')
            ->willReturnSelf();

        $this->mockView->method('render')
            ->willReturn('test');

        $this->mockResponse->expects($this->once())
            ->method('withBody')
            ->willReturn($this->mockResponse);

        $this->mockFormService->expects($this->once())
            ->method('getRenderData')
            ->willReturn([]);

        $actual = $this->sut->render($this->mockView);

        $this->assertEquals($this->mockResponse, $actual);
    }

    /**
     * @covers ::render
     * @uses \Whip\Controllers\TextHtml::__construct
     */
    public function testWillCleanQueryParamsBeforeAddingToAView()
    {
        $this->mockRequest->expects($this->once())
            ->method('getParsedBody')
            ->willReturn([]);

        $this->mockRequest->expects($this->once())
            ->method('getQueryParams')
            ->willReturn(['test'=> '1234<>']);

        $this->mockView->expects($this->any())
            ->method('addData')
            ->withConsecutive(
                [$this->equalTo('postVars'), $this->anything()],
                [
                    $this->equalTo('queryVars'),
                    $this->callback(function ($data) {
                        return $data['test'] === '1234&lt;&gt;';
                    })
                ],
                [$this->equalTo('forms'), $this->anything()]
            );

        $this->mockView->expects($this->once())
            ->method('render');

        $this->mockResponse->expects($this->once())
            ->method('withBody')
            ->willReturn($this->mockResponse);

        $this->mockFormService->expects($this->once())
            ->method('getRenderData')
            ->willReturn([]);

        $this->sut->render($this->mockView);
    }

    /**
     * @covers ::render
     * @uses \Whip\Controllers\TextHtml::__construct
     */
    public function testWillCleanPostParamsBeforeAddingToAView()
    {
        $this->mockRequest->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['test'=> '1234<>']);

        $this->mockRequest->expects($this->once())
            ->method('getQueryParams')
            ->willReturn([]);

        $this->mockView->expects($this->any())
            ->method('addData')
            ->withConsecutive(
                [$this->equalTo('postVars'),
                    $this->callback(function ($data) {
                        return $data['test'] === '1234&lt;&gt;';
                    })],
                [$this->equalTo('queryVars'), $this->anything()],
                [$this->equalTo('forms'), $this->anything()]
            );

        $this->mockView->expects($this->once())
            ->method('render');

        $this->mockResponse->expects($this->once())
            ->method('withBody')
            ->willReturn($this->mockResponse);

        $this->mockFormService->expects($this->once())
            ->method('getRenderData')
            ->willReturn([]);

        $this->sut->render($this->mockView);
    }

    /**
     * @covers ::withForms
     * @uses \Whip\Controllers\TextHtml::__construct
     * @uses \Whip\Controllers\TextHtml::render
     */
    public function testCanAddForms()
    {
        $this->mockRequest->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['test'=> '1234<>']);

        $this->mockRequest->expects($this->once())
            ->method('getQueryParams')
            ->willReturn([]);

        $this->mockView->expects($this->any())
            ->method('addData')
            ->willReturnSelf();

        $this->mockResponse->expects($this->once())
            ->method('withBody')
            ->willReturn($this->mockResponse);

        $fixture = ['testForm'];
        $this->mockFormService->expects($this->once())
            ->method('getRenderData')
            ->with($this->equalTo($fixture));

        $this->sut->withForms($fixture)
            ->render($this->mockView);
    }
}
