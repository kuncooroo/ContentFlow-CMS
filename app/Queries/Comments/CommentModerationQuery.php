<?php

namespace App\Queries\Comments;

use App\Enums\CommentStatus;
use App\Models\Comment;
use App\Support\Search\SearchTerm;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class CommentModerationQuery
{
    public function paginate(
        ?CommentStatus $status = null,
        ?string $search = null,
        int $perPage = 20,
    ): LengthAwarePaginator {
        $query = Comment::query()->with(['post', 'moderator']);

        if ($status !== null) {
            $query->where('status', $status);
        }

        $this->applySearch($query, $search);

        return $query
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function pendingCount(): int
    {
        return Comment::query()->pending()->count();
    }

    /**
     * @param  Builder<Comment>  $query
     */
    private function applySearch(Builder $query, ?string $search): void
    {
        $pattern = SearchTerm::likePattern($search);

        if ($pattern === null) {
            return;
        }

        $query->where(function (Builder $builder) use ($pattern): void {
            $builder
                ->where('author_name', 'like', $pattern)
                ->orWhere('author_email', 'like', $pattern)
                ->orWhere('content', 'like', $pattern)
                ->orWhereHas('post', fn (Builder $postQuery) => $postQuery
                    ->where('title', 'like', $pattern)
                    ->orWhere('slug', 'like', $pattern));
        });
    }
}
