<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Command;

use DateTimeZone;
use Gojira\Api\Authentication\BasicAuthentication;
use Gojira\Api\Authentication\JiraBasicAuthentication;
use Gojira\Api\Configuration\Auth;
use Gojira\Api\Configuration\AuthInterface;
use Gojira\Api\Configuration\Configuration;
use Gojira\Api\Configuration\ConfigurationInterface;
use Gojira\Api\Configuration\Options;
use Gojira\Api\Configuration\OptionsInterface;
use Gojira\Api\Configuration\Path;
use Gojira\Provider\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * Config Command, check and set config before start using the application
 *
 * @package Gojira\Command
 * @author  Toan Nguyen <me@nntoan.com>
 */
class ConfigCommand extends Command
{
    /**
     * @var \Gojira\Api\Authentication\JiraBasicAuthentication
     */
    protected $authentication = null;

    /**
     * @var \Gojira\Api\Configuration\Configuration
     */
    protected $configuration = null;

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
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->configuration = new Configuration();
        $this->authConfig = new Auth();
        $this->optionConfig = new Options();
        $this->pathConfig = new Path();
        $this->authentication = new JiraBasicAuthentication($this->configuration);

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
        $helper = $this->getHelper('question');

        if ($input->getOption('clear')) {
            $result = $this->configuration->clearConfig();
            $output->writeln('<info>' . $result['msg'] . '</info>');
        }

        if (!$this->authentication->isAuth()) {
            $jiraUrl = $helper->ask($input, $output, $this->getJiraUrlQuestion());
            $username = $helper->ask($input, $output, $this->getJiraUsernameQuestion());
            $password = $helper->ask($input, $output, $this->getJiraPasswordQuestion());
            $timezones = $helper->ask($input, $output, $this->chooseTimezoneQuestion());
            $useCache = $helper->ask($input, $output, $this->chooseCacheModeQuestion());

            if (isset($jiraUrl, $username, $password, $timezones, $useCache)) {
                $authenticate = new BasicAuthentication($username, $password);
                $authItems = [
                    AuthInterface::BASE_URI => $jiraUrl,
                    AuthInterface::USERNAME => $username,
                    AuthInterface::TOKEN_SECRET => $authenticate->getCredential(),
                    AuthInterface::CONSUMER_SECRET => uniqid(),
                    AuthInterface::BCRYPT_MODE => false
                ];
                $optionItems = array_merge(
                    $this->optionConfig->initDefaultOptionItems(),
                    [OptionsInterface::TIMEZONE => $timezones, OptionsInterface::IS_USE_CACHE => $useCache]
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

    /**
     * Get JIRA URL question
     *
     * @return Question
     */
    private function getJiraUrlQuestion()
    {
        $question = new Question('<question>Please enter your Jira URL:</question> ');
        $question->setValidator(function ($value) {
            if (trim($value) === '') {
                throw new \Exception('The URL cannot be empty');
            }

            return $value;
        });

        return $question;
    }

    /**
     * Get JIRA username question
     *
     * @return Question
     */
    private function getJiraUsernameQuestion()
    {
        $question = new Question('<question>Please enter your Jira username:</question> ');
        $question->setValidator(function ($value) {
            if (trim($value) === '') {
                throw new \Exception('The username cannot be empty');
            }

            return $value;
        });

        return $question;
    }

    /**
     * Get JIRA password question
     *
     * @return Question
     */
    private function getJiraPasswordQuestion()
    {
        $question = new Question('<question>Please enter your Jira password:</question> ');
        $question->setValidator(function ($value) {
            if (trim($value) === '') {
                throw new \Exception('The password cannot be empty');
            }

            return $value;
        });
        $question->setHidden(true);
        $question->setMaxAttempts(20);

        return $question;
    }

    /**
     * Choose server timezone question
     *
     * @return ChoiceQuestion
     */
    private function chooseTimezoneQuestion()
    {
        $timezones = DateTimeZone::listIdentifiers();
        $question = new ChoiceQuestion(
            '<question>Please choose your server timezone (defaults to Australia/Sydney):</question> ',
            $timezones,
            313
        );
        $question->setErrorMessage('Timezone %s is invalid');

        return $question;
    }

    /**
     * Select cache mode question
     *
     * @return ConfirmationQuestion
     */
    private function chooseCacheModeQuestion()
    {
        $question = new ConfirmationQuestion('<question>Please select HttpClient cache mode:</question> ', false);

        return $question;
    }
}
