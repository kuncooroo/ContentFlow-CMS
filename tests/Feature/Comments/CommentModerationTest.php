<?php

namespace Tests\Feature\Comments;

use App\Actions\Comments\ModerateComment;
use App\Enums\CommentStatus;
use App\Livewire\Admin\Comments\Index;
use App\Models\Comment;
use App\Models\User;
use App\Support\Audit\ActivityEvent;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

class CommentModerationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_editor_can_approve_pending_comment(): void
    {
        $editor = User::factory()->editor()->create();
        $comment = Comment::factory()->create(['status' => CommentStatus::Pending]);

        Livewire::actingAs($editor)
            ->test(Index::class)
            ->call('moderate', $comment->id, 'approved')
            ->assertHasNoErrors();

        $this->assertSame(CommentStatus::Approved, $comment->fresh()->status);
        $this->assertDatabaseHas('activity_logs', [
            'event' => ActivityEvent::CommentModerated,
            'subject_id' => $comment->id,
        ]);
    }

    public function test_author_cannot_moderate_comments(): void
    {
        $author = User::factory()->author()->create();
        $comment = Comment::factory()->create();

        Livewire::actingAs($author)
            ->test(Index::class)
            ->assertForbidden();
    }

    public function test_invalid_transition_is_rejected(): void
    {
        $editor = User::factory()->editor()->create();
        $comment = Comment::factory()->rejected()->create();

        $this->expectException(ValidationException::class);

        app(ModerateComment::class)->handle(
            $comment,
            CommentStatus::Spam,
            $editor,
        );
    }

    public function test_spam_comment_is_not_publicly_visible(): void
    {
        $post = Post::factory()->published()->create();
        Comment::factory()->spam()->create([
            'post_id' => $post->id,
            'content' => 'Spam comment body',
        ]);

        $this->get(route('public.posts.show', $post))
            ->assertOk()
            ->assertDontSee('Spam comment body');
    }

    public function test_rejected_comment_can_be_approved(): void
    {
        $editor = User::factory()->editor()->create();
        $comment = Comment::factory()->rejected()->create();

        app(ModerateComment::class)->handle(
            $comment,
            CommentStatus::Approved,
            $editor,
        );

        $this->assertSame(CommentStatus::Approved, $comment->fresh()->status);
    }
}
