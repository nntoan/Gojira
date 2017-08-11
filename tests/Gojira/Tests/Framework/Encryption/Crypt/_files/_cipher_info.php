<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
?>
<?php return [
    'blowfish' => [
        'ecb' => [
            'key_size' => 56,
            'iv_size' => 8,
        ],
        'cbc' => [
            'key_size' => 56,
            'iv_size' => 8,
        ],
        'cfb' => [
            'key_size' => 56,
            'iv_size' => 8,
        ],
        'ofb' => [
            'key_size' => 56,
            'iv_size' => 8,
        ],
        'nofb' => [
            'key_size' => 56,
            'iv_size' => 8,
        ],
    ],
    'rijndael-128' => [
        'ecb' => [
            'key_size' => 32,
            'iv_size' => 16,
        ],
        'cbc' => [
            'key_size' => 32,
            'iv_size' => 16,
        ],
        'cfb' => [
            'key_size' => 32,
            'iv_size' => 16,
        ],
        'ofb' => [
            'key_size' => 32,
            'iv_size' => 16,
        ],
        'nofb' => [
            'key_size' => 32,
            'iv_size' => 16,
        ],
    ],
    'rijndael-256' => [
        'ecb' => [
            'key_size' => 32,
            'iv_size' => 32,
        ],
        'cbc' => [
            'key_size' => 32,
            'iv_size' => 32,
        ],
        'cfb' => [
            'key_size' => 32,
            'iv_size' => 32,
        ],
        'ofb' => [
            'key_size' => 32,
            'iv_size' => 32,
        ],
        'nofb' => [
            'key_size' => 32,
            'iv_size' => 32,
        ],
    ],
];
