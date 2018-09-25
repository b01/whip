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
use Whip\FormFactory;
use Whip\FormService;
use Whip\Lash\Validator;
use Whip\SessionWrapper;
use Whip\Test\Mocks\MockHtmlForm;

/**
 * Class FormServiceTest
 *
 * @package \Whip
 * @coversDefaultClass \Whip\FormService
 */
class FormServiceTest extends TestCase
{
    /** @var \Whip\FormService|\PHPUnit_Framework_MockObject_MockObject */
    private $sut;

    /** @var string */
    private $formSubmitField;

    /** @var \Whip\Test\Mocks\MockHtmlForm|\PHPUnit_Framework_MockObject_MockObject */
    private $form;

    /** @var \Whip\Form|\PHPUnit_Framework_MockObject_MockObject */
    private $mockForm;

    /** @var \Whip\FormFactory|\PHPUnit_Framework_MockObject_MockObject */
    private $mockFormFactory;

    /** @var \Psr\Http\Message\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $mockResponse;

    /** @var \Psr\Http\Message\ServerRequestInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $mockServerRequest;

    /** @var \Whip\SessionWrapper|\PHPUnit_Framework_MockObject_MockObject */
    private $mockSession;

    /** @var \Whip\Lash\Validator|\PHPUnit_Framework_MockObject_MockObject */
    private $mockValidator;

    public function setUp()
    {
        $this->mockServerRequest = $this->createMock(ServerRequestInterface::class);
        $this->formSubmitField = 'testName';
        $this->mockFormFactory = $this->createMock(FormFactory::class);
        $this->mockResponse = $this->createMock(ResponseInterface::class);
        $this->mockSession = $this->createMock(SessionWrapper::class);
        $this->sut = $this->getMockForAbstractClass(
            FormService::class,
            [
                $this->mockServerRequest,
                $this->mockResponse,
                $this->formSubmitField,
                $this->mockFormFactory,
                $this->mockSession
            ]
        );
        $this->mockValidator = $this->createMock(Validator::class);
        $this->mockForm = $this->createMock(Form::class);
        $this->form = new MockHtmlForm($this->mockValidator);
    }

    /**
     * @covers ::__construct
     */
    public function testInitialization()
    {
        $this->assertInstanceOf(FormService::class, $this->sut);
    }

    /**
     * @covers ::getForm
     * @uses \Whip\FormService::process
     */
    public function testCanAddAForm()
    {
        $formIdFixture = MockHtmlForm::getId();
        $routeInfoFixture = [1234, 'test'];
        $mockUri = $this->createMock(UriInterface::class);

        $this->mockServerRequest->expects($this->once())
            ->method('getQueryParams')
            ->willReturn([$this->formSubmitField => $formIdFixture]);

        $this->mockServerRequest->expects($this->once())
            ->method('getUploadedFiles')
            ->willReturn([$this->formSubmitField => $formIdFixture]);

        $this->mockFormFactory->expects($this->once())
            ->method('get')
            ->with(MockHtmlForm::getId())
            ->willReturn($this->mockForm);

        $this->sut->process();
    }

    /**
     * @covers ::getRenderData
     * @uses \Whip\Form::getRenderData
     * @uses \Whip\FormFactory::set
     */
    public function testCanGetRenderData()
    {
        $formNameFixture = MockHtmlForm::getId();
        $expected = [__FUNCTION__];

        $this->mockForm->expects($this->once())
            ->method('getRenderData')
            ->willReturn($expected);

        $this->mockFormFactory->expects($this->once())
            ->method('get')
            ->with($formNameFixture)
            ->willReturn($this->mockForm);

        $actual = $this->sut->getRenderData([$formNameFixture]);

        $this->assertEquals($expected[0], $actual[$formNameFixture][0]);
    }

    /**
     * @covers ::process
     * @covers ::getScrubbedInput
     * @uses \Whip\FormService::__construct
     * @uses \Whip\FormService::getForm
     * @uses \Whip\Controllers\Controller::__construct
     * @uses \Whip\Controllers\Controller::redirectTo
     */
    public function testCanAddAndProcessAndGetTheSubmittedForm()
    {
        $formIdFixture = MockHtmlForm::getId();
        $mockUri = $this->createMock(UriInterface::class);

        $this->mockServerRequest->expects($this->once())
            ->method('getQueryParams')
            ->willReturn([$this->formSubmitField => $formIdFixture]);

        $this->mockServerRequest->expects($this->once())
            ->method('getUploadedFiles')
            ->willReturn([]);

        $this->mockFormFactory->expects($this->once())
            ->method('get')
            ->with($this->equalTo($formIdFixture))
            ->willReturn($this->mockForm);

        $this->mockForm->expects($this->once())
            ->method('canSubmit')
            ->willReturn(true);

        $this->mockForm->expects($this->once())
            ->method('submit')
            ->willReturn($this->mockResponse);

        $actual = $this->sut->process();

        $this->assertEquals($this->mockResponse, $actual);
    }

    /**
     * @covers ::getForm
     * @uses \Whip\FormService::process
     * @uses \Whip\FormService::getScrubbedInput
     * @expectedException \Whip\WhipException
     */
    public function testWillThrowAnExceptionWhenFormNotFound()
    {
        $this->mockServerRequest->expects($this->once())
            ->method('getQueryParams')
            ->willReturn(['testName' => __FUNCTION__]);

        $this->mockServerRequest->expects($this->once())
            ->method('getUploadedFiles')
            ->willReturn([]);

        $this->sut->process();
    }

    /**
     * @covers ::process
     * @covers ::getFormKey
     * @uses \Whip\FormService::__construct
     * @uses \Whip\FormService::getScrubbedInput
     * @uses \Whip\FormService::getForm
     * @uses \Whip\FormService::getScrubbedInput
     * @uses \Whip\Controllers\Controller::__construct
     * @uses \Whip\Controllers\Controller::redirectTo
     */
    public function testWillPerform307RedirectOnFormFailure()
    {
        $formIdFixture = MockHtmlForm::getId();
        $mockUri = $this->createMock(UriInterface::class);

        $this->mockServerRequest->expects($this->once())
            ->method('getQueryParams')
            ->willReturn([$this->formSubmitField => $formIdFixture]);

        $this->mockServerRequest->expects($this->once())
            ->method('getUploadedFiles')
            ->willReturn([]);

        $this->mockFormFactory->expects($this->once())
            ->method('get')
            ->with($this->equalTo($formIdFixture))
            ->willReturn($this->mockForm);

        $this->mockForm->expects($this->once())
            ->method('canSubmit')
            ->willReturn(false);


        $this->mockSession->expects($this->once())
            ->method('setArray')
            ->with(
                $this->equalTo('Whip\\FormService:renderData:form-mock'),
                []
            );

        $this->sut->process();
    }

    /**
     * @covers ::process
     * @uses \Whip\FormService::__construct
     * @uses \Whip\FormService::getScrubbedInput
     * @uses \Whip\FormService::getForm
     * @uses \Whip\FormService::getScrubbedInput
     * @uses \Whip\FormService::getFormKey
     * @uses \Whip\Controllers\Controller::__construct
     * @uses \Whip\Controllers\Controller::redirectTo
     */
    public function testWillSetPostInSession()
    {
        $formIdFixture = MockHtmlForm::getId();

        $this->mockServerRequest->expects($this->once())
            ->method('getQueryParams')
            ->willReturn([]);
        $this->mockServerRequest->expects($this->once())
            ->method('getUploadedFiles')
            ->willReturn([]);
        $this->mockServerRequest->expects($this->once())
            ->method('getParsedBody')
            ->willReturn([$this->formSubmitField => $formIdFixture]);

        $this->mockFormFactory->expects($this->once())
            ->method('get')
            ->with($this->equalTo($formIdFixture))
            ->willReturn($this->mockForm);

        $this->mockSession->expects($this->once())
            ->method('setArray')
            ->with(
                $this->equalTo('Whip\\FormService:renderData:' . $formIdFixture),
                $this->equalTo([])
            );

        $this->sut->process();
    }

    /**
     * @covers ::process
     * @uses \Whip\FormService::__construct
     * @uses \Whip\FormService::getForm
     * @uses \Whip\FormService::getFormKey
     */
    public function testWillGetRenderDataFromFormWhenNonInSession()
    {
        $formIdFixture = MockHtmlForm::getId();

        $this->mockFormFactory->expects($this->once())
            ->method('get')
            ->with($this->equalTo($formIdFixture))
            ->willReturn($this->mockForm);

        $this->sut->getRenderData([$formIdFixture]);
    }
}
