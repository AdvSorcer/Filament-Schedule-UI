<?php

namespace App\Filament\Resources\ScheduledTasks\Actions;

use App\Models\ScheduledTask;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ToggleActiveAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'toggleActive';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(fn (ScheduledTask $record) => $record->is_active ? __('schedule.disable') : __('schedule.enable'))
            ->icon(fn (ScheduledTask $record) => $record->is_active ? 'heroicon-o-pause' : 'heroicon-o-play')
            ->color(fn (ScheduledTask $record) => $record->is_active ? 'warning' : 'success')
            ->action(function (ScheduledTask $record) {
                $record->update(['is_active' => ! $record->is_active]);

                $taskName = $record->description ?: $record->command;
                $statusKey = $record->is_active ? 'task_enabled' : 'task_disabled';

                Notification::make()
                    ->title($record->is_active ? __('schedule.enabled_status') : __('schedule.disabled_status'))
                    ->body(__("schedule.{$statusKey}", ['name' => $taskName]))
                    ->success()
                    ->send();
            });
    }
}
