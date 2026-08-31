<?php

namespace App\Actions\Pages;

use App\Actions\Pages\Concerns\ValidatesPublishablePage;
use App\Enums\PageStatus;
use App\Models\Page;
use Illuminate\Support\Facades\DB;

class RestorePageToDraft
{
    use ValidatesPublishablePage;

    public function handle(Page $page): Page
    {
        $this->assertTransition($page, PageStatus::Draft);

        return DB::transaction(function () use ($page): Page {
            $page->status = PageStatus::Draft;
            $page->publish_at = null;
            $page->save();

            return $page->fresh();
        });
    }
}
