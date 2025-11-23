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
                // 從命令字串中提取實際的 artisan 命令
                $command = $this->extractArtisanCommand($record->command);
                $exitCode = Artisan::call($command);
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

            $notification = Notification::make()
                ->title($exitCode === 0 ? '執行成功' : '執行失敗')
                ->body($exitCode === 0 ? '排程已成功執行' : "排程執行失敗，退出碼: {$exitCode}");

            if ($exitCode === 0) {
                $notification->success();
            } else {
                $notification->danger();
            }

            $notification->send();
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

    /**
     * 從完整的命令字串中提取 artisan 命令名稱
     * 例如: "'/path/to/php' 'artisan' say:hi" -> "say:hi"
     */
    protected function extractArtisanCommand(string $command): string
    {
        // 移除引號並分割命令
        $command = trim($command, "'\"");

        // 如果命令包含 'artisan'，提取 artisan 後面的部分
        if (preg_match("/['\"]artisan['\"]\s+(['\"]?)([^\s'\"]+)\\1/", $command, $matches)) {
            return $matches[2];
        }

        // 如果命令以 "artisan " 開頭，提取後面的部分
        if (preg_match("/artisan\s+(['\"]?)([^\s'\"]+)\\1/", $command, $matches)) {
            return $matches[2];
        }

        // 如果已經是純命令名稱（不包含路徑），直接返回
        if (! str_contains($command, '/') && ! str_contains($command, 'artisan')) {
            return $command;
        }

        // 嘗試從命令中提取最後一個參數（通常是命令名稱）
        $parts = preg_split("/\s+/", $command);
        foreach (array_reverse($parts) as $part) {
            $part = trim($part, "'\"");
            if (! empty($part) && ! str_contains($part, '/') && ! str_contains($part, 'php')) {
                return $part;
            }
        }

        // 如果都無法解析，返回原命令（可能會失敗，但至少會顯示錯誤）
        return $command;
    }
}
