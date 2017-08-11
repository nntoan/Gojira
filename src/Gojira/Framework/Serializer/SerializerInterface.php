<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Framework\Serializer;

/**
 * Serializer interface
 *
 * @package Gojira\Framework\Serializer
 * @author  Toan Nguyen <me@nntoan.com>
 */
interface SerializerInterface
{
    /**
     * Encode data to JSON-object
     *
     * @param array $data
     *
     * @return string
     */
    public static function encode(array $data);

    /**
     * Decode JSON-string to array
     *
     * @param string $data
     *
     * @return array
     */
    public static function decode($data);
}
