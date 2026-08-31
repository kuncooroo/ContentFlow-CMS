<?php

namespace App\Queries\Content;

use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class PublicPostIndexQuery
{
    /**
     * @return Collection<int, Post>
     */
    public function latest(int $limit = 6): Collection
    {
        return $this->baseQuery()
            ->limit($limit)
            ->get();
    }

    public function paginate(
        ?int $categoryId = null,
        ?int $tagId = null,
        int $perPage = 12,
    ): LengthAwarePaginator {
        $query = $this->baseQuery();

        if ($categoryId !== null) {
            $query->whereHas('categories', fn (Builder $query): Builder => $query->where('categories.id', $categoryId));
        }

        if ($tagId !== null) {
            $query->whereHas('tags', fn (Builder $query): Builder => $query->where('tags.id', $tagId));
        }

        return $query->paginate($perPage);
    }

    /**
     * @return Builder<Post>
     */
    private function baseQuery(): Builder
    {
        return Post::query()
            ->publiclyVisible()
            ->with(['author'])
            ->orderByDesc('publish_at')
            ->orderByDesc('id');
    }
}
