<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Api\Configuration;

use Gojira\Api\Data\DataObject;
use Gojira\Application;

/**
 * Base class to work with path items
 *
 * @package Gojira\Api\Configuration
 * @author  Toan Nguyen <me@nntoan.com>
 */
class Path extends DataObject implements PathInterface
{
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
     * @return $this
     */
    public function setData($key, $value = null)
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
    public function getPath($key = null)
    {
        $basePath = $this->getHomePath() . self::DS . '.' . strtolower(Application::CODENAME) . self::DS;

        switch ($key) {
            case self::CONFIG_PATH:
                return $basePath . self::CONFIG_FILE;
            case self::CACHE_PATH:
                return $basePath . self::CACHE_FILE;
            case self::BASE_PATH:
            default:
                return $basePath;
        }
    }

    /**
     * Returns default paths for config item
     *
     * @return array
     */
    public function initDefaultPaths()
    {
        return [
            self::BASE_PATH => $this->getPath(),
            self::CONFIG_PATH => $this->getPath(self::CONFIG_PATH),
            self::CACHE_PATH => $this->getPath(self::CACHE_PATH)
        ];
    }

    /**
     * Returns the user's home directory
     *
     * @return null|string
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function getHomePath()
    {
        // Cannot use $_SERVER superglobal since that's empty during UnitUnishTestCase
        // getenv('HOME') isn't set on Windows and generates a Notice.
        $home = getenv('HOME');
        if (!empty($home)) {
            // home should never end with a trailing slash.
            $home = rtrim($home, '/');
        } elseif (!empty($_SERVER['HOMEDRIVE']) && !empty($_SERVER['HOMEPATH'])) {
            // home on windows
            $home = $_SERVER['HOMEDRIVE'] . $_SERVER['HOMEPATH'];
            // If HOMEPATH is a root directory the path can end with a slash. Make sure
            // that doesn't happen.
            $home = rtrim($home, '\\/');
        }

        return empty($home) ? null : $home;
    }
}
