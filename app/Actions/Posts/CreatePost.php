<?php

namespace App\Actions\Posts;

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;
use App\Support\Seo\SeoInput;
use App\Support\Slugs\UniqueSlug;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CreatePost
{
    public function __construct(
        private readonly UniqueSlug $uniqueSlug,
    ) {}

    /**
     * @param  list<int>  $categoryIds
     * @param  list<int>  $tagIds
     */
    public function handle(
        User $author,
        string $title,
        ?string $slug,
        string $content,
        ?string $excerpt,
        ?int $featuredMediaId,
        array $categoryIds,
        array $tagIds,
        ?string $seoTitle = null,
        ?string $metaDescription = null,
        ?string $canonicalUrl = null,
        bool $robotsIndex = true,
        ?int $ogMediaId = null,
    ): Post {
        $validated = $this->validate([
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'excerpt' => $excerpt,
            'featured_media_id' => $featuredMediaId,
            'category_ids' => $categoryIds,
            'tag_ids' => $tagIds,
            'seo_title' => $seoTitle,
            'meta_description' => $metaDescription,
            'canonical_url' => $canonicalUrl,
            'robots_index' => $robotsIndex,
            'og_media_id' => $ogMediaId,
        ]);

        $resolvedSlug = $validated['slug'] ?? $this->uniqueSlug->generateForPost($validated['title']);

        return DB::transaction(function () use ($author, $validated, $resolvedSlug): Post {
            $post = Post::query()->create([
                'author_id' => $author->id,
                'title' => $validated['title'],
                'slug' => $resolvedSlug,
                'content' => $validated['content'],
                'excerpt' => $validated['excerpt'],
                'featured_media_id' => $validated['featured_media_id'],
                'seo_title' => $validated['seo']['seo_title'],
                'meta_description' => $validated['seo']['meta_description'],
                'canonical_url' => $validated['seo']['canonical_url'],
                'robots_index' => $validated['seo']['robots_index'],
                'og_media_id' => $validated['seo']['og_media_id'],
                'status' => PostStatus::Draft,
            ]);

            $post->categories()->sync($validated['category_ids']);
            $post->tags()->sync($validated['tag_ids']);

            return $post->fresh(['author', 'categories', 'tags', 'featuredMedia']);
        });
    }

    /**
     * @return array{
     *     title: string,
     *     slug: ?string,
     *     content: string,
     *     excerpt: ?string,
     *     featured_media_id: ?int,
     *     category_ids: list<int>,
     *     tag_ids: list<int>,
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
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'unique:posts,slug'],
            'content' => ['required', 'string'],
            'excerpt' => ['nullable', 'string', 'max:5000'],
            'featured_media_id' => ['nullable', 'integer', Rule::exists('media', 'id')],
            'category_ids' => ['array'],
            'category_ids.*' => ['integer', Rule::exists('categories', 'id')],
            'tag_ids' => ['array'],
            'tag_ids.*' => ['integer', Rule::exists('tags', 'id')],
        ], SeoInput::rules()))->validate();

        return [
            'title' => $validated['title'],
            'slug' => $validated['slug'] ?? null,
            'content' => $validated['content'],
            'excerpt' => $validated['excerpt'] ?? null,
            'featured_media_id' => $validated['featured_media_id'] ?? null,
            'category_ids' => array_values(array_unique($validated['category_ids'] ?? [])),
            'tag_ids' => array_values(array_unique($validated['tag_ids'] ?? [])),
            'seo' => SeoInput::normalize($validated),
        ];
    }
}
