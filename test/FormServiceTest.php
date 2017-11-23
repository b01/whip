<?php namespace Whip\Tests;
/**
 * Please see the included LICENSE.txt with this source code. If no
 * LICENSE.txt was provided, then all rights for the source code in
 * this file are reserved by Khalifah Khalil Shabazz
 */

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Whip\Form;
use Whip\FormFactory;
use Whip\FormService;
use Whip\Lash\Validator;
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

    /** @var \Psr\Http\Message\ResponseInterface */
    private $mockResponse;

    /** @var \Psr\Http\Message\ServerRequestInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $mockServerRequest;

    /** @var \Whip\Lash\Validator|\PHPUnit_Framework_MockObject_MockObject */
    private $mockValidator;

    public function setUp()
    {
        $this->mockServerRequest = $this->createMock(ServerRequestInterface::class);
        $this->formSubmitField = 'testName';
        $this->mockFormFactory = $this->createMock(FormFactory::class);
        $this->mockResponse = $this->createMock(ResponseInterface::class);
        $this->sut = $this->getMockForAbstractClass(
            FormService::class,
            [$this->formSubmitField, $this->mockFormFactory, $this->mockResponse]
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
     * @covers ::addForm
     * @uses \Whip\FormService::process
     */
    public function testCanAddAForm()
    {
        $fixtureName = MockHtmlForm::getId();

        $this->mockServerRequest->expects($this->once())
            ->method('getQueryParams')
            ->willReturn([$this->formSubmitField => $fixtureName]);

        $this->mockServerRequest->expects($this->once())
            ->method('getUploadedFiles')
            ->willReturn([$this->formSubmitField => $fixtureName]);

        $closureFixture = function (){};
        $this->mockFormFactory->expects($this->once())
            ->method('set')
            ->with(MockHtmlForm::class, $closureFixture, false);

        $this->mockFormFactory->expects($this->once())
            ->method('get')
            ->with(MockHtmlForm::getId())
            ->willReturn($this->mockForm);

        $this->sut->addForm(MockHtmlForm::class, $closureFixture)
            ->process($this->mockServerRequest);
    }

    /**
     * @covers ::getRenderData
     * @uses \Whip\FormService::addForm
     */
    public function testCanGetRenderData()
    {
        $formNameFixture = MockHtmlForm::getId();
        $closureFixture = function (){};
        $expected = [__FUNCTION__];

        $this->mockForm->expects($this->once())
            ->method('getRenderData')
            ->willReturn($expected);

        $this->mockFormFactory->expects($this->once())
            ->method('get')
            ->with($formNameFixture)
            ->willReturn($this->mockForm);

        $actual = $this->sut->addForm(MockHtmlForm::class, $closureFixture)
            ->getRenderData([$formNameFixture]);

        $this->assertEquals($expected[0], $actual[$formNameFixture][0]);
    }

    /**
     * @covers ::process
     * @covers ::getScrubbedInput
     * @uses \Whip\FormService::addForm
     */
    public function testCanAddAndProcessAndGetTheSubmittedForm()
    {
        $fixtureName = MockHtmlForm::getId();

        $this->mockServerRequest->expects($this->once())
            ->method('getQueryParams')
            ->willReturn([$this->formSubmitField => $fixtureName]);

        $this->mockServerRequest->expects($this->once())
            ->method('getUploadedFiles')
            ->willReturn([]);

        $this->mockFormFactory->expects($this->once())
            ->method('get')
            ->with($this->equalTo($fixtureName))
            ->willReturn($this->mockForm);

        $this->mockForm->expects($this->once())
            ->method('canSubmit')
            ->willReturn(true);

        $actual = $this->sut->process($this->mockServerRequest);

        $this->assertEquals($this->mockForm, $actual);
    }

    /**
     * @covers ::process
     * @covers ::getScrubbedInput
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

        $this->sut->process($this->mockServerRequest);
    }
}
