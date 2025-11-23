<?php

namespace AdvSorcer\FilamentScheduleUI\Filament\Resources\ScheduledTasks\Pages;

use AdvSorcer\FilamentScheduleUI\Filament\Resources\ScheduledTasks\Actions\SyncSchedulesAction;
use AdvSorcer\FilamentScheduleUI\Filament\Resources\ScheduledTasks\ScheduledTaskResource;
use Filament\Resources\Pages\ListRecords;

class ListScheduledTasks extends ListRecords
{
    protected static string $resource = ScheduledTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            SyncSchedulesAction::make(),
        ];
    }
}

