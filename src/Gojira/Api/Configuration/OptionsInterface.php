<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Api\Configuration;

/**
 * Interface for configuration option items
 *
 * @package Gojira\Api\Configuration
 * @author  Toan Nguyen <me@nntoan.com>
 */
interface OptionsInterface
{
    const STATUS = 'status';
    const TIMEZONE = 'timezone';
    const JIRA_STOP = 'jira_stop';
    const JIRA_START = 'jira_start';
    const JIRA_REVIEW = 'jira_review';
    const JIRA_DONE = 'jira_done';
    const IS_USE_CACHE = 'is_use_cache';
    const AVAILABLE_ISSUES = 'available_issues_status';
    const ENCRYPTION_KEY = 'encryption_key';

    /**
     * Size of random string generated for store's encryption key
     */
    const KEY_RANDOM_STRING_SIZE = 32;
}
