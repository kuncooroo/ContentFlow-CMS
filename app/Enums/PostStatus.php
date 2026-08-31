<?php

namespace App\Enums;

enum PostStatus: string
{
    case Draft = 'draft';
    case Scheduled = 'scheduled';
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
            self::Scheduled => 'Scheduled',
            self::Published => 'Published',
            self::Archived => 'Archived',
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::Draft => in_array($target, [self::Scheduled, self::Published], true),
            self::Scheduled => in_array($target, [self::Draft, self::Published], true),
            self::Published => in_array($target, [self::Archived, self::Draft], true),
            self::Archived => $target === self::Draft,
        };
    }
}
