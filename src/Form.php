<?php namespace Whip;

/**
 * Class Form
 *
 * @package \Whip\Forms
 */
interface Form
{
    const NOT_PROCESSED = 0;

    const VALIDATED = 1;

    const SUBMITTED = 2;

    const INVALID = 3;

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
     * Indicates the current state of the form.
     *
     * Though optional, this should be set at the appropriate times to indicate the forms current state.
     * Optional because its only important to the application that implements them.
     *
     * @return int
     */
    public function getState() : int;

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
