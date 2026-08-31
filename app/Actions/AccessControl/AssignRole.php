<?php

namespace App\Actions\AccessControl;

use App\Models\Role;
use App\Models\User;
use App\Services\Audit\ActivityLogger;
use App\Support\AccessControl\SuperAdminProtection;
use App\Support\Demo\DemoGuard;
use App\Support\Audit\ActivityEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AssignRole
{
    public function __construct(
        private readonly ActivityLogger $activityLogger,
    ) {}

    /**
     * @param  list<int>  $roleIds
     */
    public function handle(User $user, array $roleIds, User $actor): User
    {
        if ($roleIds === []) {
            throw ValidationException::withMessages([
                'roles' => 'At least one role is required.',
            ]);
        }

        $validRoleIds = Role::query()
            ->whereIn('id', $roleIds)
            ->pluck('id')
            ->all();

        if (count($validRoleIds) !== count(array_unique($roleIds))) {
            throw ValidationException::withMessages([
                'roles' => 'One or more selected roles are invalid.',
            ]);
        }

        SuperAdminProtection::assertCanAssignRoles($user, $validRoleIds);
        DemoGuard::assertCanAssignRoles($user, $validRoleIds);

        $roleNames = Role::query()
            ->whereIn('id', $validRoleIds)
            ->orderBy('name')
            ->pluck('name')
            ->all();

        DB::transaction(function () use ($user, $validRoleIds, $actor, $roleNames): void {
            $syncData = [];

            foreach ($validRoleIds as $roleId) {
                $syncData[$roleId] = [
                    'assigned_by_user_id' => $actor->id,
                    'created_at' => now(),
                ];
            }

            $user->roles()->sync($syncData);

            $this->activityLogger->record(
                $actor,
                ActivityEvent::RoleAssigned,
                $user,
                [
                    'role_ids' => $validRoleIds,
                    'role_names' => $roleNames,
                ],
            );
        });

        return $user->fresh(['roles']);
    }
}
