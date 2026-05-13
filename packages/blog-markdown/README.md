# lornequinn/blog-markdown

Markdown body renderer for the [LQ blog suite](https://github.com/lornequinn/blog). CommonMark-backed pre-pass on the body pipeline — turns markdown into HTML before `lornequinn/blog-core`'s `ShortcodeParser` resolves `[[…]]` tokens.

## Installation

```bash
composer require lornequinn/blog-markdown
```

The service provider auto-registers via Laravel package discovery. No publishing required.

## What it does

When installed alongside `lornequinn/blog-core`, posts whose `body` contains markdown get rendered to HTML on `GET /posts/{slug}`. The pipeline order is:

```
raw markdown body
  → blog-markdown   (markdown → HTML via League/CommonMark)
  → blog-core       (shortcode parser: [[name]] resolution)
  → output HTML
```

Shortcodes pass through this package untouched — they're handled in `blog-core`'s post-processing pass. If you drop `blog-markdown`, raw HTML in `body` still renders fine and shortcodes still work.

## Markdown flavour

Default CommonMark — headings, paragraphs, lists, code blocks, inline code, links, images, blockquotes, emphasis, strong. No GFM tables or task lists by default (add them via the markdown package config in a future release if needed).

## Requirements

- PHP 8.3 or 8.4
- Laravel 12.x or 13.x
- `lornequinn/blog-core ^0.1`

## Licence

MIT.
