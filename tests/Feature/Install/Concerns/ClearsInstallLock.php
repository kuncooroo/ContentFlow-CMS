<?php

namespace Tests\Feature\Install\Concerns;

use App\Support\Install\InstallLock;
use Illuminate\Support\Facades\Storage;

trait ClearsInstallLock
{
    protected function clearInstallLock(): void
    {
        if (InstallLock::isLocked()) {
            Storage::disk('local')->delete(InstallLock::LOCK_FILE);
        }
    }
}
