<?php

namespace Tests\Feature\Public;

use App\Models\Post;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PublicPostSeoTest extends TestCase
{
    use DatabaseTransactions;

    public function test_public_post_renders_noindex_when_robots_index_is_false(): void
    {
        $post = Post::factory()->published()->create([
            'robots_index' => false,
            'seo_title' => 'Noindex Post',
        ]);

        $this->get(route('public.posts.show', $post))
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex, follow">', false)
            ->assertSee('<title>Noindex Post</title>', false);
    }

    public function test_public_post_omits_noindex_when_robots_index_is_true(): void
    {
        $post = Post::factory()->published()->create([
            'robots_index' => true,
            'seo_title' => 'Indexable Post',
        ]);

        $response = $this->get(route('public.posts.show', $post));

        $response->assertOk();
        $this->assertStringNotContainsString('noindex', (string) $response->getContent());
        $response->assertSee('<title>Indexable Post</title>', false);
    }
}
