<?php

namespace Tests\Feature\Admin\Posts;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PostAccessTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_cannot_access_posts_index(): void
    {
        $this->get(route('admin.posts.index'))
            ->assertRedirect(route('login'));
    }

    public function test_author_can_access_posts_index(): void
    {
        $author = User::factory()->author()->create();

        $this->actingAs($author)
            ->get(route('admin.posts.index'))
            ->assertOk()
            ->assertSee('Create and manage draft posts');
    }
}
