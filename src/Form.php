<?php namespace Whip;

use Psr\Http\Message\ResponseInterface;

/**
 * Class Form
 *
 * @package \Whip\Forms
 */
interface Form
{
    /**
     * Get a unique identifier for this form.
     *
     * @return mixed
     */
    public function getId() : string;

    /**
     * Get validation rules.
     * @return array
     */
    public function getRules() : array;

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param array $input
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function submit(ResponseInterface $response, array $input) : ResponseInterface;
}
