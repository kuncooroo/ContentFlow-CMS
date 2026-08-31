<?php

namespace App\Policies;

use App\Models\Page;
use App\Models\User;
use App\Policies\Concerns\ChecksPermissions;
use App\Support\Permissions\PermissionNames;

class PagePolicy
{
    use ChecksPermissions;

    public function viewAny(User $user): bool
    {
        return $this->allows($user, PermissionNames::PagesView);
    }

    public function view(User $user, Page $page): bool
    {
        return $this->allows($user, PermissionNames::PagesView);
    }

    public function create(User $user): bool
    {
        return $this->allows($user, PermissionNames::PagesCreate);
    }

    public function update(User $user, Page $page): bool
    {
        return $this->allows($user, PermissionNames::PagesUpdate);
    }

    public function delete(User $user, Page $page): bool
    {
        return $this->allows($user, PermissionNames::PagesDelete);
    }

    public function publish(User $user, Page $page): bool
    {
        return $this->allows($user, PermissionNames::PagesPublish);
    }

    public function archive(User $user, Page $page): bool
    {
        return $this->allows($user, PermissionNames::PagesPublish);
    }

    public function unpublish(User $user, Page $page): bool
    {
        return $this->allows($user, PermissionNames::PagesPublish);
    }

    public function restore(User $user, Page $page): bool
    {
        return $this->allows($user, PermissionNames::PagesUpdate);
    }

    public function preview(User $user, Page $page): bool
    {
        return $this->view($user, $page);
    }
}
