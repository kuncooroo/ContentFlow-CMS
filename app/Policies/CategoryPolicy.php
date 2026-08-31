<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use App\Policies\Concerns\ChecksPermissions;
use App\Support\Permissions\PermissionNames;

class CategoryPolicy
{
    use ChecksPermissions;

    public function viewAny(User $user): bool
    {
        return $this->allows($user, PermissionNames::CategoriesManage);
    }

    public function view(User $user, Category $category): bool
    {
        return $this->allows($user, PermissionNames::CategoriesManage);
    }

    public function create(User $user): bool
    {
        return $this->allows($user, PermissionNames::CategoriesManage);
    }

    public function update(User $user, Category $category): bool
    {
        return $this->allows($user, PermissionNames::CategoriesManage);
    }

    public function delete(User $user, Category $category): bool
    {
        return $this->allows($user, PermissionNames::CategoriesManage);
    }
}
