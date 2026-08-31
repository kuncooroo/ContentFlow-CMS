<?php

namespace App\Notifications\Auth;

use App\Services\Settings\SiteSettings;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword implements ShouldQueue
{
    use Queueable;

    /**
     * @param  mixed  $notifiable
     */
    public function toMail($notifiable): MailMessage
    {
        $siteName = app(SiteSettings::class)->siteName();

        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $expireMinutes = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');

        return (new MailMessage)
            ->from(config('mail.from.address'), $siteName)
            ->subject(__('Reset your :site password', ['site' => $siteName]))
            ->markdown('emails.auth.reset-password', [
                'url' => $url,
                'siteName' => $siteName,
                'count' => $expireMinutes,
            ]);
    }
}
