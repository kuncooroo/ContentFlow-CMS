<?php

namespace Tests\Feature\Admin\Menus;

use App\Enums\MenuItemType;
use App\Livewire\Admin\Menus\Edit;
use App\Livewire\Admin\Menus\Index;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Page;
use App\Models\Post;
use App\Models\User;
use App\Support\Audit\ActivityEvent;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class MenuManagementTest extends TestCase
{
    use DatabaseTransactions;

    public function test_administrator_can_view_menus_index(): void
    {
        $admin = User::factory()->administrator()->create();
        Menu::factory()->primary()->create();

        $this->actingAs($admin)
            ->get(route('admin.menus.index'))
            ->assertOk()
            ->assertSee('Primary Navigation');
    }

    public function test_editor_cannot_access_menus(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->get(route('admin.menus.index'))
            ->assertForbidden();
    }

    public function test_administrator_can_create_menu(): void
    {
        $admin = User::factory()->administrator()->create();

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->set('newMenuKey', 'footer')
            ->set('newMenuName', 'Footer Navigation')
            ->call('createMenu')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('menus', [
            'key' => 'footer',
            'name' => 'Footer Navigation',
        ]);
    }

    public function test_administrator_can_save_menu_structure_with_order(): void
    {
        $admin = User::factory()->administrator()->create();
        $menu = Menu::factory()->primary()->create();
        $page = Page::factory()->published()->create();
        $post = Post::factory()->published()->create();
        $category = Category::factory()->create();

        Livewire::actingAs($admin)
            ->test(Edit::class, ['menu' => $menu])
            ->set('items', [
                [
                    'label' => 'Home',
                    'type' => MenuItemType::Custom->value,
                    'page_id' => null,
                    'post_id' => null,
                    'category_id' => null,
                    'custom_url' => '/',
                ],
                [
                    'label' => 'About Page',
                    'type' => MenuItemType::Page->value,
                    'page_id' => $page->id,
                    'post_id' => null,
                    'category_id' => null,
                    'custom_url' => null,
                ],
                [
                    'label' => 'Latest Post',
                    'type' => MenuItemType::Post->value,
                    'page_id' => null,
                    'post_id' => $post->id,
                    'category_id' => null,
                    'custom_url' => null,
                ],
                [
                    'label' => 'News',
                    'type' => MenuItemType::Category->value,
                    'page_id' => null,
                    'post_id' => null,
                    'category_id' => $category->id,
                    'custom_url' => null,
                ],
            ])
            ->call('save')
            ->assertRedirect(route('admin.menus.index'));

        $this->assertDatabaseHas('menu_items', [
            'menu_id' => $menu->id,
            'label' => 'Home',
            'position' => 0,
            'custom_url' => '/',
        ]);
        $this->assertDatabaseHas('menu_items', [
            'menu_id' => $menu->id,
            'label' => 'About Page',
            'position' => 1,
            'page_id' => $page->id,
        ]);
        $this->assertDatabaseHas('activity_logs', [
            'event' => ActivityEvent::MenuUpdated,
            'subject_id' => $menu->id,
        ]);
    }

    public function test_invalid_target_is_rejected(): void
    {
        $admin = User::factory()->administrator()->create();
        $menu = Menu::factory()->primary()->create();

        Livewire::actingAs($admin)
            ->test(Edit::class, ['menu' => $menu])
            ->set('items', [
                [
                    'label' => 'Missing Page',
                    'type' => MenuItemType::Page->value,
                    'page_id' => 999999,
                    'post_id' => null,
                    'category_id' => null,
                    'custom_url' => null,
                ],
            ])
            ->call('save')
            ->assertHasErrors(['items.0.page_id']);
    }

    public function test_javascript_custom_url_is_rejected(): void
    {
        $admin = User::factory()->administrator()->create();
        $menu = Menu::factory()->primary()->create();

        Livewire::actingAs($admin)
            ->test(Edit::class, ['menu' => $menu])
            ->set('items', [
                [
                    'label' => 'Bad Link',
                    'type' => MenuItemType::Custom->value,
                    'page_id' => null,
                    'post_id' => null,
                    'category_id' => null,
                    'custom_url' => 'javascript:alert(1)',
                ],
            ])
            ->call('save')
            ->assertHasErrors(['items.0.custom_url']);
    }
}
