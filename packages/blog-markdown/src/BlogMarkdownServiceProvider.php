<?php

declare(strict_types=1);

namespace LorneQuinn\Blog\Markdown;

use Illuminate\Support\ServiceProvider;

final class BlogMarkdownServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // MarkdownRenderer binding lands in CP 29.
        // Pipeline registration lands in CP 30.
    }

    public function boot(): void
    {
        //
    }
}
