<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SayGoodAfternoonCommand extends Command
{
    protected $signature = 'say:good-afternoon';

    protected $description = 'Say good afternoon in the log';

    public function handle(): int
    {
        $this->info('=== Task Start ===');
        \Log::info('Task Start: say:good-afternoon command started at '.now()->toDateTimeString());

        $this->info('Saying Good Afternoon...');
        \Log::info('Say Good Afternoon: 午安！這是排程任務，執行時間：'.now()->toDateTimeString());
        $this->info('午安！訊息已記錄成功。');

        $this->info('=== Task End ===');
        \Log::info('Task End: say:good-afternoon command completed at '.now()->toDateTimeString());

        return Command::SUCCESS;
    }
}
