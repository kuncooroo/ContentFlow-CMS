<?php

namespace Tests\Unit\Actions\Navigation;

use App\Actions\Navigation\UpdateMenuStructure;
use App\Enums\MenuItemType;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UpdateMenuStructureTest extends TestCase
{
    use DatabaseTransactions;

    public function test_replacing_menu_items_is_atomic(): void
    {
        $admin = User::factory()->administrator()->create();
        $menu = Menu::factory()->primary()->create();

        $menu->items()->create([
            'type' => MenuItemType::Custom,
            'label' => 'Old Item',
            'custom_url' => '/old',
            'position' => 0,
        ]);

        app(UpdateMenuStructure::class)->handle(
            $menu,
            [
                [
                    'label' => 'New Item',
                    'type' => MenuItemType::Custom->value,
                    'custom_url' => '/new',
                ],
            ],
            $admin,
        );

        $this->assertSame(1, $menu->items()->count());
        $this->assertDatabaseMissing('menu_items', ['label' => 'Old Item']);
        $this->assertDatabaseHas('menu_items', ['label' => 'New Item', 'position' => 0]);
    }
}
