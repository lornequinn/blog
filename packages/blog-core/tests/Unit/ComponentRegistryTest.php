<?php

declare(strict_types=1);

use LorneQuinn\Blog\Core\Component\Component;
use LorneQuinn\Blog\Core\Component\ComponentRegistry;

function makeComponent(string $name, string $dataType, string $view = 'test.view'): Component
{
    return new class($name, $dataType, $view) extends Component
    {
        public function __construct(
            private readonly string $n,
            private readonly string $dt,
            private readonly string $v,
        ) {}

        public function name(): string
        {
            return $this->n;
        }

        public function dataType(): string
        {
            return $this->dt;
        }

        public function view(): string
        {
            return $this->v;
        }
    };
}

it('registers a component and resolves it by name', function () {
    $registry = new ComponentRegistry;
    $component = makeComponent('finishing-table', 'race-results');

    $registry->register($component);

    expect($registry->resolve('finishing-table'))->toBe($component);
});

it('returns null when resolving an unknown name', function () {
    $registry = new ComponentRegistry;

    expect($registry->resolve('does-not-exist'))->toBeNull();
});

it('returns all registered components keyed by name', function () {
    $registry = new ComponentRegistry;
    $a = makeComponent('a', 'race-results');
    $b = makeComponent('b', 'episode-ratings');

    $registry->register($a);
    $registry->register($b);

    expect($registry->all())
        ->toHaveCount(2)
        ->toHaveKeys(['a', 'b']);
});

it('returns only components that consume a given data type', function () {
    $registry = new ComponentRegistry;
    $finishingTable = makeComponent('finishing-table', 'race-results');
    $podiumStrip = makeComponent('podium-strip', 'race-results');
    $flwbrGrid = makeComponent('flwbr-grid', 'episode-ratings');

    $registry->register($finishingTable);
    $registry->register($podiumStrip);
    $registry->register($flwbrGrid);

    $forRaceResults = $registry->forDataType('race-results');

    expect($forRaceResults)
        ->toHaveCount(2)
        ->toHaveKeys(['finishing-table', 'podium-strip']);
    expect($registry->forDataType('episode-ratings'))
        ->toHaveCount(1)
        ->toHaveKey('flwbr-grid');
    expect($registry->forDataType('nothing'))->toBe([]);
});

it('reports whether a component is registered by name', function () {
    $registry = new ComponentRegistry;
    $registry->register(makeComponent('present', 'dt'));

    expect($registry->has('present'))->toBeTrue();
    expect($registry->has('absent'))->toBeFalse();
});
