<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Api\Endpoint;

/**
 * Interface for all JIRA endpoints
 *
 * @package Gojira\Api\Endpoint
 * @author  Toan Nguyen <me@nntoan.com>
 */
interface EndpointInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const BASE_ENDPOINT = '/rest/api/latest/';
    const BASE_ENDPOINT_V2 = '/rest/api/2/';
}
