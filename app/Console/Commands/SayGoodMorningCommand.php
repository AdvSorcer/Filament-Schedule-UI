<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SayGoodMorningCommand extends Command
{
    protected $signature = 'say:good-morning';

    protected $description = 'Say good morning in the log';

    public function handle(): int
    {
        $this->info('=== Task Start ===');
        \Log::info('Task Start: say:good-morning command started at '.now()->toDateTimeString());

        $this->info('Saying Good Morning...');
        \Log::info('Say Good Morning: 早安！這是排程任務，執行時間：'.now()->toDateTimeString());
        $this->info('早安！訊息已記錄成功。');

        $this->info('=== Task End ===');
        \Log::info('Task End: say:good-morning command completed at '.now()->toDateTimeString());

        return Command::SUCCESS;
    }
}
