<?php

namespace App\Actions\Taxonomy;

use App\Models\Category;
use App\Support\Slugs\UniqueSlug;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateCategory
{
    public function __construct(
        private readonly UniqueSlug $uniqueSlug,
    ) {}

    /**
     * @return array{name: string, slug: string, description: ?string}
     */
    public function validate(array $input): array
    {
        $validated = validator($input, [
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:160', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'unique:categories,slug'],
            'description' => ['nullable', 'string', 'max:5000'],
        ])->validate();

        $slug = $validated['slug'] ?? $this->uniqueSlug->generateForCategory($validated['name']);

        if ($slug === '') {
            throw ValidationException::withMessages([
                'slug' => 'Slug could not be generated from the name.',
            ]);
        }

        return [
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
        ];
    }

    public function handle(string $name, ?string $slug, ?string $description = null): Category
    {
        $validated = $this->validate([
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
        ]);

        return DB::transaction(fn (): Category => Category::query()->create($validated));
    }
}
