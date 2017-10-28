<?php namespace Whip\Test;

/**
 * Please see the included LICENSE.txt with this source code. If no
 * LICENSE.txt was provided, then all rights for the source code in
 * this file are reserved by Khalifah Khalil Shabazz
 */

use Whip\Message;
use PHPUnit\Framework\TestCase;
use Whip\Test\Mocks\MockMessage;

/**
 * Class MessageTest
 *
 * @package \Whip\Test
 * @coversDefaultClass \Whip\Message
 */
class MessageTest extends TestCase
{

    /**
     * @covers ::__construct
     * @covers ::jsonSerialize
     */
    public function testSettingMessage()
    {
        $expected = json_encode('test');

        $sut = new MockMessage('test');

        $actual = $sut->jsonSerialize();

        $this->assertEquals($expected, $actual);
    }
}
