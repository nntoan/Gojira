<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Jira\Endpoint;

use Gojira\Api\Endpoint\BaseEndpoint;
use Gojira\Api\Endpoint\EndpointInterface;

/**
 * Endpoint for Jira JQL
 *
 * @package Gojira\Jira\Endpoint
 * @author  Toan Nguyen <me@nntoan.com>
 */
class JqlEndpoint extends BaseEndpoint implements EndpointInterface
{
    /**
     * Searches for issues using JQL
     *
     * @param string      $jql           JQL query
     * @param string|null $startAt       Time
     * @param string|null $maxResults    Max results limit
     * @param string|null $validateQuery Validate the query
     * @param string|null $fields        Fields
     * @param string|null $expand        Expand sections
     *
     * @return array
     */
    public function search(
        $jql,
        $startAt = null,
        $maxResults = null,
        $validateQuery = null,
        $fields = null,
        $expand = null
    ) {
        $parameters = [
            'startAt'       => $startAt,
            'maxResults'    => $maxResults,
            'validateQuery' => $validateQuery,
            'fields'        => $fields,
            'expand'        => $expand
        ];

        return $this->apiClient->callEndpoint(__e('search?jql=%1', $jql), $parameters);
    }
}
