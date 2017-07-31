<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Gojira\Provider\Console\Command;

/**
 * Version Command, return the current version of GoJira
 *
 * @package Gojira\Command
 * @author  Toan Nguyen <me@nntoan.com>
 */
class VersionCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('version')
            ->setHidden(true)
            ->setDescription('Returns the current version of GoJira');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // This is a contrived example to show accessing services
        // from the container without needing the command itself
        // to extend from anything but Symfony Console's base Command.

        $app = $this->getApplication()->getService('console');

        $output->writeln(__(
            '<info>GoJira</info> <comment>v%1</comment> [<comment>%2</comment>]',
            $app->getVersion(),
            $app->getName()
        ));
    }
}
