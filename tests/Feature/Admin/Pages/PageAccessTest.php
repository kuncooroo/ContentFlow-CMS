<?php

namespace Tests\Feature\Admin\Pages;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PageAccessTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_cannot_access_pages_index(): void
    {
        $this->get(route('admin.pages.index'))
            ->assertRedirect(route('login'));
    }

    public function test_editor_can_access_pages_index(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->get(route('admin.pages.index'))
            ->assertOk()
            ->assertSee('Manage static pages');
    }

    public function test_author_cannot_access_pages_index(): void
    {
        $author = User::factory()->author()->create();

        $this->actingAs($author)
            ->get(route('admin.pages.index'))
            ->assertForbidden();
    }
}
