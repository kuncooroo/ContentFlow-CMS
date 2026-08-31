<?php

namespace Tests\Feature\Admin\Categories;

use App\Livewire\Admin\Taxonomy\Categories\Create;
use App\Livewire\Admin\Taxonomy\Categories\Edit;
use App\Livewire\Admin\Taxonomy\Categories\Index;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
{
    use DatabaseTransactions;

    public function test_editor_can_create_category_with_auto_slug(): void
    {
        $editor = User::factory()->editor()->create();

        Livewire::actingAs($editor)
            ->test(Create::class)
            ->set('name', 'Product Updates')
            ->call('save')
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', [
            'name' => 'Product Updates',
            'slug' => 'product-updates',
        ]);
    }

    public function test_duplicate_slug_is_rejected_on_create(): void
    {
        $editor = User::factory()->editor()->create();
        Category::factory()->create(['slug' => 'taken-slug']);

        Livewire::actingAs($editor)
            ->test(Create::class)
            ->set('name', 'Another Category')
            ->set('slug', 'taken-slug')
            ->call('save')
            ->assertHasErrors(['slug']);
    }

    public function test_editor_can_update_category(): void
    {
        $editor = User::factory()->editor()->create();
        $category = Category::factory()->create([
            'name' => 'Before',
            'slug' => 'before',
        ]);

        Livewire::actingAs($editor)
            ->test(Edit::class, ['category' => $category])
            ->set('name', 'After')
            ->set('slug', 'after')
            ->set('description', 'Updated description')
            ->call('save')
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'After',
            'slug' => 'after',
            'description' => 'Updated description',
        ]);
    }

    public function test_editor_can_delete_unused_category(): void
    {
        $editor = User::factory()->editor()->create();
        $category = Category::factory()->create();

        Livewire::actingAs($editor)
            ->test(Index::class)
            ->call('delete', $category->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_category_with_posts_cannot_be_deleted(): void
    {
        $editor = User::factory()->editor()->create();
        $category = Category::factory()->create();

        if (! Schema::hasTable('post_category')) {
            Schema::create('post_category', function (Blueprint $table): void {
                $table->unsignedBigInteger('post_id');
                $table->unsignedBigInteger('category_id');
                $table->primary(['post_id', 'category_id']);
            });
        }

        DB::table('post_category')->insert([
            'post_id' => 1,
            'category_id' => $category->id,
        ]);

        Livewire::actingAs($editor)
            ->test(Index::class)
            ->call('delete', $category->id)
            ->assertHasErrors(['category']);

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_auto_generated_slug_is_unique_when_name_collides(): void
    {
        $editor = User::factory()->editor()->create();
        Category::factory()->create([
            'name' => 'News',
            'slug' => 'news',
        ]);

        Livewire::actingAs($editor)
            ->test(Create::class)
            ->set('name', 'News')
            ->call('save')
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', [
            'name' => 'News',
            'slug' => 'news-2',
        ]);
    }
}
