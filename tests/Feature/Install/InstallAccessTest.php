<?php

namespace Tests\Feature\Install;

use App\Support\Install\InstallLock;
use Tests\Feature\Install\Concerns\ClearsInstallLock;
use Tests\LightweightTestCase;

class InstallAccessTest extends LightweightTestCase
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

    public function test_installer_routes_are_available_when_unlocked(): void
    {
        $this->get(route('install.requirements'))
            ->assertOk()
            ->assertSee('System requirements');
    }

    public function test_locked_installer_routes_return_not_found(): void
    {
        InstallLock::lock();

        $this->get(route('install.requirements'))->assertNotFound();
        $this->post(route('install.requirements.store'))->assertNotFound();
        $this->get(route('install.application'))->assertNotFound();
        $this->get(route('install.database'))->assertNotFound();
    }

    public function test_complete_page_is_accessible_after_lock(): void
    {
        InstallLock::lock();

        $this->get(route('install.complete'))
            ->assertOk()
            ->assertSee('Installation complete');
    }

    public function test_wizard_steps_require_prior_session_progress(): void
    {
        $this->get(route('install.application'))
            ->assertRedirect(route('install.requirements'));

        $this->get(route('install.database'))
            ->assertRedirect(route('install.application'));

        $this->get(route('install.administrator'))
            ->assertRedirect(route('install.database'));

        $this->get(route('install.settings'))
            ->assertRedirect(route('install.administrator'));
    }
}
