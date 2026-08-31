<?php

namespace Tests\Feature\Admin\Roles;

use App\Livewire\Admin\Roles\Edit;
use App\Livewire\Admin\Roles\Index;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Support\AccessControl\RoleName;
use App\Support\Audit\ActivityEvent;
use App\Support\Permissions\PermissionNames;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class RoleManagementTest extends TestCase
{
    use DatabaseTransactions;

    public function test_administrator_can_view_roles_index(): void
    {
        $admin = User::factory()->administrator()->create();

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->assertOk()
            ->assertSee(RoleName::Author)
            ->assertSee(RoleName::Editor);
    }

    public function test_administrator_can_sync_role_permissions(): void
    {
        $admin = User::factory()->administrator()->create();
        $role = Role::query()->where('name', RoleName::Author)->firstOrFail();
        $publishPermission = Permission::query()
            ->where('name', PermissionNames::PostsPublish)
            ->firstOrFail();

        $permissionIds = $role->permissions()->pluck('permissions.id')->all();
        $permissionIds[] = $publishPermission->id;

        Livewire::actingAs($admin)
            ->test(Edit::class, ['role' => $role])
            ->set('selectedPermissions', $permissionIds)
            ->call('save')
            ->assertRedirect(route('admin.roles.index'));

        $this->assertTrue(
            $role->fresh()->permissions->contains('name', PermissionNames::PostsPublish)
        );

        $this->assertDatabaseHas('activity_logs', [
            'actor_user_id' => $admin->id,
            'event' => ActivityEvent::RolePermissionChanged,
            'subject_type' => 'role',
            'subject_id' => $role->id,
        ]);
    }
}
