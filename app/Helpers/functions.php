<?php

use App\Models\ScheduledTask;
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
