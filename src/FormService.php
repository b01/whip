<?php namespace Whip;

use Kshabazz\Slib\Tools\Utilities;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class FormService
 *
 * @package \Whip
 */
abstract class FormService
{
    use Utilities;

    /** @var array of \Whip\Form */
    protected $forms;

    /** @var string */
    protected $formSubmitField;

    /**
     * FormService constructor.
     *
     * @param string $formSubmitField
     */
    public function __construct(string $formSubmitField)
    {
        $this->forms = [];
        $this->formSubmitField = $formSubmitField;
    }

    /**
     * Add a form to the service.
     *
     * @param \Whip\Form $form
     * @return \Whip\FormService
     */
    public function addForm(Form $form, $overwrite = false) : \Whip\FormService
    {
        $formId = \call_user_func(\get_class($form) . '::getId');

        if (\array_key_exists($formId, $this->forms) && !$overwrite) {
            throw new WhipException(WhipException::FORM_OVERWRITE, [$formId]);
        }

        $this->forms[$formId] = $form;

        return $this;
    }

    /**
     * Get data to fill in placeholders.
     *
     * @return array
     */
    public function getRenderData() : array
    {
        $formData = [];

        // Append any form input and errors to the placeholder data.
        foreach($this->forms as $key => $form) {
            $formData[$key] = $form->getRenderData();
        }

        return $formData;
    }

    /**
     * Extract form input from the Request.
     *
     * @return array
     */
    private function getScrubbedInput(ServerRequestInterface $request) : array
    {
        $get = $request->getQueryParams();
        $getVars = $this->cleanArray($get);

        $post = $request->getParsedBody();
        $postVars = \is_array($post) ? $this->cleanArray($post) : [];

        $fileVars = $request->getUploadedFiles();

        $requestVars = \array_merge($getVars, $postVars, $fileVars);

        return $requestVars;
    }

    /**
     * Try to process any form submitted by the client input in the request.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Whip\Form
     * @exception When the form cannot be found.
     */
    public function process(ServerRequestInterface $request) : ?Form
    {
        $requestVars = $this->getScrubbedInput($request);
        $returnVal = null;

        // Find the submitted form.
        $formKey = $this->getSafeArray($this->formSubmitField, $requestVars);
        $form = $this->getFromArray($formKey, $this->forms);

        // Form key was found but no form.
        if (!empty($formKey) && empty($form)) {
            throw new WhipException(WhipException::FORM_NOT_FOUND, [$this->formSubmitField]);
        }

        // This check keeps an error from being thrown when no form key or form is found.
        if (!empty($form)) {
            $form->setInput($requestVars);

            if ($form->canSubmit()) {
                $form->submit();
            }
        }

        return $form;
    }
}
