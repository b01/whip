<?php namespace Whip\Messages;
/**
 * Please see the included LICENSE.txt with this source code. If no
 * LICENSE.txt was provided, then all rights for the source code in
 * this file are reserved by Khalifah Khalil Shabazz
 */

use Whip\Message;

/**
 * Class ResponseMessage
 *
 * @package \Whip\Messages
 */
class ResponseMessage extends Message
{
    /**
     * ResponseMessage constructor.
     * @param int $status
     * @param string $message
     */
    public function __construct(int $status, string $message, array $data = null)
    {
        parent::__construct([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ]);
    }
}
