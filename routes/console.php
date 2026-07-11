<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// The Core Executor - Runs every minute to fire bot commands for pending operations
Schedule::job(new \App\Jobs\ExecutorJob)->everyMinute()->withoutOverlapping();

// Health Check - Runs every 12 hours to verify bot cookies
Schedule::job(new \App\Jobs\CheckBotsHealthJob)->twiceDaily(0, 12)->withoutOverlapping();
