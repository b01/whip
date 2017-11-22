<?php namespace Whip\Controllers;
/**
 * Performs task that are common for all HTML pages. Such as redirecting all
 * pages to the login page when not logged-in. If you need logic that is
 * specific to a page, then do that in the view.
 */

use Kshabazz\Slib\StringStream;
use Kshabazz\Slib\Tools\Utilities;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Whip\FormService;
use Whip\View;

/**
 * Class Html Render HTML response for
 *
 * @package \Whip\Controllers
 */
abstract class TextHtml extends Controller
{
    use Utilities;

    /** @var array A list of form models to pass to the render engine. */
    protected $forms;

    /** @var \Whip\FormService */
    protected $formService;

    /** @var \Psr\Http\Message\ServerRequestInterface */
    protected $request;

    /** @var \Psr\Http\Message\ResponseInterface */
    protected $response;

    /**
     * Html constructor.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param View $view
     * @param FormService|null $formService
     */
    public function __construct(
        ServerRequestInterface $request,
        ResponseInterface $response,
        FormService $formService = null
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->formService = $formService;
        $this->forms = [];
    }

    /**
     * Build a response that will render the view (a.k.a the HTML).
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function render(View $view)
    {
        $get = $this->request->getQueryParams();
        $getVars = $this->cleanArray($get);

        $post = $this->request->getParsedBody();
        $postVars = \is_array($post) ? $this->cleanArray($post) : [];

        $view->addData('postVars', $postVars);
        $view->addData('queryVars', $getVars);
        $view->addData('forms', $this->formService->getRenderData($this->forms));

        $html = $view->render();

        $output = new StringStream($html);

        $this->response = $this->response->withBody($output);

        return $this->response;
    }

    /**
     * Redirect to a specified URL.
     *
     * @param \Psr\Http\Message\UriInterface $url
     * @param int $httpStatusCode
     * @return \Psr\Http\Message\ResponseInterface;
     */
    public function redirectTo(
        int $httpStatusCode,
        string $route,
        string $httpProtocol = 'https',
        int $httpPort = 443
    ) {
        $uri = $this->request->getUri();
        $newUri = $uri->withScheme($httpProtocol)
            ->withPort($httpPort)
            ->withPath($route);

        return $this->response->withStatus($httpStatusCode)
            ->withHeader('Location', (string) $newUri);
    }

    /**
     * Add a list of form models you want to pass to the render engine.
     *
     * Adding forms here will pass them allong to the render engine. Allowing form values, error messages, etc to be
     * shown in the response.
     *
     * @param array $forms
     * @return \Whip\Controllers\TextHtml
     */
    public function withForms(array $forms)
    {
        $this->forms = $forms;

        return $this;
    }
}
