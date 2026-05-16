# lornequinn/blog-core

Core package for the LQ blog suite — Post + Tag models, publishing states, DataType + Component registries, and the polymorphic link table that DataType packages plug into.

Part of the modular [`lornequinn/blog`](https://github.com/lornequinn/blog) package suite. This is the only required package; everything else (markdown rendering, MCP editor, SEO, RSS, feeds, search, comments) is an optional add-on.

## Installation

```bash
composer require lornequinn/blog-core
```

Publish migrations and run them:

```bash
php artisan vendor:publish --tag=blog-core-migrations
php artisan migrate
```

(Optional — publish the config to override defaults.)

```bash
php artisan vendor:publish --tag=blog-core-config
```

## What's in the box

### `Post`

`title`, `slug`, `body`, `excerpt`, `published_at`, `status`. Auto-generates a unique slug from the title on creation (`Hello World` → `hello-world`, `hello-world-2`, …). Status is a backed enum — `Draft`, `Scheduled`, `Published`.

```php
use LorneQuinn\Blog\Core\Models\Post;
use LorneQuinn\Blog\Core\Enums\PostStatus;

$post = Post::create([
    'title' => 'Hello World',
    'body' => 'Body markdown goes here.',
]);

$post->update([
    'status' => PostStatus::Published,
    'published_at' => now(),
]);
```

### `Tag`

Flat taxonomy. Same slug auto-generation pattern as `Post`. Pivot table `post_tag`.

```php
use LorneQuinn\Blog\Core\Models\Tag;

$tag = Tag::create(['name' => 'PHP']);
$post->tags()->attach($tag);
```

### `DataTypeRegistry`

A DataType is a structured record attached to a Post — typically the queryable data behind a Component (`RaceResults`, `EpisodeRatings`, `MovieMetadata`). Register your DataTypes in your app's service provider:

```php
use LorneQuinn\Blog\Core\DataType\DataType;
use LorneQuinn\Blog\Core\DataType\DataTypeRegistry;

class RaceResults extends DataType
{
    public function name(): string { return 'race-results'; }
    public function model(): string { return \App\Site\Models\RaceResult::class; }
    public function rules(): array { return [
        'driver' => ['required', 'string'],
        'position' => ['required', 'integer', 'min:1'],
    ]; }
}

// AppServiceProvider::boot()
app(DataTypeRegistry::class)->register(new RaceResults());
```

### `ComponentRegistry`

A Component is a Blade view that renders a DataType. Register Components similarly:

```php
use LorneQuinn\Blog\Core\Component\Component;
use LorneQuinn\Blog\Core\Component\ComponentRegistry;

class FinishingTable extends Component
{
    public function name(): string { return 'finishing-table'; }
    public function dataType(): string { return 'race-results'; }
    public function view(): string { return 'site::components.finishing-table'; }
}

app(ComponentRegistry::class)->register(new FinishingTable());
```

Once registered, the renderer resolves `[[finishing-table]]` shortcodes in a Post body to the matching Component bound against the Post's attached DataType records.

### `ShortcodeParser`

Resolves `[[name attr=value]]` tokens in Post bodies. Names are kebab-case-lowercase. Attribute values can be bare (`a=1`), double-quoted (`a="hello world"`), or single-quoted (`a='single'`). Escape a literal token with a leading backslash: `\[[demo]]` renders as `[[demo]]`. Malformed tokens pass through unchanged.

The parser is wired into the `BodyPipeline` at priority 0 (runs first) by `BlogCoreServiceProvider`. Its resolver looks up Components in the `ComponentRegistry` and renders their Blade views with the parsed attributes as view data.

### `BodyPipeline`

Ordered chain of body renderers. Each renderer is `(string): string`. Register in your service provider:

```php
use LorneQuinn\Blog\Core\Rendering\BodyPipeline;

app(BodyPipeline::class)->register(function (string $body): string {
    return strtoupper($body); // contrived
}, priority: 50);
```

Lower priority runs earlier. Default priority is 0. Within the same priority, registration order is preserved.

Standard pipeline composition (with `lornequinn/blog-markdown` installed):

| Priority | Renderer | Notes |
|----------|----------|-------|
| 0 | `ShortcodeParser` | Resolves `[[…]]` tokens to rendered Component HTML |
| 100 | `MarkdownRenderer` (from `blog-markdown`) | Markdown → HTML, with `html_input='allow'` so resolved HTML survives |

### Route + view

`blog-core` registers `GET /posts/{slug}` → `PostController@show`, named `blog.posts.show`. The controller filters to `PostStatus::Published` with `published_at <= now()`, eager-loads `tags`, runs `body` through the `BodyPipeline`, and renders `blog-core::posts.show`.

Override the view by publishing it:

```bash
php artisan vendor:publish --tag=blog-core-views
```

It lands at `resources/views/vendor/blog-core/posts/show.blade.php`.

### `PostDataLink`

The polymorphic pivot that links a Post to records of *any* DataType. Used directly only by advanced cases (a single Data record referenced from many Posts, e.g. a driver profile). Most attachments use the DataType's own `post_id` foreign key directly.

```php
use LorneQuinn\Blog\Core\Models\PostDataLink;

PostDataLink::create([
    'post_id' => $post->id,
    'data_type' => 'driver-profile',
    'data_id' => $driver->id,
]);
```

## Requirements

- PHP 8.3 or 8.4
- Laravel 12.x or 13.x

## Tests, static analysis, style

```bash
composer install
./vendor/bin/pest
./vendor/bin/phpstan analyse
../../vendor/bin/pint --test --config ../../pint.json
```

PHPStan runs at level 8 with Larastan. Pint enforces strict types, strict params, strict comparisons.

## Scope

`blog-core` is intentionally minimal — Post, Tag, registries, link pivot. No markdown rendering, no MCP, no SEO, no feeds. Those land in separate packages so consumers only ship what they need.

This is a blog package, not a CMS. There is no web admin UI in this package or any related package. The editor surface is `lornequinn/blog-mcp` (when it ships) — Claude talks to your site via MCP, no admin panel involved.

## Licence

MIT.
