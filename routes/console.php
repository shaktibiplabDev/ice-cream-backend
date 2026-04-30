<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule email fetching every 5 minutes
Schedule::command('email:fetch')->everyFiveMinutes()->withoutOverlapping();

// Test IMAP connection
Artisan::command('email:test', function (\App\Services\EmailFetcher $fetcher) {
    $this->info('Testing IMAP connection...');

    if ($fetcher->testConnection()) {
        $this->info('Connection test passed!');
    } else {
        $this->error('Connection test failed! Check your IMAP settings.');
    }
})->purpose('Test IMAP email connection');
