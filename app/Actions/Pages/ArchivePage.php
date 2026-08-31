<?php

namespace App\Actions\Pages;

use App\Actions\Pages\Concerns\ValidatesPublishablePage;
use App\Enums\PageStatus;
use App\Models\Page;
use App\Models\User;
use App\Services\Audit\ActivityLogger;
use App\Support\Audit\ActivityEvent;
use Illuminate\Support\Facades\DB;

class ArchivePage
{
    use ValidatesPublishablePage;

    public function __construct(
        private readonly ActivityLogger $activityLogger,
    ) {}

    public function handle(Page $page, User $actor): Page
    {
        $this->assertTransition($page, PageStatus::Archived);

        return DB::transaction(function () use ($page, $actor): Page {
            $page->status = PageStatus::Archived;
            $page->save();

            $this->activityLogger->record(
                $actor,
                ActivityEvent::PageArchived,
                $page,
                [
                    'title' => $page->title,
                    'slug' => $page->slug,
                ],
            );

            return $page->fresh();
        });
    }
}
