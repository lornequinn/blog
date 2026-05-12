<?php

declare(strict_types=1);

namespace LorneQuinn\Blog\Core\Enums;

enum PostStatus: string
{
    case Draft = 'draft';
    case Scheduled = 'scheduled';
    case Published = 'published';
}
