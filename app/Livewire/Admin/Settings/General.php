<?php

namespace App\Livewire\Admin\Settings;

use App\Actions\Settings\UpdateSiteSettings;
use App\Models\Media;
use App\Models\SiteSetting;
use App\Services\Settings\SiteSettings;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class General extends Component
{
    public SiteSetting $settings;

    public string $site_name = '';

    public string $site_description = '';

    public string $contact_email = '';

    public string $contact_phone = '';

    public string $contact_address = '';

    public string $default_seo_title = '';

    public string $default_meta_description = '';

    public bool $default_robots_index = true;

    public ?int $logo_media_id = null;

    public ?int $favicon_media_id = null;

    public ?int $default_og_media_id = null;

    public string $timezone = 'UTC';

    public string $locale = 'en';

    public bool $comments_enabled = true;

    /** @var list<array{provider: string, url: string}> */
    public array $socialLinkRows = [];

    public function mount(): void
    {
        $this->authorize('viewAny', SiteSetting::class);

        $this->settings = app(SiteSettings::class)->get();

        if (! $this->settings->exists) {
            $this->settings = SiteSetting::bootstrap();
        }

        $this->site_name = $this->settings->site_name;
        $this->site_description = $this->settings->site_description ?? '';
        $this->contact_email = $this->settings->contact_email ?? '';
        $this->contact_phone = $this->settings->contact_phone ?? '';
        $this->contact_address = $this->settings->contact_address ?? '';
        $this->default_seo_title = $this->settings->default_seo_title ?? '';
        $this->default_meta_description = $this->settings->default_meta_description ?? '';
        $this->default_robots_index = $this->settings->default_robots_index;
        $this->logo_media_id = $this->settings->logo_media_id;
        $this->favicon_media_id = $this->settings->favicon_media_id;
        $this->default_og_media_id = $this->settings->default_og_media_id;
        $this->timezone = $this->settings->timezone;
        $this->locale = $this->settings->locale;
        $this->comments_enabled = $this->settings->comments_enabled;
        $this->socialLinkRows = $this->mapSocialLinksToRows($this->settings->social_links ?? []);
    }

    public function addSocialLink(): void
    {
        $this->socialLinkRows[] = [
            'provider' => '',
            'url' => '',
        ];
    }

    public function removeSocialLink(int $index): void
    {
        if (! isset($this->socialLinkRows[$index])) {
            return;
        }

        unset($this->socialLinkRows[$index]);
        $this->socialLinkRows = array_values($this->socialLinkRows);
    }

    public function save(): void
    {
        $this->authorize('manage', SiteSetting::class);

        try {
            $this->settings = app(UpdateSiteSettings::class)->handle(
                settings: $this->settings->exists ? $this->settings : SiteSetting::bootstrap(),
                actor: auth()->user(),
                siteName: $this->site_name,
                siteDescription: $this->site_description !== '' ? $this->site_description : null,
                contactEmail: $this->contact_email !== '' ? $this->contact_email : null,
                contactPhone: $this->contact_phone !== '' ? $this->contact_phone : null,
                contactAddress: $this->contact_address !== '' ? $this->contact_address : null,
                socialLinks: $this->mapRowsToSocialLinks(),
                defaultSeoTitle: $this->default_seo_title !== '' ? $this->default_seo_title : null,
                defaultMetaDescription: $this->default_meta_description !== '' ? $this->default_meta_description : null,
                defaultRobotsIndex: $this->default_robots_index,
                logoMediaId: $this->logo_media_id,
                faviconMediaId: $this->favicon_media_id,
                defaultOgMediaId: $this->default_og_media_id,
                timezone: $this->timezone,
                locale: $this->locale,
                commentsEnabled: $this->comments_enabled,
            );
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->setErrorBag($exception->validator->getMessageBag());

            return;
        }

        \App\Support\Ui\Flash::success('Site settings saved.');
    }

    public function render(): View
    {
        return view('livewire.admin.settings.general', [
            'mediaItems' => Media::query()->latest()->limit(50)->get(),
            'timezones' => timezone_identifiers_list(),
        ])->layout('components.layouts.admin', [
            'title' => 'Site settings',
        ]);
    }

    /**
     * @param  array<string, string>|null  $socialLinks
     * @return list<array{provider: string, url: string}>
     */
    private function mapSocialLinksToRows(?array $socialLinks): array
    {
        if ($socialLinks === null || $socialLinks === []) {
            return [];
        }

        $rows = [];

        foreach ($socialLinks as $provider => $url) {
            $rows[] = [
                'provider' => (string) $provider,
                'url' => (string) $url,
            ];
        }

        return $rows;
    }

    /**
     * @return array<string, string>
     */
    private function mapRowsToSocialLinks(): array
    {
        $links = [];

        foreach ($this->socialLinkRows as $row) {
            $provider = trim($row['provider'] ?? '');
            $url = trim($row['url'] ?? '');

            if ($provider === '' || $url === '') {
                continue;
            }

            $links[$provider] = $url;
        }

        return $links;
    }
}
