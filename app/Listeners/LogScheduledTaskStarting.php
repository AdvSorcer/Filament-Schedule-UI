<?php

namespace App\Listeners;

use App\Models\ScheduledTask;
use App\ScheduledTaskLogStatus;
use Illuminate\Console\Events\ScheduledTaskStarting;

class LogScheduledTaskStarting
{
    public function handle(ScheduledTaskStarting $event): void
    {
        $task = $this->findOrCreateTask($event);

        if (! $task) {
            return;
        }

        // 防止重複創建：檢查最近 1 秒內是否已經有 Running 狀態的 log
        $recentLog = $task->logs()
            ->where('status', ScheduledTaskLogStatus::Running)
            ->where('started_at', '>=', now()->subSecond())
            ->first();

        if ($recentLog) {
            // 如果最近 1 秒內已經有 Running 記錄，不創建新的
            return;
        }

        $task->logs()->create([
            'status' => ScheduledTaskLogStatus::Running,
            'started_at' => now(),
        ]);
    }

    protected function findOrCreateTask(ScheduledTaskStarting $event): ?ScheduledTask
    {
        $command = $this->extractCommand($event);
        $task = ScheduledTask::where('command', $command)->first();

        // 如果找不到記錄，自動創建一個
        if (! $task) {
            $task = $this->createTaskFromEvent($event, $command);
        }

        return $task;
    }

    protected function createTaskFromEvent(ScheduledTaskStarting $event, string $command): ?ScheduledTask
    {
        try {
            $scheduleTask = $event->task;
            $commandType = $this->extractCommandType($scheduleTask);
            $expression = $scheduleTask->expression ?? '* * * * *';
            $timezone = $this->extractTimezone($scheduleTask);
            $description = $this->extractDescription($scheduleTask);

            // 生成唯一名稱（類似 ScheduleParser 的做法）
            $name = $this->generateName($command, $commandType, $expression, []);

            $task = ScheduledTask::create([
                'name' => $name,
                'description' => $description,
                'command' => $command,
                'command_type' => $commandType,
                'expression' => $expression,
                'timezone' => $timezone,
                'is_active' => true,
                'without_overlapping' => $this->hasWithoutOverlapping($scheduleTask),
                'on_one_server' => $this->hasOnOneServer($scheduleTask),
                'run_in_background' => $this->hasRunInBackground($scheduleTask),
                'metadata' => [],
            ]);

            // 計算並更新下次執行時間
            $nextRunAt = $task->calculateNextRunAt();
            if ($nextRunAt) {
                $task->update(['next_run_at' => $nextRunAt]);
            }

            return $task;
        } catch (\Exception $e) {
            // 如果創建失敗，記錄錯誤但不中斷流程
            \Log::warning('Failed to auto-create ScheduledTask', [
                'command' => $command,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    protected function extractCommand(ScheduledTaskStarting $event): string
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

    protected function extractCommandType($scheduleTask): string
    {
        if ($scheduleTask->command) {
            return 'command';
        }

        if ($scheduleTask->callback) {
            return 'call';
        }

        return 'exec';
    }

    protected function extractTimezone($scheduleTask): ?string
    {
        try {
            return $scheduleTask->timezone ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function extractDescription($scheduleTask): ?string
    {
        try {
            return $scheduleTask->description ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function hasWithoutOverlapping($scheduleTask): bool
    {
        try {
            return property_exists($scheduleTask, 'withoutOverlapping') && $scheduleTask->withoutOverlapping;
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function hasOnOneServer($scheduleTask): bool
    {
        try {
            return property_exists($scheduleTask, 'onOneServer') && $scheduleTask->onOneServer;
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function hasRunInBackground($scheduleTask): bool
    {
        try {
            return property_exists($scheduleTask, 'runInBackground') && $scheduleTask->runInBackground;
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function generateName(string $command, string $commandType, string $expression, array $metadata): string
    {
        $parts = [$commandType, $command, $expression];
        if (! empty($metadata)) {
            $parts[] = json_encode($metadata, JSON_SORT_KEYS);
        }

        return md5(implode('|', $parts));
    }
}
