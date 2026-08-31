<?php

namespace Tests\Unit\Support\Install;

use App\Support\Install\InstallLock;
use Illuminate\Support\Facades\Storage;
use Tests\LightweightTestCase;

class InstallLockTest extends LightweightTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
    }

    public function test_install_is_unlocked_by_default(): void
    {
        $this->assertFalse(InstallLock::isLocked());
    }

    public function test_lock_creates_install_lock_file(): void
    {
        InstallLock::lock();

        $this->assertTrue(InstallLock::isLocked());
        Storage::disk('local')->assertExists(InstallLock::LOCK_FILE);
    }
}
