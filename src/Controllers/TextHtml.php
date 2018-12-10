<?php namespace Whip\Controllers;
/**
 * Performs task that are common for all HTML pages. Such as redirecting all
 * pages to the login page when not logged-in. If you need logic that is
 * specific to a page, then do that in the view.
 */

use Kshabazz\Slib\Tools\Utilities;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Whip\FormService;
use Whip\View;
use Whip\WhipException;

/**
 * Class Html Render HTML response for
 *
 * @package \Whip\Controllers
 */
abstract class TextHtml extends Controller
{
    use Utilities;

    /** @var \Whip\FormService */
    protected $formService;

    /**
     * TextHtml constructor.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param \Whip\FormService $formService
     */
    public function __construct(FormService $formService)
    {
        $this->formService = $formService;
    }

    /**
     * Build a response that will render the view (a.k.a the HTML).
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws WhipException
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        View $view,
        array $forms
    ) : ResponseInterface {
        $get = $request->getQueryParams();
        $getVars = $this->cleanArray($get);

        $post = $request->getParsedBody();
        $postVars = \is_array($post) ? $this->cleanArray($post) : [];

        $response = $this->formService->process($request, $response, $forms);

        $view->addData('postVars', $postVars);
        $view->addData('queryVars', $getVars);
        $view->addData('forms', $this->formService->getData());

        $html = $view->render();

        $output = $this->getHttpMessageBody($html);

        $response = $response->withBody($output);

        return $response;
    }
}
