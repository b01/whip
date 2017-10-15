<?php namespace Whip\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Whip\Log;

/**
 * Class LogTest
 *
 * @package \Whip\Tests
 * @coversDefaultClass \Whip\Log
 */
class LogTest extends TestCase
{
    /** @var \Whip\Log|\PHPUnit_Framework_MockObject_MockObject */
    private $log;

    /** @var \Psr\Log\LoggerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $mockLogger;

    public function setUp()
    {
        $this->log = $this->getMockForTrait(Log::class);
        $this->mockLogger = $this->createMock(LoggerInterface::class);
    }

    /**
     * @covers ::withLogger
     */
    public function testInitialization()
    {
        $this->log->withLogger($this->mockLogger);

        $this->assertAttributeEquals($this->mockLogger, 'log', $this->log);
    }
}
