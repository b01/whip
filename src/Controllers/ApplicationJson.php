<?php namespace Whip\Controllers;
use Kshabazz\Slib\StringStream;
use Kshabazz\Slib\Tools\Utilities;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Whip\View;

/**
 * Please see the included LICENSE.txt with this source code. If no
 * LICENSE.txt was provided, then all rights for the source code in
 * this file are reserved by Khalifah Khalil Shabazz
 */


class ApplicationJson extends Controller
{
    use Utilities;

    /**
     * @inheritdoc
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        View $view
    ): ResponseInterface {
        $json = $view->render();

        $output = $this->getHttpMessageBody($json);

        return $response->withBody($output)
            ->withHeader('Content-Type', 'application/json');
    }
}