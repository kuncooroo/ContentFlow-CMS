<?php

namespace App\Support\Demo;

use App\Models\Role;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class DemoGuard
{
    public static function isEnabled(): bool
    {
        return (bool) config('demo.enabled', false);
    }

    public static function ensureSafeConfiguration(): void
    {
        if (! self::isEnabled()) {
            return;
        }

        if (app()->environment('production') && ! config('demo.allow_in_production', false)) {
            throw new RuntimeException(
                'DEMO_MODE is enabled while APP_ENV=production. Set DEMO_ALLOW_IN_PRODUCTION=true only on intentional demo hosts.',
            );
        }
    }

    public static function isProtectedUser(User $user): bool
    {
        return in_array(
            strtolower($user->email),
            array_map('strtolower', config('demo.protected_user_emails', [])),
            true,
        );
    }

    public static function isProtectedEmail(string $email): bool
    {
        return in_array(
            strtolower($email),
            array_map('strtolower', config('demo.protected_user_emails', [])),
            true,
        );
    }

    public static function assertCanChangeUserStatus(User $user): void
    {
        if (! self::isEnabled()) {
            return;
        }

        if (self::isProtectedUser($user)) {
            throw ValidationException::withMessages([
                'demo' => 'Demo accounts cannot be activated or deactivated.',
            ]);
        }
    }

    public static function assertCanUpdateUser(User $user, ?string $password, string $email): void
    {
        if (! self::isEnabled()) {
            return;
        }

        if (! self::isProtectedUser($user)) {
            return;
        }

        if ($password !== null && $password !== '') {
            throw ValidationException::withMessages([
                'demo' => 'Password changes are disabled for demo accounts.',
            ]);
        }

        if (strtolower($email) !== strtolower($user->email)) {
            throw ValidationException::withMessages([
                'demo' => 'Demo account identities cannot be changed.',
            ]);
        }
    }

    /**
     * @param  list<int>  $roleIds
     */
    public static function assertCanAssignRoles(User $user, array $roleIds): void
    {
        if (! self::isEnabled()) {
            return;
        }

        if (self::isProtectedUser($user)) {
            throw ValidationException::withMessages([
                'demo' => 'Roles cannot be changed for demo accounts.',
            ]);
        }
    }

    public static function assertCanSyncRolePermissions(Role $role): void
    {
        if (! self::isEnabled()) {
            return;
        }

        if ($role->is_system) {
            throw ValidationException::withMessages([
                'demo' => 'System role permissions cannot be changed in demo mode.',
            ]);
        }
    }

    public static function assertCanUpdateSiteSettings(): void
    {
        if (! self::isEnabled()) {
            return;
        }

        throw ValidationException::withMessages([
            'demo' => 'Site settings cannot be changed in demo mode.',
        ]);
    }

    public static function assertCanResetPassword(string $email): void
    {
        if (! self::isEnabled()) {
            return;
        }

        if (self::isProtectedEmail($email)) {
            throw ValidationException::withMessages([
                'email' => 'Password reset is disabled for demo accounts.',
            ]);
        }
    }
}
