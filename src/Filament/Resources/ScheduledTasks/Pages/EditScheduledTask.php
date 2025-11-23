<?php

namespace AdvSorcer\FilamentScheduleUI\Filament\Resources\ScheduledTasks\Pages;

use AdvSorcer\FilamentScheduleUI\Filament\Resources\ScheduledTasks\ScheduledTaskResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditScheduledTask extends EditRecord
{
    protected static string $resource = ScheduledTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

