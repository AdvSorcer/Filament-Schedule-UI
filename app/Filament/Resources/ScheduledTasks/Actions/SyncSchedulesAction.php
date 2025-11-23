<?php

namespace App\Filament\Resources\ScheduledTasks\Actions;

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

        $this->label('同步排程')
            ->icon('heroicon-o-arrow-path')
            ->color('info')
            ->requiresConfirmation()
            ->modalHeading('確認同步')
            ->modalDescription('這將從程式碼中同步所有排程任務到資料庫。')
            ->action(function () {
                Artisan::call('schedule:sync');

                Notification::make()
                    ->title('同步完成')
                    ->body('排程已成功同步')
                    ->success()
                    ->send();
            });
    }
}
