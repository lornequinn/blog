<?php

declare(strict_types=1);

use LorneQuinn\Blog\Core\Shortcode\ShortcodeParser;

it('replaces a simple shortcode via the resolver', function () {
    $parser = new ShortcodeParser(fn (string $name, array $attrs): string => "<{$name}/>");

    expect($parser->parse('hello [[demo]] world'))
        ->toBe('hello <demo/> world');
});

it('passes the shortcode name to the resolver', function () {
    $capturedName = null;
    $parser = new ShortcodeParser(function (string $name, array $attrs) use (&$capturedName): string {
        $capturedName = $name;

        return '';
    });

    $parser->parse('[[finishing-table]]');

    expect($capturedName)->toBe('finishing-table');
});

it('passes bare-value attributes to the resolver', function () {
    $capturedAttrs = null;
    $parser = new ShortcodeParser(function (string $name, array $attrs) use (&$capturedAttrs): string {
        $capturedAttrs = $attrs;

        return '';
    });

    $parser->parse('[[demo key=value]]');

    expect($capturedAttrs)->toBe(['key' => 'value']);
});

it('parses double-quoted attribute values with spaces', function () {
    $capturedAttrs = null;
    $parser = new ShortcodeParser(function (string $name, array $attrs) use (&$capturedAttrs): string {
        $capturedAttrs = $attrs;

        return '';
    });

    $parser->parse('[[demo title="hello world"]]');

    expect($capturedAttrs)->toBe(['title' => 'hello world']);
});

it('parses single-quoted attribute values', function () {
    $capturedAttrs = null;
    $parser = new ShortcodeParser(function (string $name, array $attrs) use (&$capturedAttrs): string {
        $capturedAttrs = $attrs;

        return '';
    });

    $parser->parse("[[demo a='single quoted']]");

    expect($capturedAttrs)->toBe(['a' => 'single quoted']);
});

it('parses a mix of bare and quoted attributes', function () {
    $capturedAttrs = null;
    $parser = new ShortcodeParser(function (string $name, array $attrs) use (&$capturedAttrs): string {
        $capturedAttrs = $attrs;

        return '';
    });

    $parser->parse('[[demo a=1 b="two" c=3]]');

    expect($capturedAttrs)->toBe(['a' => '1', 'b' => 'two', 'c' => '3']);
});

it('preserves a backslash-escaped shortcode as a literal', function () {
    $parser = new ShortcodeParser(fn (string $name, array $attrs): string => 'CALLED');

    expect($parser->parse('\[[demo]]'))->toBe('[[demo]]');
});

it('does not invoke the resolver for escaped shortcodes', function () {
    $callCount = 0;
    $parser = new ShortcodeParser(function (string $name, array $attrs) use (&$callCount): string {
        $callCount++;

        return 'CALLED';
    });

    $parser->parse('\[[demo]] [[demo]]');

    expect($callCount)->toBe(1);
});

it('replaces multiple shortcodes in one body', function () {
    $parser = new ShortcodeParser(fn (string $name, array $attrs): string => "<{$name}/>");

    expect($parser->parse('one [[a]] two [[b]] three'))
        ->toBe('one <a/> two <b/> three');
});

it('passes through text with no shortcodes unchanged', function () {
    $parser = new ShortcodeParser(fn (string $name, array $attrs): string => 'NEVER');

    expect($parser->parse('just plain markdown body'))
        ->toBe('just plain markdown body');
});

it('passes through malformed (unclosed) shortcodes unchanged', function () {
    $parser = new ShortcodeParser(fn (string $name, array $attrs): string => 'NEVER');

    expect($parser->parse('look at [[missing-close and other text'))
        ->toBe('look at [[missing-close and other text');
});

it('passes through shortcodes with empty names unchanged', function () {
    $parser = new ShortcodeParser(fn (string $name, array $attrs): string => 'NEVER');

    expect($parser->parse('[[]]'))->toBe('[[]]');
});

it('does not collide with markdown link syntax', function () {
    $parser = new ShortcodeParser(fn (string $name, array $attrs): string => 'NEVER');
    $markdownInput = 'a [link](https://example.com) and a [ref][1]';

    expect($parser->parse($markdownInput))->toBe($markdownInput);
});

it('handles a shortcode that wraps content (newline-separated)', function () {
    $parser = new ShortcodeParser(fn (string $name, array $attrs): string => "<{$name}/>");

    $body = "Paragraph one.\n\n[[chart]]\n\nParagraph two.";

    expect($parser->parse($body))->toBe("Paragraph one.\n\n<chart/>\n\nParagraph two.");
});
