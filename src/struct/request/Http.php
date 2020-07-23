<?php

namespace go1\rest\struct\request;

use go1\rest\errors\MissingCredentialsError;
use go1\rest\errors\MissingScopeError;
use go1\rest\struct\ServiceUrl;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use function json_decode;
use function json_encode;

/**
 * Example:
 *
 *      $client = $container->get(HttpClient::class);
 *      $client->sendRequest(
 *          $client
 *              ->createRequest(
 *                  'POST',
 *                  $client
 *                      ->serviceUr('user', '/login')
 *                      ->withQuery('csrf-token', 'some-thing')
 *              )
 *              ->withBody($client->createStream('{"username": "my-name", "password": "my-password"}'))
 *      );
 */
class Http
{
    private ClientInterface $client;
    private Psr17Factory    $sf;
    private ServiceUrl      $serviceUrl;

    public function __construct(ClientInterface $client, Psr17Factory $sf, ServiceUrl $serviceUrl)
    {
        $this->client = $client;
        $this->sf = $sf;
        $this->serviceUrl = $serviceUrl;
    }

    /**
     * @param string              $method
     * @param string|UriInterface $uri
     * @return RequestInterface
     */
    public function createRequest(string $method, $uri): RequestInterface
    {
        return $this->sf->createRequest($method, $uri);
    }

    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        return $this->sf->createResponse($code, $reasonPhrase);
    }

    public function createStream(string $content): StreamInterface
    {
        return $this->sf->createStream($content);
    }

    public function sendRequest(RequestInterface $req): ResponseInterface
    {
        return $this->client->sendRequest($req);
    }

    public function serviceUri(string $service, string $path = ''): UriInterface
    {
        return $this->sf->createUri(
            $this->serviceUrl->get($service, $path)
        );
    }

    public function responseJson(ResponseInterface $response, bool $assoc = false, int $depth = 512, int $option = 0)
    {
        $body = $response->getBody();
        $body->rewind();

        return json_decode($body->getContents(), $assoc, $depth, $option);
    }
}
