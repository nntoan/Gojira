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
 * Render result for Jira JQL search
 *
 * @package Gojira\Jira\Response
 * @author  Toan Nguyen <me@nntoan.com>
 */
class JqlResponse extends BaseResponse implements ResponseInterface
{
    /**
     * {@inheritdoc}
     */
    public function render($type = null)
    {
        switch ($type) {
            case 'issue:list':
            case 'issue:list:in-progress':
                $result = $this->renderIssueList();
                break;
            default:
                $result = $this->renderNothing();
                break;
        }

        return $result;
    }

    /**
     * Render list of all JIRA issues for current user
     * - Command: issues:list
     *
     * @return array
     */
    private function renderIssueList()
    {
        $rows = [];
        $issues = $this->response[self::ISSUES];
        $totalIssues = count($issues);

        if ($totalIssues === 0) {
            $rows[] = [
                new TableCell('There are no available issues. Enjoys your day!', ['colspan' => 4])
            ];
        }

        for ($counter = 0; $counter < $totalIssues; $counter++) {
            $key = $issues[$counter][self::KEY];
            $priority = $issues[$counter][self::FIELDS][self::PRIORITY];
            $summary = $issues[$counter][self::FIELDS][self::SUMMARY];
            $status = $issues[$counter][self::FIELDS][self::STATUS];

            if (empty($priority)) {
                $priority = [
                    self::NAME => ''
                ];
            }

            if (strlen($summary) > 50) {
                $summary = substr($summary, 0, 47) . '...';
            }

            switch ($priority[self::NAME]) {
                case self::PRIORITY_MINOR:
                    $priority[self::NAME] = '<fg=green>' . $priority[self::NAME] . '</>';
                    break;
                case self::PRIORITY_MAJOR:
                case self::PRIORITY_CRITICAL:
                case self::PRIORITY_BLOCKER:
                    $priority[self::NAME] = '<fg=red>' . $priority[self::NAME] . '</>';
                    break;
                default:
                    break;
            }

            $rows[] = [
                '<fg=yellow>' . $key . '</>',
                $priority[self::NAME],
                $summary,
                $status[self::NAME]
            ];
        }

        return $rows;
    }
}
