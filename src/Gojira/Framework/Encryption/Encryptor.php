<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Framework\Encryption;

use Gojira\Api\Configuration\Configuration;
use Gojira\Framework\Math\Random;

/**
 * Class Encryptor provides basic logic for hashing strings and encrypting/decrypting misc data
 *
 * @package Gojira\Framework\Encryption
 * @author  Toan Nguyen <me@nntoan.com>
 */
class Encryptor implements EncryptorInterface
{
    /**
     * Array key of encryption key in deployment config
     */
    const PARAM_CRYPT_KEY = 'options/encryption_key';

    /**#@+
     * Cipher versions
     */
    const CIPHER_BLOWFISH = 0;

    const CIPHER_RIJNDAEL_128 = 1;

    const CIPHER_RIJNDAEL_256 = 2;

    const CIPHER_LATEST = 2;
    /**#@-*/

    /**
     * Indicate cipher
     *
     * @var int
     */
    protected $cipher = self::CIPHER_LATEST;

    /**
     * Version of encryption key
     *
     * @var int
     */
    protected $keyVersion;

    /**
     * Array of encryption keys
     *
     * @var string[]
     */
    protected $keys = [];

    /**
     * Random generator
     *
     * @var Random
     */
    protected $random;

    /**
     * @param Random        $random
     * @param Configuration $configuration
     */
    public function __construct(
        Random $random,
        Configuration $configuration
    ) {
        $this->random = $random;

        // load all possible keys
        $this->keys = preg_split('/\s+/s', trim($configuration->getData(self::PARAM_CRYPT_KEY)));
        $this->keyVersion = count($this->keys) - 1;
    }

    /**
     * Check whether specified cipher version is supported
     *
     * Returns matched supported version or throws exception
     *
     * @param int $version
     *
     * @return int
     * @throws \Exception
     */
    public function validateCipher($version)
    {
        $types = [self::CIPHER_BLOWFISH, self::CIPHER_RIJNDAEL_128, self::CIPHER_RIJNDAEL_256];

        $version = (int)$version;
        if (!in_array($version, $types, true)) {
            throw new \Exception((string)new \Gojira\Framework\Phrase('Not supported cipher version'));
        }
        return $version;
    }

    /**
     * Prepend key and cipher versions to encrypted data after encrypting
     *
     * @param string $data
     *
     * @return string
     */
    public function encrypt($data)
    {
        $crypt = $this->getCrypt();
        if (null === $crypt) {
            return $data;
        }
        return $this->keyVersion . ':' . $this->cipher . ':' . (MCRYPT_MODE_CBC ===
            $crypt->getMode() ? $crypt->getInitVector() . ':' : '') . base64_encode(
                $crypt->encrypt((string)$data)
            );
    }

    /**
     * Look for key and crypt versions in encrypted data before decrypting
     *
     * Unsupported/unspecified key version silently fallback to the oldest we have
     * Unsupported cipher versions eventually throw exception
     * Unspecified cipher version fallback to the oldest we support
     *
     * @param string $data
     *
     * @return string
     */
    public function decrypt($data)
    {
        if ($data) {
            $parts = explode(':', $data, 4);
            $partsCount = count($parts);

            $initVector = false;
            // specified key, specified crypt, specified iv
            if (4 === $partsCount) {
                list($keyVersion, $cryptVersion, $iv, $data) = $parts;
                $initVector = $iv ? $iv : false;
                $keyVersion = (int)$keyVersion;
                $cryptVersion = self::CIPHER_RIJNDAEL_256;
                // specified key, specified crypt
            } elseif (3 === $partsCount) {
                list($keyVersion, $cryptVersion, $data) = $parts;
                $keyVersion = (int)$keyVersion;
                $cryptVersion = (int)$cryptVersion;
                // no key version = oldest key, specified crypt
            } elseif (2 === $partsCount) {
                list($cryptVersion, $data) = $parts;
                $keyVersion = 0;
                $cryptVersion = (int)$cryptVersion;
                // no key version = oldest key, no crypt version = oldest crypt
            } elseif (1 === $partsCount) {
                $keyVersion = 0;
                $cryptVersion = self::CIPHER_BLOWFISH;
                // not supported format
            } else {
                return '';
            }
            // no key for decryption
            if (!isset($this->keys[$keyVersion])) {
                return '';
            }
            $crypt = $this->getCrypt($this->keys[$keyVersion], $cryptVersion, $initVector);
            if (null === $crypt) {
                return '';
            }
            return trim($crypt->decrypt(base64_decode((string)$data)));
        }
        return '';
    }

    /**
     * Return crypt model, instantiate if it is empty
     *
     * @param string|null $key NULL value means usage of the default key specified on constructor
     *
     * @return \Gojira\Framework\Encryption\Crypt
     * @throws \Exception
     */
    public function validateKey($key)
    {
        if (preg_match('/\s/s', $key)) {
            throw new \Exception((string)new \Gojira\Framework\Phrase('The encryption key format is invalid.'));
        }
        return $this->getCrypt($key);
    }

    /**
     * Attempt to append new key & version
     *
     * @param string $key
     *
     * @return $this
     */
    public function setNewKey($key)
    {
        $this->validateKey($key);
        $this->keys[] = $key;
        $this->keyVersion += 1;
        return $this;
    }

    /**
     * Export current keys as string
     *
     * @return string
     */
    public function exportKeys()
    {
        return implode("\n", $this->keys);
    }

    /**
     * Initialize crypt module if needed
     *
     * By default initializes with latest key and crypt versions
     *
     * @param string $key
     * @param int    $cipherVersion
     * @param bool   $initVector
     *
     * @return Crypt|null
     */
    protected function getCrypt($key = null, $cipherVersion = null, $initVector = true)
    {
        if (null === $key && null === $cipherVersion) {
            $cipherVersion = self::CIPHER_RIJNDAEL_256;
        }

        if (null === $key) {
            $key = $this->keys[$this->keyVersion];
        }

        if (!$key) {
            return null;
        }

        if (null === $cipherVersion) {
            $cipherVersion = $this->cipher;
        }
        $cipherVersion = $this->validateCipher($cipherVersion);

        if ($cipherVersion === self::CIPHER_RIJNDAEL_128) {
            $cipher = MCRYPT_RIJNDAEL_128;
            $mode = MCRYPT_MODE_ECB;
        } elseif ($cipherVersion === self::CIPHER_RIJNDAEL_256) {
            $cipher = MCRYPT_RIJNDAEL_256;
            $mode = MCRYPT_MODE_CBC;
        } else {
            $cipher = MCRYPT_BLOWFISH;
            $mode = MCRYPT_MODE_ECB;
        }

        return new Crypt($key, $cipher, $mode, $initVector);
    }
}
