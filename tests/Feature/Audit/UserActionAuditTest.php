<?php

namespace Tests\Feature\Audit;

use App\Actions\Users\ChangeUserStatus;
use App\Actions\Users\CreateUser;
use App\Enums\UserStatus;
use App\Livewire\Admin\Users\Create;
use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use App\Support\AccessControl\RoleName;
use App\Support\Audit\ActivityEvent;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class UserActionAuditTest extends TestCase
{
    use DatabaseTransactions;

    public function test_create_user_action_records_user_created_event(): void
    {
        $actor = User::factory()->administrator()->create();

        $user = app(CreateUser::class)->handle(
            name: 'Audited User',
            email: 'audited@example.com',
            password: 'password-1',
            status: UserStatus::Active,
            actor: $actor,
        );

        $this->assertDatabaseHas('activity_logs', [
            'actor_user_id' => $actor->id,
            'event' => ActivityEvent::UserCreated,
            'subject_type' => 'user',
            'subject_id' => $user->id,
        ]);
    }

    public function test_change_user_status_records_deactivation_event(): void
    {
        $actor = User::factory()->administrator()->create();
        $target = User::factory()->author()->create();

        app(ChangeUserStatus::class)->handle($target, UserStatus::Inactive, $actor);

        $log = ActivityLog::query()
            ->where('event', ActivityEvent::UserDeactivated)
            ->where('subject_id', $target->id)
            ->first();

        $this->assertNotNull($log);
        $this->assertSame('inactive', $log->properties['new_status']);
        $this->assertSame('active', $log->properties['previous_status']);
    }

    public function test_user_creation_flow_records_user_created_and_role_assigned_events(): void
    {
        $admin = User::factory()->administrator()->create();
        $authorRole = Role::query()->where('name', RoleName::Author)->firstOrFail();

        Livewire::actingAs($admin)
            ->test(Create::class)
            ->set('name', 'Flow User')
            ->set('email', 'flow-user@example.com')
            ->set('password', 'password-1')
            ->set('password_confirmation', 'password-1')
            ->set('status', 'active')
            ->set('selectedRoles', [$authorRole->id])
            ->call('save')
            ->assertRedirect(route('admin.users.index'));

        $user = User::query()->where('email', 'flow-user@example.com')->firstOrFail();

        $this->assertDatabaseHas('activity_logs', [
            'event' => ActivityEvent::UserCreated,
            'subject_id' => $user->id,
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'event' => ActivityEvent::RoleAssigned,
            'subject_id' => $user->id,
        ]);
    }
}
