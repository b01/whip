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
     * @param AccountStorage $accountStorage
     * @param string $username
     * @param string $password
     * @return boolean
     */
    public function lookup(AccountStorage $accountStorage, string $username, string $password);
}
