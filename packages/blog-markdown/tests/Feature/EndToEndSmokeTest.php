<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\View;
use LorneQuinn\Blog\Core\Component\Component;
use LorneQuinn\Blog\Core\Component\ComponentRegistry;
use LorneQuinn\Blog\Core\Enums\PostStatus;
use LorneQuinn\Blog\Core\Models\Post;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

function registerStubComponent(string $name, string $view): void
{
    $stub = new class($name, $view) extends Component
    {
        public function __construct(
            private readonly string $stubName,
            private readonly string $stubView,
        ) {}

        public function name(): string
        {
            return $this->stubName;
        }

        public function dataType(): string
        {
            return 'stub-data';
        }

        public function view(): string
        {
            return $this->stubView;
        }
    };

    app(ComponentRegistry::class)->register($stub);
    View::addNamespace('smoke-test', __DIR__.'/stubs/views');
}

it('renders a Post with both markdown body and shortcode resolution end-to-end', function () {
    registerStubComponent('demo', 'smoke-test::demo');

    $post = Post::factory()->create([
        'title' => 'Smoke Test Post',
        'body' => "# Heading\n\nA paragraph before.\n\n[[demo]]\n\nA paragraph after with **strong** text.",
        'status' => PostStatus::Published,
        'published_at' => now()->subHour(),
    ]);

    $response = get("/posts/{$post->slug}");

    $response->assertOk()
        ->assertSee('<h1>Heading</h1>', false)
        ->assertSee('<p>A paragraph before.</p>', false)
        ->assertSee('<div class="demo-component">DEMO COMPONENT HTML</div>', false)
        ->assertSee('<strong>strong</strong>', false);
});

it('passes quoted shortcode attributes through to the rendered Component', function () {
    registerStubComponent('titled', 'smoke-test::titled');

    $post = Post::factory()->create([
        'title' => 'Quote Smoke',
        'body' => '[[titled label="Hello World"]]',
        'status' => PostStatus::Published,
        'published_at' => now()->subHour(),
    ]);

    get("/posts/{$post->slug}")
        ->assertOk()
        ->assertSee('<div class="titled-component">Hello World</div>', false);
});

it('does not html-escape shortcode-rendered block HTML', function () {
    // The html_input='allow' setting on CommonMark is what makes this work —
    // shortcode-replaced HTML must survive the markdown pass intact, otherwise
    // we get &lt;div&gt; in the final output.
    registerStubComponent('demo', 'smoke-test::demo');

    $post = Post::factory()->create([
        'body' => "Text before.\n\n[[demo]]\n\nText after.",
        'status' => PostStatus::Published,
        'published_at' => now()->subHour(),
    ]);

    get("/posts/{$post->slug}")
        ->assertOk()
        ->assertSee('<div class="demo-component">', false)
        ->assertDontSee('&lt;div', false);
});

it('renders a Post with no shortcodes (pure markdown) end-to-end', function () {
    $post = Post::factory()->create([
        'title' => 'Plain Post',
        'body' => '# Heading only.',
        'status' => PostStatus::Published,
        'published_at' => now()->subHour(),
    ]);

    get("/posts/{$post->slug}")
        ->assertOk()
        ->assertSee('<h1>Heading only.</h1>', false);
});
