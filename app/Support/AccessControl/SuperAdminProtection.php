<?php

namespace App\Support\AccessControl;

use App\Enums\UserStatus;
use App\Models\Role;
use App\Models\User;
use App\Support\AccessControl\RoleName;
use Illuminate\Validation\ValidationException;

class SuperAdminProtection
{
    public static function activeSuperAdminCount(): int
    {
        return User::query()
            ->where('status', UserStatus::Active)
            ->whereHas('roles', fn ($query) => $query->where('name', RoleName::SuperAdmin))
            ->count();
    }

    public static function isActiveSuperAdmin(User $user): bool
    {
        return $user->isActive() && $user->hasRole(RoleName::SuperAdmin);
    }

    public static function assertCanDeactivate(User $user): void
    {
        if (self::isActiveSuperAdmin($user) && self::activeSuperAdminCount() <= 1) {
            throw ValidationException::withMessages([
                'status' => 'Cannot deactivate the last active Super Admin.',
            ]);
        }
    }

    /**
     * @param  list<int>  $roleIds
     */
    public static function assertCanAssignRoles(User $user, array $roleIds): void
    {
        $superAdminRole = Role::query()->where('name', RoleName::SuperAdmin)->first();

        if (! $superAdminRole) {
            return;
        }

        $currentlySuperAdmin = $user->hasRole(RoleName::SuperAdmin);
        $willRemainSuperAdmin = in_array($superAdminRole->id, $roleIds, true);

        if ($currentlySuperAdmin && ! $willRemainSuperAdmin && self::activeSuperAdminCount() <= 1) {
            throw ValidationException::withMessages([
                'roles' => 'Cannot remove the Super Admin role from the last active Super Admin.',
            ]);
        }

        if ($willRemainSuperAdmin && ! $user->isActive() && self::activeSuperAdminCount() === 0) {
            return;
        }
    }
}
