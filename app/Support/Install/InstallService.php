<?php

namespace App\Support\Install;

use App\Enums\UserStatus;
use App\Models\Role;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\Settings\SiteSettings;
use App\Support\AccessControl\RoleName;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\SiteSettingsSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class InstallService
{
    public function __construct(
        private readonly InstallEnvironmentWriter $environmentWriter,
        private readonly InstallDatabaseTester $databaseTester,
    ) {}

    /**
     * @param  array{app_name: string, app_url: string}  $data
     */
    public function saveApplicationConfiguration(array $data): void
    {
        if (app()->environment('testing')) {
            Config::set('app.name', $data['app_name']);
            Config::set('app.url', rtrim($data['app_url'], '/'));

            return;
        }

        $this->environmentWriter->write([
            'APP_NAME' => $data['app_name'],
            'APP_URL' => rtrim($data['app_url'], '/'),
        ]);
        $this->environmentWriter->ensureApplicationKey();

        Artisan::call('config:clear');
    }

    /**
     * @param  array{
     *     host: string,
     *     port: int,
     *     database: string,
     *     username: string,
     *     password: ?string
     * }  $data
     */
    public function configureDatabase(array $data): void
    {
        $this->databaseTester->test($data);

        if (app()->environment('testing')) {
            Config::set('database.default', 'mysql');
            Config::set('database.connections.mysql.host', $data['host']);
            Config::set('database.connections.mysql.port', $data['port']);
            Config::set('database.connections.mysql.database', $data['database']);
            Config::set('database.connections.mysql.username', $data['username']);
            Config::set('database.connections.mysql.password', $data['password'] ?? '');

            DB::purge('mysql');

            return;
        }

        $this->environmentWriter->write([
            'DB_CONNECTION' => 'mysql',
            'DB_HOST' => $data['host'],
            'DB_PORT' => (string) $data['port'],
            'DB_DATABASE' => $data['database'],
            'DB_USERNAME' => $data['username'],
            'DB_PASSWORD' => $data['password'] ?? '',
        ]);

        Artisan::call('config:clear');

        Config::set('database.default', 'mysql');
        Config::set('database.connections.mysql.host', $data['host']);
        Config::set('database.connections.mysql.port', $data['port']);
        Config::set('database.connections.mysql.database', $data['database']);
        Config::set('database.connections.mysql.username', $data['username']);
        Config::set('database.connections.mysql.password', $data['password'] ?? '');

        DB::purge('mysql');
    }

    public function migrateAndSeed(): void
    {
        if (Schema::hasTable('users') && User::query()->exists()) {
            throw ValidationException::withMessages([
                'database' => 'This database already contains user accounts. Use a fresh database or remove install.lock only after backing up data.',
            ]);
        }

        try {
            Artisan::call('migrate', ['--force' => true]);
        } catch (\Throwable $exception) {
            throw ValidationException::withMessages([
                'database' => 'Database migration failed. Verify credentials and permissions, then try again.',
            ]);
        }

        Artisan::call('db:seed', ['--class' => RolePermissionSeeder::class, '--force' => true]);
        Artisan::call('db:seed', ['--class' => SiteSettingsSeeder::class, '--force' => true]);
    }

    /**
     * @param  array{name: string, email: string, password: string}  $data
     */
    /**
     * @param  array{name: string, email: string, password: string}  $adminData
     * @param  array{
     *     site_name: string,
     *     timezone: string,
     *     locale: string
     * }  $settingsData
     */
    public function finalizeInstallation(array $adminData, array $settingsData): User
    {
        $user = DB::transaction(function () use ($adminData, $settingsData): User {
            $user = $this->createSuperAdmin($adminData);
            $this->updateInitialSiteSettings($settingsData);

            return $user;
        });

        $this->completeInstallation();

        return $user;
    }

    /**
     * @param  array{name: string, email: string, password: string}  $data
     */
    public function createSuperAdmin(array $data): User
    {
        if (User::query()->where('email', Str::lower($data['email']))->exists()) {
            throw ValidationException::withMessages([
                'email' => 'A user with this email already exists.',
            ]);
        }

        $role = Role::query()->where('name', RoleName::SuperAdmin)->first();

        if ($role === null) {
            throw ValidationException::withMessages([
                'database' => 'Super Admin role is missing. Re-run migrations and seeders, then try again.',
            ]);
        }

        return DB::transaction(function () use ($data, $role): User {
            $user = User::query()->create([
                'name' => $data['name'],
                'email' => Str::lower($data['email']),
                'password' => $data['password'],
                'status' => UserStatus::Active,
            ]);

            $user->roles()->attach($role->id, [
                'assigned_by_user_id' => null,
                'created_at' => now(),
            ]);

            return $user;
        });
    }

    /**
     * @param  array{
     *     site_name: string,
     *     timezone: string,
     *     locale: string
     * }  $data
     */
    public function updateInitialSiteSettings(array $data): void
    {
        $settings = SiteSetting::bootstrap();
        $settings->update([
            'site_name' => $data['site_name'],
            'timezone' => $data['timezone'],
            'locale' => $data['locale'],
        ]);

        app(SiteSettings::class)->invalidate();
    }

    public function completeInstallation(): void
    {
        InstallLock::lock();
    }
}
