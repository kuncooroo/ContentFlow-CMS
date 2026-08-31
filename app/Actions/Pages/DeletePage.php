<?php

namespace App\Actions\Pages;

use App\Models\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeletePage
{
    public function handle(Page $page): void
    {
        if ($page->isReferencedByMenu()) {
            throw ValidationException::withMessages([
                'page' => 'This page cannot be deleted because it is referenced by a menu item.',
            ]);
        }

        DB::transaction(function () use ($page): void {
            $page->delete();
        });
    }
}
