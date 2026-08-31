<?php

namespace Tests\Feature\Install;

use App\Models\Role;
use App\Models\SiteSetting;
use App\Models\User;
use App\Support\AccessControl\RoleName;
use App\Support\Install\InstallLock;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Feature\Install\Concerns\ClearsInstallLock;
use Tests\TestCase;

class InstallWizardTest extends TestCase
{
    use ClearsInstallLock;
    use DatabaseTransactions;

    private const ADMIN_EMAIL = 'installer@contentflow.test';

    private const ADMIN_PASSWORD = 'SecurePass123!';

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

    public function test_fresh_install_completes_happy_path(): void
    {
        $this->completeInstallation();

        $this->assertTrue(InstallLock::isLocked());

        $user = User::query()->where('email', self::ADMIN_EMAIL)->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->status->isActive());
        $this->assertTrue(
            $user->roles()->where('name', RoleName::SuperAdmin)->exists(),
        );

        $settings = SiteSetting::bootstrap();
        $this->assertSame('Installed Site', $settings->site_name);
        $this->assertSame('UTC', $settings->timezone);
        $this->assertSame('en', $settings->locale);

        $this->assertTrue(Role::query()->where('name', RoleName::SuperAdmin)->exists());
    }

    public function test_super_admin_can_log_in_after_install(): void
    {
        $this->completeInstallation();

        $this->post(route('login'), [
            'email' => self::ADMIN_EMAIL,
            'password' => self::ADMIN_PASSWORD,
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticated();
    }

    public function test_database_password_is_not_shown_on_later_steps(): void
    {
        $secretPassword = 'db-secret-not-echoed';

        $this->passRequirements();
        $this->passApplication();
        $this->postDatabaseStep(['password' => $secretPassword])
            ->assertRedirect(route('install.administrator'));

        $this->get(route('install.administrator'))
            ->assertOk()
            ->assertDontSee($secretPassword);

        $this->passAdministrator();

        $this->get(route('install.settings'))
            ->assertOk()
            ->assertDontSee($secretPassword);
    }

    public function test_duplicate_install_is_rejected_when_users_already_exist(): void
    {
        User::factory()->create();

        $this->passRequirements();
        $this->passApplication();

        $this->postDatabaseStep()
            ->assertRedirect()
            ->assertSessionHasErrors('database');

        $this->assertFalse(InstallLock::isLocked());
    }

    public function test_locked_installer_rejects_duplicate_install_attempt(): void
    {
        $this->completeInstallation();

        $this->get(route('install.requirements'))->assertNotFound();
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function postDatabaseStep(array $overrides = [])
    {
        return $this->post(route('install.database.store'), array_merge($this->databaseCredentials(), $overrides));
    }

    /**
     * @return array<string, string>
     */
    private function databaseCredentials(): array
    {
        return [
            'host' => (string) config('database.connections.mysql.host'),
            'port' => (string) config('database.connections.mysql.port'),
            'database' => (string) config('database.connections.mysql.database'),
            'username' => (string) config('database.connections.mysql.username'),
            'password' => (string) config('database.connections.mysql.password'),
        ];
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

    private function passAdministrator(): void
    {
        $this->post(route('install.administrator.store'), [
            'name' => 'Install Admin',
            'email' => self::ADMIN_EMAIL,
            'password' => self::ADMIN_PASSWORD,
            'password_confirmation' => self::ADMIN_PASSWORD,
        ])->assertRedirect(route('install.settings'));
    }

    private function completeInstallation(): void
    {
        $this->passRequirements();
        $this->passApplication();
        $this->postDatabaseStep()->assertRedirect(route('install.administrator'));
        $this->passAdministrator();

        $this->post(route('install.settings.store'), [
            'site_name' => 'Installed Site',
            'timezone' => 'UTC',
            'locale' => 'en',
        ])->assertRedirect(route('install.complete'));

        $this->get(route('install.complete'))
            ->assertOk()
            ->assertSee('Installation complete');
    }
}
