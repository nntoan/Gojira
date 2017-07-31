<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Shortcut constant for the root directory
 */
define('BP', dirname(dirname(__DIR__)));

define('VENDOR_PATH', BP . '/vendor');

if (!file_exists(VENDOR_PATH)) {
    throw new \Exception(
        'We can\'t read some files that are required to run the GoJira application. '
        . 'This usually means file permissions are set incorrectly.' . VENDOR_PATH
    );
}

$vendorAutoload = VENDOR_PATH . "/autoload.php";
/* 'composer install' validation */
if (file_exists($vendorAutoload)) {
    $composerAutoloader = include $vendorAutoload;
} else {
    throw new \Exception(
        'Vendor autoload is not found. Please run \'composer install\' under application root directory.'
    );
}
