<?php

namespace App\Actions\Users;

use App\Enums\UserStatus;
use App\Models\User;
use App\Services\Audit\ActivityLogger;
use App\Support\Audit\ActivityEvent;
use Illuminate\Support\Facades\DB;

class CreateUser
{
    public function __construct(
        private readonly ActivityLogger $activityLogger,
    ) {}

    public function handle(
        string $name,
        string $email,
        string $password,
        UserStatus $status,
        User $actor,
    ): User {
        return DB::transaction(function () use ($name, $email, $password, $status, $actor): User {
            $user = User::query()->create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'status' => $status,
            ]);

            $this->activityLogger->record(
                $actor,
                ActivityEvent::UserCreated,
                $user,
                [
                    'email' => $user->email,
                    'status' => $user->status->value,
                ],
            );

            return $user;
        });
    }
}
