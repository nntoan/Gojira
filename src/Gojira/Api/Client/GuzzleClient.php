<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Api\Client;

use Gojira\Api\Authentication\JiraBasicAuthentication;
use Gojira\Api\Configuration\Serializer;
use Gojira\Api\Exception\ApiException;
use Gojira\Api\Exception\HttpNotFoundException;
use Gojira\Api\Exception\UnauthorizedException;
use Gojira\Api\HandlerStack\GuzzleStack;
use Gojira\Api\Request\HttpMethod;
use Gojira\Api\Request\StatusCodes;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;

/**
 * Guzzle HTTP client to work with JIRA REST API
 *
 * @package Gojira\Api\Client
 * @author  Toan Nguyen <me@nntoan.com>
 */
class GuzzleClient extends BaseClient implements ClientInterface
{
    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $cookiefile;

    /**
     * @var string
     */
    protected $crtBundleFile;

    /**
     * @var int
     */
    protected $httpStatusCode;

    /**
     * Call the API with an endpoint
     *
     * @param string $endpoint
     * @param array  $endpointParameters
     * @param null   $body
     * @param string $method
     *
     * @return array
     */
    public function callEndpoint(
        $endpoint,
        array $endpointParameters = [],
        $body = null,
        $method = HttpMethod::GET
    ) {
        // Set the endpoint
        $this->setEndpoint($endpoint);
        // Set the parameters
        $this->setEndpointParameters($endpointParameters);
        // Set the body
        $this->setBody($body);
        // Set the HTTP method
        $this->setHttpMethod($method);
        // Call the endpoint
        $this->call();

        // return the result
        return $this->getResult();
    }

    /**
     * Initialize the JIRA REST API
     * This method initializes a Guzzle Http client instance and saves it in the private $httpClient variable.
     * Other methods can retrieve this Guzzle Http client instance by calling $this->getHttpClient().
     * This method should called only once
     *
     * @throws ApiException
     */
    public function init()
    {
        // Check if the curl object isn't set already
        if ($this->isInit()) {
            throw new ApiException("A Guzzle Http Client instance is already initialized");
        }

        $defaults = [
            'version' => '1.1'
        ];

        // Enable debug if debug is true
        if ($this->isDebug()) {
            $defaults['debug'] = true;
        }

        // Set crtBundleFile (certificate) if given else disable SSL verification
        if (!empty($this->crtBundleFile)) {
            $defaults['verify'] = $this->getCrtBundleFile();
        }

        // Set cookiefile for sessiondata
        if (!empty($this->cookiefile)) {
            $defaults['config'] = [
                'curl' => [
                    CURLOPT_COOKIEJAR => $this->getCookiefile()
                ]
            ];
        }

        $httpClient = new Client(array_merge_recursive([
            //'handler' => GuzzleStack::create(), //TODO: Fix fucking slow 20k concurrent requests
            'base_uri' => $this->getBaseUrl()
        ], $defaults));

        $this->setHttpClient($httpClient);
    }

    /**
     * Call the API endpoint
     *
     * @throws \Gojira\Api\Exception\UnauthorizedException
     * @throws \Gojira\Api\Exception\HttpNotFoundException
     * @throws \Gojira\Api\Exception\HttpException
     * @throws \Gojira\Api\Exception\ResultException
     */
    protected function call()
    {
        // Check if the curl object is set
        if (!$this->isInit()) {
            // If it isn't, we do it right now
            $this->init();
        }

        $options = [];
        // Set basic authentication and other headers if enabled
        if ($this->authentication instanceof JiraBasicAuthentication) {
            $this->setAccept('application/json');
            $this->setContentType('application/json;charset=UTF-8');
            $this->setAuthorization($this->authentication->getCredential());
        }

        // Set endpoint parameters if available
        foreach ($this->getEndpointParameters() as $key => $value) {
            if (!empty($value)) {
                $options[RequestOptions::QUERY] = [
                    $key => $value
                ];
            }
        }

        // Create new PSR-7 request
        $request = new Request(
            $this->getHttpMethod(),
            $this->getEndpoint(),
            $this->getHeaders(),
            $this->getBody()
        );

        try {
            $response = $this->getHttpClient()->send($request, $options);
        } catch (ClientException $e) {
            // Check if not found
            if ($e->getResponse()->getStatusCode() === StatusCodes::HTTP_NOT_FOUND) {
                throw new HttpNotFoundException();
            }

            // Check if unauthorized
            if ($e->getResponse()->getStatusCode() === StatusCodes::HTTP_UNAUTHORIZED) {
                throw new UnauthorizedException();
            }

            throw $e;
        }

        $this->setHttpStatusCode($response->getStatusCode());

        if ($this->getAccept() === 'application/json' && !empty($json = $response->getBody()->getContents())) {
            $this->setData(Serializer::decode($json));
        } else {
            $this->setData($response->getBody()->getContents());
        }
    }

    /**
     * Set HTTP client
     *
     * @param Client $httpClient
     */
    public function setHttpClient($httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Get HTTP client
     *
     * @return Client
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * Set certificate bundle file
     *
     * @param string $crtBundleFile
     */
    public function setCrtBundleFile($crtBundleFile)
    {
        $this->crtBundleFile = $crtBundleFile;
    }

    /**
     * Get certificate bundle file
     *
     * @return string
     */
    public function getCrtBundleFile()
    {
        return $this->crtBundleFile;
    }

    /**
     * Set cookie file
     *
     * @param string $cookiefile
     */
    public function setCookiefile($cookiefile)
    {
        $this->cookiefile = $cookiefile;
    }

    /**
     * Get cookie file
     *
     * @return string
     */
    public function getCookiefile()
    {
        return $this->cookiefile;
    }

    /**
     * Get response HTTP code
     *
     * @return int
     */
    public function getResultHttpCode()
    {
        return $this->httpStatusCode;
    }

    /**
     * Set HTTP status code
     *
     * @param int $httpStatusCode
     */
    public function setHttpStatusCode($httpStatusCode)
    {
        $this->httpStatusCode = $httpStatusCode;
    }

    /**
     * Returns if Curl is initialized or not
     *
     * @return bool
     */
    protected function isInit()
    {
        if ($this->getHttpClient() !== null) {
            return true;
        }

        return false;
    }
}
