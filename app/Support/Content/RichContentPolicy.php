<?php

namespace App\Support\Content;

/**
 * MVP rich-content rendering policy.
 *
 * User-authored post/page/comment bodies are stored as plain text and rendered
 * with Blade escaping only. Raw HTML and {!! !!} output are not used for
 * untrusted content. HTML sanitization may be introduced in a future phase.
 */
class RichContentPolicy
{
    public const MODE_PLAIN_ESCAPED = 'plain_escaped';

    public static function mode(): string
    {
        return self::MODE_PLAIN_ESCAPED;
    }

    public static function allowsHtml(): bool
    {
        return false;
    }
}
