<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Api\Authentication;

/**
 * Base basic authentication class
 *
 * @package Gojira\Api\Authentication
 * @author  Toan Nguyen <me@nntoan.com>
 */
class BasicAuthentication implements AuthenticationInterface
{
    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var \Gojira\Api\Configuration\ConfigurationInterface
     */
    protected $configuration;

    /**
     * Basic authenticate constructor.
     *
     * @param string $username Username
     * @param string $password Password
     */
    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function getCredential()
    {
        return base64_encode($this->username . ':' . $this->password);
    }
}
