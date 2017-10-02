<?php namespace Whip\Test\Mocks;
/**
 * Please see the included LICENSE.txt with this source code. If no
 * LICENSE.txt was provided, then all rights for the source code in
 * this file are reserved by Khalifah Khalil Shabazz
 */

use Whip\Services\HttpService;

/**
 * Class MockService
 *
 * @package \Whip\Test\Mocks
 */
class MockService extends HttpService
{
    public function doSend($method, $endpoint, array $headers, $body = null)
    {
        return $this->send($method, $endpoint, $headers, $body);
    }
}
