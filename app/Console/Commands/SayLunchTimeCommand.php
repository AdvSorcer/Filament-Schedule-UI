<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SayLunchTimeCommand extends Command
{
    protected $signature = 'say:lunch-time';

    protected $description = 'Say lunch time in the log';

    public function handle(): int
    {
        $this->info('=== Task Start ===');
        \Log::info('Task Start: say:lunch-time command started at '.now()->toDateTimeString());

        $this->info('Saying Lunch Time...');
        \Log::info('Say Lunch Time: 該吃午餐了！這是排程任務，執行時間：'.now()->toDateTimeString());
        $this->info('該吃午餐了！訊息已記錄成功。');

        $this->info('=== Task End ===');
        \Log::info('Task End: say:lunch-time command completed at '.now()->toDateTimeString());

        return Command::SUCCESS;
    }
}
