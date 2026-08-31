<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Scheduler (MVP baseline)
|--------------------------------------------------------------------------
|
| VPS cron should invoke `php artisan schedule:run` every minute.
|
*/

Schedule::command('content:publish-scheduled-posts')
    ->everyMinute()
    ->withoutOverlapping()
    ->description('Publish due scheduled posts');
