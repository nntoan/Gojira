<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/functions.php';

if (function_exists('date_default_timezone_set')
    && function_exists('date_default_timezone_get')) {
    date_default_timezone_set(@date_default_timezone_get());
}
