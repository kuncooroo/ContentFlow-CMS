<?php

namespace App\Enums;

enum PageStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';

    public function isDraft(): bool
    {
        return $this === self::Draft;
    }

    public function isPublished(): bool
    {
        return $this === self::Published;
    }

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Published => 'Published',
            self::Archived => 'Archived',
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::Draft => $target === self::Published,
            self::Published => in_array($target, [self::Archived, self::Draft], true),
            self::Archived => $target === self::Draft,
        };
    }
}
