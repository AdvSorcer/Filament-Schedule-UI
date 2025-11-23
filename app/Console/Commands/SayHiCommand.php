<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SayHiCommand extends Command
{
    protected $signature = 'say:hi';

    protected $description = 'Say hi in the log';

    public function handle(): int
    {
        $this->info('=== Task Start ===');
        \Log::info('Task Start: say:hi command started at '.now()->toDateTimeString());

        $this->info('Saying Hi...');
        \Log::info('Say Hi: Hi! This is a scheduled task running at '.now()->toDateTimeString());
        $this->info('Hi! Message logged successfully.');

        $this->info('=== Task End ===');
        \Log::info('Task End: say:hi command completed at '.now()->toDateTimeString());

        return Command::SUCCESS;
    }
}
