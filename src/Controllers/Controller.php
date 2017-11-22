<?php namespace Whip\Controllers;
/**
 * Please see the included LICENSE.txt with this source code. If no
 * LICENSE.txt was provided, then all rights for the source code in
 * this file are reserved by Khalifah Khalil Shabazz
 */

use Kshabazz\Slib\Tools\Utilities;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Controller
 *
 * @package \Whip\Controllers
 */
abstract class Controller
{
    use Utilities;

    /** @var \Psr\Http\Message\ServerRequestInterface */
    protected $request;

    /** @var \Psr\Http\Message\ResponseInterface */
    protected $response;

    /**
     * Html constructor.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     */
    public function __construct(
        ServerRequestInterface $request,
        ResponseInterface $response
    ) {
        $this->request = $request;
        $this->response = $response;
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
}
