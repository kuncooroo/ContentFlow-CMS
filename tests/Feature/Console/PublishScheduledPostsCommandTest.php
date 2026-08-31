<?php

namespace Tests\Feature\Console;

use App\Actions\Posts\PublishPost;
use App\Actions\Posts\PublishScheduledPosts;
use App\Enums\PostStatus;
use App\Models\ActivityLog;
use App\Models\Post;
use App\Support\Audit\ActivityEvent;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Mockery;
use Tests\TestCase;

class PublishScheduledPostsCommandTest extends TestCase
{
    use DatabaseTransactions;

    public function test_due_scheduled_post_is_published_by_command(): void
    {
        Carbon::setTestNow('2026-08-31 12:00:00');

        $duePost = Post::factory()->scheduled()->create([
            'publish_at' => now()->subMinute(),
            'title' => 'Due Scheduled Post',
        ]);
        $futurePost = Post::factory()->scheduled()->create([
            'publish_at' => now()->addHour(),
            'title' => 'Future Scheduled Post',
        ]);
        $draftPost = Post::factory()->create([
            'status' => PostStatus::Draft,
            'publish_at' => now()->subHour(),
        ]);

        Artisan::call('content:publish-scheduled-posts');

        $this->assertSame(PostStatus::Published, $duePost->fresh()->status);
        $this->assertSame(PostStatus::Scheduled, $futurePost->fresh()->status);
        $this->assertSame(PostStatus::Draft, $draftPost->fresh()->status);
        $this->assertDatabaseHas('activity_logs', [
            'event' => ActivityEvent::PostPublished,
            'subject_id' => $duePost->id,
            'actor_user_id' => null,
        ]);

        Carbon::setTestNow();
    }

    public function test_command_is_idempotent_on_second_run(): void
    {
        Carbon::setTestNow('2026-08-31 12:00:00');

        Post::factory()->scheduled()->create([
            'publish_at' => now()->subMinute(),
        ]);

        Artisan::call('content:publish-scheduled-posts');
        Artisan::call('content:publish-scheduled-posts');

        $this->assertSame(1, Post::query()->where('status', PostStatus::Published)->count());
        $this->assertSame(
            1,
            ActivityLog::query()
                ->where('event', ActivityEvent::PostPublished)
                ->count(),
        );

        Carbon::setTestNow();
    }

    public function test_command_continues_after_single_post_failure(): void
    {
        Carbon::setTestNow('2026-08-31 12:00:00');

        $invalidPost = Post::factory()->scheduled()->create([
            'publish_at' => now()->subMinute(),
            'content' => '',
        ]);
        $validPost = Post::factory()->scheduled()->create([
            'publish_at' => now()->subMinute(),
        ]);

        $exitCode = Artisan::call('content:publish-scheduled-posts');

        $this->assertSame(1, $exitCode);
        $this->assertSame(PostStatus::Scheduled, $invalidPost->fresh()->status);
        $this->assertSame(PostStatus::Published, $validPost->fresh()->status);

        Carbon::setTestNow();
    }

    public function test_command_is_registered_on_scheduler(): void
    {
        $event = collect(Schedule::events())
            ->first(fn ($scheduledEvent) => str_contains($scheduledEvent->command ?? '', 'content:publish-scheduled-posts'));

        $this->assertNotNull($event);
        $this->assertSame('* * * * *', $event->expression);
    }

    public function test_action_delegates_to_publish_post(): void
    {
        Carbon::setTestNow('2026-08-31 12:00:00');

        $post = Post::factory()->scheduled()->create([
            'publish_at' => now()->subMinute(),
        ]);

        $publishPost = Mockery::mock(PublishPost::class);
        $publishPost->shouldReceive('handle')
            ->once()
            ->with(Mockery::on(fn (Post $candidate) => $candidate->is($post)), null)
            ->andReturn($post);

        $this->app->instance(PublishPost::class, $publishPost);

        $result = app(PublishScheduledPosts::class)->handle();

        $this->assertSame(1, $result['published']);
        $this->assertSame(0, $result['failed']);

        Carbon::setTestNow();
    }
}
