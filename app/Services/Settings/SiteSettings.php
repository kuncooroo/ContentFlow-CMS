<?php

namespace App\Services\Settings;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

/**
 * Cached read access to the site_settings singleton (id=1).
 *
 * Note: config('app.timezone') from .env/bootstrap still drives PHP's default
 * timezone at application boot. The timezone stored here is the CMS site
 * timezone for display and scheduling context; applying it globally is a
 * separate concern if needed later.
 */
class SiteSettings
{
    public const CACHE_KEY = 'site_settings.singleton';

    public function get(): SiteSetting
    {
        if (! Schema::hasTable('site_settings')) {
            return $this->unsavedDefaults();
        }

        $cached = Cache::get(self::CACHE_KEY);

        if ($cached instanceof SiteSetting) {
            return $cached;
        }

        if ($cached !== null) {
            Cache::forget(self::CACHE_KEY);
        }

        return Cache::rememberForever(self::CACHE_KEY, function (): SiteSetting {
            return SiteSetting::bootstrap();
        });
    }

    public function invalidate(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public function siteName(): string
    {
        return $this->get()->site_name;
    }

    private function unsavedDefaults(): SiteSetting
    {
        return SiteSetting::make([
            'id' => SiteSetting::SINGLETON_ID,
            'site_name' => config('app.name', 'ContentFlow CMS'),
            'site_description' => null,
            'logo_media_id' => null,
            'favicon_media_id' => null,
            'contact_email' => null,
            'contact_phone' => null,
            'contact_address' => null,
            'social_links' => null,
            'default_seo_title' => null,
            'default_meta_description' => null,
            'default_og_media_id' => null,
            'default_robots_index' => true,
            'timezone' => config('app.timezone', 'UTC'),
            'locale' => config('app.locale', 'en'),
            'comments_enabled' => true,
        ]);
    }
}
