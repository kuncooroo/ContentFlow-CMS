<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use App\Policies\Concerns\ChecksPermissions;
use App\Support\Permissions\PermissionNames;

class RolePolicy
{
    use ChecksPermissions;

    public function viewAny(User $user): bool
    {
        return $this->allows($user, PermissionNames::RolesManage);
    }

    public function view(User $user, Role $role): bool
    {
        return $this->allows($user, PermissionNames::RolesManage);
    }

    public function update(User $user, Role $role): bool
    {
        return $this->allows($user, PermissionNames::RolesManage);
    }
}
