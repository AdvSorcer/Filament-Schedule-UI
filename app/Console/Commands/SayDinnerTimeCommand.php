<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SayDinnerTimeCommand extends Command
{
    protected $signature = 'say:dinner-time';

    protected $description = 'Say dinner time in the log';

    public function handle(): int
    {
        $this->info('=== Task Start ===');
        \Log::info('Task Start: say:dinner-time command started at '.now()->toDateTimeString());

        $this->info('Saying Dinner Time...');
        \Log::info('Say Dinner Time: 該吃晚餐了！這是排程任務，執行時間：'.now()->toDateTimeString());
        $this->info('該吃晚餐了！訊息已記錄成功。');

        $this->info('=== Task End ===');
        \Log::info('Task End: say:dinner-time command completed at '.now()->toDateTimeString());

        return Command::SUCCESS;
    }
}
