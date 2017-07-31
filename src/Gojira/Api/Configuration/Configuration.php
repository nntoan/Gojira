<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Api\Configuration;

use Gojira\Api\Data\DataObject;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Base class to work with configuration items
 *
 * @package Gojira\Api\Configuration
 * @author  Toan Nguyen <me@nntoan.com>
 */
class Configuration extends DataObject implements ConfigurationInterface
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var PathInterface
     */
    private $path;

    /**
     * @var AuthInterface
     */
    private $authConfig;

    /**
     * @var OptionsInterface
     */
    private $optionConfig;

    /**
     * Configuration items
     *
     * @var array
     */
    private $configItems = [];

    /**
     * Config constructor.
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->path = new Path();
        $this->filesystem = new Filesystem();
        $this->configItems = $this->getConfigItems();

        parent::__construct($data);
    }

    /**
     * {@inheritdoc}
     */
    public function checkConfig()
    {
        if ($this->filesystem->exists($this->path->getPath(PathInterface::CONFIG_PATH))) {
            $this->configItems = $this->getConfigItems();
            if (!isset($this->configItems[self::OPTIONS])
                || !isset($this->configItems[self::OPTIONS][OptionsInterface::JIRA_STOP])) {
                return false;
            } else {
                return true;
            }
        }

        return false;
    }

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
    public function saveConfig()
    {
        $result = [];
        $basePath = $this->path->getPath();
        $configFilePath = $this->path->getPath(PathInterface::CONFIG_PATH);
        if (!$this->filesystem->exists($basePath)) {
            $this->filesystem->mkdir($basePath);
        }

        if (!$this->filesystem->exists($configFilePath)) {
            $jsonContent = Serializer::encode(($this->getData()) ?: $this->configItems);
            $this->filesystem->dumpFile($configFilePath, $jsonContent);

            $result['msg'] = 'Your top secret information has been sent to us. Thank you!';
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function clearConfig()
    {
        $result = [];
        $basePath = $this->path->getPath();
        $configFilePath = $this->path->getPath(PathInterface::CONFIG_PATH);
        if (!$this->filesystem->exists($configFilePath)) {
            if ($this->filesystem->exists($basePath)) {
                $this->filesystem->remove($basePath);
                $result['msg'] = '<info>There is no stored data. Skipping.</info>';
            }
        } else {
            $this->filesystem->remove($configFilePath);
            $this->filesystem->remove($basePath);
            $this->configItems = $this->initDefaultConfig();
            $result['msg'] = '<info>Configuration deleted successfully!</info>';
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigItems()
    {
        if (empty($this->configItems)) {
            $this->setData($this->initDefaultConfig());
        } elseif ($this->filesystem->exists($this->path->getPath(PathInterface::CONFIG_PATH))) {
            $this->setData(Serializer::decode($this->getFileContent()));
        }

        $this->configItems = $this->getData();

        return $this->configItems;
    }

    /**
     * Get config file content
     *
     * @return bool|string
     */
    private function getFileContent()
    {
        return file_get_contents($this->path->getPath(PathInterface::CONFIG_PATH));
    }

    /**
     * Initialise the default config items
     *
     * @return array
     */
    private function initDefaultConfig()
    {
        return [
            self::PATHS   => $this->path->initDefaultPaths(),
            self::AUTH    => [],
            self::OPTIONS => []
        ];
    }
}
