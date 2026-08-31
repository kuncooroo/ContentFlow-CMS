<?php

namespace Tests\Unit\Support\Slugs;

use App\Models\Category;
use App\Models\Tag;
use App\Support\Slugs\UniqueSlug;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UniqueSlugTest extends TestCase
{
    use DatabaseTransactions;

    public function test_generates_unique_slug_with_numeric_suffix(): void
    {
        Category::factory()->create(['slug' => 'guides']);

        $slug = app(UniqueSlug::class)->generateForCategory('Guides');

        $this->assertSame('guides-2', $slug);
    }

    public function test_generates_unique_tag_slug_with_numeric_suffix(): void
    {
        Tag::factory()->create(['slug' => 'laravel']);

        $slug = app(UniqueSlug::class)->generateForTag('Laravel');

        $this->assertSame('laravel-2', $slug);
    }
}
