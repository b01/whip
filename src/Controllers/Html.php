<?php namespace Whip\Controllers;
/**
 * Performs task that are common for all HTML pages. Such as redirecting all
 * pages to the login page when not logged-in. If you need logic that is
 * specific to a page, then do that in the view.
 */

use Whip\AccountManager;
use Whip\FormService;
use Whip\Renderer;
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
    /** @var \Whip\AccountManager */
    private $account;

    /** @var string URL. */
    private $redirectUrl;

    /** @var \Whip\Renderer */
    private $renderer;

    /** @var \Psr\Http\Message\ServerRequestInterface */
    private $request;

    /** @var \Psr\Http\Message\ResponseInterface */
    private $response;

    /**
     * Html constructor.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param \Whip\Renderer $renderer
     * @param \Whip\Account $account
     */
    public function __construct(
        ServerRequestInterface $request,
        ResponseInterface $response,
        Renderer $renderer,
        FormService $formService,
        Account $account
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->renderer = $renderer;
        $this->account = $account;
    }

    /**
     * Redirect when the client is not logged.
     *
     * @param string $url Place to redirect to.
     */
    public function setRedirectUrl(string $url)
    {
        $this->redirectUrl = $url;
    }

    /**
     * Render the HTML.
     *
     * @param string $template
     * @param array & $data
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function render(string $template, array & $data = [])
    {
        if (!$this->account->isValid() && \is_string($this->redirectUrl)) {
            $this->response->withRedirect($this->redirectUrl);
        } else {
            $this->renderer->withTemplate($template);
            $output = $this->renderer->render($data);
            $this->response->getBody()->write($output);
        }

        return $this->response;
    }

    /**
     * Render the HTML.
     *
     * @param \Whip\View $view
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function renderView(View $view)
    {
        $data = $view->getRenderData($this->request);
        $this->render($view->getTemplate(), $data);

        return $this->response;
    }
}