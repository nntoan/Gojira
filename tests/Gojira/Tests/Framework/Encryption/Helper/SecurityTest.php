<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Tests\Framework\Encryption\Helper;

use Gojira\Framework\Encryption\Helper\Security;

/**
 * Test case for \Gojira\Framework\Encryption\Security
 */
class SecurityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Security
     */
    protected $util;

    /**
     * @param  string $expected
     * @param  string $actual
     * @param  bool   $result
     *
     * @dataProvider dataProvider
     */
    public function testCompareStrings($expected, $actual, $result)
    {
        $this->assertEquals($result, Security::compareStrings($expected, $actual));
    }

    public function dataProvider()
    {
        return [
            ['a@fzsd434sdfqw24', 'a@fzsd434sdfqw24', true],
            ['a@fzsd4343432432drfsffe2w24', 'a@fzsd434sdfqw24', false],
            ['0x123', '0x123', true],
            [0x123, 0x123, true],
            ['0x123', '0x11', false],
        ];
    }
}
