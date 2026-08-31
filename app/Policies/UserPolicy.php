<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\ChecksPermissions;
use App\Support\Permissions\PermissionNames;

class UserPolicy
{
    use ChecksPermissions;

    public function viewAny(User $user): bool
    {
        return $this->allows($user, PermissionNames::UsersManage);
    }

    public function view(User $user, User $model): bool
    {
        return $this->allows($user, PermissionNames::UsersManage);
    }

    public function create(User $user): bool
    {
        return $this->allows($user, PermissionNames::UsersManage);
    }

    public function update(User $user, User $model): bool
    {
        return $this->allows($user, PermissionNames::UsersManage);
    }

    public function changeStatus(User $user, User $model): bool
    {
        return $this->allows($user, PermissionNames::UsersManage);
    }
}
