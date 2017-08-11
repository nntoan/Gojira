<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Api\Response;

/**
 * Interface for REST API responses
 *
 * @package Gojira\Api\Response
 * @author  Toan Nguyen <me@nntoan.com>
 */
interface ResponseInterface
{
    /**
     * COMMON
     */
    const ID = 'id';
    const NAME = 'name';
    const FIELDS = 'fields';

    /**
     * ISSUES SECTION
     */
    const ISSUES = 'issues';
    const KEY = 'key';
    const PRIORITY = 'priority';
    const SUMMARY = 'summary';
    const STATUS = 'status';

    /**
     * WORKLOG SECTION
     */
    const WORKLOGS = 'worklogs';
    const CREATED = 'created';
    const AUTHOR = 'author';
    const DISPLAY_NAME = 'displayName';
    const TIME_SPENT = 'timeSpent';
    const COMMENT = 'comment';
    const TOTAL = 'total';

    /**
     * TRANSITIONS SECTION
     */
    const TRANSITIONS = 'transitions';

    /**
     * PRIORITY SECTION
     */
    const PRIORITY_MINOR = 'Minor';
    const PRIORITY_MAJOR = 'Major';
    const PRIORITY_CRITICAL = 'Critical';
    const PRIORITY_BLOCKER = 'Blocker';

    /**
     * MISC
     */
    const STARTED = 'started';
    const ISSUE = 'issue';
    const ERROR_MESSAGE = 'errorMessages';

    /**
     * Render response data for greater good
     *
     * @param string|null $type Type of result to render
     *
     * @return array
     */
    public function render($type = null);
}
