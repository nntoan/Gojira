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
    const EP_WORKLOG = 'worklog';

    const PARAM_ADJUST_ESTIMATE = 'adjustEstimate';
    const PARAM_NEW_ESTIMATE = 'newEstimate';
    const PARAM_REDUCE_BY = 'reduceBy';
    const PARAM_INCREASE_BY = 'increaseBy';

    const PAYLOAD_NAME = 'name';
    const PAYLOAD_COMMENT = 'comment';
    const PAYLOAD_VISIBILITY = 'visibility';
    const PAYLOAD_VISIBILITY_TYPE = 'type';
    const PAYLOAD_VISIBILITY_VALUE = 'value';
    const PAYLOAD_STARTED = 'started';
    const PAYLOAD_TIME_SPENT = 'timeSpent';
    const PAYLOAD_TIME_SPENT_SECONDS = 'timeSpentSeconds';

    /**
     * Get list of worklogs for an issue [STATUS 200]
     *
     * @param string $issue JIRA ticket number
     *
     * @return array
     */
    public function listWorklog($issue)
    {
        return $this->apiClient->callEndpoint(__e('%1/%2/%3', self::ENDPOINT, $issue, self::EP_WORKLOG));
    }

    /**
     * Add worklog for an issue [STATUS 201]
     *
     * @param string      $issue          JIRA ticket number
     *
     * @param string      $timeSpent      How much time spent (e.g. '3h 30m')
     * @param string|null $comment        [optional] Comment
     * @param string|null $started        [optional] Set date of work
     *
     * @param string|null $adjustEstimate [optional] Allows you to provide specific instructions to update the
     *                                    remaining time estimate of the issue. Valid values are
     *                                    "new" - sets the estimate to a specific value
     *                                    "leave"- leaves the estimate as is
     *                                    "manual" - specify a specific amount to increase remaining estimate by
     *                                    "auto"- Default option. Will automatically adjust the value based on the new
     *                                    timeSpent specified on the worklog
     * @param string|null $newEstimate    (required when "new" is selected for adjustEstimate) the new value for the
     *                                    remaining estimate field. e.g. "2d"
     * @param string|null $reduceBy       (required when "manual" is selected for adjustEstimate) the amount to reduce
     *                                    the remaining estimate by e.g. "2d"
     *
     * @return array
     */
    public function addWorklog(
        $issue,
        $timeSpent,
        $comment = null,
        $started = null,
        $adjustEstimate = null,
        $newEstimate = null,
        $reduceBy = null
    ) {
        $parameters = [
            self::PARAM_ADJUST_ESTIMATE => $adjustEstimate,
            self::PARAM_NEW_ESTIMATE => $newEstimate,
            self::PARAM_REDUCE_BY => $reduceBy,
        ];
        $payload = [
            self::PAYLOAD_COMMENT => $comment,
            self::PAYLOAD_TIME_SPENT => $timeSpent,
            self::PAYLOAD_STARTED => $started
        ];

        return $this->apiClient->callEndpoint(
            __e('%1/%2/%3', self::ENDPOINT, $issue, self::EP_WORKLOG),
            $parameters,
            Serializer::encode($payload),
            HttpMethod::POST
        );
    }

    /**
     * Update worklog for an issue [STATUS 200]
     *
     * @param string      $issue          JIRA ticket number
     * @param string      $worklogId      Worklog ID
     *
     * @param string      $timeSpent      How much time spent (e.g. '3h 30m')
     * @param string|null $comment        [optional] Comment
     * @param string|null $started        [optional] Set date of work
     *
     * @param string|null $adjustEstimate [optional] Allows you to provide specific instructions to update the
     *                                    remaining time estimate of the issue. Valid values are
     *                                    "new" - sets the estimate to a specific value
     *                                    "leave"- leaves the estimate as is
     *                                    "manual" - specify a specific amount to increase remaining estimate by
     *                                    "auto"- Default option. Will automatically adjust the value based on the new
     *                                    timeSpent specified on the worklog
     * @param string|null $newEstimate    (required when "new" is selected for adjustEstimate) the new value for the
     *                                    remaining estimate field. e.g. "2d"
     *
     * @return array
     */
    public function updateWorklog(
        $issue,
        $worklogId,
        $timeSpent,
        $comment = null,
        $started = null,
        $adjustEstimate = null,
        $newEstimate = null
    ) {
        $parameters = [
            self::PARAM_ADJUST_ESTIMATE => $adjustEstimate,
            self::PARAM_NEW_ESTIMATE => $newEstimate
        ];
        $payload = [
            self::PAYLOAD_COMMENT => $comment,
            self::PAYLOAD_TIME_SPENT => $timeSpent,
            self::PAYLOAD_STARTED => $started
        ];

        return $this->apiClient->callEndpoint(
            __e('%1/%2/%3/%4', self::ENDPOINT, $issue, self::EP_WORKLOG, $worklogId),
            $parameters,
            Serializer::encode($payload),
            HttpMethod::PUT
        );
    }

    /**
     * Delete worklog for an issue [STATUS 204]
     *
     * @param string      $issue          JIRA ticket number
     * @param string      $worklogId      Worklog ID
     *
     * @param string|null $adjustEstimate [optional] Allows you to provide specific instructions to update the
     *                                    remaining time estimate of the issue. Valid values are
     *                                    "new" - sets the estimate to a specific value
     *                                    "leave"- leaves the estimate as is
     *                                    "manual" - specify a specific amount to increase remaining estimate by
     *                                    "auto"- Default option. Will automatically adjust the value based on the new
     *                                    timeSpent specified on the worklog
     * @param string|null $newEstimate    (required when "new" is selected for adjustEstimate) the new value for the
     *                                    remaining estimate field. e.g. "2d"
     * @param string|null $increaseBy     (required when "manual" is selected for adjustEstimate) the amount to
     *                                    increase the remaining estimate by e.g. "2d"
     *
     * @return array
     */
    public function deleteWorklog($issue, $worklogId, $adjustEstimate = null, $newEstimate = null, $increaseBy = null)
    {
        $parameters = [
            self::PARAM_ADJUST_ESTIMATE => $adjustEstimate,
            self::PARAM_NEW_ESTIMATE => $newEstimate,
            self::PARAM_INCREASE_BY => $increaseBy,
        ];

        return $this->apiClient->callEndpoint(
            __e('%1/%2/%3/%4', self::ENDPOINT, $issue, self::EP_WORKLOG, $worklogId),
            $parameters,
            null,
            HttpMethod::DELETE
        );
    }

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
