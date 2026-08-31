<?php

namespace App\Support\Seo;

use App\Models\Media;
use App\Models\Page;
use App\Models\Post;
use App\Models\SiteSetting;
use App\Services\Settings\SiteSettings;
use Illuminate\Support\Str;

class SeoResolver
{
    public function __construct(
        private readonly SiteSettings $siteSettings,
    ) {}

    public function resolveForPost(Post $post): SeoMetadata
    {
        $post->loadMissing(['ogMedia', 'featuredMedia']);

        return $this->resolve($post);
    }

    public function resolveForPage(Page $page): SeoMetadata
    {
        $page->loadMissing(['ogMedia']);

        return $this->resolve($page);
    }

    public function resolveForSite(): SeoMetadata
    {
        $settings = $this->siteSettings->get();
        $settings->loadMissing('defaultOgMedia');

        $title = $this->firstNonEmptyString(
            $settings->default_seo_title,
            $settings->site_name,
            config('app.name', 'ContentFlow CMS'),
        ) ?? config('app.name', 'ContentFlow CMS');

        return new SeoMetadata(
            title: $title,
            metaDescription: $this->firstNonEmptyString(
                $settings->default_meta_description,
                $settings->site_description,
            ),
            robotsIndex: (bool) $settings->default_robots_index,
            ogImageUrl: $settings->defaultOgMedia?->url(),
            canonicalUrl: null,
        );
    }

    public function resolveForListing(string $heading): SeoMetadata
    {
        $settings = $this->siteSettings->get();
        $settings->loadMissing('defaultOgMedia');

        $siteName = $this->firstNonEmptyString(
            $settings->site_name,
            config('app.name', 'ContentFlow CMS'),
        ) ?? config('app.name', 'ContentFlow CMS');

        return new SeoMetadata(
            title: trim($heading).' — '.$siteName,
            metaDescription: $this->firstNonEmptyString(
                $settings->default_meta_description,
                $settings->site_description,
            ),
            robotsIndex: (bool) $settings->default_robots_index,
            ogImageUrl: $settings->defaultOgMedia?->url(),
            canonicalUrl: null,
        );
    }

    private function resolve(Post|Page $content): SeoMetadata
    {
        $defaults = $this->siteDefaults();

        $title = $this->firstNonEmptyString(
            $content->seo_title,
            $defaults['seo_title'],
            $content->title,
            config('app.name', 'ContentFlow CMS'),
        ) ?? config('app.name', 'ContentFlow CMS');

        $metaDescription = $this->firstNonEmptyString(
            $content->meta_description,
            $defaults['meta_description'],
            $content instanceof Post ? $content->excerpt : null,
            $this->excerptFromContent($content->content),
            $defaults['site_description'],
        );

        $ogImageUrl = $this->resolveOgImageUrl($content, $defaults);

        return new SeoMetadata(
            title: $title,
            metaDescription: $metaDescription,
            robotsIndex: (bool) $content->robots_index,
            ogImageUrl: $ogImageUrl,
            canonicalUrl: filled($content->canonical_url) ? trim($content->canonical_url) : null,
        );
    }

    /**
     * @return array{
     *     seo_title: ?string,
     *     meta_description: ?string,
     *     site_description: ?string,
     *     og_media: ?Media
     * }
     */
    private function siteDefaults(): array
    {
        $settings = $this->siteSettings->get();

        if (! $settings->exists) {
            return [
                'seo_title' => null,
                'meta_description' => null,
                'site_description' => null,
                'og_media' => null,
            ];
        }

        $settings->loadMissing('defaultOgMedia');

        return [
            'seo_title' => filled($settings->default_seo_title) ? $settings->default_seo_title : null,
            'meta_description' => filled($settings->default_meta_description) ? $settings->default_meta_description : null,
            'site_description' => filled($settings->site_description) ? $settings->site_description : null,
            'og_media' => $settings->defaultOgMedia,
        ];
    }

    /**
     * @param  array{og_media: ?Media}  $defaults
     */
    private function resolveOgImageUrl(Post|Page $content, array $defaults): ?string
    {
        if ($content->ogMedia !== null) {
            return $content->ogMedia->url();
        }

        if ($defaults['og_media'] !== null) {
            return $defaults['og_media']->url();
        }

        if ($content instanceof Post && $content->featuredMedia !== null) {
            return $content->featuredMedia->url();
        }

        return null;
    }

    private function excerptFromContent(string $content): ?string
    {
        $plain = trim(preg_replace('/\s+/', ' ', strip_tags($content)) ?? '');

        if ($plain === '') {
            return null;
        }

        return Str::limit($plain, 320, '');
    }

    private function firstNonEmptyString(?string ...$candidates): ?string
    {
        foreach ($candidates as $candidate) {
            if ($candidate !== null && trim($candidate) !== '') {
                return trim($candidate);
            }
        }

        return null;
    }
}
