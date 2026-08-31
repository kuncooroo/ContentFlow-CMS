<?php

namespace Tests\Feature\Admin\Settings;

use App\Livewire\Admin\Settings\General;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\Settings\SiteSettings;
use App\Support\Audit\ActivityEvent;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Tests\TestCase;

class SiteSettingsManagementTest extends TestCase
{
    use DatabaseTransactions;

    public function test_administrator_can_view_settings_page(): void
    {
        $admin = User::factory()->administrator()->create();

        $this->actingAs($admin)
            ->get(route('admin.settings.general'))
            ->assertOk()
            ->assertSee('Site settings');
    }

    public function test_editor_cannot_access_settings(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->get(route('admin.settings.general'))
            ->assertForbidden();
    }

    public function test_administrator_can_save_site_settings(): void
    {
        $admin = User::factory()->administrator()->create();
        SiteSetting::bootstrap();

        Livewire::actingAs($admin)
            ->test(General::class)
            ->set('site_name', 'Updated CMS Name')
            ->set('site_description', 'Updated description')
            ->set('default_seo_title', 'Global SEO')
            ->set('default_meta_description', 'Global meta')
            ->set('comments_enabled', false)
            ->set('timezone', 'Asia/Jakarta')
            ->set('locale', 'id')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('site_settings', [
            'id' => 1,
            'site_name' => 'Updated CMS Name',
            'default_seo_title' => 'Global SEO',
            'comments_enabled' => false,
            'timezone' => 'Asia/Jakarta',
            'locale' => 'id',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'event' => ActivityEvent::SettingsUpdated,
            'subject_id' => 1,
        ]);
    }

    public function test_cache_is_invalidated_after_save(): void
    {
        $admin = User::factory()->administrator()->create();
        SiteSetting::bootstrap();

        $service = app(SiteSettings::class);
        $this->assertSame('ContentFlow CMS', $service->get()->site_name);
        $this->assertTrue(Cache::has(SiteSettings::CACHE_KEY));

        Livewire::actingAs($admin)
            ->test(General::class)
            ->set('site_name', 'Cached Refresh Name')
            ->call('save');

        $this->assertSame('Cached Refresh Name', $service->get()->site_name);
    }

    public function test_invalid_media_reference_is_rejected(): void
    {
        $admin = User::factory()->administrator()->create();
        SiteSetting::bootstrap();

        Livewire::actingAs($admin)
            ->test(General::class)
            ->set('logo_media_id', 999999)
            ->call('save')
            ->assertHasErrors(['logo_media_id']);
    }

    public function test_invalid_timezone_is_rejected(): void
    {
        $admin = User::factory()->administrator()->create();
        SiteSetting::bootstrap();

        Livewire::actingAs($admin)
            ->test(General::class)
            ->set('timezone', 'Not/A_Timezone')
            ->call('save')
            ->assertHasErrors(['timezone']);
    }
}
