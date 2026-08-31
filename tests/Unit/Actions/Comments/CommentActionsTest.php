<?php

namespace Tests\Unit\Actions\Comments;

use App\Actions\Comments\SubmitComment;
use App\Enums\CommentStatus;
use App\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CommentActionsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_submit_comment_creates_pending_record(): void
    {
        $post = Post::factory()->published()->create();

        $comment = app(SubmitComment::class)->handle(
            post: $post,
            authorName: 'Visitor',
            authorEmail: 'visitor@example.com',
            content: 'Nice post',
        );

        $this->assertSame(CommentStatus::Pending, $comment->status);
        $this->assertSame($post->id, $comment->post_id);
    }

    public function test_submit_comment_rejects_non_public_post(): void
    {
        $post = Post::factory()->create(['status' => PostStatus::Draft]);

        $this->expectException(ValidationException::class);

        app(SubmitComment::class)->handle(
            post: $post,
            authorName: 'Visitor',
            authorEmail: 'visitor@example.com',
            content: 'Nice post',
        );
    }

    public function test_comment_status_transitions_match_business_flow(): void
    {
        $pending = CommentStatus::Pending;
        $approved = CommentStatus::Approved;
        $spam = CommentStatus::Spam;
        $rejected = CommentStatus::Rejected;

        $this->assertTrue($pending->canTransitionTo($approved));
        $this->assertTrue($approved->canTransitionTo($spam));
        $this->assertTrue($rejected->canTransitionTo($approved));
        $this->assertFalse($spam->canTransitionTo($pending));
    }
}
