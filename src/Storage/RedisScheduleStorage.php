<?php

namespace AdvSorcer\FilamentScheduleUI\Storage;

use AdvSorcer\FilamentScheduleUI\Contracts\ScheduleStorageInterface;
use AdvSorcer\FilamentScheduleUI\Models\ScheduledTask;
use Illuminate\Support\Collection;

/**
 * Redis Schedule Storage (預留實作)
 * 未來可以實作使用 Redis 存儲排程任務
 */
class RedisScheduleStorage implements ScheduleStorageInterface
{
    public function store(ScheduledTask $task): void
    {
        // TODO: 實作 Redis 存儲邏輯
        throw new \RuntimeException('Redis storage not yet implemented');
    }

    public function find(string $identifier): ?ScheduledTask
    {
        // TODO: 實作從 Redis 查找邏輯
        throw new \RuntimeException('Redis storage not yet implemented');
    }

    public function all(): Collection
    {
        // TODO: 實作從 Redis 獲取所有排程
        throw new \RuntimeException('Redis storage not yet implemented');
    }

    public function delete(string $identifier): void
    {
        // TODO: 實作從 Redis 刪除邏輯
        throw new \RuntimeException('Redis storage not yet implemented');
    }
}


