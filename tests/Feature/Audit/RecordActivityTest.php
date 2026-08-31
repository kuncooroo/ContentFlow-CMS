<?php

namespace Tests\Feature\Audit;

use App\Actions\Audit\RecordActivity;
use App\Models\User;
use App\Support\Audit\ActivityEvent;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RecordActivityTest extends TestCase
{
    use DatabaseTransactions;

    public function test_record_activity_action_delegates_to_logger(): void
    {
        $actor = User::factory()->administrator()->create();
        $subject = User::factory()->author()->create();

        $log = app(RecordActivity::class)->handle(
            $actor,
            ActivityEvent::RoleAssigned,
            $subject,
            ['role_names' => ['Author']],
        );

        $this->assertDatabaseHas('activity_logs', [
            'id' => $log->id,
            'event' => ActivityEvent::RoleAssigned,
            'subject_type' => 'user',
            'subject_id' => $subject->id,
        ]);
    }
}
