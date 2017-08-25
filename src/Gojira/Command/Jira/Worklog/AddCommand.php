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
use Gojira\Command\Jira\AbstractCommand;
use Gojira\Jira\Endpoint\IssueEndpoint;
use Gojira\Jira\Endpoint\WorklogEndpoint;
use Gojira\Jira\Response\WorklogResponse;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Add worklog for an issue (worklog:add)
 *
 * @package Gojira\Command\Jira\Worklog
 * @author  Toan Nguyen <me@nntoan.com>
 */
class AddCommand extends AbstractCommand
{
    const OPT_STARTED_AT = 'startedAt';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $help = __(
            "Worklog Add Help:\n%1\n%2\n%3",
            ' <issue>: JIRA issue to log work for',
            ' <timeSpent>: How much time spent (e.g. \'3h 30m\')',
            ' <comment> (optional) Describe what did you do'
        );

        $this
            ->setName('worklog:add')
            ->setDescription('Log work for an issue')
            ->setAliases(['wlog:a', 'worklogadd'])
            ->setHelp($help)
            ->addArgument(IssueEndpoint::ENDPOINT, InputArgument::REQUIRED, 'JIRA issue to log work for')
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
            $timeSpent = $input->getArgument(WorklogEndpoint::PAYLOAD_TIME_SPENT);
            $comment = $input->getArgument(WorklogEndpoint::PAYLOAD_COMMENT) ?: 'Comment is too precious to be added.';
            $startedAt = $input->getOption(self::OPT_STARTED_AT) ?: 'now';

            $dateTime = new \DateTime($startedAt, new \DateTimeZone($this->getOptionItem(OptionsInterface::TIMEZONE)));
            $started = $dateTime->format('Y-m-d\TH:i:s.000') . $dateTime->format('O');

            $this->doExecute(
                $output,
                StatusCodes::HTTP_CREATED,
                [
                    IssueEndpoint::ENDPOINT => $issue,
                    WorklogEndpoint::PAYLOAD_COMMENT => $comment,
                    WorklogEndpoint::PAYLOAD_TIME_SPENT => $timeSpent,
                    WorklogEndpoint::PAYLOAD_STARTED => $started
                ],
                ['ID', 'Date', 'Author', 'Time Spent', 'Comment'],
                __('<info>Worklog to issue [%1] was added!</info>', $issue)
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getResponse($filters = [])
    {
        $worklogEndpoint = new WorklogEndpoint($this->getApiClient());
        return $worklogEndpoint->addWorklog(
            $filters[IssueEndpoint::ENDPOINT],
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
