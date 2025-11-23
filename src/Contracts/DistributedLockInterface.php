<?php

namespace AdvSorcer\FilamentScheduleUI\Contracts;

interface DistributedLockInterface
{
    public function acquire(string $key, int $ttl = 3600): bool;

    public function release(string $key): bool;

    public function isLocked(string $key): bool;
}

