<?php

declare(strict_types=1);

namespace LorneQuinn\Blog\Core\DataType;

final class DataTypeRegistry
{
    /** @var array<string, DataType> */
    private array $types = [];

    public function register(DataType $type): void
    {
        $this->types[$type->name()] = $type;
    }

    public function resolve(string $name): ?DataType
    {
        return $this->types[$name] ?? null;
    }

    public function has(string $name): bool
    {
        return isset($this->types[$name]);
    }

    /** @return array<string, DataType> */
    public function all(): array
    {
        return $this->types;
    }
}
