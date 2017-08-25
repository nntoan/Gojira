<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Command\Jira\Issues;

use Gojira\Api\Request\StatusCodes;
use Gojira\Command\Jira\AbstractCommand;
use Gojira\Jira\Endpoint\JqlEndpoint;
use Gojira\Jira\Response\JqlResponse;
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

            $this->doExecute(
                $output,
                StatusCodes::HTTP_OK,
                [self::OPT_TYPE => $type, self::OPT_PROJECT => $project],
                ['Key', 'Priority', 'Summary', 'Status']
            );
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
