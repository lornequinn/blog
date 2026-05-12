<?php

declare(strict_types=1);

namespace LorneQuinn\Blog\Core\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use LorneQuinn\Blog\Core\Concerns\HasUniqueSlug;
use LorneQuinn\Blog\Core\Database\Factories\PostFactory;
use LorneQuinn\Blog\Core\Enums\PostStatus;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $body
 * @property string|null $excerpt
 * @property Carbon|null $published_at
 * @property PostStatus $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    use HasUniqueSlug;

    protected $guarded = [];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'status' => PostStatus::class,
            'published_at' => 'datetime',
        ];
    }

    protected function slugSource(): string
    {
        return 'title';
    }

    /** @return BelongsToMany<Tag, $this> */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /** @return HasMany<PostDataLink, $this> */
    public function dataLinks(): HasMany
    {
        return $this->hasMany(PostDataLink::class);
    }

    /** @return Factory<Post> */
    protected static function newFactory(): Factory
    {
        return PostFactory::new();
    }
}
