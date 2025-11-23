<?php

use App\Models\ScheduledTask;
use App\ScheduledTaskLogStatus;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Support\Facades\Schedule;

if (! function_exists('schedule_command')) {
    /**
     * 註冊一個命令排程，自動檢查 is_active 狀態
     * 使用方式：schedule_command('say:hi')->dailyAt('08:00')
     */
    function schedule_command(string $command, array $parameters = []): Event
    {
        $event = Schedule::command($command, $parameters);

        // 為每個排程命令生成唯一的輸出檔案路徑
        $outputDir = storage_path('app/temp/schedule-outputs');

        // 確保目錄存在
        if (! is_dir($outputDir)) {
            @mkdir($outputDir, 0755, true);
        }

        // 生成唯一的檔案名稱（使用命令名稱和時間戳的 hash）
        $fileHash = md5($command.serialize($parameters));
        $outputFile = $outputDir.'/schedule-'.$fileHash.'.log';

        // 將命令輸出重定向到檔案
        $event->sendOutputTo($outputFile);

        // 在命令執行完成後讀取輸出並更新資料庫
        $event->after(function () use ($outputFile, $command) {
            $task = ScheduledTask::where('command', $command)->first();

            if (! $task) {
                // 如果找不到任務，清理檔案後返回
                @unlink($outputFile);

                return;
            }

            // 找到最近的 Running 狀態的 log
            $log = $task->logs()
                ->where('status', ScheduledTaskLogStatus::Running)
                ->latest('started_at')
                ->first();

            if ($log && file_exists($outputFile)) {
                // 讀取輸出檔案內容
                $output = file_get_contents($outputFile);

                // 更新 log 的 output（只有在還沒有被設置時才更新）
                // 使用 is_null() 而不是 empty()，因為空字串也是一種有效的輸出
                if (is_null($log->output)) {
                    $log->update(['output' => $output ?: null]);
                }

                // 清理臨時檔案
                @unlink($outputFile);
            } elseif (file_exists($outputFile)) {
                // 如果找不到對應的 log，也清理檔案
                @unlink($outputFile);
            }
        });

        return $event->when(function () use ($command) {
            $task = ScheduledTask::where('command', $command)->first();

            return $task?->is_active ?? true; // 如果找不到任務，預設為啟用
        });
    }
}

if (! function_exists('schedule_call')) {
    /**
     * 註冊一個回調排程，自動檢查 is_active 狀態
     */
    function schedule_call(callable $callback, array $parameters = []): Event
    {
        $event = Schedule::call($callback, $parameters);

        // 從 callback 中提取識別符
        $identifier = extract_callback_identifier($callback);

        return $event->when(function () use ($identifier) {
            $task = ScheduledTask::where('command', $identifier)->first();

            return $task?->is_active ?? true;
        });
    }
}

if (! function_exists('schedule_exec')) {
    /**
     * 註冊一個執行排程，自動檢查 is_active 狀態
     */
    function schedule_exec(string $command, array $parameters = []): Event
    {
        $event = Schedule::exec($command, $parameters);

        return $event->when(function () use ($command) {
            $task = ScheduledTask::where('command', $command)->first();

            return $task?->is_active ?? true;
        });
    }
}

if (! function_exists('extract_callback_identifier')) {
    /**
     * 從 callback 中提取識別符
     */
    function extract_callback_identifier(callable $callback): string
    {
        if (is_string($callback)) {
            return $callback;
        }

        if (is_array($callback)) {
            $class = is_object($callback[0]) ? get_class($callback[0]) : $callback[0];
            $method = $callback[1] ?? '__invoke';

            return $class.'@'.$method;
        }

        if (is_object($callback) && method_exists($callback, '__invoke')) {
            return get_class($callback).'@__invoke';
        }

        return 'unknown';
    }
}
