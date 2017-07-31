<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Api\Configuration;

use JMS\Serializer\SerializerBuilder;

/**
 * Base class for Serializer
 *
 * @package Gojira\Api\Configuration
 * @author  Toan Nguyen <me@nntoan.com>
 */
class Serializer
{
    /**
     * Encode data to JSON-object
     *
     * @param array $data
     *
     * @return string
     */
    public static function encode(array $data)
    {
        return SerializerBuilder::create()->build()->serialize($data, 'json');
    }

    /**
     * Decode JSON-string to array
     *
     * @param string $data
     *
     * @return array
     */
    public static function decode($data)
    {
        return SerializerBuilder::create()->build()->deserialize($data, 'array', 'json');
    }
}
