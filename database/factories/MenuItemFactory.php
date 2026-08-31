<?php

namespace Database\Factories;

use App\Enums\MenuItemType;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MenuItem>
 */
class MenuItemFactory extends Factory
{
    protected $model = MenuItem::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'menu_id' => Menu::factory(),
            'type' => MenuItemType::Custom,
            'label' => fake()->words(2, true),
            'page_id' => null,
            'post_id' => null,
            'category_id' => null,
            'custom_url' => '/'.fake()->slug(),
            'position' => 0,
        ];
    }

    public function custom(string $label, string $url): static
    {
        return $this->state(fn (): array => [
            'type' => MenuItemType::Custom,
            'label' => $label,
            'custom_url' => $url,
        ]);
    }
}
