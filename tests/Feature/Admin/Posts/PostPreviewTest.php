<?php

namespace Tests\Feature\Admin\Posts;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PostPreviewTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_cannot_preview_post(): void
    {
        $post = Post::factory()->create();

        $this->get(route('admin.posts.preview', $post))
            ->assertRedirect(route('login'));
    }

    public function test_author_can_preview_own_draft_post(): void
    {
        $author = User::factory()->author()->create();
        $post = Post::factory()->create([
            'author_id' => $author->id,
            'title' => 'Preview Draft Title',
            'content' => 'Preview body content.',
        ]);

        $this->actingAs($author)
            ->get(route('admin.posts.preview', $post))
            ->assertOk()
            ->assertSee('Preview mode')
            ->assertSee('Preview Draft Title')
            ->assertSee('Preview body content.');
    }

    public function test_author_cannot_preview_another_users_post(): void
    {
        $author = User::factory()->author()->create();
        $otherAuthor = User::factory()->author()->create();
        $post = Post::factory()->create(['author_id' => $otherAuthor->id]);

        $this->actingAs($author)
            ->get(route('admin.posts.preview', $post))
            ->assertForbidden();
    }

    public function test_editor_can_preview_any_post(): void
    {
        $editor = User::factory()->editor()->create();
        $post = Post::factory()->create(['title' => 'Editor Preview Post']);

        $this->actingAs($editor)
            ->get(route('admin.posts.preview', $post))
            ->assertOk()
            ->assertSee('Editor Preview Post');
    }
}
