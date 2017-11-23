<?php namespace Whip\Test\Mocks;
/**
 * Please see the included LICENSE.txt with this source code. If no
 * LICENSE.txt was provided, then all rights for the source code in
 * this file are reserved by Khalifah Khalil Shabazz
 */

use Whip\SessionWrapper;

/**
 * Class MockSession
 *
 * @package \Whip\Test\Mocks
 */
final class MockSession extends SessionWrapper
{
    public function getSession()
    {
        return $this->session;
    }
}
