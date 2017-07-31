<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Api\HandlerStack;

use GuzzleHttp\HandlerStack;
use League\Flysystem\Adapter\Local;
use Kevinrob\GuzzleCache\Strategy\GreedyCacheStrategy;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Storage\FlysystemStorage;

/**
 * Guzzle cache middleware
 *
 * @package Gojira\Api\HandlerStack
 * @author  Toan Nguyen <me@nntoan.com>
 */
class GuzzleStack implements HandlerStackInterface
{
    /**
     * {@inheritdoc}
     */
    public static function create()
    {
        $stack = HandlerStack::create();
        $stack->push(
            new CacheMiddleware(
                new GreedyCacheStrategy(
                    new FlysystemStorage(
                        new Local('/tmp/gojira')
                    ),
                    180
                )
            ),
            'cache'
        );

        return $stack;
    }
}
