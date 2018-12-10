<?php namespace Whip\Test;
/**
 * Please see the included LICENSE.txt with this source code. If no
 * LICENSE.txt was provided, then all rights for the source code in
 * this file are reserved by Khalifah Khalil Shabazz
 */

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Whip\Form;
use Whip\FormService;
use Whip\Lash\Validator;
use Whip\SessionWrapper;
use Whip\Test\Mocks\MockHtmlForm;
use Whip\WhipException;

/**
 * Class FormServiceTest
 *
 * @package \Whip
 * @coversDefaultClass \Whip\FormService
 */
class FormServiceTest extends TestCase
{
    /** @var \Whip\Form|\PHPUnit_Framework_MockObject_MockObject */
    private $mockForm;

    /** @var \Psr\Http\Message\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $mockResponse;

    /** @var \Psr\Http\Message\ServerRequestInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $mockServerRequest;

    /** @var \Whip\SessionWrapper|\PHPUnit_Framework_MockObject_MockObject */
    private $mockSession;

    /** @var \Whip\Lash\Validator|\PHPUnit_Framework_MockObject_MockObject */
    private $mockValidation;

    public function setUp()
    {
        $this->mockServerRequest = $this->createMock(ServerRequestInterface::class);
        $this->mockSession = $this->createMock(SessionWrapper::class);
        $this->mockValidation = $this->createMock(Validator::class);
        $this->mockResponse = $this->createMock(ResponseInterface::class);
        $this->mockForm = $this->createMock(Form::class);
    }

    /**
     * @covers ::__construct
     */
    public function testInitialization()
    {
        $sut = new FormService(
            $this->mockValidation,
            $this->mockSession
        );

        $this->assertInstanceOf(FormService::class, $sut);
    }

    /**
     * @covers ::findForm
     * @uses \Whip\FormService::process
     */
    public function testCanRetrieveAForm()
    {
        $formIdFixture = 'testFormId';

        $this->mockServerRequest->expects($this->once())
            ->method('getUploadedFiles')
            ->willReturn([]);

        $this->mockServerRequest->expects($this->once())
            ->method('getQueryParams')
            ->willReturn([$formIdFixture => [
                'testField1' => __FUNCTION__
            ]]);

        $this->mockForm->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($formIdFixture);

        $this->mockForm->expects($this->once())
            ->method('getRules')
            ->willReturn([
                'testFieldX' => [
                    'validator' => 'regex',
                    'constraint' => '/[a-z]+',
                    'err' => 'bad field'
                ]
            ]);

        $sut = new FormService(
            $this->mockValidation,
            $this->mockSession
        );

        $sut->process(
            $this->mockServerRequest,
            $this->mockResponse,
            [$this->mockForm]
        );
    }

    /**
     * @covers ::getData
     */
    public function testCanGetData()
    {
        $formIdFixture = 'test1234';

        $this->mockServerRequest->expects($this->once())
            ->method('getUploadedFiles')
            ->willReturn([]);

        $this->mockServerRequest->expects($this->once())
            ->method('getQueryParams')
            ->willReturn([$formIdFixture => [
                'testField1' => __FUNCTION__
            ]]);

        $this->mockForm->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($formIdFixture);

        $this->mockForm->expects($this->once())
            ->method('getRules')
            ->willReturn([
                'testFieldX' => [
                    'validator' => 'regex',
                    'constraint' => '/[a-z]+',
                    'err' => 'bad field'
                ]
            ]);

        $sut = new FormService(
            $this->mockValidation,
            $this->mockSession
        );

        $sut->process(
            $this->mockServerRequest,
            $this->mockResponse,
            [$this->mockForm]
        );

        $actual = $sut->getData();

        $this->assertEquals($formIdFixture, $actual[$formIdFixture]['id']);
    }

    /**
     * @covers ::process
     * @covers ::getScrubbedInput
     * @uses \Whip\FormService::__construct
     * @uses \Whip\FormService::findForm
     */
    public function testCanValidateAForm()
    {
        $formIdFixture = 'test5678';
        $formInputFixture = [
            $formIdFixture => [
                'testField1' => __FUNCTION__
            ]
        ];

        $this->mockServerRequest->expects($this->once())
            ->method('getUploadedFiles')
            ->willReturn([]);

        $this->mockServerRequest->expects($this->once())
            ->method('getQueryParams')
            ->willReturn([]);

        $this->mockServerRequest->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($formInputFixture);

        $this->mockForm->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($formIdFixture);

        $this->mockForm->expects($this->once())
            ->method('getRules')
            ->willReturn([
                'testFieldX' => [
                    'validator' => 'regex',
                    'constraint' => '/[a-z]+',
                    'err' => 'bad field'
                ]
            ]);

        $this->mockValidation->expects($this->once())
            ->method('addRules');

        $this->mockValidation->expects($this->once())
            ->method('validate')
            ->with($this->equalTo($formInputFixture[$formIdFixture]))
            ->willReturn(true);

        $this->mockForm->expects($this->once())
            ->method('submit')
            ->willReturn($this->mockResponse);

        $sut = new FormService(
            $this->mockValidation,
            $this->mockSession
        );

        $actual = $sut->process(
            $this->mockServerRequest,
            $this->mockResponse,
            [$this->mockForm]
        );

        $this->assertEquals($this->mockResponse, $actual);
    }

    /**
     * @covers ::process
     * @uses \Whip\FormService::__construct
     * @uses \Whip\FormService::findForm
     * @uses \Whip\FormService::getScrubbedInput
     * @expectedException \Whip\WhipException
     */
    public function testWillNotValidateFormWithNoInput()
    {
        $formIdFixture = 'test5678';
        $formInputFixture = [
            $formIdFixture => []
        ];

        $this->mockServerRequest->expects($this->once())
            ->method('getUploadedFiles')
            ->willReturn([]);

        $this->mockServerRequest->expects($this->once())
            ->method('getQueryParams')
            ->willReturn([]);

        $this->mockServerRequest->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($formInputFixture);

        $this->mockForm->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($formIdFixture);

        $this->mockForm->expects($this->once())
            ->method('getRules')
            ->willReturn([
                'testFieldX' => [
                    'validator' => 'regex',
                    'constraint' => '/[a-z]+',
                    'err' => 'bad field'
                ]
            ]);

        $sut = new FormService(
            $this->mockValidation,
            $this->mockSession
        );

        $actual = $sut->process(
            $this->mockServerRequest,
            $this->mockResponse,
            [$this->mockForm]
        );

        $this->assertEquals($this->mockResponse, $actual);
    }

    /**
     * @covers ::process
     * @uses \Whip\FormService::__construct
     * @uses \Whip\FormService::findForm
     * @uses \Whip\FormService::getScrubbedInput
     * @expectedException \Whip\WhipException
     */
    public function testWillNotValidateFormWithNoRules()
    {
        $formIdFixture = 'test5678';
        $formInputFixture = [
            $formIdFixture => [
                'testField1' => __FUNCTION__
            ]
        ];

        $this->mockServerRequest->expects($this->once())
            ->method('getUploadedFiles')
            ->willReturn([]);

        $this->mockServerRequest->expects($this->once())
            ->method('getQueryParams')
            ->willReturn([]);

        $this->mockServerRequest->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($formInputFixture);

        $this->mockForm->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($formIdFixture);

        $this->mockForm->expects($this->once())
            ->method('getRules')
            ->willReturn([]);

        $sut = new FormService(
            $this->mockValidation,
            $this->mockSession
        );

        $actual = $sut->process(
            $this->mockServerRequest,
            $this->mockResponse,
            [$this->mockForm]
        );

        $this->assertEquals($this->mockResponse, $actual);
    }

    /**
     * @covers ::process
     * @uses \Whip\FormService::__construct
     * @uses \Whip\FormService::findForm
     * @uses \Whip\FormService::getScrubbedInput
     * @expectedException \Whip\WhipException
     */
    public function testWillNotifyWhenBadRulesAdded()
    {
        $formIdFixture = 'test5678';
        $formInputFixture = [
            $formIdFixture => [
                'testField1' => __FUNCTION__
            ]
        ];

        $this->mockServerRequest->expects($this->once())
            ->method('getUploadedFiles')
            ->willReturn([]);

        $this->mockServerRequest->expects($this->once())
            ->method('getQueryParams')
            ->willReturn([]);

        $this->mockServerRequest->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($formInputFixture);

        $this->mockForm->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($formIdFixture);

        $this->mockForm->expects($this->once())
            ->method('getRules')
            ->willReturn([
                'testFieldX' => [
                    'validator' => 'regex',
                    'constraint' => '/[a-z]+',
                    'err' => 'bad field'
                ]
            ]);

        $this->mockValidation->method('addRules')
            ->willThrowException(new \Exception(__FUNCTION__));


        $sut = new FormService(
            $this->mockValidation,
            $this->mockSession
        );

        $actual = $sut->process(
            $this->mockServerRequest,
            $this->mockResponse,
            [$this->mockForm]
        );

        $this->assertEquals($this->mockResponse, $actual);
    }

    /**
     * @covers ::process
     * @uses \Whip\FormService::__construct
     * @uses \Whip\FormService::findForm
     * @uses \Whip\FormService::getScrubbedInput
     * @expectedException \Whip\WhipException
     */
    public function testWillNotifyWhenValidateBlowsUp()
    {
        $formIdFixture = 'test5678';
        $formInputFixture = [
            $formIdFixture => [
                'testField1' => __FUNCTION__
            ]
        ];

        $this->mockServerRequest->expects($this->once())
            ->method('getUploadedFiles')
            ->willReturn([]);

        $this->mockServerRequest->expects($this->once())
            ->method('getQueryParams')
            ->willReturn([]);

        $this->mockServerRequest->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($formInputFixture);

        $this->mockForm->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($formIdFixture);

        $this->mockForm->expects($this->once())
            ->method('getRules')
            ->willReturn([
                'testFieldX' => [
                    'validator' => 'regex',
                    'constraint' => '/[a-z]+',
                    'err' => 'bad field'
                ]
            ]);

        $this->mockValidation->method('validate')
            ->willThrowException(new \Exception(__FUNCTION__));


        $sut = new FormService(
            $this->mockValidation,
            $this->mockSession
        );

        $actual = $sut->process(
            $this->mockServerRequest,
            $this->mockResponse,
            [$this->mockForm]
        );

        $this->assertEquals($this->mockResponse, $actual);
    }

    /**
     * @covers ::findForm
     * @uses \Whip\FormService::__construct
     * @uses \Whip\FormService::process
     * @uses \Whip\FormService::getScrubbedInput
     * @expectedException \Whip\WhipException
     */
    public function testWillNotifyWhenInvalidFormObject()
    {
        $formIdFixture = 'test5678';
        $formInputFixture = [
            $formIdFixture => [
                'testField1' => __FUNCTION__
            ]
        ];

        $this->mockServerRequest->expects($this->once())
            ->method('getUploadedFiles')
            ->willReturn([]);

        $this->mockServerRequest->expects($this->once())
            ->method('getQueryParams')
            ->willReturn([]);

        $this->mockServerRequest->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($formInputFixture);

        $sut = new FormService(
            $this->mockValidation,
            $this->mockSession
        );

        $actual = $sut->process(
            $this->mockServerRequest,
            $this->mockResponse,
            [1234]
        );

        $this->assertEquals($this->mockResponse, $actual);
    }
}
