<?php

namespace App\Queries\Audit;

use App\Models\ActivityLog;
use App\Support\Audit\ActivityEvent;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class ActivityLogQuery
{
    public function paginate(
        ?string $event = null,
        ?int $actorUserId = null,
        ?string $createdFrom = null,
        ?string $createdTo = null,
        int $perPage = 20,
    ): LengthAwarePaginator {
        $this->assertValidEventFilter($event);

        $query = ActivityLog::query()->with('actor');

        $this->applyEventFilter($query, $event);
        $this->applyActorFilter($query, $actorUserId);
        $this->applyCreatedDateRange($query, $createdFrom, $createdTo);

        return $query
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    /**
     * @return list<int>
     */
    public function actorIdsWithLogs(): array
    {
        return ActivityLog::query()
            ->whereNotNull('actor_user_id')
            ->distinct()
            ->orderBy('actor_user_id')
            ->pluck('actor_user_id')
            ->all();
    }

    private function assertValidEventFilter(?string $event): void
    {
        if ($event === null || $event === '') {
            return;
        }

        validator(['event' => $event], [
            'event' => ['required', 'string', Rule::in(ActivityEvent::all())],
        ])->validate();
    }

    /**
     * @param  Builder<ActivityLog>  $query
     */
    private function applyEventFilter(Builder $query, ?string $event): void
    {
        if ($event === null || $event === '') {
            return;
        }

        $query->where('event', $event);
    }

    /**
     * @param  Builder<ActivityLog>  $query
     */
    private function applyActorFilter(Builder $query, ?int $actorUserId): void
    {
        if ($actorUserId === null) {
            return;
        }

        $query->where('actor_user_id', $actorUserId);
    }

    /**
     * @param  Builder<ActivityLog>  $query
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
