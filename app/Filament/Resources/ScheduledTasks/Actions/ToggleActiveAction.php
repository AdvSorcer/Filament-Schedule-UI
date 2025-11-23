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

        $this->label(fn (ScheduledTask $record) => $record->is_active ? '停用' : '啟用')
            ->icon(fn (ScheduledTask $record) => $record->is_active ? 'heroicon-o-pause' : 'heroicon-o-play')
            ->color(fn (ScheduledTask $record) => $record->is_active ? 'warning' : 'success')
            ->action(function (ScheduledTask $record) {
                $record->update(['is_active' => ! $record->is_active]);

                Notification::make()
                    ->title($record->is_active ? '已啟用' : '已停用')
                    ->body("排程「{$record->name}」已".($record->is_active ? '啟用' : '停用'))
                    ->success()
                    ->send();
            });
    }
}
