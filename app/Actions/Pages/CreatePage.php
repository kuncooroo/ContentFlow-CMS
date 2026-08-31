<?php

namespace App\Actions\Pages;

use App\Enums\PageStatus;
use App\Models\Page;
use App\Models\User;
use App\Support\Seo\SeoInput;
use App\Support\Slugs\UniqueSlug;
use Illuminate\Support\Facades\DB;

class CreatePage
{
    public function __construct(
        private readonly UniqueSlug $uniqueSlug,
    ) {}

    public function handle(
        User $author,
        string $title,
        ?string $slug,
        string $content,
        ?int $ogMediaId,
        ?string $seoTitle = null,
        ?string $metaDescription = null,
        ?string $canonicalUrl = null,
        bool $robotsIndex = true,
    ): Page {
        $validated = $this->validate([
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'og_media_id' => $ogMediaId,
            'seo_title' => $seoTitle,
            'meta_description' => $metaDescription,
            'canonical_url' => $canonicalUrl,
            'robots_index' => $robotsIndex,
        ]);

        $resolvedSlug = $validated['slug'] ?? $this->uniqueSlug->generateForPage($validated['title']);

        return DB::transaction(function () use ($author, $validated, $resolvedSlug): Page {
            return Page::query()->create([
                'author_id' => $author->id,
                'title' => $validated['title'],
                'slug' => $resolvedSlug,
                'content' => $validated['content'],
                'og_media_id' => $validated['seo']['og_media_id'],
                'seo_title' => $validated['seo']['seo_title'],
                'meta_description' => $validated['seo']['meta_description'],
                'canonical_url' => $validated['seo']['canonical_url'],
                'robots_index' => $validated['seo']['robots_index'],
                'status' => PageStatus::Draft,
            ]);
        });
    }

    /**
     * @return array{
     *     title: string,
     *     slug: ?string,
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
    private function validate(array $input): array
    {
        $validated = validator($input, array_merge([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'unique:pages,slug'],
            'content' => ['required', 'string'],
        ], SeoInput::rules()))->validate();

        return [
            'title' => $validated['title'],
            'slug' => $validated['slug'] ?? null,
            'content' => $validated['content'],
            'seo' => SeoInput::normalize($validated),
        ];
    }
}
