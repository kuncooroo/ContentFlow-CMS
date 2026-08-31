<?php

namespace App\Queries\Users;

use App\Enums\UserStatus;
use App\Models\User;
use App\Support\Search\SearchTerm;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class UserIndexQuery
{
    public function paginate(
        ?string $search = null,
        ?UserStatus $status = null,
        int $perPage = 15,
    ): LengthAwarePaginator {
        $query = User::query();

        $this->applySearch($query, $search);

        if ($status !== null) {
            $query->where('status', $status);
        }

        return $query
            ->orderBy('name')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    /**
     * @param  Builder<User>  $query
     */
    private function applySearch(Builder $query, ?string $search): void
    {
        $pattern = SearchTerm::likePattern($search);

        if ($pattern === null) {
            return;
        }

        $query->where(function (Builder $builder) use ($pattern): void {
            $builder
                ->where('name', 'like', $pattern)
                ->orWhere('email', 'like', $pattern);
        });
    }
}
