<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Framework\App\Console;

/**
 * ProgressBar interface for console application
 *
 * @codeCoverageIgnore
 * @package Gojira\Framework\App\Console
 * @author  Toan Nguyen <me@nntoan.com>
 */
interface ProgressBarInterface
{
    /**
     * Stop the progress bar
     *
     * @return void
     */
    public function finish();
}
