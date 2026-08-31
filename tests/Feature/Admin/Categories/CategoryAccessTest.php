<?php

namespace Tests\Feature\Admin\Categories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CategoryAccessTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_cannot_access_categories_index(): void
    {
        $this->get(route('admin.categories.index'))
            ->assertRedirect(route('login'));
    }

    public function test_author_without_categories_manage_cannot_access_categories_index(): void
    {
        $author = User::factory()->author()->create();

        $this->actingAs($author)
            ->get(route('admin.categories.index'))
            ->assertForbidden();
    }

    public function test_editor_can_access_categories_index(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->get(route('admin.categories.index'))
            ->assertOk()
            ->assertSee('Manage post categories');
    }
}
