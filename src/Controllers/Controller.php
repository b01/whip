<?php namespace Whip\Controllers;
/**
 * Please see the included LICENSE.txt with this source code. If no
 * LICENSE.txt was provided, then all rights for the source code in
 * this file are reserved by Khalifah Khalil Shabazz
 */

use Kshabazz\Slib\StringStream;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Whip\Session;

/**
 * Class Controller
 *
 * @package \Whip\Controllers
 */
abstract class Controller
{
    use Session;

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
     * @param string $body
     * @return \Kshabazz\Slib\StringStream
     */
    public function getHttpMessageBody(string $body)
    {
        return new StringStream($body);
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
        array $query = null,
        string $httpProtocol = 'https',
        int $httpPort = 443
    ) {
        $uri = $this->request->getUri();
        $newUri = $uri->withScheme($httpProtocol)
            ->withPort($httpPort)
            ->withPath($route);

        if (\is_array($query)) {
            $newUri = $newUri->withQuery(\http_build_query($query));
        }

        return $this->response->withStatus($httpStatusCode)
            ->withHeader('Location', (string) $newUri);
    }
}
