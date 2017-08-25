<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Jira\Response;

use Gojira\Api\Response\BaseResponse;
use Gojira\Api\Response\ResponseInterface;
use Symfony\Component\Console\Helper\TableCell;

/**
 * Render result for JIRA REST /transitions
 *
 * @package Gojira\Jira\Response
 * @author  Toan Nguyen <me@nntoan.com>
 */
class TransitionResponse extends IssueResponse implements ResponseInterface
{
    /**
     * {@inheritdoc}
     */
    public function render($type = null)
    {
        switch ($type) {
            case 'issue:transit:get':
                $result = $this->renderListTransitions();
                break;
            case 'issue:transit:start':
            case 'issue:transit:stop':
            case 'issue:transit:review':
            case 'issue:transit:done':
                $result = $this->renderDoTransition();
                break;
            default:
                $result = $this->renderNothing();
                break;
        }

        return $result;
    }

    /**
     * Returns list of worklogs in a JIRA ticket
     * - Command: worklog:show (['worklog', 'wlog:s'])
     *
     * @return array
     */
    protected function renderListTransitions()
    {
        $rows = [];

        if ($this->response[self::TOTAL] === 0) {
            $rows[] = [new TableCell('No work yet logged.', ['colspan' => 5])];
        }

        $transitions = $this->response[self::TRANSITIONS];
        $totalTransitions = count($transitions);
        for ($counter = 0; $counter < $totalTransitions; $counter++) {
            $transitionId = $transitions[$counter][self::ID];
            $transitionName = $transitions[$counter][self::NAME];
            $author = $transitions[$counter][self::AUTHOR][self::DISPLAY_NAME];
            $timeSpent = $transitions[$counter][self::TIME_SPENT];
            $comment = $transitions[$counter][self::COMMENT];

            if (strlen($comment) > 50) {
                $comment = substr($comment, 0, 47) . '...';
            }

            $rows[] = [
                $transitionId,
                $transitionName,
                $author,
                $timeSpent,
                $comment
            ];
        }

        return $rows;
    }

    /**
     * Returns worklog data after added/updated in a JIRA ticket
     * - Command: worklog:add (['worklogadd', 'wlog:a'])
     * - Command: worklog:update (['worklogupdate', 'wlog:u'])
     *
     * @return array
     */
    protected function renderDoTransition()
    {
        $rows = [];
        $worklogId = $this->response[self::ID];
        $startDate = $this->response[self::CREATED];
        $author = $this->response[self::AUTHOR][self::DISPLAY_NAME];
        $timeSpent = $this->response[self::TIME_SPENT];
        $comment = $this->response[self::COMMENT];

        if (strlen($comment) > 50) {
            $comment = substr($comment, 0, 47) . '...';
        }

        $rows[] = [
            $worklogId,
            $startDate,
            $author,
            $timeSpent,
            $comment
        ];

        return $rows;
    }
}
