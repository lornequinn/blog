<?php

declare(strict_types=1);

namespace LorneQuinn\Blog\Core\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use LorneQuinn\Blog\Core\Concerns\HasUniqueSlug;
use LorneQuinn\Blog\Core\Database\Factories\TagFactory;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Tag extends Model
{
    /** @use HasFactory<TagFactory> */
    use HasFactory;

    use HasUniqueSlug;

    protected $guarded = [];

    protected function slugSource(): string
    {
        return 'name';
    }

    /** @return BelongsToMany<Post, $this> */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }

    /** @return Factory<Tag> */
    protected static function newFactory(): Factory
    {
        return TagFactory::new();
    }
}
