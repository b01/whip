<?php namespace Whip\Test\Services;

/**
 * Please see the included LICENSE.txt with this source code. If no
 * LICENSE.txt was provided, then all rights for the source code in
 * this file are reserved by Khalifah Khalil Shabazz
 */

use PHPStan\Testing\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Whip\Form;
use Whip\Lash\Validator;
use Whip\Services\ValidationService;

/**
 * Class ValidationService
 *
 * @package \Whip\Test\Services
 * @coversDefaultClass \Whip\Services\ValidationService
 */
class ValidationServiceTest extends TestCase
{
    /** @var \Whip\Form | \PHPUnit\Framework\MockObject\MockObject */
    private $mockForm;

    /** @var \Psr\Http\Message\ResponseInterface | \PHPUnit\Framework\MockObject\MockObject */
    private $mockResponse;

    /** @var \Psr\Http\Message\ServerRequestInterface | \PHPUnit\Framework\MockObject\MockObject */
    private $mockServerRequest;

    /** @var \Whip\Lash\Validator | \PHPUnit\Framework\MockObject\MockObject */
    private $mockValidation;

    public function setUp()
    {
        $this->mockForm = $this->createMock(Form::class);
        $this->mockServerRequest = $this->createMock(ServerRequestInterface::class);
        $this->mockResponse = $this->createMock(ResponseInterface::class);
        $this->mockValidation = $this->createMock(Validator::class);
    }

    /**
     * @covers ::__construct
     * @covers ::__invoke
     * @covers ::getRuleSets
     * @covers ::findRules
     */
    public function testCanGetAValidFormResponse()
    {
        $formIdFixture = 'SR388';
        $formInputFixture = [
            $formIdFixture => ['field1' => __FUNCTION__]
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

        $this->mockForm->expects($this->atLeastOnce())
            ->method('getRules')
            ->willReturn(['thisValueIsNotAsserted']);

        $this->mockValidation->expects($this->once())
            ->method('addRules')
            ->with($this->equalTo(['thisValueIsNotAsserted']))
            ->willReturn(true);

        $this->mockValidation->expects($this->once())
            ->method('validate')
            ->with($this->equalTo($formInputFixture[$formIdFixture]))
            ->willReturn(true);

        $this->mockResponse->expects($this->once())
            ->method('withBody')
            ->willReturnSelf();

        $sut = new ValidationService(
            $this->mockValidation,
            [$this->mockForm]
        );

        $sut(
            $this->mockServerRequest,
            $this->mockResponse
        );
    }

    /**
     * @covers ::getRuleSets
     * @uses \Whip\Services\ValidationService::__construct
     * @uses \Whip\Services\ValidationService::__invoke
     * @uses \Whip\Services\ValidationService::findRules
     * @expectedException \Whip\WhipException
     */
    public function testWillNotifyOfAFormWithoutRules()
    {
        $formIdFixture = 'Zebes';

        $this->mockForm->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($formIdFixture);

        $this->mockForm->expects($this->atLeastOnce())
            ->method('getRules')
            ->willReturn([]);

        $sut = new ValidationService(
            $this->mockValidation,
            [$this->mockForm]
        );
    }

    /**
     * @covers ::getRuleSets
     * @uses \Whip\Services\ValidationService::__construct
     * @uses \Whip\Services\ValidationService::__invoke
     * @uses \Whip\Services\ValidationService::findRules
     * @expectedException \Whip\WhipException
     */
    public function testWillNotifyOfAnObjectThatIsNotAForm()
    {
        $sut = new ValidationService(
            $this->mockValidation,
            [new \stdClass()]
        );
    }
}
