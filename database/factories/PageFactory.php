<?php

namespace Database\Factories;

use App\Enums\PageStatus;
use App\Models\Page;
use App\Models\User;
use App\Support\Slugs\UniqueSlug;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Page>
 */
class PageFactory extends Factory
{
    protected $model = Page::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'author_id' => User::factory(),
            'og_media_id' => null,
            'title' => ucfirst($title),
            'slug' => app(UniqueSlug::class)->generateForPage($title),
            'content' => fake()->paragraphs(2, true),
            'status' => PageStatus::Draft,
            'publish_at' => null,
            'seo_title' => null,
            'meta_description' => null,
            'canonical_url' => null,
            'robots_index' => true,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (): array => [
            'status' => PageStatus::Published,
            'publish_at' => now(),
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn (): array => [
            'status' => PageStatus::Archived,
            'publish_at' => now()->subDay(),
        ]);
    }
}
