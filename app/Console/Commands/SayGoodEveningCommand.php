<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SayGoodEveningCommand extends Command
{
    protected $signature = 'say:good-evening';

    protected $description = 'Say good evening in the log';

    public function handle(): int
    {
        $this->info('=== Task Start ===');
        \Log::info('Task Start: say:good-evening command started at '.now()->toDateTimeString());

        $this->info('Saying Good Evening...');
        \Log::info('Say Good Evening: 晚安！這是排程任務，執行時間：'.now()->toDateTimeString());
        $this->info('晚安！訊息已記錄成功。');

        $this->info('=== Task End ===');
        \Log::info('Task End: say:good-evening command completed at '.now()->toDateTimeString());

        return Command::SUCCESS;
    }
}
