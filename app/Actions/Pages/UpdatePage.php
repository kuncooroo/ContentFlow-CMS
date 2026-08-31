<?php

namespace App\Actions\Pages;

use App\Models\Page;
use App\Support\Seo\SeoInput;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UpdatePage
{
    public function handle(
        Page $page,
        string $title,
        string $slug,
        string $content,
        ?int $ogMediaId,
        ?string $seoTitle = null,
        ?string $metaDescription = null,
        ?string $canonicalUrl = null,
        bool $robotsIndex = true,
    ): Page {
        $validated = $this->validate($page, [
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'og_media_id' => $ogMediaId,
            'seo_title' => $seoTitle,
            'meta_description' => $metaDescription,
            'canonical_url' => $canonicalUrl,
            'robots_index' => $robotsIndex,
        ]);

        return DB::transaction(function () use ($page, $validated): Page {
            $page->fill([
                'title' => $validated['title'],
                'slug' => $validated['slug'],
                'content' => $validated['content'],
                'og_media_id' => $validated['seo']['og_media_id'],
                'seo_title' => $validated['seo']['seo_title'],
                'meta_description' => $validated['seo']['meta_description'],
                'canonical_url' => $validated['seo']['canonical_url'],
                'robots_index' => $validated['seo']['robots_index'],
            ]);
            $page->save();

            return $page->fresh(['author', 'ogMedia']);
        });
    }

    /**
     * @return array{
     *     title: string,
     *     slug: string,
     *     content: string,
     *     seo: array{
     *         seo_title: ?string,
     *         meta_description: ?string,
     *         canonical_url: ?string,
     *         robots_index: bool,
     *         og_media_id: ?int
     *     }
     * }
     */
    private function validate(Page $page, array $input): array
    {
        $validated = validator($input, array_merge([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('pages', 'slug')->ignore($page->id),
            ],
            'content' => ['required', 'string'],
        ], SeoInput::rules()))->validate();

        return [
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'content' => $validated['content'],
            'seo' => SeoInput::normalize($validated),
        ];
    }
}
