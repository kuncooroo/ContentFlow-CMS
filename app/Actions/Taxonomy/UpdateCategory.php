<?php

namespace App\Actions\Taxonomy;

use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UpdateCategory
{
    /**
     * @return array{name: string, slug: string, description: ?string}
     */
    public function validate(Category $category, array $input): array
    {
        $validated = validator($input, [
            'name' => ['required', 'string', 'max:120'],
            'slug' => [
                'required',
                'string',
                'max:160',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('categories', 'slug')->ignore($category->id),
            ],
            'description' => ['nullable', 'string', 'max:5000'],
        ])->validate();

        return [
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
        ];
    }

    public function handle(Category $category, string $name, string $slug, ?string $description = null): Category
    {
        $validated = $this->validate($category, [
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
        ]);

        return DB::transaction(function () use ($category, $validated): Category {
            $category->fill($validated);
            $category->save();

            return $category->fresh();
        });
    }
}
