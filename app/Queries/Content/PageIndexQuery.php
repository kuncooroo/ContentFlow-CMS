<?php

namespace App\Queries\Content;

use App\Enums\PageStatus;
use App\Models\Page;
use App\Support\Search\SearchTerm;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class PageIndexQuery
{
    public function paginate(
        ?string $search = null,
        ?PageStatus $status = null,
        ?int $authorId = null,
        ?string $createdFrom = null,
        ?string $createdTo = null,
        int $perPage = 15,
    ): LengthAwarePaginator {
        $query = Page::query()->with(['author', 'ogMedia']);

        $this->applySearch($query, $search);
        $this->applyStatus($query, $status);

        if ($authorId !== null) {
            $query->where('author_id', $authorId);
        }

        $this->applyCreatedDateRange($query, $createdFrom, $createdTo);

        return $query
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    /**
     * @param  Builder<Page>  $query
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
     * @param  Builder<Page>  $query
     */
    private function applyStatus(Builder $query, ?PageStatus $status): void
    {
        if ($status === null) {
            return;
        }

        $query->where('status', $status);
    }

    /**
     * @param  Builder<Page>  $query
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
}
