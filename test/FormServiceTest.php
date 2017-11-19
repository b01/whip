<?php namespace Whip\Tests;
/**
 * Please see the included LICENSE.txt with this source code. If no
 * LICENSE.txt was provided, then all rights for the source code in
 * this file are reserved by Khalifah Khalil Shabazz
 */

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
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
    private $formService;

    /** @var string */
    private $formSubmitField;

    /** @var \Whip\Form|\PHPUnit_Framework_MockObject_MockObject */
    private $mockForm;

    /** @var \Psr\Http\Message\ServerRequestInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $mockServerRequest;

    /** @var \Whip\Lash\Validator|\PHPUnit_Framework_MockObject_MockObject */
    private $mockValidator;

    public function setUp()
    {
        $this->mockServerRequest = $this->createMock(ServerRequestInterface::class);
        $this->formSubmitField = 'testName';
        $this->formService = $this->getMockForAbstractClass(FormService::class, [$this->formSubmitField]);
        $this->mockValidator = $this->createMock(Validator::class);
        $this->mockForm = new MockHtmlForm($this->mockValidator);
    }

    /**
     * @covers ::__construct
     */
    public function testInitialization()
    {
        $this->assertInstanceOf(FormService::class, $this->formService);
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

        $this->formService->addForm($this->mockForm)
            ->process($this->mockServerRequest);
    }

    /**
     * @covers ::getRenderData
     * @uses \Whip\FormService::addForm
     */
    public function testCanGetRenderData()
    {
        $expected = MockHtmlForm::getId();

        $actual = $this->formService->addForm($this->mockForm)
            ->getRenderData();

        $this->assertArrayHasKey($expected, $actual);
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

        $actual = $this->formService->addForm($this->mockForm)
            ->process($this->mockServerRequest);

        $this->assertEquals($this->mockForm, $actual);
    }

    /**
     * @covers ::process
     * @covers ::getScrubbedInput
     * @uses \Whip\FormService::addForm
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

        $this->formService->process($this->mockServerRequest);
    }
}
