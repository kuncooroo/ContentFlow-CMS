<?php

namespace Tests\Feature\Public;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PublicArchiveTest extends TestCase
{
    use DatabaseTransactions;

    public function test_category_archive_lists_published_posts_in_category(): void
    {
        $category = Category::factory()->create(['name' => 'News']);
        $visible = Post::factory()->published()->create(['title' => 'Category Post']);
        $draft = Post::factory()->create(['title' => 'Draft In Category']);
        $other = Post::factory()->published()->create(['title' => 'Other Post']);

        $visible->categories()->attach($category);
        $draft->categories()->attach($category);
        $other->categories()->attach(Category::factory()->create());

        $this->get(route('public.categories.show', $category))
            ->assertOk()
            ->assertSee('News')
            ->assertSee('Category Post')
            ->assertDontSee('Draft In Category')
            ->assertDontSee('Other Post');
    }

    public function test_tag_archive_lists_published_posts_with_tag(): void
    {
        $tag = Tag::factory()->create(['name' => 'Laravel']);
        $visible = Post::factory()->published()->create(['title' => 'Tagged Post']);
        $draft = Post::factory()->create(['title' => 'Draft Tagged Post']);

        $visible->tags()->attach($tag);
        $draft->tags()->attach($tag);

        $this->get(route('public.tags.show', $tag))
            ->assertOk()
            ->assertSee('Laravel')
            ->assertSee('Tagged Post')
            ->assertDontSee('Draft Tagged Post');
    }

    public function test_blog_index_paginates_results(): void
    {
        Post::factory()->published()->count(13)->create();

        $this->get(route('public.posts.index'))
            ->assertOk()
            ->assertSee('Blog');

        $this->get(route('public.posts.index', ['page' => 2]))
            ->assertOk();
    }
}
