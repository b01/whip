<?php namespace Whip;

use Kshabazz\Slib\Tools\Utilities;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Whip\Controllers\Controller;

/**
 * Class FormService
 *
 * @package \Whip
 */
abstract class FormService extends Controller
{
    use Utilities;

    /** @var \Whip\FormFactory */
    protected $factory;

    /** @var array List of \Whip\Form objects (keys are the form IDs). */
    protected $forms;

    /** @var string Form ID to indicate which form has posted. */
    protected $formSubmitField;

    /** @var string Key prefix for form render data stored in the session. */
    protected $sessionRenderDataKey;

    /**
     * FormService constructor.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param string $formSubmitField
     * @param FormFactory $factory
     * @param SessionWrapper $session
     */
    public function __construct(
        ServerRequestInterface $request,
        ResponseInterface $response,
        string $formSubmitField,
        FormFactory $factory,
        SessionWrapper $session
    ) {
        parent::__construct($request, $response);

        $this->formSubmitField = $formSubmitField;
        $this->factory = $factory;
        $this->forms = [];
        $this->sessionRenderDataKey = __CLASS__ . ':renderData';
        $this->session = $session;
    }

    /**
     * Get data to fill in placeholders.
     *
     * @return array
     */
    public function getRenderData(array $formNames) : array
    {
        $formData = [];

        foreach ($formNames as $formId) {
            $sessionKey = $this->getFormKey($formId);
            $sessionData = $this->session->getArray($sessionKey, null);
            $noSessionData = empty($sessionData);

            if ($noSessionData) {
                $form = $this->factory->get($formId);
                $formData[$formId] = $form->getRenderData();
            } else {
                $formData[$formId] = $sessionData;
            }
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
     * @return \Psr\Http\Message\ResponseInterface|null
     * @exception When the form key is found, but the form was not.
     */
    public function process() : ?ResponseInterface
    {
        $response = null;
        $formInput = $this->getScrubbedInput($this->request);
        // Find the submitted form.
        $formId = $this->getSafeArray($this->formSubmitField, $formInput);
        $form = $this->getForm($formId);

        // Process the form.
        if ($form instanceof Form) {
            $form->setInput($formInput);

            $routeInfo = $form->canSubmit() && $form->submit()
                ? $form->getSubmitRouteInfo()
                : $form->getPostBackRouteInfo();

            $response = \call_user_func_array([$this, 'redirectTo'], $routeInfo);

            $this->session->setArray(
                $this->getFormKey($formId),
                $form->getRenderData()
            );
        }

        return $response;
    }

    /**
     * Find and instantiate a form indicated in a request.
     *
     * @param string $formId
     * @return null|Form
     * @throws WhipException
     */
    private function getForm(string $formId) : ?Form
    {
        $form = $this->factory->get($formId);

        // Form key was found but no form.
        if (!empty($formId) && empty($form)) {
            throw new WhipException(
                WhipException::FORM_NOT_FOUND,
                [$this->formSubmitField]
            );
        }

        return $form;
    }

    /**
     * Get a form render data session key.
     *
     * @param string $formId
     * @return string
     */
    private function getFormKey(string $formId) : string
    {
        return "{$this->sessionRenderDataKey}:{$formId}";
    }
}
