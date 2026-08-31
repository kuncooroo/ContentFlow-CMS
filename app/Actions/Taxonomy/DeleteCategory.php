<?php

namespace App\Actions\Taxonomy;

use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeleteCategory
{
    public function handle(Category $category): void
    {
        if ($category->hasAttachedPosts()) {
            throw ValidationException::withMessages([
                'category' => 'This category cannot be deleted because it is assigned to one or more posts.',
            ]);
        }

        DB::transaction(function () use ($category): void {
            $category->delete();
        });
    }
}
