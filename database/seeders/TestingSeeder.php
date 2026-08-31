<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Deterministic seed data for local QA and automated regression demos.
 *
 * Usage: php artisan db:seed --class=TestingSeeder
 */
class TestingSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            SiteSettingsSeeder::class,
        ]);

        User::factory()->administrator()->create([
            'name' => 'Test Administrator',
            'email' => 'admin@contentflow.test',
        ]);

        User::factory()->editor()->create([
            'name' => 'Test Editor',
            'email' => 'editor@contentflow.test',
        ]);

        User::factory()->author()->create([
            'name' => 'Test Author',
            'email' => 'author@contentflow.test',
        ]);
    }
}
