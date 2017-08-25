<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Tests\Framework\Encryption;

use Gojira\Framework\Encryption\Encryptor;
use Gojira\Framework\Encryption\Crypt;

/**
 * Test case for \Gojira\Framework\Encryption\Encryptor
 */
class EncryptorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Gojira\Framework\Encryption\Encryptor
     */
    protected $model;

    /**
     * @var \Gojira\Framework\Math\Random|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $randomGenerator;

    protected function setUp()
    {
        $this->randomGenerator = $this->getMock('\Gojira\Framework\Math\Random', [], [], '', false);
        /** @var \Gojira\Framework\App\Configuration\Configuration|\PHPUnit_Framework_MockObject_MockObject $appConfigMock */
        $appConfigMock = $this->getMock('\Gojira\Framework\App\Configuration\Configuration', [], [], '', false);
        $appConfigMock->expects($this->any())
            ->method('getData')
            ->with(Encryptor::PARAM_CRYPT_KEY)
            ->will($this->returnValue('cryptKey'));
        $this->model = new \Gojira\Framework\Encryption\Encryptor($this->randomGenerator, $appConfigMock);
    }

    /**
     * @param mixed $key
     *
     * @dataProvider encryptWithEmptyKeyDataProvider
     */
    public function testEncryptWithEmptyKey($key)
    {
        /** @var \Gojira\Framework\App\Configuration\Configuration|\PHPUnit_Framework_MockObject_MockObject $appConfigMock */
        $appConfigMock = $this->getMock('\Gojira\Framework\App\Configuration\Configuration', [], [], '', false);
        $appConfigMock->expects($this->any())
            ->method('getData')
            ->with(Encryptor::PARAM_CRYPT_KEY)
            ->will($this->returnValue($key));
        $model = new Encryptor($this->randomGenerator, $appConfigMock);
        $value = 'arbitrary_string';
        $this->assertEquals($value, $model->encrypt($value));
    }

    public function encryptWithEmptyKeyDataProvider()
    {
        return [[null], [0], [''], ['0']];
    }

    /**
     * @param mixed $key
     *
     * @dataProvider decryptWithEmptyKeyDataProvider
     */
    public function testDecryptWithEmptyKey($key)
    {
        /** @var \Gojira\Framework\App\Configuration\Configuration|\PHPUnit_Framework_MockObject_MockObject $appConfigMock */
        $appConfigMock = $this->getMock('\Gojira\Framework\App\Configuration\Configuration', [], [], '', false);
        $appConfigMock->expects($this->any())
            ->method('getData')
            ->with(Encryptor::PARAM_CRYPT_KEY)
            ->will($this->returnValue($key));
        $model = new Encryptor($this->randomGenerator, $appConfigMock);
        $value = 'arbitrary_string';
        $this->assertEquals('', $model->decrypt($value));
    }

    public function decryptWithEmptyKeyDataProvider()
    {
        return [[null], [0], [''], ['0']];
    }

    public function testEncrypt()
    {
        // sample data to encrypt
        $data = 'Mares eat oats and does eat oats, but little lambs eat ivy.';

        $actual = $this->model->encrypt($data);

        // Extract the initialization vector and encrypted data
        $parts = explode(':', $actual, 4);
        list(, , $iv, $encryptedData) = $parts;

        // Decrypt returned data with RIJNDAEL_256 cipher, cbc mode
        $crypt = new Crypt('cryptKey', MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC, $iv);
        // Verify decrypted matches original data
        $this->assertEquals($data, $crypt->decrypt(base64_decode((string)$encryptedData)));
    }

    public function testDecrypt()
    {
        // sample data to encrypt
        $data = '0:2:z3a4ACpkU35W6pV692U4ueCVQP0m0v0p:' .
            '7ZPIIRZzQrgQH+csfF3fyxYNwbzPTwegncnoTxvI3OZyqKGYlOCTSx5i1KRqNemCC8kuCiOAttLpAymXhzjhNQ==';

        $actual = $this->model->decrypt($data);

        // Extract the initialization vector and encrypted data
        $parts = explode(':', $data, 4);
        list(, , $iv, $encrypted) = $parts;

        // Decrypt returned data with RIJNDAEL_256 cipher, cbc mode
        $crypt = new Crypt('cryptKey', MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC, $iv);
        // Verify decrypted matches original data
        $this->assertEquals($encrypted, base64_encode($crypt->encrypt($actual)));
    }

    public function testEncryptDecryptNewKeyAdded()
    {
        /** @var \Gojira\Framework\App\Configuration\Configuration|\PHPUnit_Framework_MockObject_MockObject $appConfigMock */
        $appConfigMock = $this->getMock('\Gojira\Framework\App\Configuration\Configuration', [], [], '', false);
        $appConfigMock->expects($this->at(0))
            ->method('getData')
            ->with(Encryptor::PARAM_CRYPT_KEY)
            ->will($this->returnValue("cryptKey1"));
        $appConfigMock->expects($this->at(1))
            ->method('getData')
            ->with(Encryptor::PARAM_CRYPT_KEY)
            ->will($this->returnValue("cryptKey1\ncryptKey2"));
        $model1 = new Encryptor($this->randomGenerator, $appConfigMock);
        // simulate an encryption key is being added
        $model2 = new Encryptor($this->randomGenerator, $appConfigMock);

        // sample data to encrypt
        $data = 'Mares eat oats and does eat oats, but little lambs eat ivy.';
        // encrypt with old key
        $encryptedData = $model1->encrypt($data);
        $decryptedData = $model2->decrypt($encryptedData);

        $this->assertSame($data, $decryptedData, 'Encryptor failed to decrypt data encrypted by old keys.');
    }

    public function testValidateKey()
    {
        $actual = $this->model->validateKey('some_key');
        $crypt = new Crypt('some_key', MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC, $actual->getInitVector());
        $expectedEncryptedData = base64_encode($crypt->encrypt('data'));
        $actualEncryptedData = base64_encode($actual->encrypt('data'));
        $this->assertEquals($expectedEncryptedData, $actualEncryptedData);
        $this->assertEquals($crypt->decrypt($expectedEncryptedData), $actual->decrypt($actualEncryptedData));
    }
}
