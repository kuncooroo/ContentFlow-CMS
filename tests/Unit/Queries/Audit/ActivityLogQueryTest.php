<?php

namespace Tests\Unit\Queries\Audit;

use App\Models\User;
use App\Queries\Audit\ActivityLogQuery;
use App\Services\Audit\ActivityLogger;
use App\Support\Audit\ActivityEvent;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ActivityLogQueryTest extends TestCase
{
    use DatabaseTransactions;

    public function test_paginate_orders_newest_first(): void
    {
        $admin = User::factory()->administrator()->create();
        $subject = User::factory()->author()->create();
        $logger = app(ActivityLogger::class);

        $older = $logger->record($admin, ActivityEvent::UserCreated, $subject, ['order' => 'older']);
        DB::table('activity_logs')->where('id', $older->id)->update(['created_at' => now()->subDay()]);

        $newer = $logger->record($admin, ActivityEvent::UserDeactivated, $subject, ['order' => 'newer']);

        $results = app(ActivityLogQuery::class)->paginate();

        $this->assertSame($newer->id, $results->first()?->id);
    }

    public function test_invalid_event_filter_is_rejected(): void
    {
        $this->expectException(ValidationException::class);

        app(ActivityLogQuery::class)->paginate(event: 'NOT_A_REAL_EVENT');
    }
}
