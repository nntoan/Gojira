<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Api\HandlerStack;

use GuzzleHttp\HandlerStack;

/**
 * Interface for handler stack middleware
 *
 * @package Gojira\Api\HandlerStack
 * @author  Toan Nguyen <me@nntoan.com>
 */
interface HandlerStackInterface
{
    /**
     * @return \GuzzleHttp\HandlerStack;
     */
    public static function create();
}
