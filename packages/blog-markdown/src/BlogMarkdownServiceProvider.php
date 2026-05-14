<?php

declare(strict_types=1);

namespace LorneQuinn\Blog\Markdown;

use Illuminate\Support\ServiceProvider;
use League\CommonMark\CommonMarkConverter;

final class BlogMarkdownServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(MarkdownRenderer::class, fn (): MarkdownRenderer => new MarkdownRenderer(new CommonMarkConverter));
    }

    public function boot(): void
    {
        // Pipeline registration lands in CP 30.
    }
}
