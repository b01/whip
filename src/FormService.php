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
    public function addForm(Form $form) : \Whip\FormService
    {
        $this->forms[$form->getId()] = $form;

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
     * @return string
     */
    public function process(ServerRequestInterface $request) : string
    {
        $requestVars = $this->getScrubbedInput($request);
        $form = null;
        // TODO: Figure out if we always want to go to a new location, or just
        // TODO: handle whatever submit returns.
        $newLocationUrl = '';

        // Find the submitted form.
        if (\array_key_exists($this->formSubmitField, $requestVars)) {
            $formKey = $requestVars[$this->formSubmitField];
            $form = $this->forms[$formKey];
        }

        if ($form instanceof Form) {
            $form->setInput($requestVars);

            if ($form->canSubmit()) {
                $newLocationUrl = $form->submit();
            }
        }

        return $newLocationUrl;
    }
}
