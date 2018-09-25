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
    /** Form ID key in the render data array. */
    const FORM_ID_KEY = 'id';

    /** Form input key in the render data array. */
    const FORM_INPUT_KEY = 'input';

    /** Form errors key in the render data array. */
    const FORM_ERRORS_KEY = 'errors';

    /** @var array a list of failure reasons form submission failed. */
    protected $failures;

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
        $this->failures = [];
    }

    /**
     * @inheritdoc
     */
    abstract public function getPostBackRouteInfo(): array;

    /**
     * @inheritdoc
     */
    public function getRenderData() : array
    {
        return [
            self::FORM_ID_KEY => static::getId(),
            self::FORM_INPUT_KEY => $this->input,
            self::FORM_ERRORS_KEY => $this->validation->getErrors(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getFailures(): array
    {
        return $this->failures;
    }

    /**
     * @inheritdoc
     */
    abstract public function getSubmitRouteInfo(): array;

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

    /**
     * Set a failure reason
     *
     * @return HtmlForm
     */
    protected function setFailure(string $reason) : HtmlForm
    {
        $this->failures[] = $reason;

        return $this;
    }
}
