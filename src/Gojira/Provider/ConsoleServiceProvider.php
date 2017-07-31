<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Provider;

use Pimple\Container;
use Gojira\Provider\Console\ContainerAwareApplication;

/**
 * Gojira Console Service Provider
 *
 * @author Toan Nguyen <me@nntoan.com>
 */
class ConsoleServiceProvider implements \Pimple\ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $pimple)
    {
        $pimple['console'] = function ($pimple) {
            $console = new ContainerAwareApplication($pimple['console.name'], $pimple['console.version']);
            $console->setDispatcher($pimple['dispatcher']);
            $console->setContainer($pimple);

            return $console;
        };
    }
}
