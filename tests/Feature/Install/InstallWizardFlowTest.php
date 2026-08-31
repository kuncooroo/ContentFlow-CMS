<?php

namespace Tests\Feature\Install;

use App\Support\Install\InstallDatabaseTester;
use App\Support\Install\InstallLock;
use App\Support\Install\InstallRequirements;
use App\Support\Install\InstallService;
use Illuminate\Validation\ValidationException;
use Tests\Feature\Install\Concerns\ClearsInstallLock;
use Tests\LightweightTestCase;

class InstallWizardFlowTest extends LightweightTestCase
{
    use ClearsInstallLock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->clearInstallLock();
    }

    protected function tearDown(): void
    {
        $this->clearInstallLock();

        parent::tearDown();
    }

    public function test_failed_requirements_cannot_advance(): void
    {
        $this->mock(InstallRequirements::class, function ($mock): void {
            $mock->shouldReceive('passes')->andReturn(false);
        });

        $this->post(route('install.requirements.store'))
            ->assertRedirect()
            ->assertSessionHasErrors('requirements');

        $this->get(route('install.application'))
            ->assertRedirect(route('install.requirements'));
    }

    public function test_invalid_database_credentials_show_error_without_advancing(): void
    {
        $this->mock(InstallDatabaseTester::class, function ($mock): void {
            $mock->shouldReceive('test')
                ->once()
                ->andThrow(new \InvalidArgumentException(
                    'Could not connect to the database. Check the credentials and ensure the database exists.',
                ));
        });

        $this->passRequirements();
        $this->passApplication();

        $response = $this->post(route('install.database.store'), [
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'contentflow',
            'username' => 'root',
            'password' => 'wrong-secret',
        ]);

        $response
            ->assertRedirect()
            ->assertSessionHasErrors('database');

        $this->get(route('install.database'))
            ->assertOk()
            ->assertDontSee('wrong-secret');

        $this->assertFalse(session()->has('install.database_configured'));
    }

    public function test_migration_failure_does_not_report_success(): void
    {
        $this->mock(InstallService::class, function ($mock): void {
            $mock->shouldReceive('saveApplicationConfiguration')->andReturn();
            $mock->shouldReceive('configureDatabase')->once();
            $mock->shouldReceive('migrateAndSeed')
                ->once()
                ->andThrow(ValidationException::withMessages([
                    'database' => 'Database migration failed. Verify credentials and permissions, then try again.',
                ]));
        });

        $this->passRequirements();
        $this->passApplication();

        $this->post(route('install.database.store'), [
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'contentflow',
            'username' => 'root',
            'password' => '',
        ])
            ->assertRedirect()
            ->assertSessionHasErrors('database');

        $this->assertFalse(InstallLock::isLocked());
        $this->assertFalse(session()->has('install.database_configured'));
    }

    private function passRequirements(): void
    {
        $this->post(route('install.requirements.store'))
            ->assertRedirect(route('install.application'));
    }

    private function passApplication(): void
    {
        $this->post(route('install.application.store'), [
            'app_name' => 'Test CMS',
            'app_url' => 'http://localhost',
        ])->assertRedirect(route('install.database'));
    }
}
