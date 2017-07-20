<?php namespace Whip\Tests\Controllers;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Whip\Controllers\TextHtml;
use Whip\Form;
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
    private $htmlController;

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

        $this->htmlController = $this->getMockForAbstractClass(
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
        $this->assertInstanceOf(TextHtml::class, $this->htmlController);
    }

    /**
     * @covers ::render
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
            ->willReturn('test');

        $this->mockView->method('render')
            ->willReturn('test');

        $this->mockResponse->expects($this->once())
            ->method('withBody')
            ->willReturn($this->mockResponse);

        $this->mockFormService->expects($this->once())
            ->method('getRenderData')
            ->willReturn([]);

        $actual = $this->htmlController->render($this->mockView);

        $this->assertEquals($this->mockResponse, $actual);
    }

    /**
     * @covers ::render
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

        $this->htmlController->render($this->mockView);
    }

    /**
     * @covers ::render
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

        $this->htmlController->render($this->mockView);
    }

    /**
     * @covers ::redirectTo
     */
    public function testCanPerformARedirect()
    {
        $mockUri = $this->createMock(UriInterface::class);
        $mockUri->expects($this->once())
            ->method('__toString')
            ->willReturn('test');

        $this->mockFormService->expects($this->once())
            ->method('process');

        $this->mockResponse->expects($this->once())
            ->method('withStatus')
            ->with(302)
            ->willReturnSelf();

        $this->mockResponse->expects($this->once())
            ->method('withHeader')
            ->with($this->identicalTo('Location'), $this->identicalTo('test'))
            ->willReturnSelf();

        $actual = $this->htmlController->redirectTo($mockUri, 302);

        $this->assertEquals($this->mockResponse, $actual);

    }
}
