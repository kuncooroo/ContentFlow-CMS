<?php

namespace Tests\Unit\Services\Audit;

use App\Models\ActivityLog;
use App\Models\User;
use App\Services\Audit\ActivityLogger;
use App\Support\Audit\ActivityEvent;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use InvalidArgumentException;
use Tests\TestCase;

class ActivityLoggerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_record_creates_immutable_activity_log_row(): void
    {
        $actor = User::factory()->administrator()->create();
        $subject = User::factory()->author()->create();

        $log = app(ActivityLogger::class)->record(
            $actor,
            ActivityEvent::UserCreated,
            $subject,
            ['email' => $subject->email, 'status' => 'active'],
        );

        $this->assertDatabaseHas('activity_logs', [
            'id' => $log->id,
            'actor_user_id' => $actor->id,
            'event' => ActivityEvent::UserCreated,
            'subject_type' => 'user',
            'subject_id' => $subject->id,
        ]);

        $this->assertSame(['email' => $subject->email, 'status' => 'active'], $log->fresh()->properties);
        $this->assertNotNull($log->created_at);
    }

    public function test_record_accepts_null_actor_for_system_events(): void
    {
        $subject = User::factory()->author()->create();

        $log = app(ActivityLogger::class)->record(
            null,
            ActivityEvent::UserCreated,
            $subject,
        );

        $this->assertNull($log->actor_user_id);
    }

    public function test_sensitive_properties_are_stripped(): void
    {
        $subject = User::factory()->author()->create();

        $log = app(ActivityLogger::class)->record(
            null,
            ActivityEvent::UserCreated,
            $subject,
            [
                'email' => $subject->email,
                'password' => 'secret-value',
                'nested' => ['reset_token' => 'abc', 'status' => 'active'],
            ],
        );

        $this->assertSame([
            'email' => $subject->email,
            'nested' => ['status' => 'active'],
        ], $log->properties);
    }

    public function test_invalid_event_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        app(ActivityLogger::class)->record(
            null,
            'NOT_A_REAL_EVENT',
            null,
        );
    }

    public function test_activity_logs_cannot_be_updated_or_deleted(): void
    {
        $log = app(ActivityLogger::class)->record(
            null,
            ActivityEvent::UserCreated,
            User::factory()->author()->create(),
        );

        $this->assertFalse($log->update(['event' => ActivityEvent::UserDeactivated]));
        $this->assertFalse($log->delete());
        $this->assertDatabaseHas('activity_logs', ['id' => $log->id, 'event' => ActivityEvent::UserCreated]);
    }
}
