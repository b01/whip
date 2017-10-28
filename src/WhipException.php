<?php namespace Whip;
/**
 * Please see the included LICENSE.txt with this source code. If no
 * LICENSE.txt was provided, then all rights for the source code in
 * this file are reserved by Khalifah Khalil Shabazz
 */

use Kshabazz\Slib\SlibException;

/**
 * Class WhipException
 *
 * @package \Whip
 */
class WhipException extends SlibException
{
    const BAD_TPL_DIR = 1;
    const BAD_SERVICE_REQUEST = 2;
    const FORM_NOT_FOUND = 3;
    const BAD_SESSION_DECODE = 4;

    protected static $errorMap = [
        self::BAD_TPL_DIR => 'The directory does not exists: "%s"',
        self::BAD_SERVICE_REQUEST => 'Request to %s failed because: %s.',
        self::FORM_NOT_FOUND => 'Could not find form "%s".',
        self::BAD_SESSION_DECODE => 'Unable to pull and decode session key "%s".'
    ];
}
