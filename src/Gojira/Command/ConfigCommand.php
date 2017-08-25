<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Command;

use Gojira\Api\Authentication\BasicAuthentication;
use Gojira\Framework\App\Configuration\AuthInterface;
use Gojira\Framework\App\Configuration\ConfigurationInterface;
use Gojira\Framework\App\Configuration\OptionsInterface;
use Gojira\Provider\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Config Command, check and set config before start using the application
 *
 * @package Gojira\Command
 * @author  Toan Nguyen <me@nntoan.com>
 */
class ConfigCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $help = __(
            "Config Help:\n%1\n%2\n%3\n%4\n%5",
            ' - Jira URL: https://foo.atlassian.net/',
            ' - Username: user (for user@foo.bar)',
            ' - Password: Your password',
            ' - Timezone: Choose your timezone',
            ' - Use cache: Choose cache mode'
        );

        $this
            ->setName('config')
            ->setDescription('Change configuration')
            ->setHelp($help)
            ->addOption('clear', 'c', InputOption::VALUE_NONE, 'Clear stored configuration');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('clear')) {
            $result = $this->configuration->clearConfig();
            $output->writeln('<info>' . $result['msg'] . '</info>');
        }

        if (!$this->authentication->isAuth()) {
            $jiraUrl = $this->prompt->ask($input, $output, $this->prompt->getJiraUrlQuestion());
            $username = $this->prompt->ask($input, $output, $this->prompt->getJiraUsernameQuestion());
            $password = $this->prompt->ask($input, $output, $this->prompt->getJiraPasswordQuestion());
            $timezones = $this->prompt->ask($input, $output, $this->prompt->chooseTimezoneQuestion());
            $useCache = $this->prompt->ask($input, $output, $this->prompt->chooseCacheModeQuestion());

            if (isset($jiraUrl, $username, $password, $timezones, $useCache)) {
                $authenticate = new BasicAuthentication($username, $password);
                $encryptionKey = md5($this->random->getRandomString(OptionsInterface::KEY_RANDOM_STRING_SIZE));
                $authItems = [
                    AuthInterface::BASE_URI => $jiraUrl,
                    AuthInterface::USERNAME => $username,
                    AuthInterface::TOKEN_SECRET => $authenticate->getCredential(),
                    AuthInterface::CONSUMER_SECRET => uniqid(),
                    AuthInterface::BCRYPT_MODE => false
                ];
                $optionItems = array_merge(
                    $this->optionConfig->initDefaultOptionItems(),
                    [
                        OptionsInterface::TIMEZONE => $timezones,
                        OptionsInterface::IS_USE_CACHE => $useCache,
                        OptionsInterface::ENCRYPTION_KEY => $encryptionKey
                    ]
                );
                $pathItems = $this->pathConfig->initDefaultPaths();

                $this->authConfig->setData($authItems);
                $this->optionConfig->setData($optionItems);
                $this->pathConfig->setData($pathItems);

                $this->configuration->setData(ConfigurationInterface::PATHS, $this->pathConfig->getData());
                $this->configuration->setData(ConfigurationInterface::AUTH, $this->authConfig->getData());
                $this->configuration->setData(ConfigurationInterface::OPTIONS, $this->optionConfig->getData());
                $result = $this->configuration->saveConfig();

                $output->writeln(__('<info>%1</info>', $result['msg']));
            }
        } else {
            $output->writeln(__(
                'STATUS: [<comment>%1</comment>]',
                'Authorized'
            ));

            /**
             * @var Command $command
             */
            $command = $this->getApplication()->getService('console')->find('list');
            $command->run($input, $output);
        }
    }
}
