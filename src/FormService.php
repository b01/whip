<?php namespace Whip;

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
    /** @var string */
    private $formSubmitField;

    /**
     * FormService constructor.
     *
     * @param string $formSubmitField
     */
    public function __construct(string $formSubmitField)
    {
        $this->formSubmitField = $formSubmitField;
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
        $getVars = filter_input_array(\INPUT_GET, $get);

        $post = $request->getParsedBody();
        $postVars = \is_array($post) ? filter_input_array(\INPUT_POST, $post) : [];

        $requestVars = \array_merge($getVars, $postVars);

        return $requestVars;
    }
}
