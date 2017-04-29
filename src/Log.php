<?php namespace Whip;

use Psr\Log\LoggerInterface;

/**
 * Trait Log
 *
 * @package \Whip
 */
trait Log
{
    private $log;

    /**
     * Set the logger.
     *
     * @param LoggerInterface $logger
     * @return $this
     */
    public function withLogger(LoggerInterface $logger)
    {
        $this->log = $logger;

        return $this;
    }
}