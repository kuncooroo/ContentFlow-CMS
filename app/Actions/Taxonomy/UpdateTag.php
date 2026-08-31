<?php

namespace App\Actions\Taxonomy;

use App\Models\Tag;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UpdateTag
{
    /**
     * @return array{name: string, slug: string}
     */
    public function validate(Tag $tag, array $input): array
    {
        $validated = validator($input, [
            'name' => ['required', 'string', 'max:120'],
            'slug' => [
                'required',
                'string',
                'max:160',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('tags', 'slug')->ignore($tag->id),
            ],
        ])->validate();

        return [
            'name' => $validated['name'],
            'slug' => $validated['slug'],
        ];
    }

    public function handle(Tag $tag, string $name, string $slug): Tag
    {
        $validated = $this->validate($tag, [
            'name' => $name,
            'slug' => $slug,
        ]);

        return DB::transaction(function () use ($tag, $validated): Tag {
            $tag->fill($validated);
            $tag->save();

            return $tag->fresh();
        });
    }
}
