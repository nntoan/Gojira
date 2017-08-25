<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Jira\Endpoint;

use Gojira\Framework\Serializer\Serializer;
use Gojira\Api\Endpoint\EndpointInterface;
use Gojira\Api\Request\HttpMethod;
use Gojira\Api\Response\ResponseInterface;

/**
 * Endpoint for JIRA REST /transitions
 *
 * @package Gojira\Jira\Endpoint
 * @author  Toan Nguyen <me@nntoan.com>
 */
class TransitionEndpoint extends IssueEndpoint implements EndpointInterface
{
    const EP_TRANSITIONS = 'transitions';

    const PARAM_EXPAND = 'expand';

    const PAYLOAD_TRANSITION = 'transition';
    const PAYLOAD_FIELDS = 'fields';
    const PAYLOAD_RESOLUTION = 'resolution';

    /**
     * Returns a full representation of the transitions possible for the specified issue and the fields required to
     * perform the transition. [STATUS 200]
     *
     * @param string      $issue  JIRA ticket number
     *
     * @param string|null $expand [optional] Fields will only be returned if expand=transitions.fields.
     *
     * @return array
     */
    public function getTransitions(
        $issue,
        $expand = null
    ) {
        $parameters = [
            self::PARAM_EXPAND => $expand
        ];

        return $this->apiClient->callEndpoint(
            __e('%1/%2/%3', self::ENDPOINT, $issue, self::EP_TRANSITIONS),
            $parameters
        );
    }

    /**
     * Perform a transition on an issue [STATUS 204]
     *
     * @param string      $issue        JIRA ticket number
     *
     * @param string      $transitionId Transition ID
     * @param string|null $resolutionId [optional] Resolution ID
     *
     * @param string|null $expand       [optional] Fields will only be returned if expand=transitions.fields.
     *
     * @return array
     */
    public function doTransition(
        $issue,
        $transitionId,
        $resolutionId = null,
        $expand = null
    ) {
        $parameters = [
            self::PARAM_EXPAND => $expand
        ];
        $payload = [
            self::PAYLOAD_TRANSITION => [
                ResponseInterface::ID => $transitionId
            ]
        ];
        if (!empty($resolutionId)) {
            $fields = [
                self::PAYLOAD_FIELDS => [
                    self::PAYLOAD_RESOLUTION => [
                        ResponseInterface::ID => $resolutionId
                    ]
                ]
            ];
            $payload = array_merge($payload, $fields);
        }

        return $this->apiClient->callEndpoint(
            __e('%1/%2/%3', self::ENDPOINT, $issue, self::EP_TRANSITIONS),
            $parameters,
            Serializer::encode($payload),
            HttpMethod::POST
        );
    }
}
