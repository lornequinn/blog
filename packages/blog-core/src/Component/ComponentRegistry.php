<?php

declare(strict_types=1);

namespace LorneQuinn\Blog\Core\Component;

final class ComponentRegistry
{
    /** @var array<string, Component> */
    private array $components = [];

    public function register(Component $component): void
    {
        $this->components[$component->name()] = $component;
    }

    public function resolve(string $name): ?Component
    {
        return $this->components[$name] ?? null;
    }

    public function has(string $name): bool
    {
        return isset($this->components[$name]);
    }

    /** @return array<string, Component> */
    public function all(): array
    {
        return $this->components;
    }

    /** @return array<string, Component> */
    public function forDataType(string $dataType): array
    {
        return array_filter(
            $this->components,
            static fn (Component $c): bool => $c->dataType() === $dataType,
        );
    }
}
