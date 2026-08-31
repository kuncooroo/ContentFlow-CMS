<?php

namespace Tests\Feature\Demo;

use App\Models\User;
use Database\Seeders\DemoSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class DemoBannerTest extends TestCase
{
    use DatabaseTransactions;

    protected function tearDown(): void
    {
        Config::set('demo.enabled', false);

        parent::tearDown();
    }

    public function test_admin_shows_demo_banner_when_enabled(): void
    {
        Config::set('demo.enabled', true);

        $this->seed(DemoSeeder::class);

        $admin = User::query()->where('email', 'demo-superadmin@contentflow.test')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Demo mode');
    }

    public function test_admin_hides_demo_banner_when_disabled(): void
    {
        Config::set('demo.enabled', false);

        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee('Demo mode — explore freely');
    }
}
