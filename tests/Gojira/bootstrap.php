<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__ . '/../../src/Gojira/autoload.php';
require_once __DIR__ . '/../../src/Gojira/functions.php';

if (function_exists('date_default_timezone_set') && function_exists('date_default_timezone_get')) {
    date_default_timezone_set(@date_default_timezone_get());
}

\Gojira\Framework\Phrase::setRenderer(new \Gojira\Framework\Phrase\Renderer\Placeholder());

error_reporting(E_ALL);
ini_set('display_errors', 1);
