<?php

namespace Tests\Feature\Smoke;

use Illuminate\Support\Facades\Schedule;
use Tests\TestCase;

class SchedulerBaselineTest extends TestCase
{
    public function test_scheduler_has_baseline_entry(): void
    {
        $events = collect(Schedule::events());

        $this->assertTrue(
            $events->contains(fn ($event) => str_contains($event->description ?? '', 'Publish due scheduled posts')),
            'Expected scheduled publishing command to be registered.'
        );
    }
}
