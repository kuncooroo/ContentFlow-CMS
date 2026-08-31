<?php

namespace Tests\Feature\Admin\Pages;

use App\Enums\PageStatus;
use App\Livewire\Admin\Pages\Create;
use App\Livewire\Admin\Pages\Edit;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class PageCrudTest extends TestCase
{
    use DatabaseTransactions;

    public function test_editor_can_create_draft_page(): void
    {
        $editor = User::factory()->editor()->create();

        Livewire::actingAs($editor)
            ->test(Create::class)
            ->set('title', 'About Us')
            ->set('content', 'We build great content tools.')
            ->call('save')
            ->assertRedirect(route('admin.pages.index'));

        $page = Page::query()->first();

        $this->assertNotNull($page);
        $this->assertSame(PageStatus::Draft, $page->status);
        $this->assertSame('about-us', $page->slug);
        $this->assertSame($editor->id, $page->author_id);
    }

    public function test_duplicate_slug_is_rejected_on_create(): void
    {
        $editor = User::factory()->editor()->create();
        Page::factory()->create(['slug' => 'about-us']);

        Livewire::actingAs($editor)
            ->test(Create::class)
            ->set('title', 'Another Page')
            ->set('slug', 'about-us')
            ->set('content', 'Body')
            ->call('save')
            ->assertHasErrors(['slug']);
    }

    public function test_editor_can_update_page(): void
    {
        $editor = User::factory()->editor()->create();
        $page = Page::factory()->create([
            'title' => 'Before',
            'slug' => 'before',
            'content' => 'Before content',
        ]);

        Livewire::actingAs($editor)
            ->test(Edit::class, ['page' => $page])
            ->set('title', 'After')
            ->set('slug', 'after')
            ->set('content', 'After content')
            ->call('save')
            ->assertRedirect(route('admin.pages.index'));

        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'title' => 'After',
            'slug' => 'after',
        ]);
    }

    public function test_author_cannot_create_page(): void
    {
        $author = User::factory()->author()->create();

        Livewire::actingAs($author)
            ->test(Create::class)
            ->assertForbidden();
    }
}
