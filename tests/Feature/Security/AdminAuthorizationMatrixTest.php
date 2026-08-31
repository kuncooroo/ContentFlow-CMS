<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthorizationMatrixTest extends TestCase
{
    use DatabaseTransactions;

    public function test_author_cannot_access_user_management(): void
    {
        $author = User::factory()->author()->create();

        $this->actingAs($author)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_author_cannot_access_role_management(): void
    {
        $author = User::factory()->author()->create();

        $this->actingAs($author)
            ->get(route('admin.roles.index'))
            ->assertForbidden();
    }

    public function test_author_cannot_access_site_settings(): void
    {
        $author = User::factory()->author()->create();

        $this->actingAs($author)
            ->get(route('admin.settings.general'))
            ->assertForbidden();
    }

    public function test_author_cannot_access_activity_log(): void
    {
        $author = User::factory()->author()->create();

        $this->actingAs($author)
            ->get(route('admin.audit.index'))
            ->assertForbidden();
    }

    public function test_editor_cannot_access_user_management(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_editor_cannot_access_activity_log(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->get(route('admin.audit.index'))
            ->assertForbidden();
    }

    public function test_inactive_user_cannot_access_admin_after_login_attempt(): void
    {
        $user = User::factory()->inactive()->create([
            'password' => Hash::make('password'),
        ]);

        $this->from(route('login'))->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('login'));

        $this->assertGuest();

        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));
    }
}
