<?php

namespace Tests\Unit\Models;

use App\Models\Page;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PageVisibilityScopeTest extends TestCase
{
    use DatabaseTransactions;

    public function test_publicly_visible_scope_includes_published_pages_only(): void
    {
        $visible = Page::factory()->published()->create(['title' => 'Visible Page']);
        Page::factory()->create(['title' => 'Draft Page']);
        Page::factory()->archived()->create(['title' => 'Archived Page']);

        $titles = Page::query()->publiclyVisible()->pluck('title')->all();

        $this->assertSame(['Visible Page'], $titles);
    }

    public function test_is_publicly_visible_helper(): void
    {
        $published = Page::factory()->published()->make();
        $draft = Page::factory()->make();
        $archived = Page::factory()->archived()->make();

        $this->assertTrue($published->isPubliclyVisible());
        $this->assertFalse($draft->isPubliclyVisible());
        $this->assertFalse($archived->isPubliclyVisible());
    }
}
