<?php

namespace Tests\Feature\Demo;

use App\Models\Post;
use App\Models\User;
use App\Support\Demo\DemoReset;
use Database\Seeders\DemoSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class DemoResetTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('demo.enabled', true);
    }

    protected function tearDown(): void
    {
        Config::set('demo.enabled', false);

        parent::tearDown();
    }

    public function test_demo_reset_restores_seeded_baseline(): void
    {
        $this->seed(DemoSeeder::class);

        Post::factory()->published()->create([
            'slug' => 'temporary-demo-post',
            'title' => 'Temporary Post',
        ]);

        $this->assertTrue(Post::query()->where('slug', 'temporary-demo-post')->exists());

        app(DemoReset::class)->reset();

        $this->assertFalse(Post::query()->where('slug', 'temporary-demo-post')->exists());
        $this->assertTrue(Post::query()->where('slug', 'welcome-to-contentflow')->exists());
        $this->assertTrue(
            User::query()->where('email', 'demo-superadmin@contentflow.test')->exists(),
        );
    }

    public function test_demo_reset_command_requires_demo_mode(): void
    {
        Config::set('demo.enabled', false);

        $this->artisan('demo:reset --force')
            ->assertFailed();
    }

    public function test_demo_reset_service_rejects_when_demo_mode_disabled(): void
    {
        Config::set('demo.enabled', false);

        $this->expectException(ValidationException::class);

        app(DemoReset::class)->reset();
    }
}
