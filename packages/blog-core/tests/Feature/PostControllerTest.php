<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\View;
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

    // Register an inline view source so the component has something to render.
    View::addNamespace(
        'blog-core-test',
        __DIR__.'/stubs/views',
    );

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
