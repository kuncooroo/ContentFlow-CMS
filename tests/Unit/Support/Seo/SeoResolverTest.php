<?php

namespace Tests\Unit\Support\Seo;

use App\Models\Media;
use App\Models\Page;
use App\Models\Post;
use App\Models\SiteSetting;
use App\Services\Settings\SiteSettings;
use App\Support\Seo\SeoResolver;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class SeoResolverTest extends TestCase
{
    use DatabaseTransactions;

    public function test_post_uses_content_title_when_seo_fields_are_empty(): void
    {
        $post = Post::factory()->create([
            'title' => 'Hello World',
            'seo_title' => null,
            'meta_description' => null,
            'excerpt' => null,
            'content' => 'Body copy for the post.',
        ]);

        $metadata = app(SeoResolver::class)->resolveForPost($post);

        $this->assertSame('Hello World', $metadata->title);
        $this->assertSame('Body copy for the post.', $metadata->metaDescription);
        $this->assertTrue($metadata->robotsIndex);
    }

    public function test_content_seo_overrides_are_used(): void
    {
        $post = Post::factory()->create([
            'title' => 'Hello World',
            'seo_title' => 'Custom SEO Title',
            'meta_description' => 'Custom meta description',
            'robots_index' => false,
        ]);

        $metadata = app(SeoResolver::class)->resolveForPost($post);

        $this->assertSame('Custom SEO Title', $metadata->title);
        $this->assertSame('Custom meta description', $metadata->metaDescription);
        $this->assertFalse($metadata->robotsIndex);
    }

    public function test_post_excerpt_is_used_before_body_excerpt(): void
    {
        $post = Post::factory()->create([
            'title' => 'Post title',
            'excerpt' => 'Short excerpt',
            'content' => 'Longer body content that should not be used first.',
        ]);

        $metadata = app(SeoResolver::class)->resolveForPost($post);

        $this->assertSame('Short excerpt', $metadata->metaDescription);
    }

    public function test_og_image_falls_back_to_featured_media_for_posts(): void
    {
        $media = Media::factory()->withStoredFile()->create();
        $post = Post::factory()->create([
            'featured_media_id' => $media->id,
            'og_media_id' => null,
        ]);

        $metadata = app(SeoResolver::class)->resolveForPost($post);

        $this->assertSame($media->url(), $metadata->ogImageUrl);
    }

    public function test_content_og_image_takes_priority_over_featured_media(): void
    {
        $featured = Media::factory()->withStoredFile()->create();
        $og = Media::factory()->withStoredFile()->create();
        $post = Post::factory()->create([
            'featured_media_id' => $featured->id,
            'og_media_id' => $og->id,
        ]);

        $metadata = app(SeoResolver::class)->resolveForPost($post);

        $this->assertSame($og->url(), $metadata->ogImageUrl);
    }

    public function test_page_resolver_uses_title_fallback(): void
    {
        $page = Page::factory()->create([
            'title' => 'About Us',
            'seo_title' => null,
            'content' => 'About page body.',
        ]);

        $metadata = app(SeoResolver::class)->resolveForPage($page);

        $this->assertSame('About Us', $metadata->title);
        $this->assertSame('About page body.', $metadata->metaDescription);
    }

    public function test_app_name_is_final_title_fallback(): void
    {
        Config::set('app.name', 'ContentFlow CMS');

        $post = Post::factory()->make([
            'title' => '',
            'seo_title' => null,
        ]);

        $metadata = app(SeoResolver::class)->resolveForPost($post);

        $this->assertSame('ContentFlow CMS', $metadata->title);
    }

    public function test_site_settings_defaults_are_used_when_available(): void
    {
        $defaultOg = Media::factory()->withStoredFile()->create();

        SiteSetting::query()->updateOrCreate(
            ['id' => SiteSetting::SINGLETON_ID],
            [
                'site_name' => 'ContentFlow',
                'default_seo_title' => 'Global SEO Title',
                'default_meta_description' => 'Global description',
                'default_og_media_id' => $defaultOg->id,
                'default_robots_index' => true,
                'timezone' => 'UTC',
                'locale' => 'en',
                'comments_enabled' => true,
            ],
        );

        app(SiteSettings::class)->invalidate();

        $post = Post::factory()->create([
            'title' => 'Post title',
            'seo_title' => null,
            'meta_description' => null,
            'og_media_id' => null,
            'featured_media_id' => null,
        ]);

        $metadata = app(SeoResolver::class)->resolveForPost($post);

        $this->assertSame('Global SEO Title', $metadata->title);
        $this->assertSame('Global description', $metadata->metaDescription);
        $this->assertSame($defaultOg->url(), $metadata->ogImageUrl);
    }
}
