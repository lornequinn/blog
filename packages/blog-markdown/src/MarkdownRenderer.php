<?php

declare(strict_types=1);

namespace LorneQuinn\Blog\Markdown;

use League\CommonMark\CommonMarkConverter;

/**
 * Renders a markdown body to HTML via CommonMark.
 *
 * Sits as a pre-pass on the blog/core BodyPipeline. Shortcode markers like
 * `[[name]]` are not processed here — they pass through untouched for the
 * shortcode parser to resolve in the next pipeline stage.
 */
final class MarkdownRenderer
{
    public function __construct(private readonly CommonMarkConverter $converter) {}

    public function render(string $markdown): string
    {
        return (string) $this->converter->convert($markdown);
    }
}
