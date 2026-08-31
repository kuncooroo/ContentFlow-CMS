<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\ChecksPermissions;
use App\Support\Permissions\PermissionNames;

class SiteSettingPolicy
{
    use ChecksPermissions;

    public function viewAny(User $user): bool
    {
        return $this->allows($user, PermissionNames::SettingsManage);
    }

    public function update(User $user): bool
    {
        return $this->allows($user, PermissionNames::SettingsManage);
    }

    public function manage(User $user): bool
    {
        return $this->allows($user, PermissionNames::SettingsManage);
    }
}
