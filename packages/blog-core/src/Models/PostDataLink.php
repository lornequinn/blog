<?php

declare(strict_types=1);

namespace LorneQuinn\Blog\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Pivot row linking a Post to a DataType record.
 *
 * Stored in `post_data_links` as (post_id, data_type, data_id) with a unique
 * triple. The polymorphic Data record is resolved by the consumer using the
 * DataType registry — this model holds the link only.
 *
 * @property int $id
 * @property int $post_id
 * @property string $data_type
 * @property int $data_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Post $post
 */
class PostDataLink extends Model
{
    protected $guarded = [];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'post_id' => 'integer',
            'data_id' => 'integer',
        ];
    }

    /** @return BelongsTo<Post, $this> */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
