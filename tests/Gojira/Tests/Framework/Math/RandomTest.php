<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Tests\Framework\Math;

class RandomTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param int    $length
     * @param string $chars
     *
     * @dataProvider getRandomStringDataProvider
     */
    public function testGetRandomString($length, $chars = null)
    {
        $mathRandom = new \Gojira\Framework\Math\Random();
        $string = $mathRandom->getRandomString($length, $chars);

        $this->assertEquals($length, strlen($string));
        if ($chars !== null) {
            $this->_assertContainsOnlyChars($string, $chars);
        }
    }

    public function getRandomStringDataProvider()
    {
        return [
            [0],
            [10],
            [10, \Gojira\Framework\Math\Random::CHARS_LOWERS],
            [10, \Gojira\Framework\Math\Random::CHARS_UPPERS],
            [10, \Gojira\Framework\Math\Random::CHARS_DIGITS],
            [
                20,
                \Gojira\Framework\Math\Random::CHARS_LOWERS .
                \Gojira\Framework\Math\Random::CHARS_UPPERS .
                \Gojira\Framework\Math\Random::CHARS_DIGITS
            ]
        ];
    }

    public function testGetUniqueHash()
    {
        $mathRandom = new \Gojira\Framework\Math\Random();
        $hashOne = $mathRandom->getUniqueHash();
        $hashTwo = $mathRandom->getUniqueHash();
        $this->assertTrue(is_string($hashOne));
        $this->assertTrue(is_string($hashTwo));
        $this->assertNotEquals($hashOne, $hashTwo);
    }

    /**
     * @param string $string
     * @param string $chars
     */
    protected function _assertContainsOnlyChars($string, $chars)
    {
        if (preg_match('/[^' . $chars . ']+/', $string, $matches)) {
            $this->fail(sprintf('Unexpected char "%s" found', $matches[0]));
        }
    }

    /**
     * @param $min
     * @param $max
     *
     * @dataProvider testGetRandomNumberProvider
     */
    public function testGetRandomNumber($min, $max)
    {
        $number = \Gojira\Framework\Math\Random::getRandomNumber($min, $max);
        $this->assertLessThanOrEqual($max, $number);
        $this->assertGreaterThanOrEqual($min, $number);
    }

    public function testGetRandomNumberProvider()
    {
        return [
            [0, 100],
            [0, 1],
            [0, 0],
            [-1, 0],
            [-100, 0],
            [-1, 1],
            [-100, 100]
        ];
    }
}
