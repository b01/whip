<?php namespace Whip;
/**
 * Please see the included LICENSE.txt with this source code. If no
 * LICENSE.txt was provided, then all rights for the source code in
 * this file are reserved by Khalifah Khalil Shabazz
 */

use Whip\Lash\Validator;

/**
 * Class HtmlForm
 *
 * @package \Whip
 */
abstract class HtmlForm implements Form
{
    /** Form ID key in the input array. */
    const FORM_ID_KEY = 'formId';

    /** Form errors key in the input array. */
    const FORM_ERRORS_KEY = 'errors';

    /** @var array Client form input. */
    protected $input;

    /** @var int Set at the appropriate times to indicate the forms current state. */
    protected $state;

    /** @var \Whip\Lash\Validation */
    protected $validation;

    /**
     * Login constructor.
     *
     * @param \Whip\Lash\Validator $validation
     */
    public function __construct(Validator $validation)
    {
        $this->validation = $validation;

        $this->input = [self::FORM_ID_KEY => $this->getId()];

        $this->state = static::NOT_PROCESSED;
    }

    /**
     * @inheritdoc
     */
    public function getRenderData() : array
    {
        $this->input[self::FORM_ERRORS_KEY] = $this->validation->getErrors();

        return $this->input;
    }

    /**
     * @inheritdoc
     */
    public function getState() : int
    {
        return $this->state;
    }

    /**
     * @return bool
     */
    public function hasNoErrors() : bool
    {
        $errors = $this->validation->getErrors();

        return empty($errors);
    }

    /**
     * Get error message to display to the client.
     *
     * @return array
     */
    abstract protected function getMessages() : array;
}
