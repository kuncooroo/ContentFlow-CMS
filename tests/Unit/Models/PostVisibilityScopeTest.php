<?php

namespace Tests\Unit\Models;

use App\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PostVisibilityScopeTest extends TestCase
{
    use DatabaseTransactions;

    public function test_publicly_visible_scope_includes_published_posts(): void
    {
        $visible = Post::factory()->published()->create(['title' => 'Visible Post']);
        Post::factory()->create(['title' => 'Draft Post']);
        Post::factory()->scheduled()->create(['title' => 'Scheduled Post']);
        Post::factory()->archived()->create(['title' => 'Archived Post']);

        $titles = Post::query()->publiclyVisible()->pluck('title')->all();

        $this->assertSame(['Visible Post'], $titles);
    }

    public function test_scheduled_posts_are_not_publicly_visible(): void
    {
        Post::factory()->scheduled()->create();

        $this->assertSame(0, Post::query()->publiclyVisible()->count());
    }

    public function test_due_for_publishing_scope_finds_eligible_scheduled_posts(): void
    {
        Carbon::setTestNow('2026-08-31 12:00:00');

        $due = Post::factory()->scheduled()->create([
            'publish_at' => now()->subMinute(),
        ]);
        Post::factory()->scheduled()->create([
            'publish_at' => now()->addHour(),
        ]);

        $ids = Post::query()->dueForPublishing()->pluck('id')->all();

        $this->assertSame([$due->id], $ids);

        Carbon::setTestNow();
    }

    public function test_is_publicly_visible_helper(): void
    {
        $published = Post::factory()->published()->make();
        $draft = Post::factory()->make(['status' => PostStatus::Draft]);
        $scheduled = Post::factory()->scheduled()->make();

        $this->assertTrue($published->isPubliclyVisible());
        $this->assertFalse($draft->isPubliclyVisible());
        $this->assertFalse($scheduled->isPubliclyVisible());
    }
}
