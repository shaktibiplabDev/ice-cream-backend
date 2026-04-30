<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BackupRun extends Command
{
    protected $signature = 'backup:run';
    protected $description = 'Run database and files backup';

    public function handle(): int
    {
        $this->info('Starting backup...');

        $backupPath = 'backups/' . now()->format('Y-m-d_H-i-s');
        Storage::makeDirectory($backupPath);

        // Database backup
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');
        $dbHost = config('database.connections.mysql.host');

        $dumpFile = storage_path('app/' . $backupPath . '/database.sql');
        $command = sprintf(
            'mysqldump -h %s -u %s %s %s > %s',
            escapeshellarg($dbHost),
            escapeshellarg($dbUser),
            $dbPass ? '-p' . escapeshellarg($dbPass) : '',
            escapeshellarg($dbName),
            escapeshellarg($dumpFile)
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            $this->error('Database backup failed!');
            return 1;
        }

        // Store last backup date in cache
        cache()->forever('last_backup_date', now());

        $this->info('Backup completed successfully: ' . $backupPath);

        // Clean old backups (keep last 5)
        $this->cleanupOldBackups();

        return 0;
    }

    protected function cleanupOldBackups(): void
    {
        $backups = Storage::directories('backups');
        
        if (count($backups) > 5) {
            // Sort by name (which is date-based)
            sort($backups);
            
            // Delete oldest backups
            $toDelete = array_slice($backups, 0, count($backups) - 5);
            foreach ($toDelete as $backup) {
                Storage::deleteDirectory($backup);
                $this->info('Deleted old backup: ' . $backup);
            }
        }
    }
}
