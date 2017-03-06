<?php namespace Whip;

/**
 * Class AccountStorage
 *
 * @package \Whip
 */
interface AccountStorage
{
    public function lookup(AccountStorage $accountStorage, $username, $password);
}
