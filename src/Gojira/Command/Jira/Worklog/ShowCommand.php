<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Command\Jira\Worklog;

use Gojira\Api\Data\TableInterface;
use Gojira\Api\Exception\ApiException;
use Gojira\Api\Exception\HttpNotFoundException;
use Gojira\Api\Exception\UnauthorizedException;
use Gojira\Api\Request\StatusCodes;
use Gojira\Command\Jira\AbstractCommand;
use Gojira\Jira\Endpoint\IssueEndpoint;
use Gojira\Jira\Response\IssueResponse;
use Gojira\Provider\Console\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Show worklog about an issue (worklog:show)
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

            try {
                $response = $this->getResponse([IssueEndpoint::ENDPOINT => $issue]);
                $rows = $this->renderResult($response, $this->getName());
                if ($this->getApiClient()->getResultHttpCode() === StatusCodes::HTTP_OK) {
                    $this->renderTable($output, [
                        TableInterface::HEADERS => ['ID', 'Date', 'Author', 'Time Spent', 'Comment'],
                        TableInterface::ROWS => Table::buildRows($rows)
                    ]);
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

        return $issueEndpoint->listWorklog($filters[IssueEndpoint::ENDPOINT]);
    }

    /**
     * {@inheritdoc}
     */
    protected function renderResult($response = [], $type = null)
    {
        return (new IssueResponse($response))->render($type);
    }
}
