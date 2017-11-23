<?php namespace Whip;

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
    public static function getId() : string;

    /**
     * Perform any checks that determine if the form can be submitted.
     *
     * @return bool
     */
    public function canSubmit() : bool;

    /**
     * Get failure reasons.
     *
     * @return array A list of reason why form submission failed.
     */
    public function getFailures() : array;

    /**
     * Get route and http status code to redirect to when submission fails.
     *
     * @return array An array containing the http status code at index 0, and the route at index 1.
     */
    public function getPostBackRouteInfo() : array;

    /**
     * Get data to pass to the template engine to aid rendering. For example, field value and errors.
     *
     * @return array
     */
    public function getRenderData() : array;

    /**
     * Get the route to redirect to when submission is a success.
     *
     * @return string
     */
    public function getSubmitRouteInfo() : array;

    /**
     * Clean the request variables by filtering each field, at least, through htmlspecialchars.
     *
     * @param array $requestVars
     */
    public function setInput(array $requestVars) : Form;

    /**
     * Perform the submission.
     *
     * @return bool Returns TRUE indicating that the submission was a success, FALSE on failure.
     */
    public function submit() : bool;
}
