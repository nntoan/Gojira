<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Command\Jira\Worklog;

use Gojira\Api\Exception\ApiException;
use Gojira\Api\Exception\HttpNotFoundException;
use Gojira\Api\Exception\UnauthorizedException;
use Gojira\Api\Request\StatusCodes;
use Gojira\Api\Response\ResponseInterface;
use Gojira\Command\Jira\AbstractCommand;
use Gojira\Jira\Endpoint\IssueEndpoint;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Delete worklog from an issue (worklog:delete)
 *
 * @package Gojira\Command\Jira\Worklog
 * @author  Toan Nguyen <me@nntoan.com>
 */
class DeleteCommand extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $help = __(
            "Worklog Delete Help:\n%1\n%2",
            ' <issue>: JIRA issue of worklog being update',
            ' <worklogId>: Worklog ID to delete'
        );

        $this
            ->setName('worklog:delete')
            ->setDescription('Delete worklog for an issue')
            ->setAliases(['wlog:d', 'worklogdel'])
            ->setHelp($help)
            ->addArgument(IssueEndpoint::ENDPOINT, InputArgument::REQUIRED, 'JIRA issue of worklog')
            ->addArgument(ResponseInterface::ID, InputArgument::REQUIRED, 'Worklog ID need to delete');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->authentication->isAuth()) {
            $issue = $input->getArgument(IssueEndpoint::ENDPOINT);
            $worklogId = $input->getArgument(ResponseInterface::ID);

            try {
                $this->getResponse([
                    IssueEndpoint::ENDPOINT => $issue,
                    ResponseInterface::ID   => $worklogId
                ]);
                if ($this->getApiClient()->getResultHttpCode() === StatusCodes::HTTP_NO_CONTENT) {
                    $output->writeln(__('<info>Worklog [%1] of issue [%2] was delete!</info>', $worklogId, $issue));
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

        return $issueEndpoint->deleteWorklog(
            $filters[IssueEndpoint::ENDPOINT],
            $filters[ResponseInterface::ID]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function renderResult($response = [], $type = null)
    {
        return null;
    }
}
