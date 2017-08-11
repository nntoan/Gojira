<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Provider\Console;

use DateTimeZone;
use Gojira\Framework\ObjectManager\ObjectManager;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * Base class for Gojira console prompt questions.
 *
 * @package Gojira\Provider\Console
 * @author  Toan Nguyen <me@nntoan.com>
 */
class Prompt
{
    /**
     * @var QuestionHelper
     */
    protected $prompt;

    /**
     * Prompt constructor.
     */
    public function __construct()
    {
        $this->prompt = ObjectManager::create(QuestionHelper::class);
    }

    /**
     * Asks a question to the user.
     *
     * @param InputInterface  $input    An InputInterface instance
     * @param OutputInterface $output   An OutputInterface instance
     * @param Question        $question The question to ask
     *
     * @return mixed The user answer
     *
     * @throws RuntimeException If there is no data to read in the input stream
     */
    public function ask(InputInterface $input, OutputInterface $output, Question $question)
    {
        return $this->prompt->ask($input, $output, $question);
    }

    /**
     * Get JIRA URL question
     *
     * @return Question
     */
    public function getJiraUrlQuestion()
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
    public function getJiraUsernameQuestion()
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
    public function getJiraPasswordQuestion()
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
    public function chooseTimezoneQuestion()
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
    public function chooseCacheModeQuestion()
    {
        $question = new ConfirmationQuestion('<question>Please select HttpClient cache mode:</question> ', false);

        return $question;
    }
}
