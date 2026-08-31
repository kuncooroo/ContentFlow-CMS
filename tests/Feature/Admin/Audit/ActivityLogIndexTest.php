<?php

namespace Tests\Feature\Admin\Audit;

use App\Livewire\Admin\Audit\Index;
use App\Models\User;
use App\Services\Audit\ActivityLogger;
use App\Support\Audit\ActivityEvent;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Tests\TestCase;

class ActivityLogIndexTest extends TestCase
{
    use DatabaseTransactions;

    public function test_administrator_can_browse_activity_log(): void
    {
        $admin = User::factory()->administrator()->create();
        $subject = User::factory()->author()->create();

        app(ActivityLogger::class)->record(
            $admin,
            ActivityEvent::UserCreated,
            $subject,
            ['email' => $subject->email, 'status' => 'active'],
        );

        $this->actingAs($admin)
            ->get(route('admin.audit.index'))
            ->assertOk()
            ->assertSee('Activity log')
            ->assertSee('User created')
            ->assertSee($admin->name)
            ->assertSee('User #'.$subject->id)
            ->assertSee('email: '.$subject->email);
    }

    public function test_editor_cannot_access_activity_log(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->get(route('admin.audit.index'))
            ->assertForbidden();
    }

    public function test_author_cannot_access_activity_log(): void
    {
        $author = User::factory()->author()->create();

        $this->actingAs($author)
            ->get(route('admin.audit.index'))
            ->assertForbidden();
    }

    public function test_deleted_actor_is_shown_as_deleted_user(): void
    {
        $admin = User::factory()->administrator()->create();
        $actor = User::factory()->administrator()->create();
        $subject = User::factory()->author()->create();

        app(ActivityLogger::class)->record(
            $actor,
            ActivityEvent::UserCreated,
            $subject,
        );

        $actor->delete();

        $this->actingAs($admin)
            ->get(route('admin.audit.index'))
            ->assertOk()
            ->assertSee('Deleted user')
            ->assertDontSee($actor->name);
    }

    public function test_event_filter_limits_results(): void
    {
        $admin = User::factory()->administrator()->create();
        $subject = User::factory()->author()->create();

        app(ActivityLogger::class)->record($admin, ActivityEvent::UserCreated, $subject);
        app(ActivityLogger::class)->record($admin, ActivityEvent::SettingsUpdated, null, ['site_name' => 'Test']);

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->set('eventFilter', ActivityEvent::UserCreated)
            ->assertSee('User created')
            ->assertDontSee('Settings updated');
    }

    public function test_pagination_returns_second_page(): void
    {
        $admin = User::factory()->administrator()->create();
        $subject = User::factory()->author()->create();

        for ($i = 0; $i < 21; $i++) {
            app(ActivityLogger::class)->record(
                $admin,
                ActivityEvent::UserCreated,
                $subject,
                ['index' => $i],
            );
        }

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->call('gotoPage', 2)
            ->assertSee('index: 0');
    }

    public function test_detail_modal_shows_properties_without_secrets(): void
    {
        $admin = User::factory()->administrator()->create();
        $subject = User::factory()->author()->create();

        $log = app(ActivityLogger::class)->record(
            $admin,
            ActivityEvent::UserCreated,
            $subject,
            [
                'email' => $subject->email,
                'password' => 'secret-value',
                'nested' => ['reset_token' => 'abc', 'status' => 'active'],
            ],
        );

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->call('showDetails', $log->id)
            ->assertSet('selectedLogId', $log->id)
            ->assertSee($subject->email)
            ->assertSee('"status": "active"')
            ->assertDontSee('secret-value')
            ->assertDontSee('reset_token')
            ->assertDontSee('password');
    }

    public function test_empty_state_when_no_logs_match_filters(): void
    {
        $admin = User::factory()->administrator()->create();

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->set('eventFilter', ActivityEvent::MediaDeleted)
            ->assertSee('No activity logs match your filters.');
    }

    public function test_no_mutation_routes_exist_for_audit(): void
    {
        $mutationRoutes = collect(Route::getRoutes())->filter(function ($route): bool {
            $name = $route->getName() ?? '';

            if (! str_starts_with($name, 'admin.audit')) {
                return false;
            }

            foreach ($route->methods() as $method) {
                if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
                    return true;
                }
            }

            return false;
        });

        $this->assertCount(0, $mutationRoutes);
    }

    public function test_activity_logs_remain_immutable_from_ui_layer(): void
    {
        $admin = User::factory()->administrator()->create();
        $subject = User::factory()->author()->create();

        $log = app(ActivityLogger::class)->record(
            $admin,
            ActivityEvent::UserCreated,
            $subject,
        );

        $this->assertFalse($log->update(['event' => ActivityEvent::UserDeactivated]));
        $this->assertFalse($log->delete());
        $this->assertDatabaseHas('activity_logs', [
            'id' => $log->id,
            'event' => ActivityEvent::UserCreated,
        ]);
    }
}
