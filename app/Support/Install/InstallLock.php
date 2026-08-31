<?php

namespace App\Support\Install;

use Illuminate\Support\Facades\Storage;

class InstallLock
{
    public const LOCK_FILE = 'install.lock';

    public static function isLocked(): bool
    {
        return Storage::disk('local')->exists(self::LOCK_FILE);
    }

    public static function lock(): void
    {
        Storage::disk('local')->put(
            self::LOCK_FILE,
            'installed_at='.now()->toIso8601String().PHP_EOL,
        );
    }

    public static function path(): string
    {
        return Storage::disk('local')->path(self::LOCK_FILE);
    }
}
