<?php namespace Whip\Controllers;
/**
 * Performs task that are common for all HTML pages. Such as redirecting all
 * pages to the login page when not logged-in. If you need logic that is
 * specific to a page, then do that in the view.
 */

use Kshabazz\Slib\StringStream;
use Kshabazz\Slib\Tools\Utilities;
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
    use Utilities;

    /** @var \Whip\FormService */
    protected $formService;

    /** @var \Psr\Http\Message\ServerRequestInterface */
    protected $request;

    /** @var \Psr\Http\Message\ResponseInterface */
    protected $response;

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
        FormService $formService = null
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->formService = $formService;
    }

    /**
     * Add a form to the page.
     *
     * Multiple forms can be added.
     *
     * @param \Whip\Form $form
     * @return $this
     */
    public function addForm(Form $form)
    {
        $this->formService->addForm($form);

        return $this;
    }

    /**
     * Render the HTML.
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
        $view->addData('form', $this->formService->getRenderData());

        $html = $view->render();

        $output = new StringStream($html);

        $this->response = $this->response->withBody($output);

        return $this->response;
    }

    /**
     * Add a form to the page.
     *
     * Multiple forms can be added.
     *
     * @param \Whip\Form $form
     * @return $this
     */
    public function addForm(Form $form)
    {
        $this->formService->addForm($form);

        return $this;
    }
}
