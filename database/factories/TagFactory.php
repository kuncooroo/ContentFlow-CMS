<?php

namespace Database\Factories;

use App\Models\Tag;
use App\Support\Slugs\UniqueSlug;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tag>
 */
class TagFactory extends Factory
{
    protected $model = Tag::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->word();

        return [
            'name' => ucfirst($name),
            'slug' => app(UniqueSlug::class)->generateForTag($name),
        ];
    }
}
