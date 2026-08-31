<?php

namespace App\Policies;

use App\Models\ActivityLog;
use App\Models\User;
use App\Policies\Concerns\ChecksPermissions;
use App\Support\Permissions\PermissionNames;

class ActivityLogPolicy
{
    use ChecksPermissions;

    public function viewAny(User $user): bool
    {
        return $this->allows($user, PermissionNames::AuditView);
    }

    public function view(User $user, ActivityLog $activityLog): bool
    {
        return $this->allows($user, PermissionNames::AuditView);
    }
}
