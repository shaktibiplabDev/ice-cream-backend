<?php

namespace App\Console\Commands;

use App\Services\EmailFetcher;
use Illuminate\Console\Command;

class FetchEmails extends Command
{
    protected $signature = 'email:fetch';
    protected $description = 'Fetch emails from IMAP server';

    public function handle(EmailFetcher $fetcher): int
    {
        $this->info('Starting email fetch...');

        $result = $fetcher->fetch();

        if ($result['status'] === 'success') {
            $this->info($result['message']);
            return self::SUCCESS;
        } elseif ($result['status'] === 'disabled') {
            $this->warn($result['message']);
            return self::SUCCESS;
        } else {
            $this->error($result['message']);
            return self::FAILURE;
        }
    }
}
