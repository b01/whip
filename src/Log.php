<?php namespace Whip;

use Psr\Log\LoggerInterface;

/**
 * Trait Log
 *
 * @package \Whip
 */
trait Log
{
    /** @var \Psr\Log\LoggerInterface */
    private $log;

    /**
     * Set the logger.
     *
     * @param LoggerInterface $logger
     * @return static
     */
    public function withLogger(LoggerInterface $logger): self
    {
        $this->log = $logger;

        return $this;
    }
}