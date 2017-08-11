<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Framework\ObjectManager;

class ObjectManager implements ObjectManagerInterface
{
    /**
     * Create new object instance
     *
     * @param string $type
     * @param array  $arguments
     *
     * @return mixed
     */
    public static function create($type, array $arguments = [])
    {
        $type = ltrim($type, '\\');
        return call_user_func_array(
            [new \ReflectionClass($type), 'newInstance'],
            $arguments
        );
    }
}
