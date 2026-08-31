<?php

namespace Tests\Feature\Public;

use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PublicHomeSeoTest extends TestCase
{
    use DatabaseTransactions;

    public function test_homepage_renders_site_seo_meta_tags(): void
    {
        SiteSetting::bootstrap()->update([
            'default_seo_title' => 'ContentFlow Public Home',
            'default_meta_description' => 'Welcome to our CMS powered site.',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<title>ContentFlow Public Home</title>', false)
            ->assertSee('content="Welcome to our CMS powered site."', false);
    }
}
