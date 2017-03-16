<?php namespace Whip;

use Kshabazz\Slib\Tools\Utilities;
use Serializable;

/**
 * Class Account
 *
 * @package \Whip
 */
abstract class AccountManager implements Serializable
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
    public function serialize()
    {
        return $this->serialize($this->account);
    }

    /**
     *
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $this->account = $this->unserialize($serialized);
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
        $account = $accountStorage->lookup($username, $password);
        $accountToken = null;

        if ($account) {
            // TODO: generate a hash that can be stored in the DB.
            // Tie the hash to this computer somehow.
             $accountToken = \Sodium\crypto_pwhash_scryptsalsa208sha256_str(
                 $password,
                 \Sodium\CRYPTO_PWHASH_SCRYPTSALSA208SHA256_OPSLIMIT_INTERACTIVE,
                 \Sodium\CRYPTO_PWHASH_SCRYPTSALSA208SHA256_MEMLIMIT_INTERACTIVE
             );
        }

        return $accountToken;
    }
}
