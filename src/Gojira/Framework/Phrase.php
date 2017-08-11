<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Framework;

use Gojira\Framework\Phrase\Renderer\Placeholder as RendererPlaceholder;
use Gojira\Framework\Phrase\RendererInterface;

/**
 * Class Phrase
 *
 * @package Gojira\Framework
 * @author  Toan Nguyen <me@nntoan.com>
 */
class Phrase
{
    /**
     * Default phrase renderer. Allows stacking renderers that "don't know about each other"
     *
     * @var RendererInterface
     */
    private static $renderer;

    /**
     * String for rendering
     *
     * @var string
     */
    private $text;

    /**
     * Arguments for placeholder values
     *
     * @var array
     */
    private $arguments;

    /**
     * Set default Phrase renderer
     *
     * @param RendererInterface $renderer
     *
     * @return void
     */
    public static function setRenderer(RendererInterface $renderer)
    {
        self::$renderer = $renderer;
    }

    /**
     * Get default Phrase renderer
     *
     * @return RendererInterface
     */
    public static function getRenderer()
    {
        if (!self::$renderer) {
            self::$renderer = new RendererPlaceholder();
        }
        return self::$renderer;
    }

    /**
     * Phrase construct
     *
     * @param string $text
     * @param array  $arguments
     */
    public function __construct($text, array $arguments = [])
    {
        $this->text = (string)$text;
        $this->arguments = $arguments;
    }

    /**
     * Get phrase base text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Get phrase message arguments
     *
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Render phrase
     *
     * @return string
     */
    public function render()
    {
        try {
            return self::getRenderer()->render([$this->text], $this->getArguments());
        } catch (\Exception $e) {
            return $this->getText();
        }
    }

    /**
     * Defers rendering to the last possible moment (when converted to string)
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
}
