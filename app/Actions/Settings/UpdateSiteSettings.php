<?php

namespace App\Actions\Settings;

use App\Models\SiteSetting;
use App\Models\User;
use App\Services\Audit\ActivityLogger;
use App\Services\Settings\SiteSettings;
use App\Support\Audit\ActivityEvent;
use App\Support\Demo\DemoGuard;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UpdateSiteSettings
{
    public function __construct(
        private readonly ActivityLogger $activityLogger,
        private readonly SiteSettings $siteSettings,
    ) {}

    /**
     * @param  array<string, string|null>  $socialLinks
     */
    public function handle(
        SiteSetting $settings,
        User $actor,
        string $siteName,
        ?string $siteDescription,
        ?string $contactEmail,
        ?string $contactPhone,
        ?string $contactAddress,
        array $socialLinks,
        ?string $defaultSeoTitle,
        ?string $defaultMetaDescription,
        bool $defaultRobotsIndex,
        ?int $logoMediaId,
        ?int $faviconMediaId,
        ?int $defaultOgMediaId,
        string $timezone,
        string $locale,
        bool $commentsEnabled,
    ): SiteSetting {
        DemoGuard::assertCanUpdateSiteSettings();

        $validated = $this->validate([
            'site_name' => $siteName,
            'site_description' => $siteDescription,
            'contact_email' => $contactEmail,
            'contact_phone' => $contactPhone,
            'contact_address' => $contactAddress,
            'social_links' => $socialLinks,
            'default_seo_title' => $defaultSeoTitle,
            'default_meta_description' => $defaultMetaDescription,
            'default_robots_index' => $defaultRobotsIndex,
            'logo_media_id' => $logoMediaId,
            'favicon_media_id' => $faviconMediaId,
            'default_og_media_id' => $defaultOgMediaId,
            'timezone' => $timezone,
            'locale' => $locale,
            'comments_enabled' => $commentsEnabled,
        ]);

        return DB::transaction(function () use ($settings, $actor, $validated): SiteSetting {
            $settings->fill([
                'site_name' => $validated['site_name'],
                'site_description' => $validated['site_description'],
                'contact_email' => $validated['contact_email'],
                'contact_phone' => $validated['contact_phone'],
                'contact_address' => $validated['contact_address'],
                'social_links' => $validated['social_links'],
                'default_seo_title' => $validated['default_seo_title'],
                'default_meta_description' => $validated['default_meta_description'],
                'default_robots_index' => $validated['default_robots_index'],
                'logo_media_id' => $validated['logo_media_id'],
                'favicon_media_id' => $validated['favicon_media_id'],
                'default_og_media_id' => $validated['default_og_media_id'],
                'timezone' => $validated['timezone'],
                'locale' => $validated['locale'],
                'comments_enabled' => $validated['comments_enabled'],
            ]);
            $settings->save();

            $this->siteSettings->invalidate();

            $this->activityLogger->record(
                $actor,
                ActivityEvent::SettingsUpdated,
                $settings,
                [
                    'site_name' => $settings->site_name,
                    'comments_enabled' => $settings->comments_enabled,
                ],
            );

            return $settings->fresh(['logoMedia', 'faviconMedia', 'defaultOgMedia']);
        });
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array{
     *     site_name: string,
     *     site_description: ?string,
     *     contact_email: ?string,
     *     contact_phone: ?string,
     *     contact_address: ?string,
     *     social_links: array<string, string>,
     *     default_seo_title: ?string,
     *     default_meta_description: ?string,
     *     default_robots_index: bool,
     *     logo_media_id: ?int,
     *     favicon_media_id: ?int,
     *     default_og_media_id: ?int,
     *     timezone: string,
     *     locale: string,
     *     comments_enabled: bool
     * }
     */
    private function validate(array $input): array
    {
        $validated = validator($input, [
            'site_name' => ['required', 'string', 'max:150'],
            'site_description' => ['nullable', 'string', 'max:320'],
            'contact_email' => ['nullable', 'email', 'max:254'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_address' => ['nullable', 'string', 'max:5000'],
            'social_links' => ['nullable', 'array'],
            'social_links.*' => ['nullable', 'string', 'url', 'max:2048'],
            'default_seo_title' => ['nullable', 'string', 'max:255'],
            'default_meta_description' => ['nullable', 'string', 'max:320'],
            'default_robots_index' => ['boolean'],
            'logo_media_id' => ['nullable', 'integer', Rule::exists('media', 'id')],
            'favicon_media_id' => ['nullable', 'integer', Rule::exists('media', 'id')],
            'default_og_media_id' => ['nullable', 'integer', Rule::exists('media', 'id')],
            'timezone' => ['required', 'string', 'max:64', Rule::in(timezone_identifiers_list())],
            'locale' => ['required', 'string', 'max:16', 'regex:/^[a-z]{2}(_[A-Z]{2})?$/'],
            'comments_enabled' => ['boolean'],
        ])->validate();

        $socialLinks = [];

        foreach ($validated['social_links'] ?? [] as $provider => $url) {
            $provider = trim((string) $provider);
            $url = trim((string) $url);

            if ($provider === '' || $url === '') {
                continue;
            }

            $socialLinks[$provider] = $url;
        }

        return [
            'site_name' => trim($validated['site_name']),
            'site_description' => filled($validated['site_description'] ?? null) ? trim((string) $validated['site_description']) : null,
            'contact_email' => filled($validated['contact_email'] ?? null) ? trim((string) $validated['contact_email']) : null,
            'contact_phone' => filled($validated['contact_phone'] ?? null) ? trim((string) $validated['contact_phone']) : null,
            'contact_address' => filled($validated['contact_address'] ?? null) ? trim((string) $validated['contact_address']) : null,
            'social_links' => $socialLinks === [] ? null : $socialLinks,
            'default_seo_title' => filled($validated['default_seo_title'] ?? null) ? trim((string) $validated['default_seo_title']) : null,
            'default_meta_description' => filled($validated['default_meta_description'] ?? null) ? trim((string) $validated['default_meta_description']) : null,
            'default_robots_index' => (bool) ($validated['default_robots_index'] ?? true),
            'logo_media_id' => $validated['logo_media_id'] ?? null,
            'favicon_media_id' => $validated['favicon_media_id'] ?? null,
            'default_og_media_id' => $validated['default_og_media_id'] ?? null,
            'timezone' => $validated['timezone'],
            'locale' => $validated['locale'],
            'comments_enabled' => (bool) ($validated['comments_enabled'] ?? true),
        ];
    }
}
