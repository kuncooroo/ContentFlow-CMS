<?php

namespace Tests\Feature\Public;

use App\Models\Page;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PublicPageVisibilityTest extends TestCase
{
    use DatabaseTransactions;

    public function test_published_page_is_publicly_visible(): void
    {
        $page = Page::factory()->published()->create(['title' => 'About Us']);

        $this->get(route('public.pages.show', $page))
            ->assertOk()
            ->assertSee('About Us');
    }

    public function test_draft_page_returns_404(): void
    {
        $page = Page::factory()->create(['title' => 'Draft Page']);

        $this->get(route('public.pages.show', $page))
            ->assertNotFound();
    }

    public function test_archived_page_returns_404(): void
    {
        $page = Page::factory()->archived()->create(['title' => 'Archived Page']);

        $this->get(route('public.pages.show', $page))
            ->assertNotFound();
    }

    public function test_unknown_page_slug_returns_404_page(): void
    {
        $this->get('/pages/does-not-exist')
            ->assertNotFound()
            ->assertSee('Page not found');
    }
}
