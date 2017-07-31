<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Command\Jira\Issues;

use Gojira\Api\Data\TableInterface;
use Gojira\Api\Exception\ApiException;
use Gojira\Api\Exception\HttpNotFoundException;
use Gojira\Api\Exception\UnauthorizedException;
use Gojira\Api\Request\StatusCodes;
use Gojira\Command\Jira\AbstractCommand;
use Gojira\Jira\Endpoint\JqlEndpoint;
use Gojira\Jira\Response\JqlResponse;
use Gojira\Provider\Console\Table;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Show list issues command (issue:list)
 *
 * @package Gojira\Command\Jira
 * @author  Toan Nguyen <me@nntoan.com>
 */
class ListCommand extends AbstractCommand
{
    const OPT_TYPE = 'type';
    const OPT_PROJECT = 'project';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('issue:list')
            ->setAliases(['ls'])
            ->setDescription('Show list of issues')
            ->addOption(self::OPT_PROJECT, 'p', InputOption::VALUE_OPTIONAL, 'Filter by project')
            ->addOption(self::OPT_TYPE, 't', InputOption::VALUE_OPTIONAL, 'Filter by type');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->authentication->isAuth()) {
            $type = $input->getOption(self::OPT_TYPE);
            $project = $input->getOption(self::OPT_PROJECT);

            try {
                $response = $this->getResponse([self::OPT_TYPE => $type, self::OPT_PROJECT => $project]);
                $rows = $this->renderResult($response, $this->getName());
                if ($this->getApiClient()->getResultHttpCode() === StatusCodes::HTTP_OK) {
                    $this->renderTable($output, [
                        TableInterface::HEADERS => ['Key', 'Priority', 'Summary', 'Status'],
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
        $type = ($filters[self::OPT_TYPE]) ? '+AND+type="' . $filters[self::OPT_TYPE] . '"' : '';
        $project = ($filters[self::OPT_PROJECT]) ? '+AND+project="' . $filters[self::OPT_PROJECT] . '"' : '';
        $jqlQuery = __(
            '%1%2%3%4%5',
            'assignee=currentUser()',
            $type,
            $project,
            '+AND+status+in+("' . $this->getAvailableStatuses() . '")',
            '+order+by+priority+DESC,+key+ASC'
        );

        $searchEndpoint = new JqlEndpoint($this->getApiClient());
        return $searchEndpoint->search($jqlQuery);
    }

    /**
     * {@inheritdoc}
     */
    protected function renderResult($response = [], $type = null)
    {
        return (new JqlResponse($response))->render($type);
    }
}
