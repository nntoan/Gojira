<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Framework\Phrase\Renderer;

use Gojira\Framework\Phrase\RendererInterface;

/**
 * Placeholder renderer
 * __('format %1 %2 %3', $var1, $var2, $var3)
 *
 * @package Gojira\Framework\Phrase\Renderer
 * @author  Toan Nguyen <me@nntoan.com>
 */
class Placeholder implements RendererInterface
{
    /**
     * Render source text
     *
     * @param array $source
     * @param array $arguments
     * @return string
     */
    public function render(array $source, array $arguments)
    {
        $text = end($source);

        if ($arguments) {
            $placeholders = array_map([$this, 'keyToPlaceholder'], array_keys($arguments));
            $pairs = array_combine($placeholders, $arguments);
            $text = strtr($text, $pairs);
        }

        return $text;
    }

    /**
     * Get key to placeholder
     *
     * @param string|int $key
     * @return string
     */
    private function keyToPlaceholder($key)
    {
        return '%' . (is_int($key) ? strval($key + 1) : $key);
    }
}
