<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Api\Endpoint;

use Gojira\Api\Client\ClientInterface;

/**
 * Abstract base endpoint class
 *
 * @package Gojira\Api\Endpoint
 * @author  Toan Nguyen <me@nntoan.com>
 */
abstract class BaseEndpoint implements EndpointInterface
{
    /**
     * @var ClientInterface
     */
    protected $apiClient;

    /**
     * BaseEndpoint constructor.
     *
     * @param ClientInterface $apiClient
     */
    public function __construct(ClientInterface $apiClient)
    {
        $this->apiClient = $apiClient;
    }
}
