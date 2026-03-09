<?php

use App\Jobs\CheckAutoReplanJob;
use App\Jobs\ProcessRemindersJob;
use App\Jobs\UpdateUserStatsJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('sanctum:prune-expired --hours=24')->daily();
Schedule::job(new UpdateUserStatsJob, 'default')->hourly();
Schedule::job(new CheckAutoReplanJob, 'default')->twiceDaily();
Schedule::job(new ProcessRemindersJob, 'notifications')->everyFiveMinutes();
Schedule::command('model:prune')->daily();
