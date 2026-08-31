<?php

namespace Database\Factories;

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;
use App\Support\Slugs\UniqueSlug;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);

        return [
            'author_id' => User::factory(),
            'featured_media_id' => null,
            'og_media_id' => null,
            'title' => rtrim($title, '.'),
            'slug' => app(UniqueSlug::class)->generateForPost($title),
            'excerpt' => fake()->optional()->paragraph(),
            'content' => fake()->paragraphs(3, true),
            'status' => PostStatus::Draft,
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
            'status' => PostStatus::Published,
            'publish_at' => now(),
        ]);
    }

    public function scheduled(): static
    {
        return $this->state(fn (): array => [
            'status' => PostStatus::Scheduled,
            'publish_at' => now()->addDay(),
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn (): array => [
            'status' => PostStatus::Archived,
            'publish_at' => now()->subDay(),
        ]);
    }
}
