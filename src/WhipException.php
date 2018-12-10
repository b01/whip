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
    const BAD_SERVICE_REQUEST = 2;
    const BAD_SESSION_DECODE = 4;
    const COULD_NOT_ADD_FORM_RULES = 6;
    const NOT_A_FORM = 7;
    const FORM_VALIDATE_ERR = 8;
    const FORM_WITH_NO_INPUT = 9;
    const FORM_WITH_NO_RULES = 10;

    protected static $errorMap = [
        self::BAD_SERVICE_REQUEST => 'Request to %s failed because: %s.',
        self::BAD_SESSION_DECODE => 'Unable to pull and decode session key "%s".',
        self::COULD_NOT_ADD_FORM_RULES => 'Attempt to add rules for form "%s" has failed. Reason: %s.',
        self::FORM_VALIDATE_ERR => 'Attempt to validate form "%s" has failed. Reason: %s.',
        self::NOT_A_FORM => 'Form at index %s is not an instance of %s',
        self::FORM_WITH_NO_INPUT => 'An attempt to submit the form "%s" with no input has been prevented.',
        self::FORM_WITH_NO_RULES => 'No rules have been defined for the form "%s"; and submissions from this form can be accepted until there are.',
    ];
}
