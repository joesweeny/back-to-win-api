<?php

namespace GamePlatform\Framework\Buffer;

/**
 * OutputBuffer
 *
 * Utility class to wrap data which would normally be directly output to STDOUT and return
 * it as a string instead
 */
class OutputBuffer
{
    /**
     * @param callable $callback
     * @return string
     */
    public static function capture(callable $callback): string
    {
        ob_start();

        $callback();

        $content = ob_get_contents();

        ob_end_clean();

        return $content ?: '';
    }
}
