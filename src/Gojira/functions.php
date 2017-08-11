<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// @codingStandardsIgnoreFile

/**
 * Create value-object \Gojira\Framework\Phrase
 *
 * @return string
 * @SuppressWarnings(PHPMD.ShortMethodName)
 */
function __()
{
    $argc = func_get_args();

    $text = array_shift($argc);
    if (!empty($argc) && is_array($argc[0])) {
        $argc = $argc[0];
    }

    return (new \Gojira\Framework\Phrase($text, $argc))->render();
}

/**
 * Build endpoint string based on method parameters
 *
 * @return string
 */
function __e()
{
    $baseEndpoint = \Gojira\Api\Endpoint\EndpointInterface::BASE_ENDPOINT_V2;

    return $baseEndpoint . call_user_func_array('__', func_get_args());
}
