<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Api\Response;

/**
 * Abstract base response class
 *
 * @package Gojira\Api\Response
 * @author  Toan Nguyen <me@nntoan.com>
 */
abstract class BaseResponse implements ResponseInterface
{
    /**
     * @var array
     */
    protected $response;

    /**
     * BaseResponse constructor.
     *
     * @param $response
     */
    public function __construct($response)
    {
        $this->response = $response;
    }
}
