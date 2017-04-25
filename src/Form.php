<?php namespace Whip;

/**
 * Class Form
 *
 * @package \Whip\Forms
 */
interface Form
{
    /**
     * Perform any checks that determine if the form can be submitted.
     *
     * @return bool
     */
    public function canSubmit() : bool;

    /**
     * Get a unique name for this form.
     *
     * @return mixed
     */
    public function getId() : string;

    /**
     * Get data to pass to the template engine to aid rendering. For example, field value and errors.
     *
     * @return array
     */
    public function getRenderData() : array;

    /**
     * Clean the request variables by filtering each field, at least, through htmlspecialchars.
     *
     * @param array $requestVars
     */
    public function setInput(array $requestVars) : Form;

    /**
     * Perform the submission.
     *
     * @return mixed
     */
    public function submit();
}
