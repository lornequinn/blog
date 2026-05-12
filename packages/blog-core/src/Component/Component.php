<?php

declare(strict_types=1);

namespace LorneQuinn\Blog\Core\Component;

/**
 * Renders a DataType.
 *
 * Each Component subclass declares: a stable kebab-case name used in shortcodes,
 * the DataType it consumes (by name), the Blade view that renders it, and
 * optionally its required and optional shortcode attributes.
 */
abstract class Component
{
    /** Stable kebab-case identifier used in shortcodes — e.g. "finishing-table". */
    abstract public function name(): string;

    /** Name of the DataType this Component consumes. */
    abstract public function dataType(): string;

    /** Blade view path that renders this Component. */
    abstract public function view(): string;

    /**
     * Shortcode attributes that must be present.
     *
     * @return array<int, string>
     */
    public function requiredAttributes(): array
    {
        return [];
    }

    /**
     * Shortcode attributes that are optional, mapped to default values.
     *
     * @return array<string, mixed>
     */
    public function optionalAttributes(): array
    {
        return [];
    }
}
