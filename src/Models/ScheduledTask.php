<?php

namespace AdvSorcer\FilamentScheduleUI\Models;

use AdvSorcer\FilamentScheduleUI\ScheduledTaskLogStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class ScheduledTask extends Model
{
    protected $fillable = [
        'name',
        'description',
        'command',
        'command_type',
        'expression',
        'timezone',
        'is_active',
        'without_overlapping',
        'on_one_server',
        'run_in_background',
        'next_run_at',
        'last_run_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'without_overlapping' => 'boolean',
            'on_one_server' => 'boolean',
            'run_in_background' => 'boolean',
            'next_run_at' => 'datetime',
            'last_run_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ScheduledTaskLog::class);
    }

    public function generateIdentifier(): string
    {
        $parts = [
            $this->command_type,
            $this->command,
            $this->expression,
        ];

        if ($this->metadata) {
            $parts[] = json_encode($this->metadata, JSON_SORT_KEYS);
        }

        return md5(implode('|', $parts));
    }

    public function calculateNextRunAt(): ?Carbon
    {
        if (! $this->expression) {
            return null;
        }

        try {
            $cron = \Cron\CronExpression::factory($this->expression);
            $timezone = $this->timezone ?: config('app.timezone');

            return Carbon::instance($cron->getNextRunDate('now', 0, false, $timezone));
        } catch (\Exception $e) {
            return null;
        }
    }
}


