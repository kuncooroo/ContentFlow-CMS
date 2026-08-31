<?php

namespace App\Models;

use App\Enums\UserStatus;
use App\Support\AccessControl\RoleName;
use Database\Factories\UserFactory;
use App\Notifications\Auth\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

#[Fillable(['name', 'email', 'password', 'status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => UserStatus::class,
        ];
    }

    /**
     * @return BelongsToMany<Role, $this>
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)
            ->withPivot('assigned_by_user_id');
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    public function hasRole(string $roleName): bool
    {
        if (! $this->relationLoaded('roles')) {
            return $this->roles()->where('name', $roleName)->exists();
        }

        return $this->roles->contains('name', $roleName);
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->hasRole(RoleName::SuperAdmin)) {
            return true;
        }

        return $this->permissionNames()->contains($permission);
    }

    /**
     * @return Collection<int, string>
     */
    public function permissionNames(): Collection
    {
        $this->loadMissing('roles.permissions');

        return $this->roles
            ->flatMap(fn (Role $role) => $role->permissions->pluck('name'))
            ->unique()
            ->values();
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
