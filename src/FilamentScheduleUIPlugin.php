<?php

namespace AdvSorcer\FilamentScheduleUI;

use AdvSorcer\FilamentScheduleUI\Filament\Resources\ScheduledTasks\ScheduledTaskResource;
use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentScheduleUIPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-schedule-ui';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            ScheduledTaskResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }
}
