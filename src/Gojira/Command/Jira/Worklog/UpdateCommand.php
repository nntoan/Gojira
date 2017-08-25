<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Command\Jira\Worklog;

use Gojira\Framework\App\Configuration\OptionsInterface;
use Gojira\Api\Request\StatusCodes;
use Gojira\Api\Response\ResponseInterface;
use Gojira\Command\Jira\AbstractCommand;
use Gojira\Jira\Endpoint\IssueEndpoint;
use Gojira\Jira\Endpoint\WorklogEndpoint;
use Gojira\Jira\Response\WorklogResponse;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Update worklog for an issue (worklog:update)
 *
 * @package Gojira\Command\Jira\Worklog
 * @author  Toan Nguyen <me@nntoan.com>
 */
class UpdateCommand extends AbstractCommand
{
    const OPT_STARTED_AT = 'startedAt';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $help = __(
            "Worklog Update Help:\n%1\n%2\n%3\n%4",
            ' <issue>: JIRA issue of worklog being update',
            ' <worklogId>: Worklog ID to update',
            ' <timeSpent>: How much time spent (e.g. \'3h 30m\')',
            ' <comment> (optional) Describe what did you do'
        );

        $this
            ->setName('worklog:update')
            ->setDescription('Update worklog for an issue')
            ->setAliases(['wlog:u', 'worklogupdate'])
            ->setHelp($help)
            ->addArgument(IssueEndpoint::ENDPOINT, InputArgument::REQUIRED, 'JIRA issue of worklog')
            ->addArgument(ResponseInterface::ID, InputArgument::REQUIRED, 'Worklog ID need to change')
            ->addArgument(WorklogEndpoint::PAYLOAD_TIME_SPENT, InputArgument::REQUIRED, 'How much time spent')
            ->addArgument(WorklogEndpoint::PAYLOAD_COMMENT, InputArgument::OPTIONAL, 'What did you do')
            ->addOption(self::OPT_STARTED_AT, 's', InputOption::VALUE_OPTIONAL, 'Set date of work (default is now)');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->authentication->isAuth()) {
            $issue = $input->getArgument(IssueEndpoint::ENDPOINT);
            $worklogId = $input->getArgument(ResponseInterface::ID);
            $timeSpent = $input->getArgument(WorklogEndpoint::PAYLOAD_TIME_SPENT);
            $comment = $input->getArgument(WorklogEndpoint::PAYLOAD_COMMENT);
            $startedAt = $input->getOption(self::OPT_STARTED_AT) ?: 'now';

            $dateTime = new \DateTime($startedAt, new \DateTimeZone($this->getOptionItem(OptionsInterface::TIMEZONE)));
            $started = $dateTime->format('Y-m-d\TH:i:s.000') . $dateTime->format('O');

            $this->doExecute(
                $output,
                StatusCodes::HTTP_OK,
                [
                    IssueEndpoint::ENDPOINT => $issue,
                    ResponseInterface::ID => $worklogId,
                    WorklogEndpoint::PAYLOAD_COMMENT => $comment,
                    WorklogEndpoint::PAYLOAD_TIME_SPENT => $timeSpent,
                    WorklogEndpoint::PAYLOAD_STARTED => $started
                ],
                ['ID', 'Date', 'Author', 'Time Spent', 'Comment'],
                __('<info>Worklog [%1] of issue [%2] was updated!</info>', $worklogId, $issue)
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getResponse($filters = [])
    {
        $worklogEndpoint = new WorklogEndpoint($this->getApiClient());

        return $worklogEndpoint->updateWorklog(
            $filters[IssueEndpoint::ENDPOINT],
            $filters[ResponseInterface::ID],
            $filters[WorklogEndpoint::PAYLOAD_TIME_SPENT],
            $filters[WorklogEndpoint::PAYLOAD_COMMENT],
            $filters[WorklogEndpoint::PAYLOAD_STARTED]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function renderResult($response = [], $type = null)
    {
        return (new WorklogResponse($response))->render($type);
    }
}
