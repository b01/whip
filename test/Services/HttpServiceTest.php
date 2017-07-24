<?php namespace Whip\Test\Services;
/**
 * Please see the included LICENSE.txt with this source code. If no
 * LICENSE.txt was provided, then all rights for the source code in
 * this file are reserved by Khalifah Khalil Shabazz
 */

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Whip\Services\HttpService;
use Whip\Test\Mocks\MockService;

/**
 * Class HttpServiceTest
 *
 * @package \Whip\Test\Services
 * @coversDefaultClass \Whip\Services\HttpService
 */
class HttpServiceTest extends TestCase
{
    /** @var \GuzzleHttp\Client|\PHPUnit_Framework_MockObject_MockObject */
    private $mockHttpClient;

    /** @var \GuzzleHttp\Psr7\Request|\PHPUnit_Framework_MockObject_MockObject */
    private $mockRequest;

    /** @var \GuzzleHttp\Psr7\Response|\PHPUnit_Framework_MockObject_MockObject */
    private $mockResponse;

    public function setUp()
    {
        $this->mockHttpClient = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockRequest = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockResponse = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @covers ::__construct
     */
    public function testCanInitialize()
    {
        $service = $this->getMockForAbstractClass(
            HttpService::class,
            [$this->mockHttpClient, '', '', '']
        );

        $this->assertInstanceOf(HttpService::class, $service);
    }

    /**
     * @covers ::send
     * @uses \Whip\Services\HttpService::__construct
     */
    public function testCanSendRequest()
    {
        $this->mockHttpClient->expects($this->once())
            ->method('request')
            ->willReturn($this->mockRequest);

        $this->mockHttpClient->expects($this->once())
            ->method('send')
            ->with($this->mockRequest)
            ->willReturn(1234);

        $service = new MockService($this->mockHttpClient, '', '', '');

        $actual = $service->doSend('test', '/testUrl', [], 'test body');

        $this->assertEquals(1234, $actual);
    }

    /**
     * @covers ::send
     * @uses \Whip\Services\HttpService::__construct
     * @expectedException \Whip\WhipException
     */
    public function testCanCatchExceptionWhenSendBlowsUp()
    {
        $this->mockHttpClient->expects($this->once())
            ->method('request')
            ->willReturn($this->mockRequest);

        $this->mockHttpClient->expects($this->once())
            ->method('send')
            ->with($this->mockRequest)
            ->will($this->throwException(new \Exception('testing')));

        $service = new MockService($this->mockHttpClient, '', '', '');

        $service->doSend('test', '/testUrl', [], 'test body');
    }

    /**
     * @covers ::getLastRequest
     * @uses \Whip\Services\HttpService::__construct
     */
    public function testCanCatchLastRequest()
    {
        $this->mockRequest->expects($this->once())
            ->method('getUri')
            ->willReturn('/testUrl');

        $this->mockHttpClient->expects($this->once())
            ->method('request')
            ->willReturn($this->mockRequest);

        $this->mockHttpClient->expects($this->once())
            ->method('send')
            ->with($this->mockRequest);

        $service = new MockService($this->mockHttpClient, '', '', '');

        $service->doSend('test', '/testUrl', [], 'test body');

        $actual = $service->getLastRequest();

        $this->assertContains('Request URL: /testUrl', $actual);
    }
}
