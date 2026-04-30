<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule email fetching every 1 minute
Schedule::command('email:fetch')->everyMinute()->withoutOverlapping();

// Schedule database backup every 30 days (configurable via BACKUP_DAYS env)
Schedule::command('backup:run')->daily()->at('02:00')->when(function () {
    $lastBackup = cache()->get('last_backup_date');
    $backupDays = (int) env('BACKUP_DAYS', 30);
    
    if (!$lastBackup) {
        return true;
    }
    
    return now()->diffInDays($lastBackup) >= $backupDays;
})->withoutOverlapping();

// Test IMAP connection
Artisan::command('email:test', function (\App\Services\EmailFetcher $fetcher) {
    $this->info('Testing IMAP connection...');

    if ($fetcher->testConnection()) {
        $this->info('Connection test passed!');
    } else {
        $this->error('Connection test failed! Check your IMAP settings.');
    }
})->purpose('Test IMAP email connection');
