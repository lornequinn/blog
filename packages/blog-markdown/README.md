# lornequinn/blog-markdown

Markdown body renderer for the [LQ blog suite](https://github.com/lornequinn/blog). CommonMark-backed pass on the body pipeline — turns markdown into HTML *after* `lornequinn/blog-core`'s `ShortcodeParser` resolves `[[…]]` tokens, with `html_input='allow'` so resolved HTML survives.

## Installation

```bash
composer require lornequinn/blog-markdown
```

The service provider auto-registers via Laravel package discovery. No publishing required.

## What it does

When installed alongside `lornequinn/blog-core`, posts whose `body` contains markdown render at `GET /posts/{slug}` with markdown converted to HTML. The pipeline order is:

```
raw body
  → blog-core      (shortcode parser: [[name attr=value]] → Component HTML)   priority 0
  → blog-markdown  (markdown → HTML via League/CommonMark, html_input=allow)  priority 100
  → output HTML
```

**Shortcode first, markdown second.** This is opposite to the natural "render markdown then resolve shortcodes" reading because CommonMark HTML-escapes `"` to `&quot;` in any text — which would break the shortcode parser's regex on quoted attribute values. Running shortcodes against the raw body and then letting markdown pass the resolved HTML through (`html_input='allow'`) avoids the entity-escape dance and produces cleaner output for block-level Components.

If you drop `blog-markdown`, raw HTML in `body` still renders fine via `blog-core` alone, and shortcodes still work.

## Markdown flavour

Default CommonMark — headings, paragraphs, lists, code blocks, inline code, links, images, blockquotes, emphasis, strong. No GFM tables or task lists by default (add them via the markdown package config in a future release if needed).

The CommonMark converter ships with:

- `html_input` = `'allow'` — lets shortcode-resolved HTML pass through
- `allow_unsafe_links` = `false` — blocks `javascript:` and `data:` URLs

## Pipeline priority

`BlogMarkdownServiceProvider::PIPELINE_PRIORITY` is `100`. Insert custom passes between shortcode (priority 0) and markdown (priority 100) with any value in that range. Insert post-markdown passes with anything > 100.

## Requirements

- PHP 8.3 or 8.4
- Laravel 12.x or 13.x
- `lornequinn/blog-core ^0.1`
- `league/commonmark ^2.0`

## Licence

MIT.
