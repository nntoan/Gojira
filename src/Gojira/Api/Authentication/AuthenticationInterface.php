<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Api\Authentication;

/**
 * Interface for all JIRA authentication types:
 * - Basic
 * - OAuth2
 * - Keberos
 * - Anonymous
 *
 * @package Gojira\Api\Authentication
 * @author  Toan Nguyen <me@nntoan.com>
 */
interface AuthenticationInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const USERNAME = 'username';
    const PASSWORD = 'password';
    const CREDENTIAL = 'credential';

    /**
     * Get credential based on what kind of authentication
     *
     * @return string|null
     */
    public function getCredential();

    /**
     * Get JIRA Username
     *
     * @return string|null
     */
    public function getUsername();

    /**
     * Get JIRA Password (not available after authenticated)
     *
     * @return string|null
     */
    public function getPassword();
}
