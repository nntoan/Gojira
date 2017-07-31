<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Humbug\SelfUpdate\Updater;
use Humbug\SelfUpdate\VersionParser;
use Humbug\SelfUpdate\Strategy\ShaStrategy;
use Humbug\SelfUpdate\Strategy\GithubStrategy;
use Gojira\Provider\Console\Command;

/**
 * Self Update command, allow user self-update the application
 *
 * @package Gojira\Command
 * @author  Toan Nguyen <me@nntoan.com>
 */
class SelfUpdateCommand extends Command
{
    const VERSION_URL = 'https://gojira.nntoan.com/downloads/gojira.version';
    const PHAR_URL = 'https://gojira.nntoan.com/downloads/gojira.phar';
    const PACKAGE_NAME = 'nntoan/gojira';
    const FILE_NAME = 'gojira.phar';

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var string
     */
    protected $version;

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->version = $this->getApplication()->getService('console')->getVersion();
        $parser = new VersionParser;

        /**
         * Check for ancilliary options
         */
        if ($input->getOption('rollback')) {
            $this->rollback();
            return;
        }

        if ($input->getOption('check')) {
            $this->printAvailableUpdates();
            return;
        }

        /**
         * Update to any specified stability option
         */
        if ($input->getOption('dev')) {
            $this->updateToDevelopmentBuild();
            return;
        }

        if ($input->getOption('pre')) {
            $this->updateToPreReleaseBuild();
            return;
        }

        if ($input->getOption('stable')) {
            $this->updateToStableBuild();
            return;
        }

        if ($input->getOption('non-dev')) {
            $this->updateToMostRecentNonDevRemote();
            return;
        }

        /**
         * If current build is stable, only update to more recent stable
         * versions if available. User may specify otherwise using options.
         */
        if ($parser->isStable($this->version)) {
            $this->updateToStableBuild();
            return;
        }

        /**
         * By default, update to most recent remote version regardless
         * of stability.
         */
        $this->updateToMostRecentNonDevRemote();
    }

    /**
     * Get stable updater
     *
     * @return Updater
     */
    protected function getStableUpdater()
    {
        $updater = new Updater;
        $updater->setStrategy(Updater::STRATEGY_GITHUB);
        return $this->getGithubReleasesUpdater($updater);
    }

    /**
     * Get pre-released updater
     *
     * @return Updater
     */
    protected function getPreReleaseUpdater()
    {
        $updater = new Updater;
        $updater->setStrategy(Updater::STRATEGY_GITHUB);
        $updater->getStrategy()->setStability(GithubStrategy::UNSTABLE);
        return $this->getGithubReleasesUpdater($updater);
    }

    /**
     * Get most recent non-dev updater
     *
     * @return Updater
     */
    protected function getMostRecentNonDevUpdater()
    {
        $updater = new Updater;
        $updater->setStrategy(Updater::STRATEGY_GITHUB);
        $updater->getStrategy()->setStability(GithubStrategy::ANY);
        return $this->getGithubReleasesUpdater($updater);
    }

    /**
     * Get GitHub releases updater
     *
     * @param Updater $updater
     *
     * @return Updater
     */
    protected function getGithubReleasesUpdater(Updater $updater)
    {
        $updater->getStrategy()->setPackageName(self::PACKAGE_NAME);
        $updater->getStrategy()->setPharName(self::FILE_NAME);
        $updater->getStrategy()->setCurrentLocalVersion($this->version);
        return $updater;
    }

    /**
     * Get development updater
     *
     * @return Updater
     */
    protected function getDevelopmentUpdater()
    {
        $updater = new Updater;
        $updater->getStrategy()->setPharUrl(self::PHAR_URL);
        $updater->getStrategy()->setVersionUrl(self::VERSION_URL);
        return $updater;
    }

    /**
     * Update to stable build
     *
     * @return void
     */
    protected function updateToStableBuild()
    {
        $this->update($this->getStableUpdater());
    }

    /**
     * Update to pre-release build
     *
     * @return void
     */
    protected function updateToPreReleaseBuild()
    {
        $this->update($this->getPreReleaseUpdater());
    }

    /**
     * Update to most recent non-dev build
     *
     * @return void
     */
    protected function updateToMostRecentNonDevRemote()
    {
        $this->update($this->getMostRecentNonDevUpdater());
    }

    /**
     * Update to development build
     *
     * @return void
     */
    protected function updateToDevelopmentBuild()
    {
        $this->update($this->getDevelopmentUpdater());
    }

    /**
     * Update business logic
     *
     * @param Updater $updater
     *
     * @return void
     */
    protected function update(Updater $updater)
    {
        $this->output->writeln('Updating...' . PHP_EOL);
        try {
            $result = $updater->update();

            $newVersion = $updater->getNewVersion();
            $oldVersion = $updater->getOldVersion();
            if (strlen($newVersion) == 40) {
                $newVersion = 'dev-' . $newVersion;
            }
            if (strlen($oldVersion) == 40) {
                $oldVersion = 'dev-' . $oldVersion;
            }

            if ($result) {
                $this->output->writeln('<fg=green>GoJira has been updated.</fg=green>');
                $this->output->writeln(sprintf(
                    '<fg=green>Current version is:</fg=green> <options=bold>%s</options=bold>.',
                    $newVersion
                ));
                $this->output->writeln(sprintf(
                    '<fg=green>Previous version was:</fg=green> <options=bold>%s</options=bold>.',
                    $oldVersion
                ));
            } else {
                $this->output->writeln('<fg=green>GoJira is currently up to date.</fg=green>');
                $this->output->writeln(sprintf(
                    '<fg=green>Current version is:</fg=green> <options=bold>%s</options=bold>.',
                    $oldVersion
                ));
            }
        } catch (\Exception $e) {
            $this->output->writeln(sprintf('Error: <fg=yellow>%s</fg=yellow>', $e->getMessage()));
        }
        $this->output->write(PHP_EOL);
        $this->output->writeln('You can also select update stability using --dev, --pre (alpha/beta/rc) or --stable.');
    }

    /**
     * Rollback to previous version
     *
     * @return void
     */
    protected function rollback()
    {
        $updater = new Updater;
        try {
            $result = $updater->rollback();
            if ($result) {
                $this->output->writeln('<fg=green>GoJira has been rolled back to prior version.</fg=green>');
            } else {
                $this->output->writeln('<fg=red>Rollback failed for reasons unknown.</fg=red>');
            }
        } catch (\Exception $e) {
            $this->output->writeln(sprintf('Error: <fg=yellow>%s</fg=yellow>', $e->getMessage()));
        }
    }

    /**
     * Print all available updates
     *
     * @return void
     */
    protected function printAvailableUpdates()
    {
        $this->printCurrentLocalVersion();
        $this->printCurrentStableVersion();
        $this->printCurrentPreReleaseVersion();
        $this->printCurrentDevVersion();
        $this->output->writeln('You can select update stability using --dev, --pre or --stable when self-updating.');
    }

    /**
     * Print current local version
     *
     * @return void
     */
    protected function printCurrentLocalVersion()
    {
        $this->output->writeln(sprintf(
            'Your current local build version is: <options=bold>%s</options=bold>',
            $this->version
        ));
    }

    /**
     * Print current stable version
     *
     * @return void
     */
    protected function printCurrentStableVersion()
    {
        $this->printVersion($this->getStableUpdater());
    }

    /**
     * Print current pre-release version
     *
     * @return void
     */
    protected function printCurrentPreReleaseVersion()
    {
        $this->printVersion($this->getPreReleaseUpdater());
    }

    /**
     * Print current development version
     *
     * @return void
     */
    protected function printCurrentDevVersion()
    {
        $this->printVersion($this->getDevelopmentUpdater());
    }

    /**
     * Print version
     *
     * @param Updater $updater
     *
     * @return void
     */
    protected function printVersion(Updater $updater)
    {
        $stability = 'stable';
        if ($updater->getStrategy() instanceof ShaStrategy) {
            $stability = 'development';
        } elseif ($updater->getStrategy() instanceof GithubStrategy
            && $updater->getStrategy()->getStability() == GithubStrategy::UNSTABLE) {
            $stability = 'pre-release';
        }

        try {
            if ($updater->hasUpdate()) {
                $this->output->writeln(sprintf(
                    'The current %s build available remotely is: <options=bold>%s</options=bold>',
                    $stability,
                    $updater->getNewVersion()
                ));
            } elseif (false === $updater->getNewVersion()) {
                $this->output->writeln(sprintf('There are no %s builds available.', $stability));
            } else {
                $this->output->writeln(sprintf('You have the current %s build installed.', $stability));
            }
        } catch (\Exception $e) {
            $this->output->writeln(sprintf('Error: <fg=yellow>%s</fg=yellow>', $e->getMessage()));
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('self-update')
            ->setDescription('Update GoJIRA to most recent stable, pre-release or development build.')
            ->addOption(
                'dev',
                'd',
                InputOption::VALUE_NONE,
                'Update to most recent development build of GoJIRA.'
            )
            ->addOption(
                'non-dev',
                'N',
                InputOption::VALUE_NONE,
                'Update to most recent non-development (alpha/beta/stable) build of GoJIRA tagged on Github.'
            )
            ->addOption(
                'pre',
                'p',
                InputOption::VALUE_NONE,
                'Update to most recent pre-release version of GoJIRA (alpha/beta/rc) tagged on Github.'
            )
            ->addOption(
                'stable',
                's',
                InputOption::VALUE_NONE,
                'Update to most recent stable version tagged on Github.'
            )
            ->addOption(
                'rollback',
                'r',
                InputOption::VALUE_NONE,
                'Rollback to previous version of GoJIRA if available on filesystem.'
            )
            ->addOption(
                'check',
                'c',
                InputOption::VALUE_NONE,
                'Checks what updates are available across all possible stability tracks.'
            );
    }
}
