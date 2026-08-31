<?php

namespace Tests\Unit\Support\Seo;

use App\Models\SiteSetting;
use App\Services\Settings\SiteSettings;
use App\Support\Seo\SeoResolver;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SeoResolverSiteTest extends TestCase
{
    use DatabaseTransactions;

    public function test_resolve_for_site_uses_default_seo_fields(): void
    {
        SiteSetting::bootstrap()->update([
            'site_name' => 'My Site',
            'default_seo_title' => 'My Site Home',
            'default_meta_description' => 'Site description',
        ]);
        app(SiteSettings::class)->invalidate();
        Cache::forget(SiteSettings::CACHE_KEY);

        $metadata = app(SeoResolver::class)->resolveForSite();

        $this->assertSame('My Site Home', $metadata->title);
        $this->assertSame('Site description', $metadata->metaDescription);
    }

    public function test_resolve_for_listing_appends_site_name(): void
    {
        SiteSetting::bootstrap()->update([
            'site_name' => 'My Site',
        ]);
        app(SiteSettings::class)->invalidate();
        Cache::forget(SiteSettings::CACHE_KEY);

        $metadata = app(SeoResolver::class)->resolveForListing('Blog');

        $this->assertSame('Blog — My Site', $metadata->title);
    }
}
