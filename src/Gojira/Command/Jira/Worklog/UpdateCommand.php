<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Command\Jira\Worklog;

use Gojira\Api\Configuration\OptionsInterface;
use Gojira\Api\Data\TableInterface;
use Gojira\Api\Exception\ApiException;
use Gojira\Api\Exception\HttpNotFoundException;
use Gojira\Api\Exception\UnauthorizedException;
use Gojira\Api\Request\StatusCodes;
use Gojira\Api\Response\ResponseInterface;
use Gojira\Command\Jira\AbstractCommand;
use Gojira\Jira\Endpoint\IssueEndpoint;
use Gojira\Jira\Response\IssueResponse;
use Gojira\Provider\Console\Table;
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
            ->addArgument(IssueEndpoint::PAYLOAD_TIME_SPENT, InputArgument::REQUIRED, 'How much time spent')
            ->addArgument(IssueEndpoint::PAYLOAD_COMMENT, InputArgument::OPTIONAL, 'What did you do')
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
            $timeSpent = $input->getArgument(IssueEndpoint::PAYLOAD_TIME_SPENT);
            $comment = $input->getArgument(IssueEndpoint::PAYLOAD_COMMENT);
            $startedAt = $input->getOption(self::OPT_STARTED_AT) ?: 'now';

            $dt = new \DateTime($startedAt, new \DateTimeZone($this->getOptionItem(OptionsInterface::TIMEZONE)));
            $started = $dt->format('Y-m-d\TH:i:s.000') . $dt->format('O');

            try {
                $response = $this->getResponse([
                    IssueEndpoint::ENDPOINT           => $issue,
                    ResponseInterface::ID             => $worklogId,
                    IssueEndpoint::PAYLOAD_COMMENT    => $comment,
                    IssueEndpoint::PAYLOAD_TIME_SPENT => $timeSpent,
                    IssueEndpoint::PAYLOAD_STARTED    => $started
                ]);
                $rows = $this->renderResult($response, $this->getName());
                if ($this->getApiClient()->getResultHttpCode() === StatusCodes::HTTP_OK) {
                    $this->renderTable($output, [
                        TableInterface::HEADERS => ['ID', 'Date', 'Author', 'Time Spent', 'Comment'],
                        TableInterface::ROWS    => Table::buildRows($rows)
                    ]);
                    $output->writeln(__('<info>Worklog to issue [%1] was updated!</info>', $issue));
                }
            } catch (ApiException $e) {
                if ($e instanceof HttpNotFoundException || $e instanceof UnauthorizedException) {
                    $output->writeln(__(
                        '<error>%1</error>',
                        StatusCodes::getMessageForCode($this->getApiClient()->getResultHttpCode())
                    ));
                } else {
                    $output->writeln(__('<error>Something went wrong.</error>'));
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getResponse($filters = [])
    {
        $issueEndpoint = new IssueEndpoint($this->getApiClient());

        return $issueEndpoint->updateWorklog(
            $filters[IssueEndpoint::ENDPOINT],
            $filters[ResponseInterface::ID],
            $filters[IssueEndpoint::PAYLOAD_TIME_SPENT],
            $filters[IssueEndpoint::PAYLOAD_COMMENT],
            $filters[IssueEndpoint::PAYLOAD_STARTED]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function renderResult($response = [], $type = null)
    {
        return (new IssueResponse($response))->render($type);
    }
}
