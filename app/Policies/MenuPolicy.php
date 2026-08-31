<?php

namespace App\Policies;

use App\Models\Menu;
use App\Models\User;
use App\Policies\Concerns\ChecksPermissions;
use App\Support\Permissions\PermissionNames;

class MenuPolicy
{
    use ChecksPermissions;

    public function viewAny(User $user): bool
    {
        return $this->allows($user, PermissionNames::MenusManage);
    }

    public function view(User $user, Menu $menu): bool
    {
        return $this->allows($user, PermissionNames::MenusManage);
    }

    public function create(User $user): bool
    {
        return $this->allows($user, PermissionNames::MenusManage);
    }

    public function update(User $user, Menu $menu): bool
    {
        return $this->allows($user, PermissionNames::MenusManage);
    }

    public function manage(User $user): bool
    {
        return $this->allows($user, PermissionNames::MenusManage);
    }
}
