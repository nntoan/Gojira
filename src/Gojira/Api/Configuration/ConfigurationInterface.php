<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Api\Configuration;

/**
 * Interface for configuration items
 *
 * @package Gojira\Api\Configuration
 * @author  Toan Nguyen <me@nntoan.com>
 */
interface ConfigurationInterface
{
    const DS = '/';
    const CONFIG_FILE = 'config.json';
    const CACHE_FILE = 'cache.json';

    const PATHS = 'paths';
    const AUTH = 'auth';
    const OPTIONS = 'options';

    const FILE_BASE_PATH = 'base_path';
    const FILE_CFG_PATH = 'config_path';
    const FILE_CACHE_PATH = 'cache_path';

    const AUTH_BASE_URI = 'base_uri';
    const AUTH_USERNAME = 'username';
    const AUTH_TOKEN_SECRET = 'token_secret';
    const AUTH_CONSUMER_SECRET = 'consumer_secret';
    const AUTH_BCRYPT_MODE = 'security_mode';

    const OPT_STATUS = 'status';
    const OPT_JIRA_STOP = 'jira_stop';
    const OPT_JIRA_START = 'jira_start';
    const OPT_JIRA_REVIEW = 'jira_review';
    const OPT_JIRA_DONE = 'jira_done';
    const OPT_AVAILABLE_ISSUES = 'available_issues_status';

    /**
     * Check if config items being set
     *
     * @return bool
     */
    public function checkConfig();

    /**
     * Get configuration items
     *
     * @return array
     */
    public function getConfigItems();

    /**
     * Save config items to file
     *
     * @return array
     */
    public function saveConfig();

    /**
     * Clear all config items in file
     *
     * @return array
     */
    public function clearConfig();
}
