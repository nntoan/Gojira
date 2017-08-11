<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Framework\Encryption;

/**
 * Tools for cryptography
 *
 * @package Gojira\Framework\Encryption
 * @author  Toan Nguyen <me@nntoan.com>
 */
class Utils
{
    /**
     * Compare two strings to avoid timing attacks
     *
     * C function memcmp() internally used by PHP, exits as soon as a difference
     * is found in the two buffers. That makes possible of leaking
     * timing information useful to an attacker attempting to iteratively guess
     * the unknown string (e.g. password).
     * The length will leak.
     *
     * @param  string $expected
     * @param  string $actual
     *
     * @return bool
     */
    public static function compareStrings($expected, $actual)
    {
        $expected = (string)$expected;
        $actual = (string)$actual;

        if (function_exists('hash_equals')) {
            return hash_equals($expected, $actual);
        }

        $lenExpected = strlen($expected);
        $lenActual = strlen($actual);
        $len = min($lenExpected, $lenActual);

        $result = 0;
        for ($i = 0; $i < $len; $i++) {
            $result |= ord($expected[$i]) ^ ord($actual[$i]);
        }
        $result |= $lenExpected ^ $lenActual;

        return ($result === 0);
    }
}
