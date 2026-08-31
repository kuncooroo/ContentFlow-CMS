<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use App\Policies\Concerns\ChecksPermissions;
use App\Support\AccessControl\RoleName;
use App\Support\Permissions\PermissionNames;

class PostPolicy
{
    use ChecksPermissions;

    public function viewAny(User $user): bool
    {
        return $this->allows($user, PermissionNames::PostsView);
    }

    public function view(User $user, Post $post): bool
    {
        return $this->canManageExistingPost($user, $post, PermissionNames::PostsView);
    }

    public function create(User $user): bool
    {
        return $this->allows($user, PermissionNames::PostsCreate);
    }

    public function update(User $user, Post $post): bool
    {
        return $this->canManageExistingPost($user, $post, PermissionNames::PostsUpdate);
    }

    public function delete(User $user, Post $post): bool
    {
        return $this->canManageExistingPost($user, $post, PermissionNames::PostsDelete);
    }

    public function publish(User $user, Post $post): bool
    {
        return $this->canManageExistingPost($user, $post, PermissionNames::PostsPublish);
    }

    public function schedule(User $user, Post $post): bool
    {
        return $this->canManageExistingPost($user, $post, PermissionNames::PostsSchedule);
    }

    public function archive(User $user, Post $post): bool
    {
        return $this->canManageExistingPost($user, $post, PermissionNames::PostsPublish);
    }

    public function unpublish(User $user, Post $post): bool
    {
        return $this->canManageExistingPost($user, $post, PermissionNames::PostsPublish);
    }

    public function cancelSchedule(User $user, Post $post): bool
    {
        return $this->canManageExistingPost($user, $post, PermissionNames::PostsSchedule);
    }

    public function restore(User $user, Post $post): bool
    {
        return $this->canManageExistingPost($user, $post, PermissionNames::PostsUpdate);
    }

    public function preview(User $user, Post $post): bool
    {
        return $this->view($user, $post);
    }

    private function canManageExistingPost(User $user, Post $post, string $permission): bool
    {
        if (! $this->allows($user, $permission)) {
            return false;
        }

        if ($this->requiresOwnership($user)) {
            return $user->id === $post->author_id;
        }

        return true;
    }

    private function requiresOwnership(User $user): bool
    {
        if ($user->hasRole(RoleName::SuperAdmin)
            || $user->hasRole(RoleName::Administrator)
            || $user->hasRole(RoleName::Editor)) {
            return false;
        }

        return $user->hasRole(RoleName::Author);
    }
}
