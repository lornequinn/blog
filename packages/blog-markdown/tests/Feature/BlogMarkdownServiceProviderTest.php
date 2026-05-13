<?php

declare(strict_types=1);

use LorneQuinn\Blog\Core\BlogCoreServiceProvider;
use LorneQuinn\Blog\Markdown\BlogMarkdownServiceProvider;

it('boots without errors when registered alongside blog-core', function () {
    expect(app()->getProvider(BlogMarkdownServiceProvider::class))->not->toBeNull();
    expect(app()->getProvider(BlogCoreServiceProvider::class))->not->toBeNull();
});
