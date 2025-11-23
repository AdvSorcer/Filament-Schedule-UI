<?php

namespace AdvSorcer\FilamentScheduleUI\Models;

use AdvSorcer\FilamentScheduleUI\ScheduledTaskLogStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduledTaskLog extends Model
{
    protected $fillable = [
        'scheduled_task_id',
        'status',
        'started_at',
        'finished_at',
        'duration',
        'output',
        'error_message',
        'exit_code',
    ];

    protected function casts(): array
    {
        return [
            'status' => ScheduledTaskLogStatus::class,
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
            'duration' => 'integer',
            'exit_code' => 'integer',
        ];
    }

    public function scheduledTask(): BelongsTo
    {
        return $this->belongsTo(ScheduledTask::class);
    }
}


