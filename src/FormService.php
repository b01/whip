<?php namespace Whip;

use Kshabazz\Slib\Tools\Utilities;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class FormService
 *
 * @package \Whip
 *
 * TODO: See if this should become middle-ware, if so, then make it thus.
 */
abstract class FormService
{
    use Utilities;

    /** @var array of \Whip\Form */
    protected $forms;

    /** @var string */
    private $formSubmitField;

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
     * @param string $formName
     * @param \Whip\Form $form
     */
    public function addForm($formName, Form $form)
    {
        $this->forms[$formName] = $form;
    }

    /**
     * Get data to fill in placeholders.
     *
     * @return array
     */
    public function getRenderData()
    {
        $formData = [];

        // Append any form input and errors to the placeholder data.
        foreach($this->forms as $key => $form) {
            $formData['form.'][$key] = $form->getRenderData();
        }

        return $formData;
    }

    /**
     * Process a form in the request.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return bool|mixed
     */
    public function process(ServerRequestInterface $request, Form $form)
    {
        $formSubmitted = false;
        $requestVars = $this->getScrubbedInput($request);

        $form->setInput($requestVars);

        if ($form->canSubmit()) {
            $formSubmitted = $form->submit();
        }

        return $formSubmitted;
    }

    /**
     * Extract form input from the Request.
     *
     * @return array
     */
    public function getScrubbedInput(ServerRequestInterface $request)
    {
        $get = $request->getQueryParams();
        $getVars = $this->cleanArray($get);

        $post = $request->getParsedBody();
        $postVars = \is_array($post) ? $this->cleanArray($post) : [];

        $requestVars = \array_merge($getVars, $postVars);

        return $requestVars;
    }
}
