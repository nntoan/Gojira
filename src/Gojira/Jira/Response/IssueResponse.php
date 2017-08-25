<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Jira\Response;

use Gojira\Api\Response\BaseResponse;
use Gojira\Api\Response\ResponseInterface;
use Symfony\Component\Console\Helper\TableCell;

/**
 * Render result for JIRA REST /issue
 *
 * @package Gojira\Jira\Response
 * @author  Toan Nguyen <me@nntoan.com>
 */
class IssueResponse extends BaseResponse implements ResponseInterface
{
    /**
     * {@inheritdoc}
     */
    public function render($type = null)
    {
        switch ($type) {
            default:
                $result = $this->renderNothing();
                break;
        }

        return $result;
    }
}
