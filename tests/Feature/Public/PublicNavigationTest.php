<?php

namespace Tests\Feature\Public;

use App\Enums\MenuItemType;
use App\Models\Menu;
use App\Models\Page;
use App\Models\Post;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PublicNavigationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_primary_menu_omits_unpublished_targets(): void
    {
        $menu = Menu::factory()->primary()->create();
        $publishedPage = Page::factory()->published()->create(['title' => 'Public About']);
        $draftPage = Page::factory()->create(['title' => 'Secret Draft Page']);
        $publishedPost = Post::factory()->published()->create(['title' => 'Public Announcement']);

        $menu->items()->createMany([
            [
                'type' => MenuItemType::Page,
                'label' => 'About',
                'page_id' => $publishedPage->id,
                'position' => 0,
            ],
            [
                'type' => MenuItemType::Page,
                'label' => 'Hidden Draft',
                'page_id' => $draftPage->id,
                'position' => 1,
            ],
            [
                'type' => MenuItemType::Post,
                'label' => 'Announcement',
                'post_id' => $publishedPost->id,
                'position' => 2,
            ],
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('About')
            ->assertSee('Announcement')
            ->assertDontSee('Hidden Draft');
    }
}
