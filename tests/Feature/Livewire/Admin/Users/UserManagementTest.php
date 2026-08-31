<?php

namespace Tests\Feature\Livewire\Admin\Users;

use App\Enums\UserStatus;
use App\Livewire\Admin\Users\Create;
use App\Livewire\Admin\Users\Edit;
use App\Livewire\Admin\Users\Index;
use App\Models\Role;
use App\Models\User;
use App\Support\AccessControl\RoleName;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use DatabaseTransactions;

    public function test_administrator_can_create_user_with_roles(): void
    {
        $admin = User::factory()->administrator()->create();
        $authorRole = Role::query()->where('name', RoleName::Author)->firstOrFail();

        Livewire::actingAs($admin)
            ->test(Create::class)
            ->set('name', 'New Editor')
            ->set('email', 'editor@example.com')
            ->set('password', 'password-1')
            ->set('password_confirmation', 'password-1')
            ->set('status', 'active')
            ->set('selectedRoles', [$authorRole->id])
            ->call('save')
            ->assertRedirect(route('admin.users.index'));

        $user = User::query()->where('email', 'editor@example.com')->firstOrFail();

        $this->assertSame(UserStatus::Active, $user->status);
        $this->assertTrue($user->hasRole(RoleName::Author));
    }

    public function test_duplicate_email_is_rejected_on_create(): void
    {
        $admin = User::factory()->administrator()->create();
        $authorRole = Role::query()->where('name', RoleName::Author)->firstOrFail();
        User::factory()->create(['email' => 'taken@example.com']);

        Livewire::actingAs($admin)
            ->test(Create::class)
            ->set('name', 'Duplicate User')
            ->set('email', 'taken@example.com')
            ->set('password', 'password-1')
            ->set('password_confirmation', 'password-1')
            ->set('selectedRoles', [$authorRole->id])
            ->call('save')
            ->assertHasErrors(['email']);
    }

    public function test_administrator_can_update_user_profile_and_roles(): void
    {
        $admin = User::factory()->administrator()->create();
        $authorRole = Role::query()->where('name', RoleName::Author)->firstOrFail();
        $editorRole = Role::query()->where('name', RoleName::Editor)->firstOrFail();
        $user = User::factory()->author()->create([
            'name' => 'Before',
            'email' => 'before@example.com',
        ]);

        Livewire::actingAs($admin)
            ->test(Edit::class, ['user' => $user])
            ->set('name', 'After')
            ->set('email', 'after@example.com')
            ->set('selectedRoles', [$editorRole->id])
            ->call('save')
            ->assertRedirect(route('admin.users.index'));

        $user->refresh();

        $this->assertSame('After', $user->name);
        $this->assertSame('after@example.com', $user->email);
        $this->assertFalse($user->hasRole(RoleName::Author));
        $this->assertTrue($user->hasRole(RoleName::Editor));
    }

    public function test_administrator_can_deactivate_user_and_login_is_blocked(): void
    {
        $admin = User::factory()->administrator()->create();
        $target = User::factory()->author()->create([
            'password' => Hash::make('password-1'),
        ]);

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->call('deactivate', $target->id)
            ->assertHasNoErrors();

        $this->assertSame(UserStatus::Inactive, $target->fresh()->status);

        $this->post(route('logout'));

        $this->post(route('login'), [
            'email' => $target->email,
            'password' => 'password-1',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_administrator_cannot_deactivate_own_account(): void
    {
        $admin = User::factory()->administrator()->create();

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->call('deactivate', $admin->id)
            ->assertHasErrors(['status']);
    }

    public function test_administrator_can_reactivate_user(): void
    {
        $admin = User::factory()->administrator()->create();
        $target = User::factory()->author()->inactive()->create();

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->call('activate', $target->id)
            ->assertHasNoErrors();

        $this->assertSame(UserStatus::Active, $target->fresh()->status);
    }
}
