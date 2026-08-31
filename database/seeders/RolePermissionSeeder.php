<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Support\AccessControl\RoleName;
use App\Support\Permissions\PermissionNames;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (PermissionNames::definitions() as $definition) {
            Permission::query()->updateOrCreate(
                ['name' => $definition['name']],
                [
                    'group_name' => $definition['group_name'],
                    'description' => $definition['description'],
                ],
            );
        }

        $roles = [
            RoleName::SuperAdmin => [
                'description' => 'Full administrative access.',
                'permissions' => PermissionNames::all(),
            ],
            RoleName::Administrator => [
                'description' => 'Site operations including users, content, and settings.',
                'permissions' => [
                    PermissionNames::PostsView,
                    PermissionNames::PostsCreate,
                    PermissionNames::PostsUpdate,
                    PermissionNames::PostsDelete,
                    PermissionNames::PostsPublish,
                    PermissionNames::PostsSchedule,
                    PermissionNames::PagesView,
                    PermissionNames::PagesCreate,
                    PermissionNames::PagesUpdate,
                    PermissionNames::PagesDelete,
                    PermissionNames::PagesPublish,
                    PermissionNames::MediaView,
                    PermissionNames::MediaUpload,
                    PermissionNames::MediaUpdate,
                    PermissionNames::MediaDelete,
                    PermissionNames::CommentsView,
                    PermissionNames::CommentsModerate,
                    PermissionNames::CategoriesManage,
                    PermissionNames::TagsManage,
                    PermissionNames::MenusManage,
                    PermissionNames::SeoManage,
                    PermissionNames::UsersManage,
                    PermissionNames::RolesManage,
                    PermissionNames::SettingsManage,
                    PermissionNames::AuditView,
                ],
            ],
            RoleName::Editor => [
                'description' => 'Editorial publishing and moderation.',
                'permissions' => [
                    PermissionNames::PostsView,
                    PermissionNames::PostsCreate,
                    PermissionNames::PostsUpdate,
                    PermissionNames::PostsDelete,
                    PermissionNames::PostsPublish,
                    PermissionNames::PostsSchedule,
                    PermissionNames::PagesView,
                    PermissionNames::PagesCreate,
                    PermissionNames::PagesUpdate,
                    PermissionNames::PagesDelete,
                    PermissionNames::PagesPublish,
                    PermissionNames::MediaView,
                    PermissionNames::MediaUpload,
                    PermissionNames::MediaUpdate,
                    PermissionNames::CommentsView,
                    PermissionNames::CommentsModerate,
                    PermissionNames::CategoriesManage,
                    PermissionNames::TagsManage,
                    PermissionNames::SeoManage,
                ],
            ],
            RoleName::Author => [
                'description' => 'Create and manage own draft content.',
                'permissions' => [
                    PermissionNames::PostsView,
                    PermissionNames::PostsCreate,
                    PermissionNames::PostsUpdate,
                    PermissionNames::PostsDelete,
                    PermissionNames::MediaView,
                    PermissionNames::MediaUpload,
                    PermissionNames::TagsManage,
                ],
            ],
        ];

        foreach ($roles as $name => $config) {
            $role = Role::query()->updateOrCreate(
                ['name' => $name],
                [
                    'description' => $config['description'],
                    'is_system' => true,
                ],
            );

            $permissionIds = Permission::query()
                ->whereIn('name', $config['permissions'])
                ->pluck('id')
                ->all();

            $syncData = [];

            foreach ($permissionIds as $permissionId) {
                $syncData[$permissionId] = ['created_at' => now()];
            }

            $role->permissions()->sync($syncData);
        }
    }
}
