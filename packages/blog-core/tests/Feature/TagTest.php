<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use LorneQuinn\Blog\Core\Models\Post;
use LorneQuinn\Blog\Core\Models\Tag;

uses(RefreshDatabase::class);

it('creates a tag via factory with auto-generated slug', function () {
    $tag = Tag::factory()->create(['name' => 'Object-Oriented PHP']);

    expect($tag->name)->toBe('Object-Oriented PHP');
    expect($tag->slug)->toBe('object-oriented-php');
});

it('preserves an explicitly-set tag slug', function () {
    $tag = Tag::factory()->create(['name' => 'PHP', 'slug' => 'php-lang']);

    expect($tag->slug)->toBe('php-lang');
});

it('enforces unique slugs on tags by suffixing', function () {
    Tag::factory()->create(['name' => 'PHP']);
    $second = Tag::factory()->create(['name' => 'PHP']);

    expect($second->slug)->toBe('php-2');
});

it('lists posts associated with a tag via posts relationship', function () {
    $tag = Tag::factory()->create();
    $a = Post::factory()->create();
    $b = Post::factory()->create();

    $tag->posts()->attach([$a->id, $b->id]);

    expect($tag->posts()->count())->toBe(2);
});
