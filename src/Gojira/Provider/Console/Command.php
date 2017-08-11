<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Provider\Console;

use Gojira\Api\Authentication\JiraBasicAuthentication;
use Gojira\Api\Configuration\Auth;
use Gojira\Api\Configuration\Configuration;
use Gojira\Api\Configuration\Options;
use Gojira\Api\Configuration\Path;
use Gojira\Framework\Math\Random;
use Gojira\Framework\ObjectManager\ObjectManager;
use Symfony\Component\Console\Command\Command as BaseCommand;

/**
 * Base class for Gojira commands.
 *
 * @method ContainerAwareApplication getApplication() Gets the application instance for this command.
 *
 * @author Toan Nguyen <me@nntoan.com>
 *
 * @api
 */
abstract class Command extends BaseCommand
{
    /**
     * @var \Gojira\Api\Configuration\Configuration
     */
    protected $configuration = null;

    /**
     * @var \Gojira\Api\Authentication\JiraBasicAuthentication
     */
    protected $authentication = null;

    /**
     * @var \Gojira\Api\Configuration\Path
     */
    protected $pathConfig = null;

    /**
     * @var \Gojira\Api\Configuration\Auth
     */
    protected $authConfig = null;

    /**
     * @var \Gojira\Api\Configuration\Options
     */
    protected $optionConfig = null;

    /**
     * @var \Gojira\Provider\Console\Prompt
     */
    protected $prompt = null;

    /**
     * @var \Gojira\Framework\Math\Random
     */
    protected $random = null;

    /**
     * AbstractCommand constructor.
     *
     * @param null $name
     */
    public function __construct($name = null)
    {
        $this->__initialise();
        parent::__construct($name);
    }

    /**
     * Returns the application container.
     *
     * @return \Gojira\Application
     */
    public function getContainer()
    {
        return $this->getApplication()->getContainer();
    }

    /**
     * Returns a service contained in the application container or null if none
     * is found with that name.
     *
     * This is a convenience method used to retrieve an element from the
     * Application container without having to assign the results of the
     * getContainer() method in every call.
     *
     * @param string $name Name of the service
     *
     * @see self::getContainer()
     *
     * @api
     *
     * @return \stdClass|null
     */
    public function getService($name)
    {
        return $this->getApplication()->getService($name);
    }

    /**
     * Initialise all object instances
     *
     * @return void
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    private function __initialise()
    {
        $this->configuration = ObjectManager::create(Configuration::class);
        $this->authentication = ObjectManager::create(JiraBasicAuthentication::class, [$this->configuration]);
        $this->authConfig = ObjectManager::create(Auth::class);
        $this->optionConfig = ObjectManager::create(Options::class);
        $this->pathConfig = ObjectManager::create(Path::class);
        $this->prompt = ObjectManager::create(Prompt::class);
        $this->random = ObjectManager::create(Random::class);
    }
}
