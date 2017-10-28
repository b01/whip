<?php namespace Whip;
/**
 * Please see the included LICENSE.txt with this source code. If no
 * LICENSE.txt was provided, then all rights for the source code in
 * this file are reserved by Khalifah Khalil Shabazz
 */

/**
 * Class Session
 *
 * @package \Whip
 */
trait Session
{
    /** @var \Whip\SessionWrapper */
    private $session;

    /**
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public function getArrayFromSession($key, $default = null)
    {
        $encoded = $this->getSessionVal($key, $default);

        // Restore the array.
        try {
            $decoded = \json_decode($encoded, true);
        } catch (\Exception $exception)
        {
            throw new WhipException(WhipException::BAD_SESSION_DECODE, [$key]);
        }

        return $decoded;
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getSessionVal(string $key, $default = null)
    {
        $value = $default;

        if (\array_key_exists($key, $_SESSION)) {
            $value = $_SESSION[$key];
        }

        return $value;
    }

    /**
     * @param string $key
     * @param int|string|double|float|array $value
     * @return static
     * @throws \Exception
     */
    public function setSessionVal(string $key, $value)
    {
        if (\is_object($value)) {
            throw new \Exception('Do not add objects to the session.');
        }

        $_SESSION[$key] = $value;

        return $this;
    }

    /**
     * JSON Encode an array before setting it in the session.
     *
     * @param string $key
     * @param array $value
     * @return static
     */
    public function setArrayInSession(string $key, array $value)
    {
        $encoded = \json_encode($value);

        $this->setSessionVal($key, $encoded);

        return $this;
    }

    /**
     * Set an object to read/write session data.
     *
     * @param SessionWrapper $session
     * @return static
     */
    public function withSession(SessionWrapper $session)
    {
        $this->session = $session;

        return $this;
    }
}
