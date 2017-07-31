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
 * Base interface for the Api client
 *
 * @package Gojira\Api\Client
 * @author  Toan Nguyen <me@nntoan.com>
 */
interface ClientInterface
{
    /**
     * Call the endpoint
     *
     * @param string $endpoint
     * @param array  $endpointParameters
     * @param string $body
     * @param string $method
     *
     * @return array
     */
    public function callEndpoint(
        $endpoint,
        array $endpointParameters = [],
        $body = null,
        $method = HttpMethod::GET
    );

    /**
     * Set authentication for HttpClient
     *
     * @param AuthenticationInterface $authentication
     */
    public function setAuthentication(AuthenticationInterface $authentication);

    /**
     * Set base url for HttpClient
     *
     * @param string $baseUrl
     */
    public function setBaseUrl($baseUrl);

    /**
     * Set HTTP method
     *
     * @param string $httpMethod
     */
    public function setHttpMethod($httpMethod);

    /**
     * Get response result
     *
     * @return mixed
     */
    public function getResult();

    /**
     * Get response status code
     *
     * @return int
     */
    public function getResultHttpCode();
}
