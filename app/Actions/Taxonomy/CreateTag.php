<?php

namespace App\Actions\Taxonomy;

use App\Models\Tag;
use App\Support\Slugs\UniqueSlug;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateTag
{
    public function __construct(
        private readonly UniqueSlug $uniqueSlug,
    ) {}

    /**
     * @return array{name: string, slug: string}
     */
    public function validate(array $input): array
    {
        $validated = validator($input, [
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:160', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'unique:tags,slug'],
        ])->validate();

        $slug = $validated['slug'] ?? $this->uniqueSlug->generateForTag($validated['name']);

        if ($slug === '') {
            throw ValidationException::withMessages([
                'slug' => 'Slug could not be generated from the name.',
            ]);
        }

        return [
            'name' => $validated['name'],
            'slug' => $slug,
        ];
    }

    public function handle(string $name, ?string $slug): Tag
    {
        $validated = $this->validate([
            'name' => $name,
            'slug' => $slug,
        ]);

        return DB::transaction(fn (): Tag => Tag::query()->create($validated));
    }
}
