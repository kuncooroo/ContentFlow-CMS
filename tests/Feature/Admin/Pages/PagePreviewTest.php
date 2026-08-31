<?php

namespace Tests\Feature\Admin\Pages;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PagePreviewTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_cannot_preview_page(): void
    {
        $page = Page::factory()->create();

        $this->get(route('admin.pages.preview', $page))
            ->assertRedirect(route('login'));
    }

    public function test_editor_can_preview_draft_page(): void
    {
        $editor = User::factory()->editor()->create();
        $page = Page::factory()->create([
            'title' => 'Preview Page Title',
            'content' => 'Preview page content.',
        ]);

        $this->actingAs($editor)
            ->get(route('admin.pages.preview', $page))
            ->assertOk()
            ->assertSee('Preview mode')
            ->assertSee('Preview Page Title')
            ->assertSee('Preview page content.');
    }

    public function test_author_cannot_preview_page(): void
    {
        $author = User::factory()->author()->create();
        $page = Page::factory()->create();

        $this->actingAs($author)
            ->get(route('admin.pages.preview', $page))
            ->assertForbidden();
    }
}
