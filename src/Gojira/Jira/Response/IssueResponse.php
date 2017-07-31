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
 * Render result for JIRA REST /issue
 *
 * @package Gojira\Jira\Response
 * @author  Toan Nguyen <me@nntoan.com>
 */
class IssueResponse extends BaseResponse implements ResponseInterface
{
    /**
     * {@inheritdoc}
     */
    public function render($type = null)
    {
        $result = [];
        switch ($type) {
            case 'worklog:show':
                $result = $this->renderListWorklogs();
                break;
            case 'worklog:add':
            case 'worklog:update':
                $result = $this->renderAddUpdateWorklog();
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
    private function renderListWorklogs()
    {
        $rows = [];

        if ($this->response[self::TOTAL] === 0) {
            $rows[] = [new TableCell('No work yet logged.', ['colspan' => 5])];
        }

        $worklogs = $this->response[self::WORKLOGS];
        $totalWorklogs = count($worklogs);
        for ($counter = 0; $counter < $totalWorklogs; $counter++) {
            $worklogId = $worklogs[$counter][self::ID];
            $startDate = $worklogs[$counter][self::CREATED];
            $author = $worklogs[$counter][self::AUTHOR][self::DISPLAY_NAME];
            $timeSpent = $worklogs[$counter][self::TIME_SPENT];
            $comment = $worklogs[$counter][self::COMMENT];

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
    private function renderAddUpdateWorklog()
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
