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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Show list issues command (issue:list:in-progress)
 *
 * @package Gojira\Command\Jira
 * @author  Toan Nguyen <me@nntoan.com>
 */
class ListInProgressCommand extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('issue:list:in-progress')
            ->setAliases(['running'])
            ->setDescription('Show list of issues in progress');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->authentication->isAuth()) {
            $this->doExecute(
                $output,
                StatusCodes::HTTP_OK,
                [],
                ['Key', 'Priority', 'Summary', 'Status']
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getResponse($filters = [])
    {
        $jqlQuery = __(
            '%1%2%3',
            'assignee=currentUser()',
            '+AND+status+in+("In Progress")',
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
