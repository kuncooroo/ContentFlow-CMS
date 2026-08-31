<?php

namespace App\Actions\Users;

use App\Enums\UserStatus;
use App\Models\User;
use App\Services\Audit\ActivityLogger;
use App\Support\AccessControl\SuperAdminProtection;
use App\Support\Demo\DemoGuard;
use App\Support\Audit\ActivityEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ChangeUserStatus
{
    public function __construct(
        private readonly ActivityLogger $activityLogger,
    ) {}

    public function handle(User $user, UserStatus $status, User $actor): User
    {
        if ($actor->is($user) && $status === UserStatus::Inactive) {
            throw ValidationException::withMessages([
                'status' => 'You cannot deactivate your own account.',
            ]);
        }

        if ($status === UserStatus::Inactive) {
            DemoGuard::assertCanChangeUserStatus($user);
            SuperAdminProtection::assertCanDeactivate($user);
        }

        $previousStatus = $user->status;

        if ($previousStatus === $status) {
            return $user;
        }

        return DB::transaction(function () use ($user, $status, $actor, $previousStatus): User {
            $user->status = $status;
            $user->save();

            if ($status === UserStatus::Inactive) {
                $this->invalidateUserSessions($user);
            }

            $event = $status === UserStatus::Inactive
                ? ActivityEvent::UserDeactivated
                : ActivityEvent::UserReactivated;

            $this->activityLogger->record(
                $actor,
                $event,
                $user,
                [
                    'previous_status' => $previousStatus->value,
                    'new_status' => $status->value,
                ],
            );

            return $user->fresh();
        });
    }

    private function invalidateUserSessions(User $user): void
    {
        if (config('session.driver') !== 'database') {
            return;
        }

        DB::table(config('session.table', 'sessions'))
            ->where('user_id', $user->id)
            ->delete();
    }
}
