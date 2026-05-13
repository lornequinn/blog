<?php

declare(strict_types=1);

namespace LorneQuinn\Blog\Core\Shortcode;

use Closure;

/**
 * Resolves `[[name attr=value]]` shortcodes in a body string.
 *
 * Operates as a post-processing pass on already-rendered HTML (or any text):
 * - `[[name]]` and `[[name attr=value]]` are replaced via the resolver.
 * - `\[[name]]` renders as the literal `[[name]]` (escape).
 * - Anything that doesn't match the shortcode grammar passes through unchanged.
 *
 * The resolver is `(string $name, array<string, string> $attrs): string` —
 * the parser stays decoupled from the ComponentRegistry; consumers wire
 * resolution in their service provider / pipeline configuration.
 */
final class ShortcodeParser
{
    /**
     * Matches a (potentially escaped) shortcode token.
     *
     * Capture groups:
     *   1. optional leading backslash (escape marker)
     *   2. shortcode name (kebab-case, lowercase)
     *   3. attribute string (everything between the name and `]]`)
     */
    private const TOKEN_PATTERN = '/(\\\\)?\[\[\s*([a-z][a-z0-9-]*)\s*([^\]]*)\]\]/';

    /**
     * Matches one attribute: bare-value, double-quoted, or single-quoted.
     */
    private const ATTR_PATTERN = '/([a-z][a-z0-9_-]*)\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|(\S+))/i';

    /** @var Closure(string, array<string, string>): string */
    private Closure $resolver;

    /**
     * @param  callable(string, array<string, string>): string  $resolver
     */
    public function __construct(callable $resolver)
    {
        $this->resolver = Closure::fromCallable($resolver);
    }

    public function parse(string $body): string
    {
        $result = preg_replace_callback(
            self::TOKEN_PATTERN,
            function (array $match): string {
                $escaped = ! empty($match[1]);
                if ($escaped) {
                    // Strip the leading backslash; emit the raw token literally.
                    return substr($match[0], 1);
                }

                $name = $match[2];
                $attrs = self::parseAttrs($match[3]);

                return ($this->resolver)($name, $attrs);
            },
            $body,
        );

        return $result ?? $body;
    }

    /**
     * @return array<string, string>
     */
    private static function parseAttrs(string $attrString): array
    {
        if (trim($attrString) === '') {
            return [];
        }

        /** @var array<int, array<int, string>> $matches */
        $matches = [];
        preg_match_all(self::ATTR_PATTERN, $attrString, $matches, PREG_SET_ORDER);

        $attrs = [];
        foreach ($matches as $m) {
            $key = $m[1];
            $value = match (true) {
                ($m[2] ?? '') !== '' => $m[2],
                ($m[3] ?? '') !== '' => $m[3],
                default => $m[4] ?? '',
            };
            $attrs[$key] = $value;
        }

        return $attrs;
    }
}
