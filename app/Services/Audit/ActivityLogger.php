<?php

namespace App\Services\Audit;

use App\Models\ActivityLog;
use App\Models\User;
use App\Support\Audit\ActivityEvent;
use App\Support\Audit\SubjectType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * Records immutable business accountability events to activity_logs.
 */
class ActivityLogger
{
    /**
     * @param  array<string, mixed>  $properties
     */
    public function record(
        ?User $actor,
        string $event,
        ?Model $subject = null,
        array $properties = [],
    ): ActivityLog {
        $this->assertValidEvent($event);

        if ($subject !== null && ! $subject->exists) {
            throw new InvalidArgumentException('Audit subject must be persisted before logging.');
        }

        return ActivityLog::query()->create([
            'actor_user_id' => $actor?->id,
            'event' => $event,
            'subject_type' => $subject ? SubjectType::forModel($subject) : null,
            'subject_id' => $subject?->getKey(),
            'properties' => $this->sanitizeProperties($properties),
            'created_at' => now(),
        ]);
    }

    private function assertValidEvent(string $event): void
    {
        if ($event === '' || strlen($event) > 80) {
            throw new InvalidArgumentException('Audit event name is required and must be 80 characters or fewer.');
        }

        if (! ActivityEvent::isValid($event)) {
            throw new InvalidArgumentException("Unsupported audit event: {$event}");
        }
    }

    /**
     * @param  array<string, mixed>  $properties
     * @return array<string, mixed>
     */
    private function sanitizeProperties(array $properties): array
    {
        $sanitized = [];

        foreach ($properties as $key => $value) {
            if ($this->isSensitiveKey((string) $key)) {
                continue;
            }

            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizeProperties($value);

                continue;
            }

            $sanitized[$key] = $value;
        }

        return $sanitized;
    }

    private function isSensitiveKey(string $key): bool
    {
        $normalized = Str::lower($key);

        foreach ([
            'password',
            'password_confirmation',
            'token',
            'remember_token',
            'secret',
            'api_key',
            'smtp_password',
            'mail_password',
            'credentials',
            'session_id',
            'reset_token',
        ] as $sensitiveKey) {
            if ($normalized === $sensitiveKey || str_contains($normalized, $sensitiveKey)) {
                return true;
            }
        }

        return false;
    }
}
