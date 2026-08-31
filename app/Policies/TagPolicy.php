<?php

namespace App\Policies;

use App\Models\Tag;
use App\Models\User;
use App\Policies\Concerns\ChecksPermissions;
use App\Support\Permissions\PermissionNames;

class TagPolicy
{
    use ChecksPermissions;

    public function viewAny(User $user): bool
    {
        return $this->allows($user, PermissionNames::TagsManage);
    }

    public function view(User $user, Tag $tag): bool
    {
        return $this->allows($user, PermissionNames::TagsManage);
    }

    public function create(User $user): bool
    {
        return $this->allows($user, PermissionNames::TagsManage);
    }

    public function update(User $user, Tag $tag): bool
    {
        return $this->allows($user, PermissionNames::TagsManage);
    }

    public function delete(User $user, Tag $tag): bool
    {
        return $this->allows($user, PermissionNames::TagsManage);
    }
}
