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
     * @param \Whip\Form $form
     */
    public function addForm(Form $form)
    {
        $this->forms[$form->getId()] = $form;
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

    /**
     * Process a form in the request.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return bool
     */
    public function process(ServerRequestInterface $request) : bool
    {
        $formSubmitted = false;
        $requestVars = $this->getScrubbedInput($request);

        foreach ($this->forms as $form) {
            $form->setInput($requestVars);

            if ($form->canSubmit()) {
                $form->submit();
                $formSubmitted = true;
            }
        }

        return $formSubmitted;
    }
}
