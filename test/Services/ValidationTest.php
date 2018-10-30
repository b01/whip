<?php namespace Whip\Test\Services;

use PHPUnit\Framework\TestCase;
use Whip\Services\Validation;

/**
 * Class ValidationTest
 *
 * @package \Whip\Test\Services
 * @coversDefaultClass \Whip\Services\Validation
 */
class ValidationTest extends TestCase
{
    public function setUp()
    {

    }

    /**
     * @covers ::__construct
     */
    public function testCanInitialize()
    {
        $sut = new Validation();

        $this->assertInstanceOf(Validation::class, $sut);
    }
}
