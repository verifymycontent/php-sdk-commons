<?php

namespace VerifyMyContent\Commons\Transport;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use GuzzleHttp\RequestOptions;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;


class HTTP
{
    const __UNSAFE_GUZZLE_CLIENT = "__unsafe_guzzleClient";
    /**
     * @property GuzzleClient guzzleClient
     * @property string baseURL
     */

    /**
     * @var GuzzleClient
     */
    private $guzzleClient;
    /**
     * @var null
     */
    private $baseURL;
    /**
     * @var string
     */
    private $userAgent;

    /**
     * @param string $baseURL
     * @param string $userAgent
     */
    function __construct(
        string $baseURL = "",
        string $userAgent = "VerifyMyContent Commons"
    )
    {
        $this->baseURL = rtrim($baseURL, "/");
        $this->guzzleClient = new GuzzleClient();
        $this->userAgent = $userAgent;
    }

    /**
     * @return string
     */
    public function getBaseURL(): string
    {
        return $this->baseURL;
    }

    /**
     * @param null $baseURL
     * @return HTTP
     */
    public function setBaseURL($baseURL): HTTP
    {
        $this->baseURL = $baseURL;
        return $this;
    }

    /**
     * @param string $path
     * @param array $headers
     * @param array $validStatusCodes
     * @return ResponseInterface
     * @throws InvalidStatusCodeException
     */
    public function get(string $path, array $headers = [], array $validStatusCodes = []): ResponseInterface
    {
        return $this->request("GET", $path, null, $headers, $validStatusCodes);
    }

    /**
     * @param string $path
     * @param $body
     * @param array $headers
     * @param array $validStatusCodes
     * @return ResponseInterface
     * @throws InvalidStatusCodeException
     */
    public function post(string $path, $body, array $headers = [], array $validStatusCodes = []): ResponseInterface
    {
        return $this->request("POST", $path, $body, $headers, $validStatusCodes);
    }

    /**
     * @param string $path
     * @param $body
     * @param array $headers
     * @param array $validStatusCodes
     * @return ResponseInterface
     * @throws InvalidStatusCodeException
     */
    public function put(string $path, $body, array $headers = [], array $validStatusCodes = []): ResponseInterface
    {
        return $this->request("PUT", $path, $body, $headers, $validStatusCodes);
    }

    /**
     * @param string $path
     * @param $body
     * @param array $headers
     * @param array $validStatusCodes
     * @return ResponseInterface
     * @throws InvalidStatusCodeException
     */
    public function patch(string $path, $body, array $headers = [], array $validStatusCodes = []): ResponseInterface
    {
        return $this->request("PATCH", $path, $body, $headers, $validStatusCodes);
    }

    /**
     * @param string $path
     * @param array $headers
     * @param array $validStatusCodes
     * @return ResponseInterface
     * @throws InvalidStatusCodeException
     */
    public function delete(string $path, array $headers = [], array $validStatusCodes = []): ResponseInterface
    {
        return $this->request("DELETE", $path, null, $headers, $validStatusCodes);
    }


    /**
     * @param $method
     * @param $uri
     * @param $body
     * @param array $headers
     * @param array $validStatusCodes
     * @return ResponseInterface
     * @throws InvalidStatusCodeException
     * @throws GuzzleException
     */
    private function request($method, $uri, $body, array $headers = [], array $validStatusCodes = []): ResponseInterface
    {
        $url = $this->baseURL . $uri;
        $headers = array_merge([
            "Accept" => "application/json",
            "Content-Type" => "application/json",
            "User-Agent" => $this->userAgent
        ], $headers);

        $response = $this->guzzleClient->request(
            $method,
            $url,
            [
                RequestOptions::HEADERS => $headers,
                RequestOptions::JSON => $body,
                RequestOptions::HTTP_ERRORS => false
            ]
        );

        $this->validate_response($response, $validStatusCodes);
        return $response;
    }

    /**
     * @param ResponseInterface $response
     * @param array $validStatusCodes
     * @throws InvalidStatusCodeException
     */
    private function validate_response(ResponseInterface $response, array $validStatusCodes = []): void
    {
        if (!is_array($validStatusCodes) || count($validStatusCodes) === 0) {
            if (substr($response->getStatusCode(), 0, 1) !== "2") {
                throw new InvalidStatusCodeException($response->getStatusCode());
            }

            return;
        }

        if (!in_array($response->getStatusCode(), $validStatusCodes)) {
            throw new InvalidStatusCodeException($response->getStatusCode());
        }
    }

    private function _setGuzzleClient($value): void
    {
        if (!($value instanceof GuzzleClient)) {
            throw new \TypeError("Value must be an instance of GuzzleHttp\Client");
        }

        $this->guzzleClient = $value;
    }

    public function __set($name, $value)
    {
        if ($name === self::__UNSAFE_GUZZLE_CLIENT) {
            return $this->_setGuzzleClient($value);
        }

        throw new \InvalidArgumentException("Invalid HTTP property on set: " . $name);
    }
}
