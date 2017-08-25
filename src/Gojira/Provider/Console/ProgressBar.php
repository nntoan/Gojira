<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Provider\Console;

use Gojira\Framework\App\Console\ProgressBarInterface;
use Symfony\Component\Console\Helper\ProgressBar as BaseProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Base class for ProgressBar output render
 *
 * @codeCoverageIgnore
 * @package Gojira\Provider\Console
 * @author  Toan Nguyen <me@nntoan.com>
 */
class ProgressBar implements ProgressBarInterface
{
    /**
     * @var \Symfony\Component\Console\Helper\ProgressBar
     */
    protected $progressBar = null;

    /**
     * ProgressBar constructor.
     *
     * @param OutputInterface $output   Console output
     * @param int             $maxSteps Maximum steps
     */
    public function __construct(OutputInterface $output, $maxSteps = 100)
    {
        $this->progressBar = new BaseProgressBar($output, $maxSteps);
        $this->progressBar->setFormat('minimal');
    }

    /**
     * @return \Symfony\Component\Console\Helper\ProgressBar
     */
    public function getProgressBar()
    {
        return $this->progressBar;
    }

    /**
     * {@inheritdoc}
     */
    public function finish()
    {
        $this->progressBar->finish();
    }
}
