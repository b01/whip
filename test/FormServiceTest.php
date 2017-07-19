<?php namespace Whip\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Whip\Form;
use Whip\FormService;

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

    public function setUp()
    {
        $this->mockServerRequest = $this->createMock(ServerRequestInterface::class);
        $this->formSubmitField = 'testName';
        $this->formService = $this->getMockForAbstractClass(FormService::class, [$this->formSubmitField]);
        $this->mockForm = $this->createMock(Form::class);
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
        $fixtureName = '1234';

        $this->mockServerRequest->expects($this->once())
            ->method('getQueryParams')
            ->willReturn([$this->formSubmitField => $fixtureName]);

        $this->mockServerRequest->expects($this->once())
            ->method('getUploadedFiles')
            ->willReturn([$this->formSubmitField => $fixtureName]);

        $this->mockForm->expects($this->once())
            ->method('getId')
            ->willReturn($fixtureName);

        $this->mockForm->expects($this->once())
            ->method('canSubmit')
            ->willReturn(false);

        $this->formService->addForm($this->mockForm)
            ->process($this->mockServerRequest);
    }

    /**
     * @covers ::getRenderData
     * @uses \Whip\FormService::addForm
     */
    public function testCanGetRenderData()
    {
        $fixtureName = '1234';

        $this->mockForm->expects($this->once())
            ->method('getId')
            ->willReturn($fixtureName);

        $this->mockForm->expects($this->once())
            ->method('getRenderData')
            ->willReturn([]);

        $actual = $this->formService->addForm($this->mockForm)
            ->getRenderData();

        $this->assertArrayHasKey('1234', $actual);
    }

    /**
     * @covers ::process
     * @covers ::getScrubbedInput
     * @uses \Whip\FormService::addForm
     */
    public function testCanProcessSubmittedFormAndGetNewLocationUrl()
    {
        $fixtureName = __FUNCTION__;
        $fixtureUrl = 'test.url';

        $this->mockServerRequest->expects($this->once())
            ->method('getQueryParams')
            ->willReturn([$this->formSubmitField => $fixtureName]);

        $this->mockServerRequest->expects($this->once())
            ->method('getUploadedFiles')
            ->willReturn([$this->formSubmitField => $fixtureName]);

        $this->mockForm->expects($this->once())
            ->method('getId')
            ->willReturn($fixtureName);

        $this->mockForm->expects($this->once())
            ->method('setInput')
            ->willReturn($this->mockForm);

        $this->mockForm->expects($this->once())
            ->method('canSubmit')
            ->willReturn(true);

        $this->mockForm->expects($this->once())
            ->method('submit')
            ->willReturn($fixtureUrl);

        $actual = $this->formService->addForm($this->mockForm)
            ->process($this->mockServerRequest);

        $this->assertEquals($fixtureUrl, $actual);
    }
}
