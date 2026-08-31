<?php

namespace Tests\Feature\Admin\Search;

use App\Enums\CommentStatus;
use App\Enums\PostStatus;
use App\Enums\UserStatus;
use App\Livewire\Admin\Comments\Index as CommentsIndex;
use App\Livewire\Admin\Media\Index as MediaIndex;
use App\Livewire\Admin\Pages\Index as PagesIndex;
use App\Livewire\Admin\Posts\Index as PostsIndex;
use App\Livewire\Admin\Users\Index as UsersIndex;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Media;
use App\Models\Page;
use App\Models\Post;
use App\Models\User;
use App\Queries\Content\PostIndexQuery;
use App\Queries\Media\MediaLibraryQuery;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class AdminSearchFiltersTest extends TestCase
{
    use DatabaseTransactions;

    public function test_editor_can_search_posts_by_title(): void
    {
        $editor = User::factory()->editor()->create();

        Post::factory()->create(['title' => 'Laravel Tips']);
        Post::factory()->create(['title' => 'Unrelated Article']);

        Livewire::actingAs($editor)
            ->test(PostsIndex::class)
            ->set('search', 'Laravel')
            ->assertSee('Laravel Tips')
            ->assertDontSee('Unrelated Article');
    }

    public function test_editor_can_filter_posts_by_status(): void
    {
        $editor = User::factory()->editor()->create();

        Post::factory()->create(['title' => 'Draft Post', 'status' => PostStatus::Draft]);
        Post::factory()->published()->create(['title' => 'Published Post']);

        Livewire::actingAs($editor)
            ->test(PostsIndex::class)
            ->set('statusFilter', PostStatus::Published->value)
            ->assertSee('Published Post')
            ->assertDontSee('Draft Post');
    }

    public function test_author_search_is_scoped_to_own_posts(): void
    {
        $author = User::factory()->author()->create();
        $otherAuthor = User::factory()->author()->create();

        Post::factory()->create([
            'author_id' => $author->id,
            'title' => 'My Hidden Match',
        ]);
        Post::factory()->create([
            'author_id' => $otherAuthor->id,
            'title' => 'My Hidden Match',
        ]);

        $summary = app(PostIndexQuery::class)->paginateForUser(
            user: $author,
            search: 'Hidden',
        );

        $this->assertCount(1, $summary->items());
        $this->assertSame($author->id, $summary->items()[0]->author_id);
    }

    public function test_editor_can_filter_posts_by_category(): void
    {
        $editor = User::factory()->editor()->create();
        $category = Category::factory()->create(['name' => 'News']);

        $matching = Post::factory()->create(['title' => 'Category Match']);
        $matching->categories()->attach($category->id);
        Post::factory()->create(['title' => 'No Category']);

        Livewire::actingAs($editor)
            ->test(PostsIndex::class)
            ->set('categoryFilter', $category->id)
            ->assertSee('Category Match')
            ->assertDontSee('No Category');
    }

    public function test_post_index_supports_pagination(): void
    {
        $editor = User::factory()->editor()->create();
        Post::factory()->count(16)->create();

        $results = app(PostIndexQuery::class)->paginateForUser($editor, perPage: 15);

        $this->assertTrue($results->hasPages());
        $this->assertSame(2, $results->lastPage());
    }

    public function test_clear_filters_resets_post_search(): void
    {
        $editor = User::factory()->editor()->create();
        Post::factory()->create(['title' => 'Find Me']);
        Post::factory()->create(['title' => 'Other Post']);

        Livewire::actingAs($editor)
            ->test(PostsIndex::class)
            ->set('search', 'Find Me')
            ->call('clearFilters')
            ->assertSet('search', '')
            ->assertSee('Find Me')
            ->assertSee('Other Post');
    }

    public function test_editor_can_search_pages_by_title(): void
    {
        $editor = User::factory()->editor()->create();

        Page::factory()->create(['title' => 'About Us']);
        Page::factory()->create(['title' => 'Contact']);

        Livewire::actingAs($editor)
            ->test(PagesIndex::class)
            ->set('search', 'About')
            ->assertSee('About Us')
            ->assertDontSee('Contact');
    }

    public function test_editor_can_search_media_by_filename(): void
    {
        $editor = User::factory()->editor()->create();

        Media::factory()->create(['original_name' => 'hero-banner.jpg']);
        Media::factory()->create(['original_name' => 'footer-logo.png']);

        Livewire::actingAs($editor)
            ->test(MediaIndex::class)
            ->set('search', 'hero')
            ->assertSee('hero-banner.jpg')
            ->assertDontSee('footer-logo.png');

        $results = app(MediaLibraryQuery::class)->paginate(search: 'hero');
        $this->assertCount(1, $results->items());
    }

    public function test_editor_can_search_comments_by_author_name(): void
    {
        $editor = User::factory()->editor()->create();
        $post = Post::factory()->published()->create(['title' => 'Target Post']);

        Comment::factory()->create([
            'post_id' => $post->id,
            'author_name' => 'Jane Reader',
            'content' => 'Nice article',
        ]);
        Comment::factory()->create([
            'post_id' => $post->id,
            'author_name' => 'John Doe',
            'content' => 'Another comment',
        ]);

        Livewire::actingAs($editor)
            ->test(CommentsIndex::class)
            ->set('search', 'Jane')
            ->assertSee('Jane Reader')
            ->assertDontSee('John Doe');
    }

    public function test_comment_search_shows_empty_state_when_no_matches(): void
    {
        $editor = User::factory()->editor()->create();
        $post = Post::factory()->published()->create();

        Comment::factory()->create([
            'post_id' => $post->id,
            'author_name' => 'Jane Reader',
            'status' => CommentStatus::Pending,
        ]);

        Livewire::actingAs($editor)
            ->test(CommentsIndex::class)
            ->set('search', 'missing-term')
            ->assertSee('No comments match your filters.');
    }

    public function test_administrator_can_search_users_by_email(): void
    {
        $admin = User::factory()->administrator()->create();

        User::factory()->create([
            'name' => 'Alice Admin',
            'email' => 'alice@example.com',
        ]);
        User::factory()->create([
            'name' => 'Bob User',
            'email' => 'bob@example.com',
        ]);

        Livewire::actingAs($admin)
            ->test(UsersIndex::class)
            ->set('search', 'alice@example.com')
            ->assertSee('Alice Admin')
            ->assertDontSee('Bob User');
    }

    public function test_administrator_can_filter_users_by_status(): void
    {
        $admin = User::factory()->administrator()->create();

        User::factory()->create(['name' => 'Active User', 'status' => UserStatus::Active]);
        User::factory()->inactive()->create(['name' => 'Inactive User']);

        Livewire::actingAs($admin)
            ->test(UsersIndex::class)
            ->set('statusFilter', UserStatus::Inactive->value)
            ->assertSee('Inactive User')
            ->assertDontSee('Active User');
    }
}
