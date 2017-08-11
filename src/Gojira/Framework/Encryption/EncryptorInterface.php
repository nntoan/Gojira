<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Framework\Encryption;

/**
 * Encryptor interface
 *
 * @package Gojira\Framework\Encryption
 * @author  Toan Nguyen <me@nntoan.com>
 */
interface EncryptorInterface
{
    /**
     * Encrypt a string
     *
     * @param string $data
     *
     * @return string
     */
    public function encrypt($data);

    /**
     * Decrypt a string
     *
     * @param string $data
     *
     * @return string
     */
    public function decrypt($data);

    /**
     * Return crypt model, instantiate if it is empty
     *
     * @param string $key
     *
     * @return \Gojira\Framework\Encryption\Crypt
     */
    public function validateKey($key);
}
