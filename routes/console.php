<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\SendProjectReminder;
use App\Console\Commands\SendProjectReminderTest;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('send:project-reminder', function () {
    SendProjectReminder::dispatch();
    $this->info('Reminder emails sent!');
})->purpose('Send reminder email for projects due in 24 hours')->daily();

Artisan::command('send:project-reminder-test', function () {
    // Call the test command via Artisan
    $this->call(SendProjectReminderTest::class);
    $this->info('Reminder emails sent without queues!');
})->purpose('Send reminder email for projects due in 24 hours (no queue)')->daily();