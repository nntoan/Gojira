<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Api\Client;

use Gojira\Api\Authentication\AuthenticationInterface;
use Gojira\Api\Request\HttpMethod;

/**
 * Abstract base class for the Api client
 *
 * @package Gojira\Api\Client
 * @author  Toan Nguyen <me@nntoan.com>
 */
abstract class BaseClient
{
    /**
     * @var AuthenticationInterface
     */
    protected $authentication;

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var string
     */
    protected $endpoint;

    /**
     * @var array
     */
    protected $endpointParameters = [];

    /**
     * @var string
     */
    protected $body = null;

    /**
     * @var string
     */
    protected $httpMethod = HttpMethod::GET;

    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * @var bool
     */
    protected $useCache = false;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * Constructor.
     *
     * @param string                  $baseUrl        JIRA base url
     * @param AuthenticationInterface $authentication Authentication object
     * @param bool                    $debug          Debug mode
     * @param bool                    $useCache       Cache mode
     */
    public function __construct($baseUrl, AuthenticationInterface $authentication, $debug = false, $useCache = false)
    {
        $this->setBaseUrl($baseUrl);
        $this->setAuthentication($authentication);
        $this->setDebug($debug);
        $this->setUseCache($useCache);
    }

    /**
     * Set authentication for current session
     *
     * @param \Gojira\Api\Authentication\AuthenticationInterface $authentication
     */
    public function setAuthentication(AuthenticationInterface $authentication)
    {
        $this->authentication = $authentication;
    }

    /**
     * Get authentication object instance
     *
     * @return \Gojira\Api\Authentication\AuthenticationInterface
     */
    public function getAuthentication()
    {
        return $this->authentication;
    }

    /**
     * Set JIRA base url
     *
     * @param string $baseUrl
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * Get JIRA base url
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Set JIRA endpoint
     *
     * @param string $endpoint
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
    }

    /**
     * Get JIRA endpoint
     *
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * Set endpoint parameters
     *
     * @param array $endpointParameters
     */
    public function setEndpointParameters(array $endpointParameters)
    {
        $this->endpointParameters = $endpointParameters;
    }

    /**
     * Get endpoint parameters
     *
     * @return array
     */
    public function getEndpointParameters()
    {
        return $this->endpointParameters;
    }

    /**
     * Set request body
     *
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * Get request body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set HTTP method
     *
     * @param string $httpMethod
     */
    public function setHttpMethod($httpMethod)
    {
        $this->httpMethod = $httpMethod;
    }

    /**
     * Get HTTP method
     *
     * @return string
     */
    public function getHttpMethod()
    {
        return $this->httpMethod;
    }

    /**
     * Set debug mode
     *
     * @param boolean $debug
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    /**
     * Check is debug mode
     *
     * @return bool
     */
    public function isDebug()
    {
        return $this->debug;
    }

    /**
     * Set use cache mode
     *
     * @param boolean $isUseCache
     */
    public function setUseCache($isUseCache)
    {
        $this->useCache = $isUseCache;
    }

    /**
     * Check use cache mode
     *
     * @return bool
     */
    public function isUseCache()
    {
        return $this->useCache;
    }

    /**
     * Set data for the current object
     *
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Get data
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get deserialize response data (object data)
     *
     * @return array
     */
    public function getResult()
    {
        return $this->data;
    }

    /**
     * Set request headers
     *
     * @param array $headers
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * Get request headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Set header "Content-Type"
     *
     * @param string $contentType
     */
    public function setContentType($contentType)
    {
        $this->headers['Content-Type'] = $contentType;
    }

    /**
     * Get header "Content-Type"
     *
     * @return string|null
     */
    public function getContentType()
    {
        if (isset($this->headers['Content-Type'])) {
            return $this->headers['Content-Type'];
        }

        return null;
    }

    /**
     * Set header "Accept"
     *
     * @param string $accept
     */
    public function setAccept($accept)
    {
        $this->headers['Accept'] = $accept;
    }

    /**
     * Get header "Accept"
     *
     * @return string|null
     */
    public function getAccept()
    {
        if (isset($this->headers['Accept'])) {
            return $this->headers['Accept'];
        }

        return null;
    }

    /**
     * Set header "Authorization"
     *
     * @param string $token
     */
    public function setAuthorization($token)
    {
        $this->headers['Authorization'] = 'Basic ' . $token;
    }

    /**
     * Get header "Authorization"
     *
     * @return string|null
     */
    public function getAuthorization()
    {
        if (isset($this->headers['Authorization'])) {
            return $this->headers['Authorization'];
        }

        return null;
    }
}
