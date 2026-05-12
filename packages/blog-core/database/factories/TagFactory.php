<?php

declare(strict_types=1);

namespace LorneQuinn\Blog\Core\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use LorneQuinn\Blog\Core\Models\Tag;

/**
 * @extends Factory<Tag>
 */
class TagFactory extends Factory
{
    protected $model = Tag::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
        ];
    }
}
