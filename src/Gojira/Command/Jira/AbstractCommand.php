<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Command\Jira;

use Gojira\Api\Authentication\JiraBasicAuthentication;
use Gojira\Api\Client\GuzzleClient;
use Gojira\Api\Configuration\AuthInterface;
use Gojira\Api\Configuration\Configuration;
use Gojira\Api\Configuration\ConfigurationInterface;
use Gojira\Api\Configuration\Options;
use Gojira\Api\Configuration\OptionsInterface;
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
     * @var \Gojira\Api\Configuration\Configuration
     */
    protected $configuration = null;

    /**
     * @var \Gojira\Api\Authentication\JiraBasicAuthentication
     */
    protected $authentication = null;

    /**
     * @var \Gojira\Provider\Console\Table
     */
    private $table = null;

    /**
     * @var array
     */
    protected $optionItems = null;

    /**
     * AbstractCommand constructor.
     *
     * @param null $name
     */
    public function __construct($name = null)
    {
        $this->configuration = new Configuration();
        $this->authentication = new JiraBasicAuthentication($this->configuration);
        parent::__construct($name);
    }

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
