<?php

namespace Tests\Feature\Admin\Pages;

use App\Enums\PageStatus;
use App\Livewire\Admin\Pages\Edit;
use App\Models\Page;
use App\Models\User;
use App\Support\Audit\ActivityEvent;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class PagePublishingTest extends TestCase
{
    use DatabaseTransactions;

    public function test_editor_can_publish_draft_page(): void
    {
        $editor = User::factory()->editor()->create();
        $page = Page::factory()->create(['status' => PageStatus::Draft]);

        Livewire::actingAs($editor)
            ->test(Edit::class, ['page' => $page])
            ->call('publish')
            ->assertHasNoErrors();

        $this->assertSame(PageStatus::Published, $page->fresh()->status);
        $this->assertDatabaseHas('activity_logs', [
            'event' => ActivityEvent::PagePublished,
            'subject_id' => $page->id,
        ]);
    }

    public function test_author_cannot_publish_page(): void
    {
        $author = User::factory()->author()->create();
        $page = Page::factory()->create(['status' => PageStatus::Draft]);

        Livewire::actingAs($author)
            ->test(Edit::class, ['page' => $page])
            ->assertForbidden();
    }

    public function test_editor_can_archive_published_page(): void
    {
        $editor = User::factory()->editor()->create();
        $page = Page::factory()->published()->create();

        Livewire::actingAs($editor)
            ->test(Edit::class, ['page' => $page])
            ->call('archive')
            ->assertHasNoErrors();

        $this->assertSame(PageStatus::Archived, $page->fresh()->status);
        $this->assertDatabaseHas('activity_logs', [
            'event' => ActivityEvent::PageArchived,
            'subject_id' => $page->id,
        ]);
    }

    public function test_editor_can_unpublish_published_page(): void
    {
        $editor = User::factory()->editor()->create();
        $page = Page::factory()->published()->create();

        Livewire::actingAs($editor)
            ->test(Edit::class, ['page' => $page])
            ->call('unpublish')
            ->assertHasNoErrors();

        $fresh = $page->fresh();
        $this->assertSame(PageStatus::Draft, $fresh->status);
        $this->assertNull($fresh->publish_at);
    }

    public function test_editor_can_restore_archived_page(): void
    {
        $editor = User::factory()->editor()->create();
        $page = Page::factory()->archived()->create();

        Livewire::actingAs($editor)
            ->test(Edit::class, ['page' => $page])
            ->call('restoreToDraft')
            ->assertHasNoErrors();

        $this->assertSame(PageStatus::Draft, $page->fresh()->status);
    }

    public function test_draft_page_cannot_be_archived(): void
    {
        $editor = User::factory()->editor()->create();
        $page = Page::factory()->create(['status' => PageStatus::Draft]);

        Livewire::actingAs($editor)
            ->test(Edit::class, ['page' => $page])
            ->call('archive')
            ->assertHasErrors(['status']);
    }
}
