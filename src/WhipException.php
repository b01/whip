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

    protected static $errorMap = [
        self::BAD_TPL_DIR => 'The directory does not exists: "%s"'
    ];
}
