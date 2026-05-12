<?php

declare(strict_types=1);

use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LorneQuinn\Blog\Core\Models\Post;
use LorneQuinn\Blog\Core\Models\PostDataLink;

uses(RefreshDatabase::class);

it('creates a post-data link by post + data type + data id', function () {
    $post = Post::factory()->create();

    $link = PostDataLink::create([
        'post_id' => $post->id,
        'data_type' => 'race-results',
        'data_id' => 42,
    ]);

    expect($link->exists)->toBeTrue();
    expect($link->post_id)->toBe($post->id);
    expect($link->data_type)->toBe('race-results');
    expect($link->data_id)->toBe(42);
});

it('exposes a post relationship from the link back to the post', function () {
    $post = Post::factory()->create();

    $link = PostDataLink::create([
        'post_id' => $post->id,
        'data_type' => 'race-results',
        'data_id' => 1,
    ]);

    expect($link->post->is($post))->toBeTrue();
});

it('lists all links from a post via dataLinks relationship', function () {
    $post = Post::factory()->create();

    PostDataLink::create(['post_id' => $post->id, 'data_type' => 'race-results', 'data_id' => 1]);
    PostDataLink::create(['post_id' => $post->id, 'data_type' => 'episode-ratings', 'data_id' => 2]);

    expect($post->dataLinks()->count())->toBe(2);
});

it('prevents duplicate (post, data_type, data_id) triples', function () {
    $post = Post::factory()->create();

    PostDataLink::create(['post_id' => $post->id, 'data_type' => 'race-results', 'data_id' => 7]);

    expect(fn () => PostDataLink::create([
        'post_id' => $post->id,
        'data_type' => 'race-results',
        'data_id' => 7,
    ]))->toThrow(UniqueConstraintViolationException::class);
});
