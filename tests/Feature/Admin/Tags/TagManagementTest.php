<?php

namespace Tests\Feature\Admin\Tags;

use App\Livewire\Admin\Taxonomy\Tags\Create;
use App\Livewire\Admin\Taxonomy\Tags\Edit;
use App\Livewire\Admin\Taxonomy\Tags\Index;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class TagManagementTest extends TestCase
{
    use DatabaseTransactions;

    public function test_author_can_create_tag_with_auto_slug(): void
    {
        $author = User::factory()->author()->create();

        Livewire::actingAs($author)
            ->test(Create::class)
            ->set('name', 'Release Notes')
            ->call('save')
            ->assertRedirect(route('admin.tags.index'));

        $this->assertDatabaseHas('tags', [
            'name' => 'Release Notes',
            'slug' => 'release-notes',
        ]);
    }

    public function test_duplicate_slug_is_rejected_on_create(): void
    {
        $author = User::factory()->author()->create();
        Tag::factory()->create(['slug' => 'taken-slug']);

        Livewire::actingAs($author)
            ->test(Create::class)
            ->set('name', 'Another Tag')
            ->set('slug', 'taken-slug')
            ->call('save')
            ->assertHasErrors(['slug']);
    }

    public function test_author_can_update_tag(): void
    {
        $author = User::factory()->author()->create();
        $tag = Tag::factory()->create([
            'name' => 'Before',
            'slug' => 'before',
        ]);

        Livewire::actingAs($author)
            ->test(Edit::class, ['tag' => $tag])
            ->set('name', 'After')
            ->set('slug', 'after')
            ->call('save')
            ->assertRedirect(route('admin.tags.index'));

        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => 'After',
            'slug' => 'after',
        ]);
    }

    public function test_author_can_delete_unused_tag(): void
    {
        $author = User::factory()->author()->create();
        $tag = Tag::factory()->create();

        Livewire::actingAs($author)
            ->test(Index::class)
            ->call('delete', $tag->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }

    public function test_tag_with_posts_cannot_be_deleted(): void
    {
        $author = User::factory()->author()->create();
        $tag = Tag::factory()->create();

        if (! Schema::hasTable('post_tag')) {
            Schema::create('post_tag', function (Blueprint $table): void {
                $table->unsignedBigInteger('post_id');
                $table->unsignedBigInteger('tag_id');
                $table->primary(['post_id', 'tag_id']);
            });
        }

        DB::table('post_tag')->insert([
            'post_id' => 1,
            'tag_id' => $tag->id,
        ]);

        Livewire::actingAs($author)
            ->test(Index::class)
            ->call('delete', $tag->id)
            ->assertHasErrors(['tag']);

        $this->assertDatabaseHas('tags', ['id' => $tag->id]);
    }

    public function test_auto_generated_slug_is_unique_when_name_collides(): void
    {
        $author = User::factory()->author()->create();
        Tag::factory()->create([
            'name' => 'News',
            'slug' => 'news',
        ]);

        Livewire::actingAs($author)
            ->test(Create::class)
            ->set('name', 'News')
            ->call('save')
            ->assertRedirect(route('admin.tags.index'));

        $this->assertDatabaseHas('tags', [
            'name' => 'News',
            'slug' => 'news-2',
        ]);
    }
}
