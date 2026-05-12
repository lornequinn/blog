<?php

declare(strict_types=1);

use LorneQuinn\Blog\Core\Enums\PostStatus;

it('has draft, scheduled, and published cases backed by string values', function () {
    expect(PostStatus::Draft->value)->toBe('draft');
    expect(PostStatus::Scheduled->value)->toBe('scheduled');
    expect(PostStatus::Published->value)->toBe('published');
});

it('exposes exactly three statuses', function () {
    expect(PostStatus::cases())->toHaveCount(3);
});
