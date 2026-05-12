<?php

declare(strict_types=1);

namespace LorneQuinn\Blog\Core\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Auto-generates a URL slug from a source field on `creating`,
 * suffixing with `-2`, `-3`, … to avoid collisions on the same table.
 *
 * Models using this trait must implement `slugSource(): string` returning
 * the name of the field to slugify (e.g. "title" for Post, "name" for Tag).
 */
trait HasUniqueSlug
{
    abstract protected function slugSource(): string;

    protected static function bootHasUniqueSlug(): void
    {
        static::creating(function (Model $model): void {
            if (! method_exists($model, 'slugSource')) {
                return;
            }
            if (! empty($model->getAttribute('slug'))) {
                return;
            }

            $source = (string) ($model->getAttribute($model->slugSource()) ?? '');
            $model->setAttribute('slug', self::generateUniqueSlugFor($model, $source));
        });
    }

    private static function generateUniqueSlugFor(Model $model, string $source): string
    {
        $base = Str::slug($source);
        $slug = $base === '' ? 'untitled' : $base;
        $suffix = 2;

        while ($model::query()->where('slug', $slug)->exists()) {
            $slug = ($base === '' ? 'untitled' : $base).'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
