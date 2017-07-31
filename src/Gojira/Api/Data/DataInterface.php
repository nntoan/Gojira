<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Api\Data;

/**
 * Interface to work with data-object
 *
 * @package Gojira\Api\Data
 * @author  Toan Nguyen <me@nntoan.com>
 */
interface DataInterface
{
    /**
     * Add data to the object.
     *
     * Retains previous data in the object.
     *
     * @param array $arr
     *
     * @return \Gojira\Api\Data\DataInterface
     */
    public function addData(array $arr);

    /**
     * Overwrite data in the object.
     *
     * The $key parameter can be string or array.
     * If $key is string, the attribute value will be overwritten by $value
     *
     * If $key is an array, it will overwrite all the data in the object.
     *
     * @param string|array $key
     * @param mixed        $value
     *
     * @return \Gojira\Api\Data\DataInterface
     */
    public function setData($key, $value);

    /**
     * Set object data with calling setter method
     *
     * @param string $key
     * @param mixed  $args
     *
     * @return \Gojira\Api\Data\DataInterface
     */
    public function setDataUsingMethod($key, $args = []);

    /**
     * Unset data from the object.
     *
     * @param null|string|array $key
     *
     * @return \Gojira\Api\Data\DataInterface
     */
    public function unsetData($key = null);

    /**
     * Object data getter
     *
     * If $key is not defined will return all the data as an array.
     * Otherwise it will return value of the element specified by $key.
     * It is possible to use keys like a/b/c for access nested array data
     *
     * If $index is specified it will assume that attribute data is an array
     * and retrieve corresponding member. If data is the string - it will be explode
     * by new line character and converted to array.
     *
     * @param string     $key
     * @param string|int $index
     *
     * @return mixed
     */
    public function getData($key = '', $index = null);

    /**
     * Get object data by path
     *
     * Method consider the path as chain of keys: a/b/c => ['a']['b']['c']
     *
     * @param string $path
     *
     * @return mixed
     */
    public function getDataByPath($path);

    /**
     * Get object data by particular key
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getDataByKey($key);

    /**
     * Get object data by key with calling getter method
     *
     * @param string $key
     * @param mixed  $args
     *
     * @return mixed
     */
    public function getDataUsingMethod($key, $args = null);

    /**
     * If $key is empty, checks whether there's any data in the object
     * Otherwise checks if the specified attribute is set.
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasData($key = '');
}
