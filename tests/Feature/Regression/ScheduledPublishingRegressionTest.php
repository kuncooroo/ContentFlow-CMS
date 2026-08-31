<?php

namespace Tests\Feature\Regression;

use App\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ScheduledPublishingRegressionTest extends TestCase
{
    use DatabaseTransactions;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_scheduled_post_becomes_publicly_visible_after_publish_command(): void
    {
        Carbon::setTestNow('2026-08-31 12:00:00');

        $post = Post::factory()->scheduled()->create([
            'title' => 'Regression Scheduled Post',
            'publish_at' => now()->subMinute(),
        ]);

        $this->get(route('public.posts.show', $post))
            ->assertNotFound();

        Artisan::call('content:publish-scheduled-posts');

        $this->assertSame(PostStatus::Published, $post->fresh()->status);

        $this->get(route('public.posts.show', $post))
            ->assertOk()
            ->assertSee('Regression Scheduled Post');
    }

    public function test_future_scheduled_post_stays_hidden_before_due(): void
    {
        Carbon::setTestNow('2026-08-31 12:00:00');

        $post = Post::factory()->scheduled()->create([
            'publish_at' => now()->addHour(),
            'title' => 'Future Scheduled Post',
        ]);

        Artisan::call('content:publish-scheduled-posts');

        $this->assertSame(PostStatus::Scheduled, $post->fresh()->status);

        $this->get(route('public.posts.show', $post))
            ->assertNotFound();
    }

    public function test_archived_post_is_excluded_from_public_blog_listing(): void
    {
        Post::factory()->published()->create(['title' => 'Visible Blog Post']);
        Post::factory()->archived()->create(['title' => 'Archived Blog Post']);

        $this->get(route('public.posts.index'))
            ->assertOk()
            ->assertSee('Visible Blog Post')
            ->assertDontSee('Archived Blog Post');
    }
}
