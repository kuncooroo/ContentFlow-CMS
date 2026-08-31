<?php

namespace Database\Factories;

use App\Models\SiteSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SiteSetting>
 */
class SiteSettingFactory extends Factory
{
    protected $model = SiteSetting::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => SiteSetting::SINGLETON_ID,
            'site_name' => config('app.name', 'ContentFlow CMS'),
            'site_description' => fake()->optional()->sentence(),
            'logo_media_id' => null,
            'favicon_media_id' => null,
            'contact_email' => fake()->optional()->safeEmail(),
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
        ];
    }
}
