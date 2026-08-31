<?php

namespace Tests\Feature\Comments;

use App\Enums\CommentStatus;
use App\Enums\PostStatus;
use App\Models\Comment;
use App\Models\Post;
use App\Models\SiteSetting;
use App\Services\Settings\SiteSettings;
use App\Support\Comments\CommentsGate;
use App\Support\Ui\Flash;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CommentSubmissionTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_can_submit_comment_on_published_post(): void
    {
        $post = Post::factory()->published()->create();

        $response = $this->post(route('public.comments.store', $post), [
            'author_name' => 'Jane Reader',
            'author_email' => 'jane@example.com',
            'content' => 'Great article!',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas(Flash::SUCCESS);

        $comment = Comment::query()->first();
        $this->assertNotNull($comment);
        $this->assertSame(CommentStatus::Pending, $comment->status);
        $this->assertSame('Jane Reader', $comment->author_name);
    }

    public function test_pending_comment_is_not_publicly_visible(): void
    {
        $post = Post::factory()->published()->create();
        Comment::factory()->create([
            'post_id' => $post->id,
            'content' => 'Hidden pending comment',
        ]);

        $this->get(route('public.posts.show', $post))
            ->assertOk()
            ->assertDontSee('Hidden pending comment');
    }

    public function test_approved_comment_is_visible_on_public_post(): void
    {
        $post = Post::factory()->published()->create();
        Comment::factory()->approved()->create([
            'post_id' => $post->id,
            'content' => 'Visible approved comment',
        ]);

        $this->get(route('public.posts.show', $post))
            ->assertOk()
            ->assertSee('Visible approved comment');
    }

    public function test_cannot_comment_on_draft_post(): void
    {
        $post = Post::factory()->create(['status' => PostStatus::Draft]);

        $this->post(route('public.comments.store', $post), [
            'author_name' => 'Jane Reader',
            'author_email' => 'jane@example.com',
            'content' => 'Should not work',
        ])->assertNotFound();
    }

    public function test_comments_disabled_when_site_setting_is_off(): void
    {
        SiteSetting::query()->updateOrCreate(
            ['id' => SiteSetting::SINGLETON_ID],
            [
                'site_name' => 'ContentFlow CMS',
                'comments_enabled' => false,
                'timezone' => 'UTC',
                'locale' => 'en',
                'default_robots_index' => true,
            ],
        );

        app(SiteSettings::class)->invalidate();

        $this->assertFalse(CommentsGate::enabled());

        $post = Post::factory()->published()->create();

        $this->post(route('public.comments.store', $post), [
            'author_name' => 'Jane Reader',
            'author_email' => 'jane@example.com',
            'content' => 'Should fail',
        ])->assertSessionHasErrors(['content']);
    }
}
