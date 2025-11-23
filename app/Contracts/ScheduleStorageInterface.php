<?php

namespace App\Contracts;

use App\Models\ScheduledTask;
use Illuminate\Support\Collection;

interface ScheduleStorageInterface
{
    public function store(ScheduledTask $task): void;

    public function find(string $identifier): ?ScheduledTask;

    public function all(): Collection;

    public function delete(string $identifier): void;
}
