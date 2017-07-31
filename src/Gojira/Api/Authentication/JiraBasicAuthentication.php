<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Api\Authentication;

use Gojira\Api\Configuration\AuthInterface;
use Gojira\Api\Configuration\ConfigurationInterface;

/**
 * JIRA basic authentication class get token from config
 *
 * @package Gojira\Api\Authentication
 * @author  Toan Nguyen <me@nntoan.com>
 */
class JiraBasicAuthentication implements AuthenticationInterface
{
    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $credential;

    /**
     * @var array
     */
    protected $authItems = [];

    /**
     * @var \Gojira\Api\Configuration\Configuration
     */
    protected $configuration;

    /**
     * JIRA Basic authenticate constructor.
     *
     * @param ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
        $this->authItems = $this->getAuthItems();
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        if ($this->username === null && $this->isAuth()) {
            $this->username = $this->authItems[AuthInterface::USERNAME];
        }

        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getCredential()
    {
        if ($this->credential === null && $this->isAuth()) {
            $this->credential = $this->authItems[AuthInterface::TOKEN_SECRET];
        }

        return $this->credential;
    }

    /**
     * Check if JIRA client authenticated successfully
     *
     * @return bool
     */
    public function isAuth()
    {
        return $this->configuration->checkConfig();
    }

    /**
     * Get configuration auth items
     *
     * @return array
     */
    private function getAuthItems()
    {
        if (empty($this->authItems) && $this->isAuth()) {
            $this->authItems = $this->configuration->getData(ConfigurationInterface::AUTH);
        }

        return $this->authItems;
    }
}
