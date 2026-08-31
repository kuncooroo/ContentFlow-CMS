<?php

namespace Tests\Feature\Admin\Posts;

use App\Enums\PostStatus;
use App\Livewire\Admin\Posts\Edit;
use App\Models\Post;
use App\Models\User;
use App\Support\Audit\ActivityEvent;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class PostPublishingTest extends TestCase
{
    use DatabaseTransactions;

    public function test_editor_can_publish_draft_post_from_edit_screen(): void
    {
        $editor = User::factory()->editor()->create();
        $post = Post::factory()->create(['status' => PostStatus::Draft]);

        Livewire::actingAs($editor)
            ->test(Edit::class, ['post' => $post])
            ->call('publish')
            ->assertHasNoErrors();

        $this->assertSame(PostStatus::Published, $post->fresh()->status);
        $this->assertDatabaseHas('activity_logs', [
            'event' => ActivityEvent::PostPublished,
            'subject_id' => $post->id,
        ]);
    }

    public function test_author_cannot_publish_post(): void
    {
        $author = User::factory()->author()->create();
        $post = Post::factory()->create([
            'author_id' => $author->id,
            'status' => PostStatus::Draft,
        ]);

        Livewire::actingAs($author)
            ->test(Edit::class, ['post' => $post])
            ->call('publish')
            ->assertForbidden();
    }

    public function test_editor_can_schedule_draft_post(): void
    {
        $editor = User::factory()->editor()->create();
        $post = Post::factory()->create(['status' => PostStatus::Draft]);
        $scheduledAt = now()->addDay()->format('Y-m-d\TH:i');

        Livewire::actingAs($editor)
            ->test(Edit::class, ['post' => $post])
            ->set('scheduledAt', $scheduledAt)
            ->call('schedule')
            ->assertHasNoErrors();

        $fresh = $post->fresh();
        $this->assertSame(PostStatus::Scheduled, $fresh->status);
        $this->assertNotNull($fresh->publish_at);
    }

    public function test_editor_can_cancel_scheduled_post(): void
    {
        $editor = User::factory()->editor()->create();
        $post = Post::factory()->scheduled()->create();

        Livewire::actingAs($editor)
            ->test(Edit::class, ['post' => $post])
            ->call('cancelSchedule')
            ->assertHasNoErrors();

        $fresh = $post->fresh();
        $this->assertSame(PostStatus::Draft, $fresh->status);
        $this->assertNull($fresh->publish_at);
    }

    public function test_editor_can_archive_published_post(): void
    {
        $editor = User::factory()->editor()->create();
        $post = Post::factory()->published()->create();

        Livewire::actingAs($editor)
            ->test(Edit::class, ['post' => $post])
            ->call('archive')
            ->assertHasNoErrors();

        $this->assertSame(PostStatus::Archived, $post->fresh()->status);
        $this->assertDatabaseHas('activity_logs', [
            'event' => ActivityEvent::PostArchived,
            'subject_id' => $post->id,
        ]);
    }

    public function test_editor_can_unpublish_published_post(): void
    {
        $editor = User::factory()->editor()->create();
        $post = Post::factory()->published()->create();

        Livewire::actingAs($editor)
            ->test(Edit::class, ['post' => $post])
            ->call('unpublish')
            ->assertHasNoErrors();

        $fresh = $post->fresh();
        $this->assertSame(PostStatus::Draft, $fresh->status);
        $this->assertNull($fresh->publish_at);
    }

    public function test_editor_can_restore_archived_post_to_draft(): void
    {
        $editor = User::factory()->editor()->create();
        $post = Post::factory()->archived()->create();

        Livewire::actingAs($editor)
            ->test(Edit::class, ['post' => $post])
            ->call('restoreToDraft')
            ->assertHasNoErrors();

        $this->assertSame(PostStatus::Draft, $post->fresh()->status);
    }

    public function test_schedule_rejects_past_datetime_in_livewire(): void
    {
        $editor = User::factory()->editor()->create();
        $post = Post::factory()->create(['status' => PostStatus::Draft]);

        Livewire::actingAs($editor)
            ->test(Edit::class, ['post' => $post])
            ->set('scheduledAt', now()->subHour()->format('Y-m-d\TH:i'))
            ->call('schedule')
            ->assertHasErrors(['publish_at']);
    }
}
