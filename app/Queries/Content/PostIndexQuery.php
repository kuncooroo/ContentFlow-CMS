<?php

namespace App\Queries\Content;

use App\Enums\PageStatus;
use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;
use App\Support\AccessControl\RoleName;
use App\Support\Search\SearchTerm;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class PostIndexQuery
{
    public function paginateForUser(
        User $user,
        ?string $search = null,
        ?PostStatus $status = null,
        ?int $authorId = null,
        ?int $categoryId = null,
        ?string $createdFrom = null,
        ?string $createdTo = null,
        int $perPage = 15,
    ): LengthAwarePaginator {
        $query = Post::query()
            ->with(['author', 'categories', 'tags', 'featuredMedia']);

        if ($this->requiresOwnership($user)) {
            $query->where('author_id', $user->id);
        } elseif ($authorId !== null) {
            $query->where('author_id', $authorId);
        }

        $this->applySearch($query, $search);
        $this->applyStatus($query, $status);
        $this->applyCategory($query, $categoryId);
        $this->applyCreatedDateRange($query, $createdFrom, $createdTo);

        return $query
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function canFilterByAuthor(User $user): bool
    {
        return ! $this->requiresOwnership($user);
    }

    /**
     * @param  Builder<Post>  $query
     */
    private function applySearch(Builder $query, ?string $search): void
    {
        $pattern = SearchTerm::likePattern($search);

        if ($pattern === null) {
            return;
        }

        $query->where(function (Builder $builder) use ($pattern): void {
            $builder
                ->where('title', 'like', $pattern)
                ->orWhere('slug', 'like', $pattern);
        });
    }

    /**
     * @param  Builder<Post>  $query
     */
    private function applyStatus(Builder $query, ?PostStatus $status): void
    {
        if ($status === null) {
            return;
        }

        $query->where('status', $status);
    }

    /**
     * @param  Builder<Post>  $query
     */
    private function applyCategory(Builder $query, ?int $categoryId): void
    {
        if ($categoryId === null) {
            return;
        }

        $query->whereHas('categories', fn (Builder $builder) => $builder->where('categories.id', $categoryId));
    }

    /**
     * @param  Builder<Post>  $query
     */
    private function applyCreatedDateRange(Builder $query, ?string $createdFrom, ?string $createdTo): void
    {
        if ($createdFrom !== null && $createdFrom !== '') {
            $query->whereDate('created_at', '>=', Carbon::parse($createdFrom)->toDateString());
        }

        if ($createdTo !== null && $createdTo !== '') {
            $query->whereDate('created_at', '<=', Carbon::parse($createdTo)->toDateString());
        }
    }

    private function requiresOwnership(User $user): bool
    {
        if ($user->hasRole(RoleName::SuperAdmin)
            || $user->hasRole(RoleName::Administrator)
            || $user->hasRole(RoleName::Editor)) {
            return false;
        }

        return $user->hasRole(RoleName::Author);
    }
}
