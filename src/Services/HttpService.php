<?php namespace Whip\Services;
/**
 * Please see the included LICENSE.txt with this source code. If no
 * LICENSE.txt was provided, then all rights for the source code in
 * this file are reserved by Khalifah Khalil Shabazz
 */

use GuzzleHttp\Client;
use Whip\WhipException;

/**
 * Class HttpService
 *
 * @package \Whip\Services
 */
class HttpService
{
    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * @var \GuzzleHttp\Psr7\Request Information about the last request for debugging.
     */
    protected $lastRequest;

    /**
     * Service constructor.
     *
     * @param \GuzzleHttp\Client $httpClient
     * @param string $baseUrl
     */
    public function __construct(
        Client $httpClient,
        $baseUrl
    ) {
        $this->httpClient = $httpClient;
        $this->baseUrl = $baseUrl;
    }

    /**
     * @return string Represents the request headers and body.
     */
    public function getLastRequest()
    {
        $headers = $this->lastRequest->getHeaders();
        $body = $this->lastRequest->getBody();
        $url = (string) $this->lastRequest->getUri();

        return 'Request URL: ' . $url . \PHP_EOL
            . 'Request Headers: ' . print_r($headers, true) . \PHP_EOL
            . 'Request Body: ' . $body . \PHP_EOL;
    }

    /**
     * Send a request
     *
     * @param string $method
     * @param string $endpoint
     * @param string $body
     * @param array $headers
     * @return null|string
     * @throws \Whip\WhipException
     */
    protected function send($method, $endpoint = '', array $headers = null, $body = null)
    {
        $response = null;
        $url = $this->baseUrl . $endpoint;
        $options = ['headers' => $headers];

        if (!empty($body)) {
            $options['body'] = $body;
        }

        $this->lastRequest = $this->httpClient->request(
            $method,
            $url,
            $options
        );

        try {
            $response = $this->httpClient->send($this->lastRequest);
        } catch (\Exception $error) {
            throw new WhipException(
                WhipException::BAD_SERVICE_REQUEST,
                [__CLASS__, $error->getMessage()]
            );
        }

        return $response;
    }
}
