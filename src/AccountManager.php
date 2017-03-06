<?php namespace Whip;

use Kshabazz\Slib\Tools\Utilities;

/**
 * Class Account
 *
 * @package \Whip
 */
abstract class AccountManager
{
    use Utilities;

    /** @var array|null Account information. */
    private $account;

    /**
     * Login constructor.
     */
    public function __construct()
    {
        $this->account = null;
    }

    /**
     * Prepare before serialization.
     *
     * @return array
     */
    public function __sleep()
    {
        return ['account'];
    }

    /**
     * A client is considered valid when they are logged in.
     *
     * @return bool
     */
    public function isValid()
    {
        return !empty($this->account);
    }

    /**
     * Get an account from the Mongo instance.
     *
     * @param string $username
     * @param string $password
     */
    public function login(AccountStorage $accountStorage, $username, $password)
    {
        // TODO: Implement.
        $account = $accountStorage->lookup($username, $password);

        if ($account) {
            // TODO: Look into using LibSodium
            // $accountToken = \Sodium\crypto_generichash(string $msg, string $key = null, string $length = 32)
        }
    }
}
