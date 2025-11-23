<?php

namespace AdvSorcer\FilamentScheduleUI\Contracts;

use AdvSorcer\FilamentScheduleUI\Models\ScheduledTask;
use Illuminate\Support\Collection;

interface ScheduleStorageInterface
{
    public function store(ScheduledTask $task): void;

    public function find(string $identifier): ?ScheduledTask;

    public function all(): Collection;

    public function delete(string $identifier): void;
}

