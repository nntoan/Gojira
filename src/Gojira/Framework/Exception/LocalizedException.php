<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Framework\Exception;

use Gojira\Framework\Phrase;
use Gojira\Framework\Phrase\Renderer\Placeholder;

/**
 * Class LocalizedException
 *
 * @api
 * @package Gojira\Framework\Exception
 * @author  Toan Nguyen <me@nntoan.com>
 */
class LocalizedException extends \Exception
{
    /**
     * @var \Gojira\Framework\Phrase
     */
    protected $phrase;

    /**
     * @var string
     */
    protected $logMessage;

    /**
     * Constructor
     *
     * @param \Gojira\Framework\Phrase $phrase
     * @param \Exception $cause
     */
    public function __construct(Phrase $phrase, \Exception $cause = null)
    {
        $this->phrase = $phrase;
        parent::__construct($phrase->render(), 0, $cause);
    }

    /**
     * Get the un-processed message, without the parameters filled in
     *
     * @return string
     */
    public function getRawMessage()
    {
        return $this->phrase->getText();
    }

    /**
     * Get parameters, corresponding to placeholders in raw exception message
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->phrase->getArguments();
    }

    /**
     * Get the un-localized message, but with the parameters filled in
     *
     * @return string
     */
    public function getLogMessage()
    {
        if ($this->logMessage === null) {
            $renderer = new Placeholder();
            $this->logMessage = $renderer->render([$this->getRawMessage()], $this->getParameters());
        }
        return $this->logMessage;
    }
}
