<?php

namespace Tests\Feature\Authorization;

use App\Actions\AccessControl\AssignRole;
use App\Actions\Users\ChangeUserStatus;
use App\Enums\UserStatus;
use App\Models\Role;
use App\Models\User;
use App\Support\AccessControl\RoleName;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SuperAdminProtectionTest extends TestCase
{
    use DatabaseTransactions;

    public function test_cannot_deactivate_last_active_super_admin(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $actor = User::factory()->administrator()->create();

        $this->expectException(ValidationException::class);

        app(ChangeUserStatus::class)->handle(
            $superAdmin,
            UserStatus::Inactive,
            $actor,
        );
    }

    public function test_cannot_remove_super_admin_role_from_last_active_super_admin(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $actor = User::factory()->administrator()->create();
        $authorRole = Role::query()->where('name', RoleName::Author)->firstOrFail();

        $this->expectException(ValidationException::class);

        app(AssignRole::class)->handle(
            $superAdmin,
            [$authorRole->id],
            $actor,
        );
    }

    public function test_can_deactivate_super_admin_when_another_active_super_admin_exists(): void
    {
        $first = User::factory()->superAdmin()->create();
        $second = User::factory()->superAdmin()->create();
        $actor = User::factory()->administrator()->create();

        $updated = app(ChangeUserStatus::class)->handle(
            $first,
            UserStatus::Inactive,
            $actor,
        );

        $this->assertSame(UserStatus::Inactive, $updated->status);
        $this->assertTrue($second->fresh()->isActive());
    }

    public function test_author_cannot_access_roles_index(): void
    {
        $author = User::factory()->author()->create();

        $this->actingAs($author)
            ->get(route('admin.roles.index'))
            ->assertForbidden();
    }
}
