<?php

namespace App\Listeners;

use App\Models\ScheduledTask;
use App\ScheduledTaskLogStatus;
use Illuminate\Console\Events\ScheduledTaskSkipped;

class LogScheduledTaskSkipped
{
    public function handle(ScheduledTaskSkipped $event): void
    {
        $task = $this->findTask($event);

        if (! $task) {
            return;
        }

        $task->logs()->create([
            'status' => ScheduledTaskLogStatus::Skipped,
            'started_at' => now(),
            'finished_at' => now(),
            'duration' => 0,
        ]);
    }

    protected function findTask(ScheduledTaskSkipped $event): ?ScheduledTask
    {
        $command = $this->extractCommand($event);

        return ScheduledTask::where('command', $command)->first();
    }

    protected function extractCommand(ScheduledTaskSkipped $event): string
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
