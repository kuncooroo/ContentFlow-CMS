<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use App\Policies\Concerns\ChecksPermissions;
use App\Support\Permissions\PermissionNames;

class CommentPolicy
{
    use ChecksPermissions;

    public function viewAny(User $user): bool
    {
        return $this->allows($user, PermissionNames::CommentsView);
    }

    public function view(User $user, Comment $comment): bool
    {
        return $this->allows($user, PermissionNames::CommentsView);
    }

    public function moderate(User $user, Comment $comment): bool
    {
        return $this->allows($user, PermissionNames::CommentsModerate);
    }
}
