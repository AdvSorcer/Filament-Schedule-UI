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
        $deleted = 0;

        // 獲取程式碼中所有排程的名稱
        $codeScheduleNames = $parsedSchedules->pluck('name')->toArray();

        // 同步或創建排程
        foreach ($parsedSchedules as $scheduleData) {
            $task = ScheduledTask::firstOrNew(
                ['name' => $scheduleData['name']]
            );

            if ($task->exists) {
                // 更新現有排程（保留 is_active 狀態，讓用戶可以手動控制）
                $originalIsActive = $task->is_active;
                $task->fill($scheduleData);
                // 保留原本的 is_active 狀態
                $task->is_active = $originalIsActive;
                $task->save();
                $updated++;
            } else {
                // 創建新排程（使用程式碼中的預設值）
                $task->fill($scheduleData);
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

        // 刪除已經從程式碼中移除的排程
        $existingTasks = ScheduledTask::all();
        foreach ($existingTasks as $task) {
            if (! in_array($task->name, $codeScheduleNames)) {
                $task->delete();
                $deleted++;
            }
        }

        $message = "Synced {$synced} scheduled tasks ({$created} created, {$updated} updated";
        if ($deleted > 0) {
            $message .= ", {$deleted} deleted";
        }
        $message .= ').';

        $this->info($message);

        return Command::SUCCESS;
    }
}
