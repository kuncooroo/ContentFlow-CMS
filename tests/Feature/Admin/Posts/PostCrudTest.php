<?php

namespace Tests\Feature\Admin\Posts;

use App\Enums\PostStatus;
use App\Livewire\Admin\Posts\Create;
use App\Livewire\Admin\Posts\Edit;
use App\Livewire\Admin\Posts\Index;
use App\Models\Category;
use App\Models\Media;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class PostCrudTest extends TestCase
{
    use DatabaseTransactions;

    public function test_author_can_create_draft_post_with_relations(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();
        $media = Media::factory()->create();

        Livewire::actingAs($author)
            ->test(Create::class)
            ->set('title', 'My First Post')
            ->set('content', 'Draft body content.')
            ->set('excerpt', 'Short summary')
            ->set('featured_media_id', $media->id)
            ->set('selectedCategories', [$category->id])
            ->set('selectedTags', [$tag->id])
            ->call('save')
            ->assertRedirect(route('admin.posts.index'));

        $post = Post::query()->first();

        $this->assertNotNull($post);
        $this->assertSame(PostStatus::Draft, $post->status);
        $this->assertSame('my-first-post', $post->slug);
        $this->assertSame($author->id, $post->author_id);
        $this->assertSame($media->id, $post->featured_media_id);
        $this->assertTrue($post->categories->contains('id', $category->id));
        $this->assertTrue($post->tags->contains('id', $tag->id));
    }

    public function test_duplicate_slug_is_rejected_on_create(): void
    {
        $author = User::factory()->author()->create();
        Post::factory()->create(['slug' => 'taken-slug']);

        Livewire::actingAs($author)
            ->test(Create::class)
            ->set('title', 'Another Post')
            ->set('slug', 'taken-slug')
            ->set('content', 'Body')
            ->call('save')
            ->assertHasErrors(['slug']);
    }

    public function test_author_can_update_own_post(): void
    {
        $author = User::factory()->author()->create();
        $post = Post::factory()->create([
            'author_id' => $author->id,
            'title' => 'Before',
            'slug' => 'before',
            'content' => 'Before content',
        ]);

        Livewire::actingAs($author)
            ->test(Edit::class, ['post' => $post])
            ->set('title', 'After')
            ->set('slug', 'after')
            ->set('content', 'After content')
            ->call('save')
            ->assertRedirect(route('admin.posts.index'));

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'After',
            'slug' => 'after',
            'status' => PostStatus::Draft->value,
        ]);
    }

    public function test_author_cannot_edit_another_users_post(): void
    {
        $author = User::factory()->author()->create();
        $otherAuthor = User::factory()->author()->create();
        $post = Post::factory()->create(['author_id' => $otherAuthor->id]);

        Livewire::actingAs($author)
            ->test(Edit::class, ['post' => $post])
            ->assertForbidden();
    }

    public function test_author_index_lists_only_own_posts(): void
    {
        $author = User::factory()->author()->create();
        $otherAuthor = User::factory()->author()->create();

        $ownPost = Post::factory()->create([
            'author_id' => $author->id,
            'title' => 'Own Draft Post',
        ]);
        Post::factory()->create([
            'author_id' => $otherAuthor->id,
            'title' => 'Hidden Draft Post',
        ]);

        Livewire::actingAs($author)
            ->test(Index::class)
            ->assertSee('Own Draft Post')
            ->assertDontSee('Hidden Draft Post');
    }

    public function test_editor_can_edit_any_post(): void
    {
        $editor = User::factory()->editor()->create();
        $author = User::factory()->author()->create();
        $post = Post::factory()->create(['author_id' => $author->id]);

        Livewire::actingAs($editor)
            ->test(Edit::class, ['post' => $post])
            ->set('title', 'Editor Updated')
            ->set('content', 'Updated by editor')
            ->call('save')
            ->assertRedirect(route('admin.posts.index'));

        $this->assertSame('Editor Updated', $post->fresh()->title);
    }
}
