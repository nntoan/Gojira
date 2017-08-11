<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Framework\Math;

/**
 * Random data generator
 *
 * @api
 */
class Random
{
    /**#@+
     * Frequently used character classes
     */
    const CHARS_LOWERS = 'abcdefghijklmnopqrstuvwxyz';

    const CHARS_UPPERS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    const CHARS_DIGITS = '0123456789';

    /**#@-*/

    /**
     * Get random string
     *
     * @param int         $length
     * @param null|string $chars
     *
     * @return string
     * @throws \Gojira\Api\Exception\ApiException
     */
    public function getRandomString($length, $chars = null)
    {
        $str = '';
        if (null === $chars) {
            $chars = self::CHARS_LOWERS . self::CHARS_UPPERS . self::CHARS_DIGITS;
        }

        if (function_exists('openssl_random_pseudo_bytes')) {
            // use openssl lib if it is installed
            for ($i = 0, $lc = strlen($chars) - 1; $i < $length; $i++) {
                $bytes = openssl_random_pseudo_bytes(PHP_INT_SIZE);
                $hex = bin2hex($bytes); // hex() doubles the length of the string
                $rand = abs(hexdec($hex) % $lc); // random integer from 0 to $lc
                $str .= $chars[$rand]; // random character in $chars
            }
        } elseif ($fp = @fopen('/dev/urandom', 'rb')) {
            // attempt to use /dev/urandom if it exists but openssl isn't available
            for ($i = 0, $lc = strlen($chars) - 1; $i < $length; $i++) {
                $bytes = @fread($fp, PHP_INT_SIZE);
                $hex = bin2hex($bytes); // hex() doubles the length of the string
                $rand = abs(hexdec($hex) % $lc); // random integer from 0 to $lc
                $str .= $chars[$rand]; // random character in $chars
            }
            fclose($fp);
        } else {
            throw new \Gojira\Api\Exception\ApiException(
                new \Gojira\Framework\Phrase("Please make sure you have 'openssl' extension installed")
            );
        }

        return $str;
    }

    /**
     * Return a random number in the specified range
     *
     * @param $min [optional]
     * @param $max [optional]
     *
     * @return int A random integer value between min (or 0) and max
     * @throws \Gojira\Api\Exception\ApiException
     */
    public static function getRandomNumber($min = 0, $max = null)
    {
        if (null === $max) {
            $max = mt_getrandmax();
        }
        $range = $max - $min + 1;
        $offset = 0;

        if (function_exists('openssl_random_pseudo_bytes')) {
            // use openssl lib if it is installed
            $bytes = openssl_random_pseudo_bytes(PHP_INT_SIZE);
            $hex = bin2hex($bytes); // hex() doubles the length of the string
            $offset = abs(hexdec($hex) % $range); // random integer from 0 to $range
        } elseif ($fp = @fopen('/dev/urandom', 'rb')) {
            // attempt to use /dev/urandom if it exists but openssl isn't available
            $bytes = @fread($fp, PHP_INT_SIZE);
            $hex = bin2hex($bytes); // hex() doubles the length of the string
            $offset = abs(hexdec($hex) % $range); // random integer from 0 to $range
            fclose($fp);
        } else {
            throw new \Gojira\Api\Exception\ApiException(
                new \Gojira\Framework\Phrase("Please make sure you have 'openssl' extension installed")
            );
        }

        return $min + $offset; // random integer from $min to $max
    }

    /**
     * Generate a hash from unique ID
     *
     * @param string $prefix
     *
     * @return string
     */
    public function getUniqueHash($prefix = '')
    {
        return $prefix . md5(uniqid(microtime() . self::getRandomNumber(), true));
    }
}