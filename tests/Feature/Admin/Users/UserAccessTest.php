<?php

namespace Tests\Feature\Admin\Users;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserAccessTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_cannot_access_users_index(): void
    {
        $this->get(route('admin.users.index'))
            ->assertRedirect(route('login'));
    }

    public function test_guest_cannot_access_user_create_page(): void
    {
        $this->get(route('admin.users.create'))
            ->assertRedirect(route('login'));
    }

    public function test_administrator_can_access_users_index(): void
    {
        $admin = User::factory()->administrator()->create();

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('Manage administrative and editorial user accounts');
    }

    public function test_author_without_users_manage_cannot_access_users_index(): void
    {
        $author = User::factory()->author()->create();

        $this->actingAs($author)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }
}
