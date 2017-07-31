<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Api\Authentication;

/**
 * JIRA anonymous authentication class
 *
 * @package Gojira\Api\Authentication
 * @author  Toan Nguyen <me@nntoan.com>
 */
class AnonymousAuthentication implements AuthenticationInterface
{
    /**
     * @return null
     */
    public function getUsername()
    {
        return null;
    }

    /**
     * @return null
     */
    public function getPassword()
    {
        return null;
    }

    /**
     * @return null
     */
    public function getCredential()
    {
        return null;
    }
}
