<?php

namespace Tests\Unit\Queries\Navigation;

use App\Enums\MenuItemType;
use App\Models\Menu;
use App\Models\Page;
use App\Models\Post;
use App\Queries\Navigation\MenuNavigationQuery;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MenuNavigationQueryTest extends TestCase
{
    use DatabaseTransactions;

    public function test_public_items_include_visibility_flags(): void
    {
        $menu = Menu::factory()->primary()->create();
        $publishedPage = Page::factory()->published()->create();
        $draftPage = Page::factory()->create();
        $publishedPost = Post::factory()->published()->create();

        $menu->items()->createMany([
            [
                'type' => MenuItemType::Custom,
                'label' => 'Home',
                'custom_url' => '/',
                'position' => 0,
            ],
            [
                'type' => MenuItemType::Page,
                'label' => 'Published Page',
                'page_id' => $publishedPage->id,
                'position' => 1,
            ],
            [
                'type' => MenuItemType::Page,
                'label' => 'Draft Page',
                'page_id' => $draftPage->id,
                'position' => 2,
            ],
            [
                'type' => MenuItemType::Post,
                'label' => 'Published Post',
                'post_id' => $publishedPost->id,
                'position' => 3,
            ],
        ]);

        $items = app(MenuNavigationQuery::class)->publicItemsForMenu('primary');

        $this->assertCount(4, $items);
        $this->assertTrue($items[0]['is_publicly_visible']);
        $this->assertTrue($items[1]['is_publicly_visible']);
        $this->assertFalse($items[2]['is_publicly_visible']);
        $this->assertTrue($items[3]['is_publicly_visible']);
    }
}
