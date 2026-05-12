<?php

declare(strict_types=1);

namespace LorneQuinn\Blog\Core\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use LorneQuinn\Blog\Core\Enums\PostStatus;
use LorneQuinn\Blog\Core\Models\Post;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'body' => $this->faker->paragraphs(3, true),
            'excerpt' => $this->faker->sentence(),
            'status' => PostStatus::Draft->value,
            'published_at' => null,
        ];
    }
}
