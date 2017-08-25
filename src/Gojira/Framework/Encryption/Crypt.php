<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Framework\Encryption;

/**
 * Class encapsulates cryptographic algorithm
 *
 * @package Gojira\Framework\Encryption
 * @author  Toan Nguyen <me@nntoan.com>
 */
class Crypt
{
    /**
     * @var string
     */
    protected $cipher;

    /**
     * @var string
     */
    protected $mode;

    /**
     * @var string
     */
    protected $initVector;

    /**
     * Encryption algorithm module handle
     *
     * @var resource
     */
    protected $handle;

    /**
     * Constructor
     *
     * @param  string      $key        Secret encryption key.
     *                                 It's unsafe to store encryption key in memory, so no getter for key exists.
     * @param  string      $cipher     Cipher algorithm (one of the MCRYPT_ciphername constants)
     * @param  string      $mode       Mode of cipher algorithm (MCRYPT_MODE_modeabbr constants)
     * @param  string|bool $initVector Initial vector to fill algorithm blocks.
     *                                 TRUE generates a random initial vector.
     *                                 FALSE fills initial vector with zero bytes to not use it.
     *
     * @throws \Exception
     */
    public function __construct($key, $cipher = MCRYPT_BLOWFISH, $mode = MCRYPT_MODE_ECB, $initVector = false)
    {
        $this->cipher = $cipher;
        $this->mode = $mode;
        $this->handle = mcrypt_module_open($cipher, '', $mode, '');
        try {
            $maxKeySize = mcrypt_enc_get_key_size($this->handle);
            if (strlen($key) > $maxKeySize) {
                throw new \Gojira\Framework\Exception\LocalizedException(
                    new \Gojira\Framework\Phrase('Key must not exceed %1 bytes.', [$maxKeySize])
                );
            }
            $initVectorSize = mcrypt_enc_get_iv_size($this->handle);
            if (true === $initVector) {
                /* Generate a random vector from human-readable characters */
                $abc = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                $initVector = '';
                for ($i = 0; $i < $initVectorSize; $i++) {
                    $initVector .= $abc[rand(0, strlen($abc) - 1)];
                }
            } elseif (false === $initVector) {
                /* Set vector to zero bytes to not use it */
                $initVector = str_repeat("\0", $initVectorSize);
            } elseif (!is_string($initVector) || strlen($initVector) != $initVectorSize) {
                throw new \Gojira\Framework\Exception\LocalizedException(
                    new \Gojira\Framework\Phrase('Init vector must be a string of %1 bytes.', [$initVectorSize])
                );
            }
            $this->initVector = $initVector;
        } catch (\Exception $e) {
            mcrypt_module_close($this->handle);
            throw $e;
        }
        mcrypt_generic_init($this->handle, $key, $initVector);
    }

    /**
     * Destructor frees allocated resources
     *
     * @return void
     */
    public function __destruct()
    {
        mcrypt_generic_deinit($this->handle);
        mcrypt_module_close($this->handle);
    }

    /**
     * Retrieve a name of currently used cryptographic algorithm
     *
     * @return string
     */
    public function getCipher()
    {
        return $this->cipher;
    }

    /**
     * Mode in which cryptographic algorithm is running
     *
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Retrieve an actual value of initial vector that has been used to initialize a cipher
     *
     * @return string
     */
    public function getInitVector()
    {
        return $this->initVector;
    }

    /**
     * Encrypt a data
     *
     * @param  string $data String to encrypt
     *
     * @return string
     */
    public function encrypt($data)
    {
        if (strlen($data) == 0) {
            return $data;
        }
        return mcrypt_generic($this->handle, $data);
    }

    /**
     * Decrypt a data
     *
     * @param  string $data String to decrypt
     *
     * @return string
     */
    public function decrypt($data)
    {
        if (strlen($data) == 0) {
            return $data;
        }
        $data = mdecrypt_generic($this->handle, $data);
        /*
         * Returned string can in fact be longer than the unencrypted string due to the padding of the data
         * @link http://www.php.net/manual/en/function.mdecrypt-generic.php
         */
        $data = rtrim($data, "\0");
        return $data;
    }
}
