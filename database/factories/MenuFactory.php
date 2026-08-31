<?php

namespace Database\Factories;

use App\Models\Menu;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Menu>
 */
class MenuFactory extends Factory
{
    protected $model = Menu::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $key = Str::slug(fake()->unique()->words(2, true), '_');

        return [
            'key' => $key,
            'name' => ucwords(str_replace('_', ' ', $key)),
        ];
    }

    public function primary(): static
    {
        return $this->state(fn (): array => [
            'key' => 'primary',
            'name' => 'Primary Navigation',
        ]);
    }
}
