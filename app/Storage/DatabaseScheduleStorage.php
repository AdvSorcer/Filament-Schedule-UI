<?php

namespace App\Storage;

use App\Contracts\ScheduleStorageInterface;
use App\Models\ScheduledTask;
use Illuminate\Support\Collection;

class DatabaseScheduleStorage implements ScheduleStorageInterface
{
    public function store(ScheduledTask $task): void
    {
        $task->save();
    }

    public function find(string $identifier): ?ScheduledTask
    {
        return ScheduledTask::where('name', $identifier)->first();
    }

    public function all(): Collection
    {
        return ScheduledTask::all();
    }

    public function delete(string $identifier): void
    {
        ScheduledTask::where('name', $identifier)->delete();
    }
}
