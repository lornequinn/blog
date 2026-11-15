<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use LorneQuinn\Blog\Core\Component\Component;
use LorneQuinn\Blog\Core\Component\ComponentRegistry;
use LorneQuinn\Blog\Core\Enums\PostStatus;
use LorneQuinn\Blog\Core\Models\Post;
use LorneQuinn\Blog\Core\Rendering\BodyPipeline;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('renders a published post on GET /posts/{slug}', function () {
    $post = Post::factory()->create([
        'title' => 'Hello World',
        'body' => 'A simple body.',
        'status' => PostStatus::Published,
        'published_at' => now()->subDay(),
    ]);

    $response = get("/posts/{$post->slug}");

    $response->assertOk();
    $response->assertSee('Hello World');
    $response->assertSee('A simple body.', false);
});

it('renders a standalone page when no layout is configured', function () {
    $post = Post::factory()->create([
        'title' => 'Standalone',
        'body' => 'Body.',
        'status' => PostStatus::Published,
        'published_at' => now()->subDay(),
    ]);

    get("/posts/{$post->slug}")
        ->assertOk()
        ->assertSee('<!doctype html>', false)
        ->assertDontSee('consumer-layout', false);
});

it('renders inside the consumer layout when one is configured', function () {
    config(['blog-core.layout' => 'blog-core-test::layout']);

    $post = Post::factory()->create([
        'title' => 'In Layout',
        'body' => 'Wrapped **body**.',
        'status' => PostStatus::Published,
        'published_at' => now()->subDay(),
    ]);

    get("/posts/{$post->slug}")
        ->assertOk()
        ->assertSee('<div id="consumer-layout">', false)
        ->assertSee('<title>In Layout</title>', false)
        ->assertSee('<h1>In Layout</h1>', false)
        ->assertSee('Wrapped', false)
        ->assertDontSee('<!doctype html>', false);
});

it('yields into the configured layout section', function () {
    config([
        'blog-core.layout' => 'blog-core-test::main-layout',
        'blog-core.layout_section' => 'main',
    ]);

    $post = Post::factory()->create([
        'title' => 'Custom Section',
        'status' => PostStatus::Published,
        'published_at' => now()->subDay(),
    ]);

    get("/posts/{$post->slug}")
        ->assertOk()
        ->assertSee('<div id="consumer-main-layout">', false)
        ->assertSee('<h1>Custom Section</h1>', false);
});

it('returns 404 for a draft post', function () {
    $post = Post::factory()->create([
        'status' => PostStatus::Draft,
    ]);

    get("/posts/{$post->slug}")->assertNotFound();
});

it('returns 404 for a scheduled-future post', function () {
    $post = Post::factory()->create([
        'status' => PostStatus::Scheduled,
        'published_at' => now()->addWeek(),
    ]);

    get("/posts/{$post->slug}")->assertNotFound();
});

it('returns 404 for an unknown slug', function () {
    get('/posts/does-not-exist')->assertNotFound();
});

it('runs the body through the BodyPipeline renderers', function () {
    $pipeline = app(BodyPipeline::class);
    $pipeline->register(fn (string $body): string => strtoupper($body), 999);

    $post = Post::factory()->create([
        'title' => 'Pipeline Test',
        'body' => 'whisper',
        'status' => PostStatus::Published,
        'published_at' => now()->subHour(),
    ]);

    get("/posts/{$post->slug}")
        ->assertOk()
        ->assertSee('WHISPER', false);
});

it('resolves a registered shortcode through the wired-up parser', function () {
    $registry = app(ComponentRegistry::class);
    $stub = new class extends Component
    {
        public function name(): string
        {
            return 'demo';
        }

        public function dataType(): string
        {
            return 'demo-data';
        }

        public function view(): string
        {
            return 'blog-core-test::demo';
        }
    };
    $registry->register($stub);

    $post = Post::factory()->create([
        'title' => 'Shortcode Test',
        'body' => 'Before [[demo]] after',
        'status' => PostStatus::Published,
        'published_at' => now()->subHour(),
    ]);

    get("/posts/{$post->slug}")
        ->assertOk()
        ->assertSee('STUB COMPONENT OUTPUT', false);
});
