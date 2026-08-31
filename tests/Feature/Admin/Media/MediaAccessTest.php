<?php

namespace Tests\Feature\Admin\Media;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MediaAccessTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_cannot_access_media_library(): void
    {
        $this->get(route('admin.media.index'))
            ->assertRedirect(route('login'));
    }

    public function test_author_can_access_media_library(): void
    {
        $author = User::factory()->author()->create();

        $this->actingAs($author)
            ->get(route('admin.media.index'))
            ->assertOk()
            ->assertSee('Media Library');
    }
}
