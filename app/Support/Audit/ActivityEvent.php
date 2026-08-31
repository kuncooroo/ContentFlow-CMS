<?php

namespace App\Support\Audit;

class ActivityEvent
{
    public const UserCreated = 'USER_CREATED';

    public const UserDeactivated = 'USER_DEACTIVATED';

    public const UserReactivated = 'USER_REACTIVATED';

    public const RoleAssigned = 'ROLE_ASSIGNED';

    public const RolePermissionChanged = 'ROLE_PERMISSION_CHANGED';

    public const PostPublished = 'POST_PUBLISHED';

    public const PostArchived = 'POST_ARCHIVED';

    public const PostScheduled = 'POST_SCHEDULED';

    public const PagePublished = 'PAGE_PUBLISHED';

    public const PageArchived = 'PAGE_ARCHIVED';

    public const CommentModerated = 'COMMENT_MODERATED';

    public const SettingsUpdated = 'SETTINGS_UPDATED';

    public const MediaDeleted = 'MEDIA_DELETED';

    public const MenuUpdated = 'MENU_UPDATED';

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return [
            self::UserCreated,
            self::UserDeactivated,
            self::UserReactivated,
            self::RoleAssigned,
            self::RolePermissionChanged,
            self::PostPublished,
            self::PostArchived,
            self::PostScheduled,
            self::PagePublished,
            self::PageArchived,
            self::CommentModerated,
            self::SettingsUpdated,
            self::MediaDeleted,
            self::MenuUpdated,
        ];
    }

    public static function isValid(string $event): bool
    {
        return in_array($event, self::all(), true);
    }
}
