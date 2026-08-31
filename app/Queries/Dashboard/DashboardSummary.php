<?php

namespace App\Queries\Dashboard;

use Illuminate\Support\Collection;

readonly class DashboardSummary
{
    /**
     * @param  array{draft: int, scheduled: int, published: int, archived: int}|null  $postCounts
     * @param  array{draft: int, published: int, archived: int}|null  $pageCounts
     */
    public function __construct(
        public ?array $postCounts,
        public ?array $pageCounts,
        public ?int $pendingComments,
        public Collection $recentPosts,
        public Collection $recentPages,
        public Collection $scheduledPosts,
    ) {}
}
