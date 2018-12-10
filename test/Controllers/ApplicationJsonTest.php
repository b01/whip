<?php namespace Whip\Test\Controllers;
/**
 * Please see the included LICENSE.txt with this source code. If no
 * LICENSE.txt was provided, then all rights for the source code in
 * this file are reserved by Khalifah Khalil Shabazz
 */

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Whip\Controllers\ApplicationJson;
use PHPUnit\Framework\TestCase;
use Whip\Controllers\Controller;
use Whip\View;

/**
 * Class ApplicationJsonTest
 * @package \Whip\Test\Controllers
 * @coversDefaultClass \Whip\Controllers\ApplicationJson
 */
class ApplicationJsonTest extends TestCase
{
    /** @var \Whip\Controllers\TextHtml|\PHPUnit_Framework_MockObject_MockObject */
    private $sut;

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
    }

    /**
     * @covers ::__invoke
     */
    public function testCanPerformARedirect()
    {
        $fixture = ['test'=>1234];
        $encodeFixture = \json_encode($fixture);

        $this->mockResponse->expects($this->once())
            ->method('withHeader')
            ->with($this->identicalTo('Content-Type'), $this->identicalTo('application/json'))
            ->willReturnSelf();

        $this->mockResponse->expects($this->once())
            ->method('withBody')
            ->willReturnSelf();

        $this->mockView->expects($this->once())
            ->method('render')
            ->willReturn($encodeFixture);

        $this->sut = new ApplicationJson();

        $actual = $this->sut->__invoke(
            $this->mockRequest,
            $this->mockResponse,
            $this->mockView
        );

        $this->assertEquals($this->mockResponse, $actual);
    }
}
