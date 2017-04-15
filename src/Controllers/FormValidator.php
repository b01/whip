<?php namespace Whip\Controllers;

/**
 * Interface FormValidator
 * @package \Whip\Controllers
 */
interface FormValidator
{
    /**
     * Perform validation.
     *
     * @param array $errors
     * @return bool
     */
    public function isValid(array & $errors) : bool;
}
