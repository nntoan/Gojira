<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Api\Data;

/**
 * Class DataObject
 *
 * @package Gojira\Api\Data
 * @author  Toan Nguyen <me@nntoan.com>
 */
class DataObject implements DataInterface
{
    /**
     * Object attributes
     *
     * @var array
     */
    protected $_data = [];

    /**
     * Constructor
     *
     * By default is looking for first argument as array and assigns it as object attributes
     * This behavior may change in child classes
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->_data = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function addData(array $arr)
    {
        foreach ($arr as $index => $value) {
            $this->setData($index, $value);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setData($key, $value)
    {
        if ($key === (array)$key) {
            $this->_data = $key;
        } else {
            $this->_data[$key] = $value;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function unsetData($key = null)
    {
        if ($key === null) {
            $this->setData([]);
        } elseif (is_string($key)) {
            if (isset($this->_data[$key]) || array_key_exists($key, $this->_data)) {
                unset($this->_data[$key]);
            }
        } elseif ($key === (array)$key) {
            foreach ($key as $element) {
                $this->unsetData($element);
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setDataUsingMethod($key, $args = [])
    {
        $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
        $this->{$method}($args);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($key = '', $index = null)
    {
        if ('' === $key) {
            return $this->_data;
        }
        /* process a/b/c key as ['a']['b']['c'] */
        if (strpos($key, '/')) {
            $data = $this->getDataByPath($key);
        } else {
            $data = $this->_getData($key);
        }
        if ($index !== null) {
            if ($data === (array)$data) {
                $data = isset($data[$index]) ? $data[$index] : null;
            } elseif (is_string($data)) {
                $data = explode(PHP_EOL, $data);
                $data = isset($data[$index]) ? $data[$index] : null;
            } elseif ($data instanceof \Gojira\Api\Data\DataObject) {
                $data = $data->getData($index);
            } else {
                $data = null;
            }
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataByKey($key)
    {
        return $this->_getData($key);
    }

    /**
     * {@inheritdoc}
     */
    public function getDataByPath($path)
    {
        $keys = explode('/', $path);
        $data = $this->_data;
        foreach ($keys as $key) {
            if ((array)$data === $data && isset($data[$key])) {
                $data = $data[$key];
            } elseif ($data instanceof \Gojira\Api\Data\DataObject) {
                $data = $data->getDataByKey($key);
            } else {
                return null;
            }
        }

        return $data;
    }

    /**
     * Get value from _data array without parse key
     *
     * @param   string $key
     *
     * @return  mixed
     */
    protected function _getData($key)
    {
        if (isset($this->_data[$key])) {
            return $this->_data[$key];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataUsingMethod($key, $args = null)
    {
        $method = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));

        return $this->{$method}($args);
    }

    /**
     * {@inheritdoc}
     */
    public function hasData($key = '')
    {
        if (empty($key) || !is_string($key)) {
            return !empty($this->_data);
        }

        return array_key_exists($key, $this->_data);
    }
}
