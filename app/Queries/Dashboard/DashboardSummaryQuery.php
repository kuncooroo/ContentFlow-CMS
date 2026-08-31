<?php

namespace App\Queries\Dashboard;

use App\Enums\CommentStatus;
use App\Enums\PageStatus;
use App\Enums\PostStatus;
use App\Models\Comment;
use App\Models\Page;
use App\Models\Post;
use App\Models\User;
use App\Support\AccessControl\RoleName;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;

class DashboardSummaryQuery
{
    private const RECENT_LIMIT = 5;

    public function forUser(User $user): DashboardSummary
    {
        $canViewPosts = Gate::forUser($user)->allows('viewAny', Post::class);
        $canViewPages = Gate::forUser($user)->allows('viewAny', Page::class);
        $canViewComments = Gate::forUser($user)->allows('viewAny', Comment::class);

        return new DashboardSummary(
            postCounts: $canViewPosts ? $this->postCountsForUser($user) : null,
            pageCounts: $canViewPages ? $this->pageCounts() : null,
            pendingComments: $canViewComments ? $this->pendingCommentCount() : null,
            recentPosts: $canViewPosts ? $this->recentPostsForUser($user) : collect(),
            recentPages: $canViewPages ? $this->recentPages() : collect(),
            scheduledPosts: $canViewPosts ? $this->scheduledPostsForUser($user) : collect(),
        );
    }

    /**
     * @return array{draft: int, scheduled: int, published: int, archived: int}
     */
    private function postCountsForUser(User $user): array
    {
        $counts = $this->scopedPostQuery($user)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'draft' => (int) ($counts[PostStatus::Draft->value] ?? 0),
            'scheduled' => (int) ($counts[PostStatus::Scheduled->value] ?? 0),
            'published' => (int) ($counts[PostStatus::Published->value] ?? 0),
            'archived' => (int) ($counts[PostStatus::Archived->value] ?? 0),
        ];
    }

    /**
     * @return array{draft: int, published: int, archived: int}
     */
    private function pageCounts(): array
    {
        $counts = Page::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'draft' => (int) ($counts[PageStatus::Draft->value] ?? 0),
            'published' => (int) ($counts[PageStatus::Published->value] ?? 0),
            'archived' => (int) ($counts[PageStatus::Archived->value] ?? 0),
        ];
    }

    private function pendingCommentCount(): int
    {
        return Comment::query()
            ->where('status', CommentStatus::Pending)
            ->count();
    }

    private function recentPostsForUser(User $user)
    {
        return $this->scopedPostQuery($user)
            ->with('author:id,name')
            ->latest('updated_at')
            ->limit(self::RECENT_LIMIT)
            ->get(['id', 'title', 'slug', 'status', 'author_id', 'updated_at']);
    }

    private function recentPages()
    {
        return Page::query()
            ->with('author:id,name')
            ->latest('updated_at')
            ->limit(self::RECENT_LIMIT)
            ->get(['id', 'title', 'slug', 'status', 'author_id', 'updated_at']);
    }

    private function scheduledPostsForUser(User $user)
    {
        return $this->scopedPostQuery($user)
            ->with('author:id,name')
            ->where('status', PostStatus::Scheduled)
            ->orderBy('publish_at')
            ->limit(self::RECENT_LIMIT)
            ->get(['id', 'title', 'slug', 'status', 'author_id', 'publish_at']);
    }

    /**
     * @return Builder<Post>
     */
    private function scopedPostQuery(User $user): Builder
    {
        $query = Post::query();

        if ($this->requiresOwnership($user)) {
            $query->where('author_id', $user->id);
        }

        return $query;
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
