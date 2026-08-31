<?php

namespace App\Actions\AccessControl;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\Audit\ActivityLogger;
use App\Support\Audit\ActivityEvent;
use App\Support\Demo\DemoGuard;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SyncRolePermissions
{
    public function __construct(
        private readonly ActivityLogger $activityLogger,
    ) {}

    /**
     * @param  list<int>  $permissionIds
     */
    public function handle(Role $role, array $permissionIds, User $actor): Role
    {
        DemoGuard::assertCanSyncRolePermissions($role);

        if ($role->is_system && $role->name === 'Super Admin' && $permissionIds === []) {
            throw ValidationException::withMessages([
                'permissions' => 'Super Admin must retain permissions.',
            ]);
        }

        $validPermissionIds = Permission::query()
            ->whereIn('id', $permissionIds)
            ->pluck('id')
            ->all();

        if (count($validPermissionIds) !== count(array_unique($permissionIds))) {
            throw ValidationException::withMessages([
                'permissions' => 'One or more selected permissions are invalid.',
            ]);
        }

        $previousPermissionNames = $role->permissions()->orderBy('name')->pluck('name')->all();

        DB::transaction(function () use ($role, $validPermissionIds, $actor, $previousPermissionNames): void {
            $syncData = [];

            foreach ($validPermissionIds as $permissionId) {
                $syncData[$permissionId] = ['created_at' => now()];
            }

            $role->permissions()->sync($syncData);

            $newPermissionNames = Permission::query()
                ->whereIn('id', $validPermissionIds)
                ->orderBy('name')
                ->pluck('name')
                ->all();

            $this->activityLogger->record(
                $actor,
                ActivityEvent::RolePermissionChanged,
                $role,
                [
                    'previous_permissions' => $previousPermissionNames,
                    'new_permissions' => $newPermissionNames,
                ],
            );
        });

        return $role->fresh(['permissions']);
    }
}
