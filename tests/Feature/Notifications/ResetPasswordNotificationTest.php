<?php

namespace Tests\Feature\Notifications;

use App\Models\SiteSetting;
use App\Models\User;
use App\Notifications\Auth\ResetPasswordNotification;
use App\Services\Settings\SiteSettings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ResetPasswordNotificationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_password_reset_notification_is_sent_and_queued(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'reset@example.com',
        ]);

        $this->post(route('password.email'), [
            'email' => $user->email,
        ])->assertRedirect(route('password.request'));

        Notification::assertSentTo($user, ResetPasswordNotification::class);

        Notification::assertSentTo($user, function (ResetPasswordNotification $notification): bool {
            return in_array(ShouldQueue::class, class_implements($notification), true);
        });
    }

    public function test_password_reset_email_uses_site_name_branding(): void
    {
        SiteSetting::bootstrap()->update([
            'site_name' => 'Branded CMS',
        ]);
        app(SiteSettings::class)->invalidate();
        Cache::forget(SiteSettings::CACHE_KEY);

        $user = User::factory()->create([
            'email' => 'reset@example.com',
        ]);

        $notification = new ResetPasswordNotification('test-token');
        $mail = $notification->toMail($user);

        $this->assertStringContainsString('Branded CMS', $mail->subject);
        $this->assertSame('emails.auth.reset-password', $mail->markdown);
    }
}
