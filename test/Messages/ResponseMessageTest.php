<?php namespace Whip\Test\Messages;

/**
 * Please see the included LICENSE.txt with this source code. If no
 * LICENSE.txt was provided, then all rights for the source code in
 * this file are reserved by Khalifah Khalil Shabazz
 */

use Whip\Messages\ResponseMessage;
use PHPUnit\Framework\TestCase;

/**
 * Class ResponseMessageTest
 *
 * @package \Whip\Test\Messages
 * @coversDefaultClass \Whip\Messages\ResponseMessage
 */
class ResponseMessageTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testCanSerialize()
    {
        $expected = \json_encode(['status' => 1, 'message' => 'test', 'data' => null]);

        $sut = new ResponseMessage(1, 'test');

        $actual = $sut->jsonSerialize();

        $this->assertEquals($expected, $actual);
    }
}
