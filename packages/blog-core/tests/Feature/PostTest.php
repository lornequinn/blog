<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use LorneQuinn\Blog\Core\Enums\PostStatus;
use LorneQuinn\Blog\Core\Models\Post;
use LorneQuinn\Blog\Core\Models\Tag;

uses(RefreshDatabase::class);

it('creates a post via factory with a default draft status', function () {
    $post = Post::factory()->create(['title' => 'Hello World']);

    expect($post->title)->toBe('Hello World');
    expect($post->status)->toBe(PostStatus::Draft);
});

it('auto-generates a slug from the title on creation', function () {
    $post = Post::factory()->create(['title' => 'Hello World']);

    expect($post->slug)->toBe('hello-world');
});

it('preserves an explicitly-set slug', function () {
    $post = Post::factory()->create(['title' => 'Hello World', 'slug' => 'custom-slug']);

    expect($post->slug)->toBe('custom-slug');
});

it('suffixes the slug when there is a collision', function () {
    Post::factory()->create(['title' => 'Hello World']);
    $second = Post::factory()->create(['title' => 'Hello World']);
    $third = Post::factory()->create(['title' => 'Hello World']);

    expect($second->slug)->toBe('hello-world-2');
    expect($third->slug)->toBe('hello-world-3');
});

it('casts published_at to a Carbon instance and status to the enum', function () {
    $post = Post::factory()->create([
        'status' => PostStatus::Published->value,
        'published_at' => '2026-05-13 10:00:00',
    ]);

    expect($post->status)->toBe(PostStatus::Published);
    expect($post->published_at)->toBeInstanceOf(Carbon::class);
});

it('attaches and detaches tags via the tags relationship', function () {
    $post = Post::factory()->create();
    $tag = Tag::factory()->create(['name' => 'php']);

    $post->tags()->attach($tag);
    expect($post->tags()->count())->toBe(1);
    expect($post->tags->first()->name)->toBe('php');

    $post->tags()->detach($tag);
    expect($post->tags()->count())->toBe(0);
});
