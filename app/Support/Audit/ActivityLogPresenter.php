<?php

namespace App\Support\Audit;

use Illuminate\Support\Str;

class ActivityLogPresenter
{
    public static function eventLabel(string $event): string
    {
        return match ($event) {
            ActivityEvent::UserCreated => 'User created',
            ActivityEvent::UserDeactivated => 'User deactivated',
            ActivityEvent::UserReactivated => 'User reactivated',
            ActivityEvent::RoleAssigned => 'Role assigned',
            ActivityEvent::RolePermissionChanged => 'Role permissions changed',
            ActivityEvent::PostPublished => 'Post published',
            ActivityEvent::PostArchived => 'Post archived',
            ActivityEvent::PostScheduled => 'Post scheduled',
            ActivityEvent::PagePublished => 'Page published',
            ActivityEvent::PageArchived => 'Page archived',
            ActivityEvent::CommentModerated => 'Comment moderated',
            ActivityEvent::SettingsUpdated => 'Settings updated',
            ActivityEvent::MediaDeleted => 'Media deleted',
            ActivityEvent::MenuUpdated => 'Menu updated',
            default => Str::title(str_replace('_', ' ', strtolower($event))),
        };
    }

    public static function subjectLabel(?string $subjectType, ?int $subjectId): string
    {
        if ($subjectType === null || $subjectId === null) {
            return '—';
        }

        $label = match ($subjectType) {
            'user' => 'User',
            'role' => 'Role',
            'media' => 'Media',
            'post' => 'Post',
            'page' => 'Page',
            'comment' => 'Comment',
            'menu' => 'Menu',
            'site_setting' => 'Site settings',
            default => Str::title(str_replace('_', ' ', $subjectType)),
        };

        return "{$label} #{$subjectId}";
    }

    /**
     * @param  array<string, mixed>|null  $properties
     */
    public static function propertiesSummary(?array $properties): string
    {
        if ($properties === null || $properties === []) {
            return '—';
        }

        $parts = [];

        foreach ($properties as $key => $value) {
            if (is_scalar($value) || $value === null) {
                $parts[] = $key.': '.($value ?? 'null');
            }
        }

        if ($parts === []) {
            return 'View details';
        }

        return Str::limit(implode(', ', $parts), 100);
    }

    /**
     * @param  array<string, mixed>|null  $properties
     */
    public static function propertiesForDisplay(?array $properties): string
    {
        if ($properties === null || $properties === []) {
            return '{}';
        }

        return json_encode($properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '{}';
    }
}
