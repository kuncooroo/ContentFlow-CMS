<?php

namespace Tests\Feature\Auth;

use App\Notifications\Auth\ResetPasswordNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use DatabaseTransactions;

    public function test_forgot_password_shows_success_message_for_active_user(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'reset@example.com',
        ]);

        $response = $this->from(route('password.request'))->post(route('password.email'), [
            'email' => $user->email,
        ]);

        $response
            ->assertRedirect(route('password.request'))
            ->assertSessionHas('status', __('passwords.sent'));

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_forgot_password_does_not_reveal_unknown_email(): void
    {
        Notification::fake();

        $response = $this->from(route('password.request'))->post(route('password.email'), [
            'email' => 'unknown@example.com',
        ]);

        $response
            ->assertRedirect(route('password.request'))
            ->assertSessionHas('status', __('passwords.sent'));

        Notification::assertNothingSent();
    }

    public function test_inactive_user_does_not_receive_reset_link_but_gets_generic_response(): void
    {
        Notification::fake();

        $user = User::factory()->inactive()->create([
            'email' => 'inactive@example.com',
        ]);

        $response = $this->from(route('password.request'))->post(route('password.email'), [
            'email' => $user->email,
        ]);

        $response
            ->assertRedirect(route('password.request'))
            ->assertSessionHas('status', __('passwords.sent'));

        Notification::assertNotSentTo($user, ResetPasswordNotification::class);
    }

    public function test_user_can_reset_password_with_valid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'reset@example.com',
            'password' => Hash::make('old-password'),
        ]);

        $token = Password::createToken($user);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password-1',
            'password_confirmation' => 'new-password-1',
        ]);

        $response
            ->assertRedirect(route('login'))
            ->assertSessionHas('status', __('passwords.reset'));

        $this->assertTrue(Hash::check('new-password-1', $user->fresh()->password));
    }

    public function test_invalid_reset_token_is_rejected(): void
    {
        $user = User::factory()->create([
            'email' => 'reset@example.com',
        ]);

        $response = $this->from(route('password.reset', ['token' => 'invalid-token']))
            ->post(route('password.update'), [
                'token' => 'invalid-token',
                'email' => $user->email,
                'password' => 'new-password-1',
                'password_confirmation' => 'new-password-1',
            ]);

        $response
            ->assertRedirect(route('password.reset', ['token' => 'invalid-token']))
            ->assertSessionHasErrors('email');
    }
}
