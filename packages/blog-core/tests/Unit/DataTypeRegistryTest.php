<?php

declare(strict_types=1);

use LorneQuinn\Blog\Core\DataType\DataType;
use LorneQuinn\Blog\Core\DataType\DataTypeRegistry;

function makeDataType(string $name, string $model = 'App\\Models\\Test'): DataType
{
    return new class($name, $model) extends DataType
    {
        public function __construct(private readonly string $n, private readonly string $m) {}

        public function name(): string
        {
            return $this->n;
        }

        public function model(): string
        {
            return $this->m;
        }

        public function rules(): array
        {
            return [];
        }
    };
}

it('registers a data type and resolves it by name', function () {
    $registry = new DataTypeRegistry;
    $type = makeDataType('test-type');

    $registry->register($type);

    expect($registry->resolve('test-type'))->toBe($type);
});

it('returns null when resolving an unknown name', function () {
    $registry = new DataTypeRegistry;

    expect($registry->resolve('does-not-exist'))->toBeNull();
});

it('returns all registered data types keyed by name', function () {
    $registry = new DataTypeRegistry;
    $a = makeDataType('a');
    $b = makeDataType('b');

    $registry->register($a);
    $registry->register($b);

    expect($registry->all())
        ->toHaveCount(2)
        ->toHaveKeys(['a', 'b'])
        ->and($registry->all()['a'])->toBe($a)
        ->and($registry->all()['b'])->toBe($b);
});

it('overwrites a registration when the same name is registered twice', function () {
    $registry = new DataTypeRegistry;
    $first = makeDataType('dup', 'App\\Models\\First');
    $second = makeDataType('dup', 'App\\Models\\Second');

    $registry->register($first);
    $registry->register($second);

    expect($registry->all())->toHaveCount(1);
    expect($registry->resolve('dup'))->toBe($second);
});

it('reports whether a data type is registered by name', function () {
    $registry = new DataTypeRegistry;
    $registry->register(makeDataType('present'));

    expect($registry->has('present'))->toBeTrue();
    expect($registry->has('absent'))->toBeFalse();
});
