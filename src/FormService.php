<?php namespace Whip;

use Kshabazz\Slib\Tools\Utilities;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Whip\Controllers\Controller;
use Whip\Lash\Validator;

/**
 * Class FormService
 *
 * @package \Whip
 */
class FormService extends Controller
{
    use Tools;
    use Utilities;

    /** @var \Whip\SessionWrapper */
    protected $session;

    /** @var array Data for a processed form. */
    protected $data;

    /** @var \Whip\Lash\Validation */
    private $validation;

    /**
     * FormService constructor.
     *
     * @param \Whip\Lash\Validation $validation
     * @param \Whip\SessionWrapper $session
     */
    public function __construct(
        Validator $validation,
        SessionWrapper $session
    ) {
        $this->validation = $validation;
        $this->session = $session;
        $this->data = [];
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Try to process any form submitted by the client input in the request.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Whip\WhipException
     */
    public function process(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $forms
    ) : ResponseInterface {
        $input = $this->getScrubbedInput($request);

        // This looks for the form ID in the input array.
        $form = $this->findForm($forms, $input, $this->data);

        // Process the form.
        if ($form instanceof Form) {
            $formId = $form->getId();
            $formRules = $form->getRules();
            $formInput = $input[$formId]; // keep only the form input.
            $input = null; // we don't need this variable anymore.

            // A form with no input probably means testing is being done.
            if (\count($formInput) === 0) {
                throw new WhipException(WhipException::FORM_WITH_NO_INPUT, [$formId]);
            }

            // A form with no rules probably means a new form is being built and someone forgot
            // to add some rules to validate its input.
            if (\count($formRules) === 0) {
                throw new WhipException(WhipException::FORM_WITH_NO_RULES, [$formId]);
            }

            // Add validation rules.
            try {
                $this->validation->addRules($formRules);
            } catch (\Exception $err) {
                throw new WhipException(
                    WhipException::COULD_NOT_ADD_FORM_RULES,
                    [$formId, $err->getMessage()]
                );
            }

            // Validate the form
            try {
                $isValid = $this->validation->validate(
                    $formInput
                );
            } catch (\Exception $err) {
                throw new WhipException(
                    WhipException::FORM_VALIDATE_ERR,
                    [$formId, $err->getMessage()]
                );
            }

            $this->data[$formId] = [
                'id' => $formId, // yes redundant, but more for the template engine.
                'errors' => $this->validation->getErrors(),
                'input' => $formInput,
                'isValid' => $isValid
            ];

            // Overwrites previous session data without consideration.
            $this->session->setArray($formId, $this->data);

            if ($isValid === true) {
                $response = $form->submit($response, $formInput);
            }
        }

        return $response;
    }

    /**
     * Find and instantiate a form indicated in a request.
     *
     * @param array $forms
     * @return null|\Whip\Form
     * @throws WhipException
     */
    private function findForm(array $forms, array $input, array & $data) : ?Form
    {
        $rV = null;

        // Loop through forms, if their ID is present in the input
        // then return that form. Only the first form found will be
        // processed.
        foreach ($forms as $i => $form) {
            if (!$form instanceof Form) {
                throw new WhipException(
                    WhipException::NOT_A_FORM, [$i, Form::class]
                );
            }

            $name = $form->getId();
            $data[$name] = ['id' => $name];

            if (\array_key_exists($name, $input)) {
                $rV = $form;
//                break;
            }
        }

        return $rV;
    }
}
