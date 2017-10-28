<?php namespace Whip;
/**
 * Please see the included LICENSE.txt with this source code. If no
 * LICENSE.txt was provided, then all rights for the source code in
 * this file are reserved by Khalifah Khalil Shabazz
 */

/**
 * Class Message
 *
 * @package \Whip
 */
abstract class Message implements \JsonSerializable
{
    /** @var mixed The content body of this message. */
    protected $content;

    /**
     * Message constructor.
     *
     * @param string $message
     */
    public function __construct($content)
    {
        $this->content = $content;
    }

    /**
     * Return the message as a string.
     *
     * @return string
     */
    public function jsonSerialize()
    {
        return \json_encode($this->content);
    }
}
