<?php

namespace App\Actions\Pages;

use App\Actions\Pages\Concerns\ValidatesPublishablePage;
use App\Enums\PageStatus;
use App\Models\Page;
use App\Models\User;
use App\Services\Audit\ActivityLogger;
use App\Support\Audit\ActivityEvent;
use Illuminate\Support\Facades\DB;

class PublishPage
{
    use ValidatesPublishablePage;

    public function __construct(
        private readonly ActivityLogger $activityLogger,
    ) {}

    public function handle(Page $page, ?User $actor = null): Page
    {
        if ($page->status === PageStatus::Published) {
            return $page;
        }

        $this->assertTransition($page, PageStatus::Published);
        $this->assertPublishable($page);

        return DB::transaction(function () use ($page, $actor): Page {
            $page->status = PageStatus::Published;
            $page->publish_at = now();
            $page->save();

            $this->activityLogger->record(
                $actor,
                ActivityEvent::PagePublished,
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
