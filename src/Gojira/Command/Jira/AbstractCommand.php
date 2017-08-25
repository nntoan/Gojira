<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Command\Jira;

use Gojira\Api\Client\GuzzleClient;
use Gojira\Framework\App\Configuration\AuthInterface;
use Gojira\Framework\App\Configuration\ConfigurationInterface;
use Gojira\Framework\App\Configuration\OptionsInterface;
use Gojira\Framework\App\Console\TableInterface;
use Gojira\Api\Exception\ApiException;
use Gojira\Api\Exception\HttpNotFoundException;
use Gojira\Api\Exception\UnauthorizedException;
use Gojira\Api\Request\StatusCodes;
use Gojira\Provider\Console\Command;
use Gojira\Provider\Console\Table;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Base class for all JIRA commands.
 *
 * @package Gojira\Command\Jira
 * @author  Toan Nguyen <me@nntoan.com>
 */
abstract class AbstractCommand extends Command
{
    /**
     * @var \Gojira\Api\Client\GuzzleClient
     */
    protected $apiClient = null;

    /**
     * @var \Gojira\Provider\Console\Table
     */
    protected $table = null;

    /**
     * @var array
     */
    protected $optionItems = null;

    /**
     * Get response data w/ specified endpoint instance
     *
     * @param array $filters Filters passing to endpoint
     *
     * @return mixed
     */
    abstract protected function getResponse($filters = []);

    /**
     * Render response data for outputting
     *
     * @param array       $response REST API response after deserialized
     * @param string|null $type     Result type to render
     *
     * @return mixed
     */
    abstract protected function renderResult($response = [], $type = null);

    /**
     * Abstract method for all execute() commands
     *
     * @param OutputInterface $output             Symfony console output
     * @param int             $acceptedStatusCode HTTP status code to continue
     * @param array           $filters            Payload/Params passing to endpoint
     * @param array           $tableHeaders       Console table header
     * @param string          $infoMessage        Info message after table rendered successfully
     *
     * @return void
     */
    protected function doExecute(
        OutputInterface $output,
        $acceptedStatusCode = StatusCodes::HTTP_OK,
        array $filters = [],
        array $tableHeaders = [],
        $infoMessage = ''
    ) {
        try {
            $response = $this->getResponse($filters);
            $rows = $this->renderResult($response, $this->getName());

            if ($this->getApiClient()->getResultHttpCode() === $acceptedStatusCode) {
                if (!empty($tableHeaders)) {
                    $this->renderTable($output, [
                        TableInterface::HEADERS => $tableHeaders,
                        TableInterface::ROWS => Table::buildRows($rows)
                    ]);
                }

                if (!empty($infoMessage)) {
                    $output->writeln($infoMessage);
                }
            }
        } catch (ApiException $e) {
            $message = 'Something went wrong.';
            if ($e instanceof HttpNotFoundException || $e instanceof UnauthorizedException) {
                $message = StatusCodes::getMessageForCode($this->getApiClient()->getResultHttpCode());
            }

            $output->writeln(__('<error>%1</error>', $message));
        }
    }

    /**
     * Render table faster
     *
     * @param OutputInterface $output
     * @param array           $data
     *
     * @return void
     */
    protected function renderTable(OutputInterface $output, array $data)
    {
        $this->table = new Table($output, $data);
        $this->table->render();
    }

    /**
     * Get Base URI
     *
     * @return string
     */
    protected function getBaseUri()
    {
        return $this->configuration->getData(ConfigurationInterface::AUTH . '/' . AuthInterface::BASE_URI);
    }

    /**
     * Returns ApiClient object instance
     *
     * @param bool $debug Debug current request
     *
     * @return GuzzleClient
     */
    protected function getApiClient($debug = false)
    {
        if ($this->apiClient === null) {
            $this->apiClient = new GuzzleClient(
                $this->getBaseUri(),
                $this->authentication,
                (bool)$debug,
                (bool)$this->optionItems[OptionsInterface::IS_USE_CACHE]
            );
        }

        return $this->apiClient;
    }

    /**
     * Get available issue statues
     *
     * @return string
     */
    protected function getAvailableStatuses()
    {
        $configItems = $this->configuration->getData(ConfigurationInterface::OPTIONS);

        return implode('", "', $configItems[OptionsInterface::AVAILABLE_ISSUES]);
    }

    /**
     * Get option item
     *
     * @param string $key
     *
     * @return array|mixed|null
     */
    protected function getOptionItem($key = null)
    {
        $optionItems = $this->configuration->getData(ConfigurationInterface::OPTIONS);
        if ($key === null) {
            return $optionItems;
        }

        return $optionItems[$key];
    }

    /**
     * Get application container throught Pimple
     *
     * @return mixed|null
     */
    protected function getApp()
    {
        return $this->getApplication()->getService('console');
    }
}
