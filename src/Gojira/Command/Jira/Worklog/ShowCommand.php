<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Command\Jira\Worklog;

use Gojira\Api\Request\StatusCodes;
use Gojira\Command\Jira\AbstractCommand;
use Gojira\Jira\Endpoint\IssueEndpoint;
use Gojira\Jira\Endpoint\WorklogEndpoint;
use Gojira\Jira\Response\WorklogResponse;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Show all worklogs of an issue (worklog:show)
 *
 * @package Gojira\Command\Jira\Worklog
 * @author  Toan Nguyen <me@nntoan.com>
 */
class ShowCommand extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('worklog:show')
            ->setAliases(['wlog:s', 'worklogshow'])
            ->setDescription('Show worklog about an issue')
            ->addArgument(IssueEndpoint::ENDPOINT, InputArgument::REQUIRED, 'JIRA issue to show worklog');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->authentication->isAuth()) {
            $issue = $input->getArgument(IssueEndpoint::ENDPOINT);

            $this->doExecute(
                $output,
                StatusCodes::HTTP_OK,
                [IssueEndpoint::ENDPOINT => $issue],
                ['ID', 'Date', 'Author', 'Time Spent', 'Comment']
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getResponse($filters = [])
    {
        $worklogEndpoint = new WorklogEndpoint($this->getApiClient());

        return $worklogEndpoint->listWorklog($filters[IssueEndpoint::ENDPOINT]);
    }

    /**
     * {@inheritdoc}
     */
    protected function renderResult($response = [], $type = null)
    {
        return (new WorklogResponse($response))->render($type);
    }
}
