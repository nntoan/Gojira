<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Api\Configuration;

/**
 * Interface for path config items
 *
 * @package Gojira\Api\Configuration
 * @author  Toan Nguyen <me@nntoan.com>
 */
interface PathInterface
{
    const DS = '/';
    const CONFIG_FILE = 'config.json';
    const CACHE_FILE = 'cache.json';

    const BASE_PATH = 'base_path';
    const CONFIG_PATH = 'config_path';
    const CACHE_PATH = 'cache_path';

    /**
     * Get path
     *
     * @param string|null $key Key to determine which path
     *
     * @return string
     */
    public function getPath($key = null);
}
