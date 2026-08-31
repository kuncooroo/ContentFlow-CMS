<?php

namespace App\Queries\Content;

use App\Enums\CommentStatus;
use App\Queries\Comments\CommentModerationQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * @deprecated Use CommentModerationQuery directly.
 */
class CommentIndexQuery
{
    public function __construct(
        private readonly CommentModerationQuery $commentModerationQuery,
    ) {}

    public function paginate(?CommentStatus $status = null, int $perPage = 20): LengthAwarePaginator
    {
        return $this->commentModerationQuery->paginate($status, null, $perPage);
    }

    public function pendingCount(): int
    {
        return $this->commentModerationQuery->pendingCount();
    }
}
