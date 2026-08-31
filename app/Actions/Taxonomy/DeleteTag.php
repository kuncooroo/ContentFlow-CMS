<?php

namespace App\Actions\Taxonomy;

use App\Models\Tag;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeleteTag
{
    public function handle(Tag $tag): void
    {
        if ($tag->hasAttachedPosts()) {
            throw ValidationException::withMessages([
                'tag' => 'This tag cannot be deleted because it is assigned to one or more posts.',
            ]);
        }

        DB::transaction(function () use ($tag): void {
            $tag->delete();
        });
    }
}
