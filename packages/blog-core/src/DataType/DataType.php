<?php

declare(strict_types=1);

namespace LorneQuinn\Blog\Core\DataType;

/**
 * Structured data records attached to a Post.
 *
 * Each DataType subclass declares: a stable kebab-case name, the Eloquent model
 * that stores its records, validation rules enforced on attach/update, and
 * optionally a primary Component to render with.
 */
abstract class DataType
{
    /** Stable kebab-case identifier — e.g. "race-results". */
    abstract public function name(): string;

    /** Fully-qualified Eloquent model class that stores records of this DataType. */
    abstract public function model(): string;

    /**
     * Laravel validator rules enforced when attaching or updating a record.
     *
     * @return array<string, mixed>
     */
    abstract public function rules(): array;

    /**
     * Component name that renders this DataType by default. Optional —
     * consumers may register additional Components for the same DataType.
     */
    public function primaryComponent(): ?string
    {
        return null;
    }
}
