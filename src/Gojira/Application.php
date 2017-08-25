<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira;

use Gojira\Provider\ConsoleServiceProvider;
use Gojira\Provider\DispatcherServiceProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\EventListenerProviderInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

/**
 * The Gojira framework class.
 *
 * @author Toan Nguyen <me@nntoan.com>
 *
 * @api
 */
class Application extends Container
{
    const VERSION = '1.0.0';
    const CODENAME = 'Gorira';

    /**
     * @var ServiceProviderInterface[]
     */
    private $providers = [];

    /**
     * @var boolean
     */
    private $booted = false;

    /**
     * @var \ReflectionObject
     */
    private $reflected;

    /**
     * Registers the autoloader and necessary components.
     *
     * @param string      $name    Name for this application.
     * @param string|null $version Version number for this application.
     * @param array       $values
     */
    public function __construct($name, $version = null, array $values = [])
    {
        parent::__construct($values);

        $this->register(new DispatcherServiceProvider);
        $this->register(
            new ConsoleServiceProvider,
            [
                'console.name'    => $name,
                'console.version' => $version,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function register(ServiceProviderInterface $provider, array $values = [])
    {
        parent::register($provider, $values);

        $this->providers[] = $provider;
    }

    /**
     * Boots the Application by calling boot on every provider added and then subscribe
     * in order to add listeners.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->booted) {
            return;
        }

        $this->booted = true;

        foreach ($this->providers as $provider) {
            if ($provider instanceof EventListenerProviderInterface) {
                $provider->subscribe($this, $this['dispatcher']);
            }
        }
    }

    /**
     * Executes this application.
     *
     * @param InputInterface|null  $input
     * @param OutputInterface|null $output
     *
     * @return integer
     */
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        $this->boot();

        return $this['console']->run($input, $output);
    }

    /**
     * Allows you to add a command as Command object or as a command name+callable
     *
     * @param string|Command $nameOrCommand
     * @param callable|null  $callable Must be a callable if $nameOrCommand is the command's name
     *
     * @return Command The command instance that you can further configure
     * @api
     */
    public function command($nameOrCommand, $callable = null)
    {
        if ($nameOrCommand instanceof Command) {
            $command = $nameOrCommand;
        } else {
            if (!is_callable($callable)) {
                throw new \InvalidArgumentException('$callable must be a valid callable with the command\'s code');
            }

            $command = new Command($nameOrCommand);
            $command->setCode($callable);
        }

        $this['console']->add($command);

        return $command;
    }

    /**
     * Allows you to add array of commands
     *
     * @param array $namesOrCommands
     *
     * @return void
     * @api
     */
    public function commands($namesOrCommands)
    {
        $commands = [];
        foreach ($namesOrCommands as $nameOrCommand) {
            if ($nameOrCommand instanceof Command) {
                $commands[] = $nameOrCommand;
            } else {
                $commands[] = new Command($nameOrCommand);
            }
        }

        $this['console']->addCommands($commands);
    }

    /**
     * Allows you to set a command as Command object or as a command name+callable to default command
     *
     * @param string|Command $nameOrCommand
     *
     * @return void
     * @api
     */
    public function defaultCmd($nameOrCommand)
    {
        if ($nameOrCommand instanceof Command) {
            $command = $nameOrCommand;
        } else {
            $command = new Command($nameOrCommand);
        }

        $name = $command->getName();
        $this['console']->setDefaultCommand($name);
    }

    /**
     * Allows you to get a command as Command object or as a command name
     *
     * @param string $name The command name or alias
     *
     * @return Command The command instance that you can further configure
     * @api
     */
    public function get($name)
    {
        $command = $this['console']->get($name);

        return $command;
    }

    /**
     * Finds and registers Commands.
     *
     * Override this method if your bundle commands do not follow the conventions:
     *
     * * Commands are in the 'Command' sub-directory
     * * Commands extend Symfony\Component\Console\Command\Command
     *
     * @api
     */
    public function registerCommands()
    {
        if (!is_dir($dir = $this->getPath() . '/Command')) {
            return;
        }

        $finder = new Finder();
        $finder->files()->name('*Command.php')->in($dir);

        $prefix = $this->getNamespace() . '\\Command';
        foreach ($finder as $file) {
            $ns = $prefix;
            if ($relativePath = $file->getRelativePath()) {
                $ns .= '\\' . strtr($relativePath, '/', '\\');
            }
            if ($file->getBasename('.php') !== 'SelfUpdateCommand') {
                $r = new \ReflectionClass($ns . '\\' . $file->getBasename('.php'));
                if ($r->isSubclassOf('Symfony\\Component\\Console\\Command\\Command') && !$r->isAbstract()
                    && !$r->getConstructor()->getNumberOfRequiredParameters()) {
                    $this['console']->add($r->newInstance());
                }
            }
        }
    }

    /**
     * Gets the Bundle namespace.
     *
     * @return string The Bundle namespace
     *
     * @api
     */
    public function getNamespace()
    {
        if (null === $this->reflected) {
            $this->reflected = new \ReflectionObject($this);
        }

        return $this->reflected->getNamespaceName();
    }

    /**
     * Gets the Bundle directory path.
     *
     * @return string The Bundle absolute path
     *
     * @api
     */
    public function getPath()
    {
        if (null === $this->reflected) {
            $this->reflected = new \ReflectionObject($this);
        }

        return dirname($this->reflected->getFileName());
    }
}
