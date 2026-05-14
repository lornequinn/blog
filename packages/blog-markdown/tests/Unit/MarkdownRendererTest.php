<?php

declare(strict_types=1);

use League\CommonMark\CommonMarkConverter;
use LorneQuinn\Blog\Markdown\MarkdownRenderer;

function renderer(): MarkdownRenderer
{
    return new MarkdownRenderer(new CommonMarkConverter);
}

it('renders a plain paragraph', function () {
    expect(renderer()->render('Hello world'))->toContain('<p>Hello world</p>');
});

it('renders headings', function () {
    expect(renderer()->render('# Hello'))->toContain('<h1>Hello</h1>');
    expect(renderer()->render('## Hello'))->toContain('<h2>Hello</h2>');
});

it('renders inline emphasis and strong', function () {
    expect(renderer()->render('*italic* and **bold**'))
        ->toContain('<em>italic</em>')
        ->toContain('<strong>bold</strong>');
});

it('renders inline code', function () {
    expect(renderer()->render('use `array_filter()`'))
        ->toContain('<code>array_filter()</code>');
});

it('renders fenced code blocks', function () {
    $markdown = "```\n\$x = 1;\n```";

    expect(renderer()->render($markdown))
        ->toContain('<pre><code>')
        ->toContain('$x = 1;');
});

it('renders links', function () {
    expect(renderer()->render('[Lorne](https://lornequinn.com)'))
        ->toContain('<a href="https://lornequinn.com">Lorne</a>');
});

it('renders unordered lists', function () {
    $markdown = "- one\n- two\n- three";

    expect(renderer()->render($markdown))
        ->toContain('<ul>')
        ->toContain('<li>one</li>')
        ->toContain('<li>three</li>');
});

it('renders ordered lists', function () {
    $markdown = "1. one\n2. two";

    expect(renderer()->render($markdown))
        ->toContain('<ol>')
        ->toContain('<li>one</li>');
});

it('passes shortcode markers through unchanged for the shortcode parser to handle', function () {
    expect(renderer()->render('Body before [[demo]] body after'))
        ->toContain('[[demo]]');
});

it('preserves shortcode markers with bare-value attributes verbatim', function () {
    expect(renderer()->render('[[finishing-table highlight=Murphy]]'))
        ->toContain('[[finishing-table highlight=Murphy]]');
});

it('html-encodes quotes inside shortcode markers (correct CommonMark text-escape behaviour)', function () {
    // CommonMark always HTML-escapes `"` to `&quot;` in text content. The
    // shortcode parser in CP 30's pipeline integration is responsible for
    // either entity-decoding before matching, or running before markdown
    // with html_input=allow. Asserting the escape here so we don't drift.
    $rendered = renderer()->render('[[demo title="hello world"]]');

    expect($rendered)->toContain('[[demo title=')
        ->and($rendered)->toContain(']]')
        ->and($rendered)->toContain('&quot;');
});

it('returns a string', function () {
    expect(renderer()->render('anything'))->toBeString();
});
