<?php

namespace App\Enums;

enum CommentStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Spam = 'spam';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Approved => 'Approved',
            self::Spam => 'Spam',
            self::Rejected => 'Rejected',
        };
    }

    public function isPubliclyVisible(): bool
    {
        return $this === self::Approved;
    }

    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::Pending => in_array($target, [self::Approved, self::Spam, self::Rejected], true),
            self::Approved => in_array($target, [self::Rejected, self::Spam], true),
            self::Rejected => $target === self::Approved,
            self::Spam => in_array($target, [self::Approved, self::Rejected], true),
        };
    }
}
