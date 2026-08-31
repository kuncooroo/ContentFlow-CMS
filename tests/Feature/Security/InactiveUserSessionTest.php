<?php

namespace Tests\Feature\Security;

use App\Enums\UserStatus;
use App\Livewire\Admin\Users\Index;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class InactiveUserSessionTest extends TestCase
{
    use DatabaseTransactions;

    public function test_deactivated_user_is_logged_out_on_next_request(): void
    {
        $admin = User::factory()->administrator()->create();
        $target = User::factory()->author()->create([
            'password' => Hash::make('password-1'),
        ]);

        $this->actingAs($target)
            ->get(route('admin.dashboard'))
            ->assertOk();

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->call('deactivate', $target->id)
            ->assertHasNoErrors();

        $this->actingAs($target->fresh())
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_deactivation_removes_database_sessions_for_user(): void
    {
        if (config('session.driver') !== 'database') {
            $this->markTestSkipped('Database session driver required.');
        }

        $admin = User::factory()->administrator()->create();
        $target = User::factory()->author()->create([
            'password' => Hash::make('password-1'),
        ]);

        $this->actingAs($target)->get(route('admin.dashboard'))->assertOk();

        $this->assertTrue(
            DB::table(config('session.table', 'sessions'))
                ->where('user_id', $target->id)
                ->exists(),
        );

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->call('deactivate', $target->id)
            ->assertHasNoErrors();

        $this->assertSame(UserStatus::Inactive, $target->fresh()->status);
        $this->assertFalse(
            DB::table(config('session.table', 'sessions'))
                ->where('user_id', $target->id)
                ->exists(),
        );
    }
}
