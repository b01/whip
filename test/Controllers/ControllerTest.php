<?php namespace Whip\Tests\Controllers;

/**
 * Please see the included LICENSE.txt with this source code. If no
 * LICENSE.txt was provided, then all rights for the source code in
 * this file are reserved by Khalifah Khalil Shabazz
 */

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Whip\Controllers\Controller;
use PHPUnit\Framework\TestCase;

/**
 * Class ControllerTest
 *
 * @package \Whip\Tests\Controllers
 * @coversDefaultClass \Whip\Controllers\Controller
 */
class ControllerTest extends TestCase
{
    /** @var \Whip\Controllers\Controller|\PHPUnit_Framework_MockObject_MockObject */
    private $sut;

    /** @var \Psr\Http\Message\ServerRequestInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $mockRequest;

    /** @var \Psr\Http\Message\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $mockResponse;

    public function setUp()
    {
        $this->mockRequest = $this->createMock(ServerRequestInterface::class);
        $this->mockResponse = $this->createMock(ResponseInterface::class);

        $this->sut = $this->getMockForAbstractClass(
            Controller::class,
            [
                $this->mockRequest,
                $this->mockResponse
            ]
        );
    }

    /**
     * @covers ::__construct
     */
    public function testCanInitialize()
    {
        $this->assertInstanceOf(Controller::class, $this->sut);
    }

    /**
     * @covers ::redirectTo
     * @uses \Whip\Controllers\Controller::__construct
     */
    public function testCanPerformARedirect()
    {
        $mockUri = $this->createMock(UriInterface::class);
        $mockUri->expects($this->once())
            ->method('__toString')
            ->willReturn('test');

        $mockUri->expects($this->once())
            ->method('withPort')
            ->with(443)
            ->willReturnSelf();

        $mockUri->expects($this->once())
            ->method('withScheme')
            ->with('https')
            ->willReturnSelf();

        $mockUri->expects($this->once())
            ->method('withPath')
            ->with('/test')
            ->willReturnSelf();

        $this->mockResponse->expects($this->once())
            ->method('withStatus')
            ->with(302)
            ->willReturnSelf();

        $this->mockResponse->expects($this->once())
            ->method('withHeader')
            ->with($this->identicalTo('Location'), $this->identicalTo('test'))
            ->willReturnSelf();

        $this->mockRequest->expects($this->once())
            ->method('getUri')
            ->willReturn($mockUri);

        $actual = $this->sut->redirectTo(302, '/test');

        $this->assertEquals($this->mockResponse, $actual);
    }

    /**
     * @covers ::getHttpMessageBody
     */
    public function testCanSetResponseBodyWithAString()
    {
        $stream = $this->sut->getHttpMessageBody(__FUNCTION__);
        $this->assertEquals(__FUNCTION__, (string)$stream);
    }

    /**
     * @covers ::redirectTo
     */
    public function testCanCarryQueryStringWhenRedirectingToANewUrl()
    {
        $fixture = ['test' => __FUNCTION__];

        $mockUri = $this->createMock(UriInterface::class);
        $mockUri->expects($this->once())
            ->method('withScheme')
            ->willReturnSelf();
        $mockUri->expects($this->once())
            ->method('withPort')
            ->willReturnSelf();
        $mockUri->expects($this->once())
            ->method('withPath')
            ->willReturnSelf();
        $mockUri->expects($this->once())
            ->method('withQuery')
            ->with(\http_build_query($fixture))
            ->willReturnSelf();

        $this->mockRequest->expects($this->once())
            ->method('getUri')
            ->willReturn($mockUri);

        $this->mockResponse->expects($this->once())
            ->method('withStatus')
            ->willReturnSelf();

        $this->sut->redirectTo(-1, '2fdafa', $fixture);
    }
}
