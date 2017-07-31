<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Api\Data;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Table interface for console application
 *
 * @package Gojira\Api\Data
 * @author  Toan Nguyen <me@nntoan.com>
 */
interface TableInterface
{
    const HEADERS = 'headers';
    const ROWS = 'rows';

    /**
     * Generate table for console application
     *
     * @param OutputInterface $output
     * @param array           $data
     *
     * @return Table
     */
    public function generateTable(OutputInterface $output, array $data);

    /**
     * Render generated table
     *
     * @return void
     */
    public function render();
}
