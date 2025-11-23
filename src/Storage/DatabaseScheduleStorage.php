<?php

namespace AdvSorcer\FilamentScheduleUI\Storage;

use AdvSorcer\FilamentScheduleUI\Contracts\ScheduleStorageInterface;
use AdvSorcer\FilamentScheduleUI\Models\ScheduledTask;
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

