<?php namespace Whip\Controllers;
/**
 * Please see the included LICENSE.txt with this source code. If no
 * LICENSE.txt was provided, then all rights for the source code in
 * this file are reserved by Khalifah Khalil Shabazz
 */

use Kshabazz\Slib\StringStream;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Controller
 *
 * @package \Whip\Controllers
 */
abstract class Controller
{
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
     * @return \ResponseInterface;
     */
    /**
     * @param \Psr\Http\MessageServerRequestInterface $request
     * @param \Psr\Http\MessageResponseInterface $response
     * @param int $httpStatusCode
     * @param string $route
     * @param array|null $query
     * @param string $httpProtocol
     * @param int $httpPort
     * @return \Psr\Http\MessageResponseInterface
     */
    public function redirectTo(
        ServerRequestInterface $request,
        ResponseInterface $response,
        int $httpStatusCode,
        string $route,
        array $query = null,
        string $httpProtocol = 'https',
        int $httpPort = 443
    ) : ResponseInterface {
        $uri = $request->getUri();
        $newUri = $uri->withScheme($httpProtocol)
            ->withPort($httpPort)
            ->withPath($route);

        if (\is_array($query)) {
            $newUri = $newUri->withQuery(\http_build_query($query));
        }

        return $response->withStatus($httpStatusCode)
            ->withHeader('Location', (string) $newUri);
    }
}
