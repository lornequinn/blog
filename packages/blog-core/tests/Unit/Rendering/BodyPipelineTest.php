<?php

declare(strict_types=1);

use LorneQuinn\Blog\Core\Rendering\BodyPipeline;

it('returns the body unchanged when no renderers are registered', function () {
    $pipeline = new BodyPipeline;

    expect($pipeline->run('hello'))->toBe('hello');
});

it('runs a single registered renderer', function () {
    $pipeline = new BodyPipeline;
    $pipeline->register(fn (string $body): string => strtoupper($body));

    expect($pipeline->run('hello'))->toBe('HELLO');
});

it('runs multiple renderers in ascending priority order (lower runs first)', function () {
    $pipeline = new BodyPipeline;
    $pipeline->register(fn (string $body): string => $body.'-late', 100);
    $pipeline->register(fn (string $body): string => $body.'-early', 0);

    expect($pipeline->run('seed'))->toBe('seed-early-late');
});

it('preserves registration order within the same priority', function () {
    $pipeline = new BodyPipeline;
    $pipeline->register(fn (string $body): string => $body.'-A', 0);
    $pipeline->register(fn (string $body): string => $body.'-B', 0);

    expect($pipeline->run('seed'))->toBe('seed-A-B');
});

it('treats default priority as 0', function () {
    $pipeline = new BodyPipeline;
    $pipeline->register(fn (string $body): string => $body.'-mid'); // default 0
    $pipeline->register(fn (string $body): string => $body.'-late', 100);
    $pipeline->register(fn (string $body): string => $body.'-early', -100);

    expect($pipeline->run('seed'))->toBe('seed-early-mid-late');
});

it('passes the renderer output as the next renderer input (real chain)', function () {
    $pipeline = new BodyPipeline;
    $pipeline->register(fn (string $body): string => str_replace('hello', 'HOWDY', $body), 0);
    $pipeline->register(fn (string $body): string => $body.'!!!', 10);

    expect($pipeline->run('hello world'))->toBe('HOWDY world!!!');
});
