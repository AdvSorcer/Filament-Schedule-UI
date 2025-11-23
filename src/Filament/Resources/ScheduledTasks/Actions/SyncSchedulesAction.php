<?php

namespace AdvSorcer\FilamentScheduleUI\Filament\Resources\ScheduledTasks\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;

class SyncSchedulesAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'syncSchedules';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('schedule.sync_schedules'))
            ->icon('heroicon-o-arrow-path')
            ->color('info')
            ->requiresConfirmation()
            ->modalHeading(__('schedule.confirm_sync'))
            ->modalDescription(__('schedule.confirm_sync_description'))
            ->action(function () {
                Artisan::call('schedule:sync');

                Notification::make()
                    ->title(__('schedule.sync_complete'))
                    ->body(__('schedule.sync_success'))
                    ->success()
                    ->send();
            });
    }
}

