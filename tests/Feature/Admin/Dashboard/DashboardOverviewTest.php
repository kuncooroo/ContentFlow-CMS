<?php

namespace Tests\Feature\Admin\Dashboard;

use App\Enums\CommentStatus;
use App\Enums\PostStatus;
use App\Livewire\Admin\Dashboard\Overview;
use App\Models\Comment;
use App\Models\Page;
use App\Models\Post;
use App\Models\User;
use App\Queries\Dashboard\DashboardSummaryQuery;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardOverviewTest extends TestCase
{
    use DatabaseTransactions;

    public function test_editor_sees_operational_post_counts_for_all_authors(): void
    {
        $editor = User::factory()->editor()->create();
        $author = User::factory()->author()->create();

        Post::factory()->create(['author_id' => $author->id, 'status' => PostStatus::Draft]);
        Post::factory()->published()->create(['author_id' => $author->id]);
        Post::factory()->scheduled()->create(['author_id' => $author->id]);
        Post::factory()->archived()->create(['author_id' => $author->id]);

        Livewire::actingAs($editor)
            ->test(Overview::class)
            ->assertSee('Operational overview')
            ->assertSee('Draft')
            ->assertSee('Scheduled')
            ->assertSee('Published')
            ->assertSee('Archived');

        $summary = app(DashboardSummaryQuery::class)->forUser($editor);

        $this->assertSame(1, $summary->postCounts['draft']);
        $this->assertSame(1, $summary->postCounts['scheduled']);
        $this->assertSame(1, $summary->postCounts['published']);
        $this->assertSame(1, $summary->postCounts['archived']);
    }

    public function test_author_only_sees_own_post_metrics(): void
    {
        $author = User::factory()->author()->create();
        $otherAuthor = User::factory()->author()->create();

        Post::factory()->create([
            'author_id' => $author->id,
            'title' => 'My Draft Post',
            'status' => PostStatus::Draft,
        ]);
        Post::factory()->published()->create([
            'author_id' => $otherAuthor->id,
            'title' => 'Someone Else Published',
        ]);

        $summary = app(DashboardSummaryQuery::class)->forUser($author);

        $this->assertSame(1, $summary->postCounts['draft']);
        $this->assertSame(0, $summary->postCounts['published']);
        $this->assertCount(1, $summary->recentPosts);
        $this->assertSame('My Draft Post', $summary->recentPosts->first()->title);
    }

    public function test_scheduled_posts_are_ordered_by_publish_at(): void
    {
        $editor = User::factory()->editor()->create();

        Post::factory()->scheduled()->create([
            'title' => 'Later Post',
            'publish_at' => Carbon::parse('2026-09-10 09:00:00'),
        ]);
        Post::factory()->scheduled()->create([
            'title' => 'Soon Post',
            'publish_at' => Carbon::parse('2026-09-01 09:00:00'),
        ]);

        $summary = app(DashboardSummaryQuery::class)->forUser($editor);

        $this->assertSame(['Soon Post', 'Later Post'], $summary->scheduledPosts->pluck('title')->all());
    }

    public function test_editor_sees_pending_comment_count(): void
    {
        $editor = User::factory()->editor()->create();
        $post = Post::factory()->published()->create();

        Comment::factory()->create([
            'post_id' => $post->id,
            'status' => CommentStatus::Pending,
        ]);
        Comment::factory()->approved()->create([
            'post_id' => $post->id,
        ]);

        $summary = app(DashboardSummaryQuery::class)->forUser($editor);

        $this->assertSame(1, $summary->pendingComments);

        Livewire::actingAs($editor)
            ->test(Overview::class)
            ->assertSee('Pending moderation');
    }

    public function test_author_does_not_see_pending_comment_metrics(): void
    {
        $author = User::factory()->author()->create();
        $post = Post::factory()->published()->create(['author_id' => $author->id]);

        Comment::factory()->create([
            'post_id' => $post->id,
            'status' => CommentStatus::Pending,
        ]);

        $summary = app(DashboardSummaryQuery::class)->forUser($author);

        $this->assertNull($summary->pendingComments);

        Livewire::actingAs($author)
            ->test(Overview::class)
            ->assertDontSee('Pending moderation');
    }

    public function test_author_does_not_see_page_metrics(): void
    {
        $author = User::factory()->author()->create();
        Page::factory()->create(['title' => 'Hidden Page']);

        $summary = app(DashboardSummaryQuery::class)->forUser($author);

        $this->assertNull($summary->pageCounts);
        $this->assertTrue($summary->recentPages->isEmpty());
    }

    public function test_dashboard_uses_bounded_query_count(): void
    {
        $editor = User::factory()->editor()->create();

        Post::factory()->count(3)->create();
        Page::factory()->count(2)->create();
        $post = Post::factory()->published()->create();
        Comment::factory()->count(2)->create([
            'post_id' => $post->id,
            'status' => CommentStatus::Pending,
        ]);

        DB::flushQueryLog();
        DB::enableQueryLog();

        app(DashboardSummaryQuery::class)->forUser($editor);

        $this->assertLessThanOrEqual(8, count(DB::getQueryLog()));
    }
}
