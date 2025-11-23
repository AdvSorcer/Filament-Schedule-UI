<?php

namespace App\Locks;

use App\Contracts\DistributedLockInterface;
use Illuminate\Support\Facades\DB;

class DatabaseLock implements DistributedLockInterface
{
    protected string $table = 'schedule_locks';

    public function acquire(string $key, int $ttl = 3600): bool
    {
        try {
            DB::table($this->table)->insert([
                'key' => $key,
                'expires_at' => now()->addSeconds($ttl),
                'created_at' => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            // 鎖已存在或表不存在
            return false;
        }
    }

    public function release(string $key): bool
    {
        return DB::table($this->table)->where('key', $key)->delete() > 0;
    }

    public function isLocked(string $key): bool
    {
        $lock = DB::table($this->table)
            ->where('key', $key)
            ->where('expires_at', '>', now())
            ->first();

        return $lock !== null;
    }
}
