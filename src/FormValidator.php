<?php namespace Whip;

/**
 * Interface FormValidator
 *
 * @package \Whip
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
