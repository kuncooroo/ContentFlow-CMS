<?php

namespace App\Listeners;

use Illuminate\Mail\Events\MessageSendingFailed;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LogMailDeliveryFailure
{
    public function handleMessageSendingFailed(MessageSendingFailed $event): void
    {
        Log::warning('Mail delivery failed', [
            'subject' => $event->message->getSubject(),
            'recipients' => array_keys($event->message->getTo()),
            'message' => $this->sanitize($event->exception?->getMessage()),
        ]);
    }

    public function handleNotificationFailed(NotificationFailed $event): void
    {
        $exceptionMessage = $event->data['exception'] ?? null;

        if ($exceptionMessage instanceof \Throwable) {
            $exceptionMessage = $exceptionMessage->getMessage();
        }

        Log::warning('Notification delivery failed', [
            'notification' => $event->notification::class,
            'channel' => $event->channel,
            'message' => $this->sanitize(is_string($exceptionMessage) ? $exceptionMessage : null),
        ]);
    }

    private function sanitize(?string $message): ?string
    {
        if ($message === null || $message === '') {
            return null;
        }

        $sanitized = $message;

        foreach ([
            'password',
            'token',
            'secret',
            'credentials',
            'smtp',
            'api_key',
            'authorization',
        ] as $needle) {
            $sanitized = (string) preg_replace(
                '/'.preg_quote($needle, '/').'[^\s]*/i',
                '[redacted]',
                $sanitized,
            );
        }

        return Str::limit(trim($sanitized), 500, '');
    }
}
