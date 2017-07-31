<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Provider;

use Pimple\Container;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Registers EventDispatcher and related services with the Pimple Container
 *
 * @api
 */
class DispatcherServiceProvider implements \Pimple\ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $pimple)
    {
        $pimple['dispatcher'] = function () {
            return new EventDispatcher;
        };
    }
}
