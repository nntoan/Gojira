<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Provider\Console;

use Gojira\Api\Data\TableInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Helper\Table as BaseTable;

/**
 * Base class for Table output render
 *
 * @package Gojira\Provider\Console
 * @author  Toan Nguyen <me@nntoan.com>
 */
class Table implements TableInterface
{
    /**
     * @var BaseTable
     */
    protected $table = null;

    /**
     * Table constructor.
     *
     * @param OutputInterface $output
     * @param array           $data
     */
    public function __construct(OutputInterface $output, array $data)
    {
        $this->table = $this->generateTable($output, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function generateTable(OutputInterface $output, array $data)
    {
        $table = new BaseTable($output);
        $table->setHeaders($data[static::HEADERS]);
        $table->setRows($data[static::ROWS]);
        $table->setStyle($this->getTableStyle());

        return $table;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $this->table->render();
    }

    /**
     * Build rows data
     *
     * @param array $rows Row data
     *
     * @return array
     */
    public static function buildRows(array $rows)
    {
        $rowData = [];

        foreach ($rows as $row) {
            $rowData[] = $row;
            array_push($rowData, self::getSeparator());
        }

        array_pop($rowData);

        return $rowData;
    }

    /**
     * Get table separator
     *
     * @return TableSeparator
     */
    public static function getSeparator()
    {
        return new TableSeparator();
    }

    /**
     * Get table style
     *
     * @return TableStyle
     */
    private function getTableStyle()
    {
        $style = new TableStyle();
        $style->setCellHeaderFormat('<fg=cyan>%s</>');

        return $style;
    }
}
