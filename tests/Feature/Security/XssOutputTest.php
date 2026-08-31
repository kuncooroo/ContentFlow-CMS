<?php

namespace Tests\Feature\Security;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class XssOutputTest extends TestCase
{
    use DatabaseTransactions;

    public function test_post_title_with_html_is_escaped(): void
    {
        $post = Post::factory()->published()->create([
            'title' => '<script>alert("xss")</script>Safe title',
            'content' => 'Plain body',
        ]);

        $html = (string) $this->get(route('public.posts.show', $post))->getContent();

        $this->assertStringNotContainsString('<script>alert("xss")</script>', $html);
        $this->assertStringContainsString('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;Safe title', $html);
    }

    public function test_post_content_with_html_is_escaped(): void
    {
        $post = Post::factory()->published()->create([
            'title' => 'XSS Post',
            'content' => '<img src=x onerror=alert(1)>Hello',
        ]);

        $html = (string) $this->get(route('public.posts.show', $post))->getContent();

        $this->assertStringNotContainsString('<img src=x onerror=alert(1)>', $html);
        $this->assertStringContainsString('&lt;img src=x onerror=alert(1)&gt;Hello', $html);
    }

    public function test_approved_comment_with_html_is_escaped(): void
    {
        $post = Post::factory()->published()->create();
        Comment::factory()->approved()->create([
            'post_id' => $post->id,
            'author_name' => '<b>Reader</b>',
            'content' => '<script>alert(1)</script>Nice post',
        ]);

        $html = (string) $this->get(route('public.posts.show', $post))->getContent();

        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;Nice post', $html);
        $this->assertStringContainsString('&lt;b&gt;Reader&lt;/b&gt;', $html);
    }
}
