<?php namespace Whip\Controllers;
/**
 * Performs task that are common for all HTML pages. Such as redirecting all
 * pages to the login page when not logged-in. If you need logic that is
 * specific to a page, then do that in the view.
 */

use Whip\FormService;
use Whip\View;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Html Render HTML response for
 *
 * @package \Whip\Controllers
 */
class Html
{
    /** @var \Whip\FormService */
    private $formService;

    /** @var \Whip\View */
    private $view;

    /** @var \Psr\Http\Message\ServerRequestInterface */
    private $request;

    /** @var \Psr\Http\Message\ResponseInterface */
    private $response;

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
     * Will perform a redirect when does not pass all checks put in place.
     *
     * @param string $url
     * @param callable $hasMetRequirements
     */
    public function redirectOnCheck(string $url, callable $hasMetRequirements)
    {
        if (!$hasMetRequirements) {
            $this->response->withRedirect($url);
        }
    }

    /**
     * Render the HTML.
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function render()
    {
        $output = $this->view->render();

        $this->response->getBody()->write($output);

        return $this->response;
    }
}