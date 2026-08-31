<?php

namespace Tests\Feature\Auth;

use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        RateLimiter::clear('test@example.com|127.0.0.1');
    }

    public function test_active_user_can_log_in_and_reach_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_password_is_rejected_with_generic_message(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $response = $this->from(route('login'))->post(route('login'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertSame(__('auth.failed'), session('errors')->get('email')[0]);
    }

    public function test_unknown_email_is_rejected_with_generic_message(): void
    {
        $response = $this->from(route('login'))->post(route('login'), [
            'email' => 'unknown@example.com',
            'password' => 'password',
        ]);

        $response
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_inactive_user_cannot_log_in(): void
    {
        $user = User::factory()->inactive()->create([
            'password' => Hash::make('password'),
        ]);

        $response = $this->from(route('login'))->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertSame(UserStatus::Inactive, $user->fresh()->status);
    }

    public function test_login_regenerates_session(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $this->get(route('login'));
        $originalSessionId = session()->getId();

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertNotSame($originalSessionId, session()->getId());
    }

    public function test_authenticated_user_is_redirected_away_from_login(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('login'));

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_login_is_rate_limited_after_repeated_failures(): void
    {
        $user = User::factory()->create([
            'email' => 'throttle@example.com',
            'password' => Hash::make('password'),
        ]);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->from(route('login'))->post(route('login'), [
                'email' => $user->email,
                'password' => 'wrong-password',
            ]);
        }

        $response = $this->from(route('login'))->post(route('login'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
