<?php

namespace Tests\Unit\Queries\Content;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Queries\Content\PublicPostIndexQuery;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PublicPostIndexQueryTest extends TestCase
{
    use DatabaseTransactions;

    public function test_paginate_excludes_non_public_posts(): void
    {
        Post::factory()->published()->create(['title' => 'Visible']);
        Post::factory()->create(['title' => 'Draft']);

        $results = app(PublicPostIndexQuery::class)->paginate();

        $this->assertCount(1, $results);
        $this->assertSame('Visible', $results->first()?->title);
    }

    public function test_paginate_filters_by_category(): void
    {
        $category = Category::factory()->create();
        $otherCategory = Category::factory()->create();

        $inCategory = Post::factory()->published()->create(['title' => 'In Category']);
        $outside = Post::factory()->published()->create(['title' => 'Outside']);

        $inCategory->categories()->attach($category);
        $outside->categories()->attach($otherCategory);

        $results = app(PublicPostIndexQuery::class)->paginate(categoryId: $category->id);

        $this->assertCount(1, $results);
        $this->assertSame('In Category', $results->first()?->title);
    }

    public function test_paginate_filters_by_tag(): void
    {
        $tag = Tag::factory()->create();
        $otherTag = Tag::factory()->create();

        $tagged = Post::factory()->published()->create(['title' => 'Tagged']);
        $untagged = Post::factory()->published()->create(['title' => 'Untagged']);

        $tagged->tags()->attach($tag);
        $untagged->tags()->attach($otherTag);

        $results = app(PublicPostIndexQuery::class)->paginate(tagId: $tag->id);

        $this->assertCount(1, $results);
        $this->assertSame('Tagged', $results->first()?->title);
    }
}
