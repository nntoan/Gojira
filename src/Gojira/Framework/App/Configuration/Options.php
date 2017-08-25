<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Framework\App\Configuration;

use Gojira\Framework\Data\DataObject;

/**
 * Base class to work with option items
 *
 * @package Gojira\Framework\App\Configuration
 * @author  Toan Nguyen <me@nntoan.com>
 */
class Options extends DataObject implements OptionsInterface
{
    /**
     * Overwrite data in the object.
     *
     * The $key parameter can be string or array.
     * If $key is string, the attribute value will be overwritten by $value
     *
     * If $key is an array, it will overwrite all the data in the object.
     *
     * @param string|array $key
     * @param mixed        $value
     *
     * @return $this
     */
    public function setData($key, $value = null)
    {
        if ($key === (array)$key) {
            $this->_data = $key;
        } else {
            $this->_data[$key] = $value;
        }

        return $this;
    }

    /**
     * Init default option items
     *
     * @return array
     */
    public function initDefaultOptionItems()
    {
        return [
            self::JIRA_STOP        => [
                self::STATUS => 'To Do'
            ],
            self::JIRA_START       => [
                self::STATUS => 'In Progress'
            ],
            self::JIRA_REVIEW      => [
                self::STATUS => 'In Review'
            ],
            self::JIRA_DONE        => [
                self::STATUS => 'Done'
            ],
            self::AVAILABLE_ISSUES => [
                'Open',
                'In Progress',
                'Reopened',
                'To Do',
                'In Review',
                'Blocked',
                'Internal Testing'
            ]
        ];
    }
}
