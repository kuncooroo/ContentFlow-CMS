<?php

namespace Tests\Unit\Actions\Pages;

use App\Actions\Pages\ArchivePage;
use App\Actions\Pages\PublishPage;
use App\Actions\Pages\RestorePageToDraft;
use App\Actions\Pages\UnpublishPage;
use App\Enums\PageStatus;
use App\Models\Page;
use App\Models\User;
use App\Support\Audit\ActivityEvent;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PagePublishingActionsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_draft_page_can_be_published(): void
    {
        $editor = User::factory()->editor()->create();
        $page = Page::factory()->create(['status' => PageStatus::Draft]);

        $published = app(PublishPage::class)->handle($page, $editor);

        $this->assertSame(PageStatus::Published, $published->status);
        $this->assertNotNull($published->publish_at);
        $this->assertDatabaseHas('activity_logs', [
            'event' => ActivityEvent::PagePublished,
            'subject_type' => 'page',
            'subject_id' => $page->id,
        ]);
    }

    public function test_publish_is_idempotent_when_already_published(): void
    {
        $page = Page::factory()->published()->create();
        $originalPublishAt = $page->publish_at;

        $result = app(PublishPage::class)->handle($page, User::factory()->editor()->create());

        $this->assertSame(PageStatus::Published, $result->status);
        $this->assertTrue($originalPublishAt->eq($result->publish_at));
    }

    public function test_archived_page_cannot_be_published(): void
    {
        $page = Page::factory()->archived()->create();

        $this->expectException(ValidationException::class);

        app(PublishPage::class)->handle($page, User::factory()->editor()->create());
    }

    public function test_published_page_can_be_archived(): void
    {
        $editor = User::factory()->editor()->create();
        $page = Page::factory()->published()->create();

        $archived = app(ArchivePage::class)->handle($page, $editor);

        $this->assertSame(PageStatus::Archived, $archived->status);
    }

    public function test_draft_page_cannot_be_archived(): void
    {
        $page = Page::factory()->create(['status' => PageStatus::Draft]);

        $this->expectException(ValidationException::class);

        app(ArchivePage::class)->handle($page, User::factory()->editor()->create());
    }

    public function test_published_page_can_be_unpublished(): void
    {
        $page = Page::factory()->published()->create();

        $draft = app(UnpublishPage::class)->handle($page);

        $this->assertSame(PageStatus::Draft, $draft->status);
        $this->assertNull($draft->publish_at);
    }

    public function test_archived_page_can_be_restored_to_draft(): void
    {
        $page = Page::factory()->archived()->create();

        $draft = app(RestorePageToDraft::class)->handle($page);

        $this->assertSame(PageStatus::Draft, $draft->status);
        $this->assertNull($draft->publish_at);
    }
}
