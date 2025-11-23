<?php

namespace App\Console\Commands;

use App\Models\ScheduledTask;
use App\Services\ScheduleParser;
use Illuminate\Console\Command;

class SyncSchedulesCommand extends Command
{
    protected $signature = 'schedule:sync';

    protected $description = 'Sync scheduled tasks from code to database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(ScheduleParser $parser): int
    {
        $this->info('Syncing scheduled tasks...');

        $parsedSchedules = $parser->parse();
        $synced = 0;
        $created = 0;
        $updated = 0;

        foreach ($parsedSchedules as $scheduleData) {
            $task = ScheduledTask::firstOrNew(
                ['name' => $scheduleData['name']],
                $scheduleData
            );

            if ($task->exists) {
                // 更新現有排程
                $task->fill($scheduleData);
                $task->save();
                $updated++;
            } else {
                // 創建新排程
                $task->save();
                $created++;
            }

            // 計算並更新下次執行時間
            $nextRunAt = $task->calculateNextRunAt();
            if ($nextRunAt) {
                $task->update(['next_run_at' => $nextRunAt]);
            }

            $synced++;
        }

        $this->info("Synced {$synced} scheduled tasks ({$created} created, {$updated} updated).");

        return Command::SUCCESS;
    }
}
