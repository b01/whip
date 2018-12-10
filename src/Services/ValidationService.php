<?php namespace Whip\Services;
/**
 * Performs task that are common for all HTML pages. Such as redirecting all
 * pages to the login page when not logged-in. If you need logic that is
 * specific to a page, then do that in the view.
 */

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Whip\Controllers\Controller;
use Whip\Form;
use Whip\Lash\Validator;
use Whip\Tools;
use Whip\WhipException;

/**
 * Class ValidationService
 * @package \Whip\Services
 */
class ValidationService extends Controller
{
    use Tools;

    /** @var array A hash array where each key is an array of rules. */
    private $rulesSets;

    /** @var \Whip\Lash\Validation */
    private $validation;

    /**
     * ValidationService constructor.
     *
     * @param \Whip\Lash\Validation $validation
     */
    public function __construct(Validator $validation, array $forms)
    {
        $this->validation = $validation;
        // Keep only each forms rules mapped to their corresponding ID.
        // Note order matters when 1 or more rules have the same key/name.
        $this->rulesSets = $this->getRuleSets($forms);
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response
    ) : ResponseInterface {
        $input = $this->getScrubbedInput($request);
        $rules = $this->findRules($input);

        if (!empty($rules)) {
            $this->validation->addRules($rules);
            $isValid = $this->validation->validate($input);
        }

        $responseData = [
            'errors' => $this->validation->getErrors(),
            'isValid' => $isValid
        ];

        $responseBody = $this->getHttpMessageBody(json_encode($responseData));

        return $response->withBody($responseBody);
    }

    private function findRules(array & $input) : array
    {
        $rules = [];

        foreach ($input as $key => $value) {
            if (\array_key_exists($key, $this->rulesSets)) {
                $rules = $this->rulesSets[$key];
                $input = $value;
            }
        }

        return $rules;
    }

    /**
     * Get rules from each form.
     *
     * @param array $forms
     * @return array
     */
    private function getRuleSets(array $forms) : array
    {
        $ruleSets = [];

        foreach ($forms as $i => $form) {
            // Let the dev know immediately they have a bad form in the mix.
            if (!$form instanceof Form) {
                throw new WhipException(
                    WhipException::NOT_A_FORM,
                    [$i, Form::class]
                );
            }

            $rules = $form->getRules();

            if (empty($rules)) {
                throw new WhipException(
                    WhipException::FORM_WITH_NO_RULES,
                    [$form->getId()]
                );
            }

            $ruleSets[$form->getId()] = $rules;
        }

        return $ruleSets;
    }
}
