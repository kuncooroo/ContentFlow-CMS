<?php

namespace Tests\Feature\Admin\Tags;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TagAccessTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_cannot_access_tags_index(): void
    {
        $this->get(route('admin.tags.index'))
            ->assertRedirect(route('login'));
    }

    public function test_editor_can_access_tags_index(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->get(route('admin.tags.index'))
            ->assertOk()
            ->assertSee('Manage post tags');
    }

    public function test_author_can_access_tags_index(): void
    {
        $author = User::factory()->author()->create();

        $this->actingAs($author)
            ->get(route('admin.tags.index'))
            ->assertOk();
    }
}
