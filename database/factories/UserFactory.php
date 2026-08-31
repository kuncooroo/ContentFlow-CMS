<?php

namespace Database\Factories;

use App\Enums\UserStatus;
use App\Models\Role;
use App\Models\User;
use App\Support\AccessControl\RoleName;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'status' => UserStatus::Active,
            'remember_token' => Str::random(10),
        ];
    }

    public function withRole(string $roleName): static
    {
        return $this->afterCreating(function (User $user) use ($roleName): void {
            $role = Role::query()->where('name', $roleName)->firstOrFail();
            $user->roles()->sync([
                $role->id => [
                    'assigned_by_user_id' => null,
                    'created_at' => now(),
                ],
            ]);
        });
    }

    public function superAdmin(): static
    {
        return $this->withRole(RoleName::SuperAdmin);
    }

    public function administrator(): static
    {
        return $this->withRole(RoleName::Administrator);
    }

    public function editor(): static
    {
        return $this->withRole(RoleName::Editor);
    }

    public function author(): static
    {
        return $this->withRole(RoleName::Author);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => UserStatus::Inactive,
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
