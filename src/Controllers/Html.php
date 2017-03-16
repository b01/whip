<?php namespace Whip\Controllers;
/**
 * Performs task that are common for all HTML pages. Such as redirecting all
 * pages to the login page when not logged-in. If you need logic that is
 * specific to a page, then do that in the view.
 */

use Whip\Form;
use Whip\FormService;
use Whip\View;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Html Render HTML response for
 *
 * @package \Whip\Controllers
 */
abstract class Html
{

    /** @var array of \Whip\Form */
    protected $forms;

    /** @var \Whip\FormService */
    protected $formService;

    /** @var array Query string data when present in the request. */
    protected $queryString;

    /** @var array POST method data when present in the request. */
    protected $postBody;

    /** @var \Psr\Http\Message\ServerRequestInterface */
    protected $request;

    /** @var \Psr\Http\Message\ResponseInterface */
    protected $response;

    /** @var \Whip\View */
    protected $view;

    /**
     * Html constructor.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param View $view
     * @param FormService|null $formService
     */
    public function __construct(
        ServerRequestInterface $request,
        ResponseInterface $response,
        View $view,
        FormService $formService = null
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->view = $view;
        $this->formService = $formService;
    }

    /**
     * Will perform a redirect under certain conditions.
     *
     * @param string $url
     * @param callable $metConditions
     */
    abstract public function shouldRedirect(string $url);

    /**
     * Render the HTML.
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function render()
    {
        // Add POST body and query params to the view.
        $this->view->addData('post', $this->request->getParsedBody())
            ->addData('query', $this->request->getQueryParams())
            ->addData('form', $this->getFormData());

        $output = $this->view->render();

        $this->response->getBody()->write($output);

        return $this->response;
    }

    /**
     * Add a form to the page.
     *
     *  Multiple forms can be added.
     * @param \Whip\Form $form
     * @return $this
     */
    public function addForm($key, Form $form)
    {
        $this->formService->addForm($key, $form);

        return $this;
    }

    /**
     * Get data to fill in placeholders.
     *
     * @return array
     */
    protected function getFormData()
    {
        $formData = [];

        // Append any form input and errors to the placeholder data.
        if (is_array($this->forms) && count($this->forms) > 0) {
            foreach($this->forms as $key => $form) {
                $formData['form.'][$key] = $form->getRenderData();
            }
        }

        return $formData;
    }
}