<?php

namespace App\Actions\Users;

use App\Models\User;
use App\Support\Demo\DemoGuard;

class UpdateUser
{
    /**
     * @param  array{name: string, email: string, password?: string|null}  $attributes
     */
    public function handle(User $user, array $attributes): User
    {
        DemoGuard::assertCanUpdateUser(
            $user,
            $attributes['password'] ?? null,
            $attributes['email'],
        );

        $user->fill([
            'name' => $attributes['name'],
            'email' => $attributes['email'],
        ]);

        if (! empty($attributes['password'])) {
            $user->password = $attributes['password'];
        }

        $user->save();

        return $user->fresh();
    }
}
