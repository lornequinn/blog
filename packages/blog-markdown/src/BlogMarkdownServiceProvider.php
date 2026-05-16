<?php

declare(strict_types=1);

namespace LorneQuinn\Blog\Markdown;

use Illuminate\Support\ServiceProvider;
use League\CommonMark\CommonMarkConverter;
use LorneQuinn\Blog\Core\Rendering\BodyPipeline;

final class BlogMarkdownServiceProvider extends ServiceProvider
{
    /**
     * Pipeline priority for the markdown pre-pass.
     *
     * Higher than blog-core's ShortcodeParser (priority 0) so markdown runs
     * AFTER shortcodes have been resolved into raw HTML. CommonMark renders
     * with html_input='allow' so resolved shortcode HTML survives the pass.
     *
     * @see docs/graph/decisions/body-pipeline.md
     */
    public const PIPELINE_PRIORITY = 100;

    public function register(): void
    {
        $this->app->singleton(MarkdownRenderer::class, fn (): MarkdownRenderer => new MarkdownRenderer(
            new CommonMarkConverter(['html_input' => 'allow', 'allow_unsafe_links' => false]),
        ));
    }

    public function boot(): void
    {
        $pipeline = $this->app->make(BodyPipeline::class);
        $renderer = $this->app->make(MarkdownRenderer::class);

        $pipeline->register(
            fn (string $body): string => $renderer->render($body),
            self::PIPELINE_PRIORITY,
        );
    }
}
