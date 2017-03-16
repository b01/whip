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
    protected $account;

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
     * Perform action to restore to previous state.
     */
    public function __wakeup()
    {
    }

    /**
     * A client is considered valid when they are logged in.
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return array_key_exists('username', $this->account);
    }

    /**
     * Get an account from the Mongo instance.
     *
     * @param string $username
     * @param string $password
     */
    public function login(AccountStorage $accountStorage, $username, $password)
    {
        $accountFound = $accountStorage->lookup($username, $password);
        $accountToken = null;

        if ($accountFound) {
            // TODO: generate a hash that can be stored in the DB.
            $accountToken = \Sodium\crypto_pwhash_scryptsalsa208sha256_str(
                 $password,
                 \Sodium\CRYPTO_PWHASH_SCRYPTSALSA208SHA256_OPSLIMIT_INTERACTIVE,
                 \Sodium\CRYPTO_PWHASH_SCRYPTSALSA208SHA256_MEMLIMIT_INTERACTIVE
             );

            // TODO: Set the hash to a secure cookie to tie it to a browser on a computer.
            $this->account['username'] = $username;
            $this->account['token'] = $accountToken;
        }

        return $accountToken;
    }
}
