<?php

namespace App\Policies;

use App\Models\Media;
use App\Models\User;
use App\Policies\Concerns\ChecksPermissions;
use App\Support\Permissions\PermissionNames;

class MediaPolicy
{
    use ChecksPermissions;

    public function viewAny(User $user): bool
    {
        return $this->allows($user, PermissionNames::MediaView);
    }

    public function view(User $user, Media $media): bool
    {
        return $this->allows($user, PermissionNames::MediaView);
    }

    public function upload(User $user): bool
    {
        return $this->allows($user, PermissionNames::MediaUpload);
    }

    public function update(User $user, Media $media): bool
    {
        return $this->allows($user, PermissionNames::MediaUpdate);
    }

    public function delete(User $user, Media $media): bool
    {
        return $this->allows($user, PermissionNames::MediaDelete);
    }
}
