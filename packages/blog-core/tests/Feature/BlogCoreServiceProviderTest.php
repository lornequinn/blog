<?php

declare(strict_types=1);

use LorneQuinn\Blog\Core\Component\ComponentRegistry;
use LorneQuinn\Blog\Core\DataType\DataTypeRegistry;

it('binds the data type registry as a singleton', function () {
    expect(app(DataTypeRegistry::class))
        ->toBeInstanceOf(DataTypeRegistry::class)
        ->toBe(app(DataTypeRegistry::class));
});

it('binds the component registry as a singleton', function () {
    expect(app(ComponentRegistry::class))
        ->toBeInstanceOf(ComponentRegistry::class)
        ->toBe(app(ComponentRegistry::class));
});

it('exposes its config under the blog-core namespace', function () {
    expect(config('blog-core'))->toBeArray();
});
