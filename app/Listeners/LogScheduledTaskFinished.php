<?php

namespace App\Listeners;

use App\Models\ScheduledTask;
use App\ScheduledTaskLogStatus;
use Illuminate\Console\Events\ScheduledTaskFinished;

class LogScheduledTaskFinished
{
    public function handle(ScheduledTaskFinished $event): void
    {
        $task = $this->findTask($event);

        if (! $task) {
            return;
        }

        $log = $task->logs()
            ->where('status', ScheduledTaskLogStatus::Running)
            ->latest('started_at')
            ->first();

        if ($log) {
            $finishedAt = now();
            $duration = $log->started_at->diffInMilliseconds($finishedAt);

            // ScheduledTaskFinished event 沒有 output 屬性
            // output 已經在 schedule_command() 的 after() hook 中捕獲並更新
            // 這裡不更新 output，讓 after() hook 來處理
            $log->update([
                'status' => ScheduledTaskLogStatus::Success,
                'finished_at' => $finishedAt,
                'duration' => $duration,
                'exit_code' => 0,
            ]);
        }

        // 更新最後執行時間並重新計算下次執行時間
        $nextRunAt = $task->calculateNextRunAt();
        $updateData = ['last_run_at' => now()];
        if ($nextRunAt) {
            $updateData['next_run_at'] = $nextRunAt;
        }
        $task->update($updateData);
    }

    protected function findTask(ScheduledTaskFinished $event): ?ScheduledTask
    {
        $command = $this->extractCommand($event);

        return ScheduledTask::where('command', $command)->first();
    }

    protected function extractCommand(ScheduledTaskFinished $event): string
    {
        $task = $event->task;

        if ($task->command) {
            // 從完整的命令字串中提取純粹的 artisan 命令名稱
            return $this->extractArtisanCommandFromFullCommand($task->command);
        }

        if ($task->callback) {
            if (is_string($task->callback)) {
                return $task->callback;
            }

            if (is_array($task->callback)) {
                $class = is_object($task->callback[0]) ? get_class($task->callback[0]) : $task->callback[0];
                $method = $task->callback[1] ?? '__invoke';

                return $class.'@'.$method;
            }

            if (is_object($task->callback) && method_exists($task->callback, '__invoke')) {
                return get_class($task->callback).'@__invoke';
            }
        }

        return 'unknown';
    }

    protected function extractArtisanCommandFromFullCommand(string $command): string
    {
        // 移除引號並分割命令
        $command = trim($command, "'\"");

        // 如果命令包含 'artisan'，提取 artisan 後面的部分
        if (preg_match("/['\"]artisan['\"]\s+(['\"]?)([^\s'\"]+)\\1/", $command, $matches)) {
            return $matches[2];
        }

        // 如果命令以 "artisan " 開頭，提取後面的部分
        if (preg_match("/artisan\s+(['\"]?)([^\s'\"]+)\\1/", $command, $matches)) {
            return $matches[2];
        }

        // 如果已經是純命令名稱（不包含路徑），直接返回
        if (! str_contains($command, '/') && ! str_contains($command, 'artisan')) {
            return $command;
        }

        // 嘗試從命令中提取最後一個參數（通常是命令名稱）
        $parts = preg_split("/\s+/", $command);
        foreach (array_reverse($parts) as $part) {
            $part = trim($part, "'\"");
            if (! empty($part) && ! str_contains($part, '/') && ! str_contains($part, 'php')) {
                return $part;
            }
        }

        // 如果都無法解析，返回原命令
        return $command;
    }
}
