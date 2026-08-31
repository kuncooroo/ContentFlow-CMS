<?php

namespace Tests\Feature\Public;

use App\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PublicContentVisibilityTest extends TestCase
{
    use DatabaseTransactions;

    public function test_published_post_is_publicly_visible(): void
    {
        $post = Post::factory()->published()->create(['title' => 'Visible Post']);

        $this->get(route('public.posts.show', $post))
            ->assertOk()
            ->assertSee('Visible Post');
    }

    public function test_draft_post_returns_404(): void
    {
        $post = Post::factory()->create(['title' => 'Draft Post']);

        $this->get(route('public.posts.show', $post))
            ->assertNotFound();
    }

    public function test_scheduled_post_before_due_returns_404(): void
    {
        $post = Post::factory()->scheduled()->create(['title' => 'Scheduled Post']);

        $this->get(route('public.posts.show', $post))
            ->assertNotFound();
    }

    public function test_archived_post_returns_404(): void
    {
        $post = Post::factory()->archived()->create(['title' => 'Archived Post']);

        $this->get(route('public.posts.show', $post))
            ->assertNotFound();
    }

    public function test_published_post_with_future_publish_at_returns_404(): void
    {
        $post = Post::factory()->create([
            'status' => PostStatus::Published,
            'publish_at' => now()->addDay(),
            'title' => 'Future Publish Post',
        ]);

        $this->get(route('public.posts.show', $post))
            ->assertNotFound();
    }

    public function test_homepage_lists_only_publicly_visible_posts(): void
    {
        $visible = Post::factory()->published()->create(['title' => 'Listed Post']);
        Post::factory()->create(['title' => 'Draft Post']);
        Post::factory()->archived()->create(['title' => 'Archived Post']);
        Post::factory()->scheduled()->create(['title' => 'Scheduled Post']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Listed Post')
            ->assertDontSee('Draft Post')
            ->assertDontSee('Archived Post')
            ->assertDontSee('Scheduled Post');
    }

    public function test_blog_index_lists_only_publicly_visible_posts(): void
    {
        Post::factory()->published()->create(['title' => 'Blog Visible Post']);
        Post::factory()->create(['title' => 'Blog Draft Post']);

        $this->get(route('public.posts.index'))
            ->assertOk()
            ->assertSee('Blog Visible Post')
            ->assertDontSee('Blog Draft Post');
    }

    public function test_unknown_post_slug_returns_404_page(): void
    {
        $this->get('/posts/does-not-exist')
            ->assertNotFound()
            ->assertSee('Page not found');
    }
}
