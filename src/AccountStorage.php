<?php namespace Whip;

/**
 * Class AccountStorage
 *
 * @package \Whip
 */
interface AccountStorage
{
    /**
     * Lookup an account in the storage facility.
     *
     * @param string $username
     * @param string $password
     * @return boolean
     */
    public function lookup(string $username, string $password);
}
