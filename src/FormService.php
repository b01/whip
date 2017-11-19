<?php namespace Whip;

use Kshabazz\Slib\Tools\Utilities;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class FormService
 *
 * @package \Whip
 */
abstract class FormService
{
    use Utilities;

    /** @var \Whip\FormFactory */
    protected $factory;

    /** @var array of \Whip\Form */
    protected $forms;

    /** @var string */
    protected $formSubmitField;

    /**
     * FormService constructor.
     *
     * @param string $formSubmitField
     */
    public function __construct(
        string $formSubmitField,
        FormFactory $factory
    ) {
        $this->forms = [];
        $this->formSubmitField = $formSubmitField;
        $this->factory = $factory;
    }

    /**
     * Add a form to the service.
     *
     * @param \Whip\Form $form
     * @return \Whip\FormService
     */
    public function addForm(string $fullClassName, callable $initializer, bool $overwrite = false) : FormService
    {
        $this->factory->set($fullClassName, $initializer, $overwrite);

        return $this;
    }

    /**
     * Get data to fill in placeholders.
     *
     * @return array
     */
    public function getRenderData(array $formNames) : array
    {
        $formData = [];

        foreach ($formNames as $formName) {
            $this->forms[$formName] = $this->factory->get($formName);
            $formData[$formName] = $this->forms[$formName]->getRenderData();
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
        // TODO: Extract to separate function.
        $requestVars = $this->getScrubbedInput($request);
        $returnVal = null;

        // Find the submitted form.
        $formId = $this->getSafeArray($this->formSubmitField, $requestVars);
        $form = $this->factory->get($formId);

        // Form key was found but no form.
        if (!empty($formId) && empty($form)) {
            throw new WhipException(WhipException::FORM_NOT_FOUND, [$this->formSubmitField]);
        }
        // TODO: End extraction.

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
