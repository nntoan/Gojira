<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Api\Configuration;

/**
 * Interface for auth config items
 *
 * @package Gojira\Api\Configuration
 * @author  Toan Nguyen <me@nntoan.com>
 */
interface AuthInterface
{
    const BASE_URI = 'base_uri';
    const USERNAME = 'username';
    const TOKEN_SECRET = 'token_secret';
    const CONSUMER_SECRET = 'consumer_secret';
    const BCRYPT_MODE = 'security_mode';
}
