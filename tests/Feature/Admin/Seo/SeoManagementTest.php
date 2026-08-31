<?php

namespace Tests\Feature\Admin\Seo;

use App\Livewire\Admin\Pages\Edit as PagesEdit;
use App\Livewire\Admin\Posts\Edit as PostsEdit;
use App\Models\Media;
use App\Models\Page;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class SeoManagementTest extends TestCase
{
    use DatabaseTransactions;

    public function test_author_can_save_post_seo_fields(): void
    {
        $author = User::factory()->author()->create();
        $post = Post::factory()->create(['author_id' => $author->id]);
        $media = Media::factory()->create();

        Livewire::actingAs($author)
            ->test(PostsEdit::class, ['post' => $post])
            ->set('seo_title', 'SEO Post Title')
            ->set('meta_description', 'SEO description for the post.')
            ->set('canonical_url', 'https://example.com/posts/custom')
            ->set('robots_index', false)
            ->set('og_media_id', $media->id)
            ->call('save')
            ->assertRedirect(route('admin.posts.index'));

        $post->refresh();

        $this->assertSame('SEO Post Title', $post->seo_title);
        $this->assertSame('SEO description for the post.', $post->meta_description);
        $this->assertSame('https://example.com/posts/custom', $post->canonical_url);
        $this->assertFalse($post->robots_index);
        $this->assertSame($media->id, $post->og_media_id);
    }

    public function test_editor_can_save_page_seo_fields(): void
    {
        $editor = User::factory()->editor()->create();
        $page = Page::factory()->create();
        $media = Media::factory()->create();

        Livewire::actingAs($editor)
            ->test(PagesEdit::class, ['page' => $page])
            ->set('seo_title', 'SEO Page Title')
            ->set('meta_description', 'SEO description for the page.')
            ->set('canonical_url', '/about')
            ->set('robots_index', true)
            ->set('og_media_id', $media->id)
            ->call('save')
            ->assertRedirect(route('admin.pages.index'));

        $page->refresh();

        $this->assertSame('SEO Page Title', $page->seo_title);
        $this->assertSame('SEO description for the page.', $page->meta_description);
        $this->assertSame('/about', $page->canonical_url);
        $this->assertTrue($page->robots_index);
        $this->assertSame($media->id, $page->og_media_id);
    }

    public function test_invalid_og_media_id_is_rejected_on_post_save(): void
    {
        $author = User::factory()->author()->create();
        $post = Post::factory()->create(['author_id' => $author->id]);

        Livewire::actingAs($author)
            ->test(PostsEdit::class, ['post' => $post])
            ->set('og_media_id', 999999)
            ->call('save')
            ->assertHasErrors(['og_media_id']);
    }

    public function test_seo_title_max_length_is_enforced(): void
    {
        $author = User::factory()->author()->create();
        $post = Post::factory()->create(['author_id' => $author->id]);

        Livewire::actingAs($author)
            ->test(PostsEdit::class, ['post' => $post])
            ->set('seo_title', str_repeat('a', 256))
            ->call('save')
            ->assertHasErrors(['seo_title']);
    }
}
