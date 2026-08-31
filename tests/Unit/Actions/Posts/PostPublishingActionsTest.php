<?php

namespace Tests\Unit\Actions\Posts;

use App\Actions\Posts\ArchivePost;
use App\Actions\Posts\CancelScheduledPost;
use App\Actions\Posts\PublishPost;
use App\Actions\Posts\RestorePostToDraft;
use App\Actions\Posts\SchedulePost;
use App\Actions\Posts\UnpublishPost;
use App\Enums\PostStatus;
use App\Models\ActivityLog;
use App\Models\Post;
use App\Models\User;
use App\Support\Audit\ActivityEvent;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PostPublishingActionsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_draft_post_can_be_published(): void
    {
        $editor = User::factory()->editor()->create();
        $post = Post::factory()->create(['status' => PostStatus::Draft]);

        $published = app(PublishPost::class)->handle($post, $editor);

        $this->assertSame(PostStatus::Published, $published->status);
        $this->assertNotNull($published->publish_at);
        $this->assertDatabaseHas('activity_logs', [
            'event' => ActivityEvent::PostPublished,
            'subject_type' => 'post',
            'subject_id' => $post->id,
            'actor_user_id' => $editor->id,
        ]);
    }

    public function test_scheduled_post_can_be_published_immediately(): void
    {
        $editor = User::factory()->editor()->create();
        $post = Post::factory()->scheduled()->create();

        $published = app(PublishPost::class)->handle($post, $editor);

        $this->assertSame(PostStatus::Published, $published->status);
    }

    public function test_publish_is_idempotent_when_already_published(): void
    {
        $post = Post::factory()->published()->create();
        $originalPublishAt = $post->publish_at;

        $result = app(PublishPost::class)->handle($post, User::factory()->editor()->create());

        $this->assertSame(PostStatus::Published, $result->status);
        $this->assertTrue($originalPublishAt->eq($result->publish_at));
        $this->assertSame(
            0,
            ActivityLog::query()->where('event', ActivityEvent::PostPublished)->where('subject_id', $post->id)->count(),
        );
    }

    public function test_archived_post_cannot_be_published(): void
    {
        $post = Post::factory()->archived()->create();

        $this->expectException(ValidationException::class);

        app(PublishPost::class)->handle($post, User::factory()->editor()->create());
    }

    public function test_post_without_body_cannot_be_published(): void
    {
        $post = Post::factory()->create([
            'status' => PostStatus::Draft,
            'content' => '',
        ]);

        $this->expectException(ValidationException::class);

        app(PublishPost::class)->handle($post, User::factory()->editor()->create());
    }

    public function test_draft_post_can_be_scheduled(): void
    {
        Carbon::setTestNow('2026-08-31 12:00:00');

        $editor = User::factory()->editor()->create();
        $post = Post::factory()->create(['status' => PostStatus::Draft]);
        $publishAt = now()->addHours(2);

        $scheduled = app(SchedulePost::class)->handle($post, $editor, $publishAt);

        $this->assertSame(PostStatus::Scheduled, $scheduled->status);
        $this->assertTrue($scheduled->publish_at->eq($publishAt));
        $this->assertDatabaseHas('activity_logs', [
            'event' => ActivityEvent::PostScheduled,
            'subject_id' => $post->id,
        ]);

        Carbon::setTestNow();
    }

    public function test_schedule_rejects_past_datetime(): void
    {
        Carbon::setTestNow('2026-08-31 12:00:00');

        $post = Post::factory()->create(['status' => PostStatus::Draft]);

        $this->expectException(ValidationException::class);

        app(SchedulePost::class)->handle(
            $post,
            User::factory()->editor()->create(),
            now()->subMinute(),
        );

        Carbon::setTestNow();
    }

    public function test_published_post_cannot_be_scheduled(): void
    {
        $post = Post::factory()->published()->create();

        $this->expectException(ValidationException::class);

        app(SchedulePost::class)->handle(
            $post,
            User::factory()->editor()->create(),
            now()->addDay(),
        );
    }

    public function test_scheduled_post_can_be_returned_to_draft(): void
    {
        $post = Post::factory()->scheduled()->create();

        $draft = app(CancelScheduledPost::class)->handle($post);

        $this->assertSame(PostStatus::Draft, $draft->status);
        $this->assertNull($draft->publish_at);
    }

    public function test_draft_post_cannot_cancel_schedule(): void
    {
        $post = Post::factory()->create(['status' => PostStatus::Draft]);

        $this->expectException(ValidationException::class);

        app(CancelScheduledPost::class)->handle($post);
    }

    public function test_published_post_can_be_archived(): void
    {
        $editor = User::factory()->editor()->create();
        $post = Post::factory()->published()->create();

        $archived = app(ArchivePost::class)->handle($post, $editor);

        $this->assertSame(PostStatus::Archived, $archived->status);
        $this->assertDatabaseHas('activity_logs', [
            'event' => ActivityEvent::PostArchived,
            'subject_id' => $post->id,
        ]);
    }

    public function test_draft_post_cannot_be_archived(): void
    {
        $post = Post::factory()->create(['status' => PostStatus::Draft]);

        $this->expectException(ValidationException::class);

        app(ArchivePost::class)->handle($post, User::factory()->editor()->create());
    }

    public function test_published_post_can_be_unpublished_to_draft(): void
    {
        $post = Post::factory()->published()->create();

        $draft = app(UnpublishPost::class)->handle($post);

        $this->assertSame(PostStatus::Draft, $draft->status);
        $this->assertNull($draft->publish_at);
    }

    public function test_archived_post_can_be_restored_to_draft(): void
    {
        $post = Post::factory()->archived()->create();

        $draft = app(RestorePostToDraft::class)->handle($post);

        $this->assertSame(PostStatus::Draft, $draft->status);
        $this->assertNull($draft->publish_at);
    }

    public function test_published_post_cannot_be_restored_via_restore_action(): void
    {
        $post = Post::factory()->published()->create();

        $this->expectException(ValidationException::class);

        app(RestorePostToDraft::class)->handle($post);
    }
}
