<?php

namespace App\Queries\Media;

use App\Models\Media;
use App\Support\Search\SearchTerm;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class MediaLibraryQuery
{
    public function paginate(?string $search = null, int $perPage = 18): LengthAwarePaginator
    {
        $query = Media::query();

        $this->applySearch($query, $search);

        return $query
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    /**
     * @param  Builder<Media>  $query
     */
    private function applySearch(Builder $query, ?string $search): void
    {
        $pattern = SearchTerm::likePattern($search);

        if ($pattern === null) {
            return;
        }

        $query->where(function (Builder $builder) use ($pattern): void {
            $builder
                ->where('original_name', 'like', $pattern)
                ->orWhere('file_name', 'like', $pattern)
                ->orWhere('alt_text', 'like', $pattern);
        });
    }
}
