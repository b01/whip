<?php namespace Whip\Tests;
/**
 * Please see the included LICENSE.txt with this source code. If no
 * LICENSE.txt was provided, then all rights for the source code in
 * this file are reserved by Khalifah Khalil Shabazz
 */

use Whip\HtmlForm;
use PHPUnit\Framework\TestCase;
use Whip\Lash\Validator;
use Whip\Test\Mocks\MockHtmlForm;

/**
 * Class HtmlFormTest
 *
 * @package \Whip\Tests
 * @coversDefaultClass \Whip\HtmlForm
 */
class HtmlFormTest extends TestCase
{
    /** @var \Whip\HtmlForm|\PHPUnit_Framework_MockObject_MockObject */
    private $sut;

    /** @var \Whip\Lash\Validator|\PHPUnit_Framework_MockObject_MockObject */
    private $mockValidator;

    public function setUp()
    {
        $this->mockValidator = $this->createMock(Validator::class);
        $this->sut = new MockHtmlForm($this->mockValidator);
    }

    /**
     * @covers ::__construct
     */
    public function testCanInitialize()
    {
        $this->assertInstanceOf(HtmlForm::class, $this->sut);
    }

    /**
     * @covers ::getRenderData
     * @uses \Whip\HtmlForm::__construct
     */
    public function testCanGetRenderData()
    {
        $this->mockValidator->expects($this->once())
            ->method('getErrors')
            ->willReturn(['test']);

        $actual = $this->sut->getRenderData();

        $this->assertEquals('test', $actual[HtmlForm::FORM_ERRORS_KEY][0]);
    }

    /**
     * @covers ::hasNoErrors
     * @uses \Whip\HtmlForm::__construct
     */
    public function testCanIndicateHasNoErrors()
    {
        $this->mockValidator->expects($this->once())
            ->method('getErrors')
            ->willReturn(['test']);

        $actual = $this->sut->hasNoErrors();

        $this->assertFalse($actual);
    }

    /**
     * @covers ::hasNoErrors
     * @uses \Whip\HtmlForm::__construct
     */
    public function testCanIndicateHasErrors()
    {
        $this->mockValidator->expects($this->once())
            ->method('getErrors')
            ->willReturn([]);

        $actual = $this->sut->hasNoErrors();

        $this->assertTrue($actual);
    }

    /**
     * @covers ::setFailure
     * @covers ::getFailures
     */
    public function testCanSetAndGetFailures()
    {
        $actual = $this->sut->proxySetAndGetFailures(__FUNCTION__);
        $this->assertEquals(__FUNCTION__, $actual[0]);
    }
}
