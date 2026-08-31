<?php

namespace App\Actions\Pages\Concerns;

use App\Enums\PageStatus;
use App\Models\Page;
use Illuminate\Validation\ValidationException;

trait ValidatesPublishablePage
{
    protected function assertPublishable(Page $page): void
    {
        if ($page->title === '' || $page->slug === '' || $page->content === '') {
            throw ValidationException::withMessages([
                'page' => 'Page must have a title, slug, and body before publishing.',
            ]);
        }
    }

    protected function assertTransition(Page $page, PageStatus $target): void
    {
        if (! $page->status->canTransitionTo($target)) {
            throw ValidationException::withMessages([
                'status' => "Cannot transition from {$page->status->label()} to {$target->label()}.",
            ]);
        }
    }
}
