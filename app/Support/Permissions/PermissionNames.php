<?php

namespace App\Support\Permissions;

class PermissionNames
{
    public const PostsView = 'posts.view';

    public const PostsCreate = 'posts.create';

    public const PostsUpdate = 'posts.update';

    public const PostsDelete = 'posts.delete';

    public const PostsPublish = 'posts.publish';

    public const PostsSchedule = 'posts.schedule';

    public const PagesView = 'pages.view';

    public const PagesCreate = 'pages.create';

    public const PagesUpdate = 'pages.update';

    public const PagesDelete = 'pages.delete';

    public const PagesPublish = 'pages.publish';

    public const MediaView = 'media.view';

    public const MediaUpload = 'media.upload';

    public const MediaUpdate = 'media.update';

    public const MediaDelete = 'media.delete';

    public const CommentsView = 'comments.view';

    public const CommentsModerate = 'comments.moderate';

    public const CategoriesManage = 'categories.manage';

    public const TagsManage = 'tags.manage';

    public const MenusManage = 'menus.manage';

    public const SeoManage = 'seo.manage';

    public const UsersManage = 'users.manage';

    public const RolesManage = 'roles.manage';

    public const SettingsManage = 'settings.manage';

    public const AuditView = 'audit.view';

    /**
     * @return list<array{name: string, group_name: string, description: string}>
     */
    public static function definitions(): array
    {
        return [
            ['name' => self::PostsView, 'group_name' => 'Posts', 'description' => 'View posts'],
            ['name' => self::PostsCreate, 'group_name' => 'Posts', 'description' => 'Create posts'],
            ['name' => self::PostsUpdate, 'group_name' => 'Posts', 'description' => 'Update posts'],
            ['name' => self::PostsDelete, 'group_name' => 'Posts', 'description' => 'Delete posts'],
            ['name' => self::PostsPublish, 'group_name' => 'Posts', 'description' => 'Publish posts'],
            ['name' => self::PostsSchedule, 'group_name' => 'Posts', 'description' => 'Schedule posts'],
            ['name' => self::PagesView, 'group_name' => 'Pages', 'description' => 'View pages'],
            ['name' => self::PagesCreate, 'group_name' => 'Pages', 'description' => 'Create pages'],
            ['name' => self::PagesUpdate, 'group_name' => 'Pages', 'description' => 'Update pages'],
            ['name' => self::PagesDelete, 'group_name' => 'Pages', 'description' => 'Delete pages'],
            ['name' => self::PagesPublish, 'group_name' => 'Pages', 'description' => 'Publish pages'],
            ['name' => self::MediaView, 'group_name' => 'Media', 'description' => 'View media library'],
            ['name' => self::MediaUpload, 'group_name' => 'Media', 'description' => 'Upload media'],
            ['name' => self::MediaUpdate, 'group_name' => 'Media', 'description' => 'Update media metadata'],
            ['name' => self::MediaDelete, 'group_name' => 'Media', 'description' => 'Delete media'],
            ['name' => self::CommentsView, 'group_name' => 'Comments', 'description' => 'View comments'],
            ['name' => self::CommentsModerate, 'group_name' => 'Comments', 'description' => 'Moderate comments'],
            ['name' => self::CategoriesManage, 'group_name' => 'Taxonomy', 'description' => 'Manage categories'],
            ['name' => self::TagsManage, 'group_name' => 'Taxonomy', 'description' => 'Manage tags'],
            ['name' => self::MenusManage, 'group_name' => 'Navigation', 'description' => 'Manage menus'],
            ['name' => self::SeoManage, 'group_name' => 'SEO', 'description' => 'Manage SEO settings'],
            ['name' => self::UsersManage, 'group_name' => 'Users', 'description' => 'Manage users'],
            ['name' => self::RolesManage, 'group_name' => 'Access', 'description' => 'Manage roles and permissions'],
            ['name' => self::SettingsManage, 'group_name' => 'Settings', 'description' => 'Manage site settings'],
            ['name' => self::AuditView, 'group_name' => 'Audit', 'description' => 'View activity log'],
        ];
    }

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return array_column(self::definitions(), 'name');
    }
}
