<?php

declare(strict_types=1);

use LorneQuinn\Blog\Markdown\MarkdownRenderer;

it('binds MarkdownRenderer as a singleton', function () {
    expect(app(MarkdownRenderer::class))
        ->toBeInstanceOf(MarkdownRenderer::class)
        ->toBe(app(MarkdownRenderer::class));
});

it('renders markdown via the container-resolved renderer', function () {
    $output = app(MarkdownRenderer::class)->render('# Hello');

    expect($output)->toContain('<h1>Hello</h1>');
});
