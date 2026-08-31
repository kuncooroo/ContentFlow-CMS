<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        Menu::query()->updateOrCreate(
            ['key' => 'primary'],
            ['name' => 'Primary Navigation'],
        );

        Menu::query()->updateOrCreate(
            ['key' => 'footer'],
            ['name' => 'Footer Navigation'],
        );
    }
}
