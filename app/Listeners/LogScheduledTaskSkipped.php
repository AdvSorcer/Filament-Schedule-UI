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
