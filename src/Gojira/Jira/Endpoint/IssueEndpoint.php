<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Jira\Endpoint;

use Gojira\Api\Configuration\Serializer;
use Gojira\Api\Endpoint\BaseEndpoint;
use Gojira\Api\Endpoint\EndpointInterface;
use Gojira\Api\Request\HttpMethod;

/**
 * Endpoint for JIRA REST /issue
 *
 * @package Gojira\Jira\Endpoint
 * @author  Toan Nguyen <me@nntoan.com>
 */
class IssueEndpoint extends BaseEndpoint implements EndpointInterface
{
    const ENDPOINT = 'issue';
    const EP_ASSIGNEE = 'assignee';

    const PAYLOAD_NAME = 'name';

    /**
     * Assign an issue to a user
     *
     * @param string      $issue    JIRA ticket number
     * @param string|null $assignee (optional) If the name is "-1" automatic assignee is used. A null name will remove
     *                              the assignee.
     *
     * @return array
     */
    public function assign($issue, $assignee = null)
    {
        $parameters = [];
        $payload = [
            self::PAYLOAD_NAME => $assignee
        ];

        return $this->apiClient->callEndpoint(
            __e('%1/%2/%3', self::ENDPOINT, $issue, self::EP_ASSIGNEE),
            $parameters,
            Serializer::encode($payload),
            HttpMethod::PUT
        );
    }
}
