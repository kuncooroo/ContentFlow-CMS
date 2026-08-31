<?php

namespace Tests;

use App\Models\Role;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\SiteSettingsSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

abstract class TestCase extends BaseTestCase
{
    protected static bool $schemaPrepared = false;

    protected function setUp(): void
    {
        parent::setUp();

        if (! static::$schemaPrepared) {
            Artisan::call('migrate:fresh', ['--force' => true]);
            static::$schemaPrepared = true;
        }

        if (Schema::hasTable('roles')) {
            $this->seed(RolePermissionSeeder::class);
        }

        if (Schema::hasTable('site_settings')) {
            $this->seed(SiteSettingsSeeder::class);
        }
    }
}
