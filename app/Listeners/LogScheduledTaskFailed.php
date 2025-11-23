<?php

namespace App\Listeners;

use App\Models\ScheduledTask;
use App\ScheduledTaskLogStatus;
use Illuminate\Console\Events\ScheduledTaskFailed;

class LogScheduledTaskFailed
{
    public function handle(ScheduledTaskFailed $event): void
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

            $log->update([
                'status' => ScheduledTaskLogStatus::Failed,
                'finished_at' => $finishedAt,
                'duration' => $duration,
                'output' => $event->output?->__toString(),
                'error_message' => $event->exception?->getMessage(),
                'exit_code' => $event->exception?->getCode() ?? 1,
            ]);
        }

        $task->update(['last_run_at' => now()]);
    }

    protected function findTask(ScheduledTaskFailed $event): ?ScheduledTask
    {
        $command = $this->extractCommand($event);

        return ScheduledTask::where('command', $command)->first();
    }

    protected function extractCommand(ScheduledTaskFailed $event): string
    {
        $task = $event->task;

        if ($task->command) {
            return $task->command;
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
}
