<?php

namespace Tests\Feature\Security;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CsrfProtectionTest extends TestCase
{
    use DatabaseTransactions;

    public function test_login_post_without_csrf_token_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->get(route('login'));

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(419);
        $this->assertGuest();
    }

    public function test_comment_post_without_csrf_token_is_rejected(): void
    {
        $post = Post::factory()->published()->create();

        $this->get(route('public.posts.show', $post));

        $response = $this->post(route('public.comments.store', $post), [
            'author_name' => 'Reader',
            'author_email' => 'reader@example.com',
            'content' => 'Hello',
        ]);

        $response->assertStatus(419);
    }
}
