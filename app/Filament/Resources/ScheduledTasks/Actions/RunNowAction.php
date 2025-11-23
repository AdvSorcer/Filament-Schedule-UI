<?php

namespace App\Filament\Resources\ScheduledTasks\Actions;

use App\Models\ScheduledTask;
use App\ScheduledTaskLogStatus;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;

class RunNowAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'runNow';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('立即執行')
            ->icon('heroicon-o-play')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('確認執行排程')
            ->modalDescription('您確定要立即執行這個排程嗎？')
            ->action(function (ScheduledTask $record) {
                $this->executeTask($record);
            });
    }

    protected function executeTask(ScheduledTask $record): void
    {
        $startedAt = now();
        $log = $record->logs()->create([
            'status' => ScheduledTaskLogStatus::Running,
            'started_at' => $startedAt,
        ]);

        try {
            $output = '';
            $exitCode = 0;

            if ($record->command_type === 'command') {
                $exitCode = Artisan::call($record->command);
                $output = Artisan::output();
            } elseif ($record->command_type === 'call') {
                // 對於 call 類型，我們需要執行回調
                // 這裡簡化處理，實際可能需要更複雜的邏輯
                $output = 'Call type execution not fully supported in manual run';
            } else {
                // exec 類型
                exec($record->command, $outputArray, $exitCode);
                $output = implode("\n", $outputArray);
            }

            $finishedAt = now();
            $duration = $startedAt->diffInMilliseconds($finishedAt);

            $log->update([
                'status' => $exitCode === 0 ? ScheduledTaskLogStatus::Success : ScheduledTaskLogStatus::Failed,
                'finished_at' => $finishedAt,
                'duration' => $duration,
                'output' => $output,
                'exit_code' => $exitCode,
                'error_message' => $exitCode !== 0 ? "Command exited with code {$exitCode}" : null,
            ]);

            $record->update(['last_run_at' => $finishedAt]);

            Notification::make()
                ->title($exitCode === 0 ? '執行成功' : '執行失敗')
                ->body($exitCode === 0 ? '排程已成功執行' : "排程執行失敗，退出碼: {$exitCode}")
                ->success($exitCode === 0)
                ->danger($exitCode !== 0)
                ->send();
        } catch (\Exception $e) {
            $finishedAt = now();
            $duration = $startedAt->diffInMilliseconds($finishedAt);

            $log->update([
                'status' => ScheduledTaskLogStatus::Failed,
                'finished_at' => $finishedAt,
                'duration' => $duration,
                'error_message' => $e->getMessage(),
                'exit_code' => 1,
            ]);

            Notification::make()
                ->title('執行錯誤')
                ->body('執行排程時發生錯誤: '.$e->getMessage())
                ->danger()
                ->send();
        }
    }
}
