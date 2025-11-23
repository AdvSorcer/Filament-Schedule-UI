<?php

namespace App\Filament\Resources\ScheduledTasks\RelationManagers;

use App\ScheduledTaskLogStatus;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LogsRelationManager extends RelationManager
{
    protected static string $relationship = 'logs';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('status')
                    ->label('狀態')
                    ->badge()
                    ->color(fn (ScheduledTaskLogStatus $state): string => match ($state) {
                        ScheduledTaskLogStatus::Running => 'info',
                        ScheduledTaskLogStatus::Success => 'success',
                        ScheduledTaskLogStatus::Failed => 'danger',
                        ScheduledTaskLogStatus::Skipped => 'warning',
                    }),
                TextColumn::make('started_at')
                    ->label('開始時間')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('finished_at')
                    ->label('結束時間')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('duration')
                    ->label('執行時長')
                    ->formatStateUsing(fn (?int $state): string => $state ? number_format($state / 1000, 2).' 秒' : '-'),
                TextColumn::make('exit_code')
                    ->label('退出碼')
                    ->badge()
                    ->color(fn (?int $state): string => $state === 0 ? 'success' : 'danger'),
                TextColumn::make('error_message')
                    ->label('錯誤訊息')
                    ->limit(50)
                    ->wrap(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('狀態')
                    ->options([
                        ScheduledTaskLogStatus::Running->value => '執行中',
                        ScheduledTaskLogStatus::Success->value => '成功',
                        ScheduledTaskLogStatus::Failed->value => '失敗',
                        ScheduledTaskLogStatus::Skipped->value => '跳過',
                    ]),
            ])
            ->defaultSort('started_at', 'desc')
            ->poll('30s'); // 每 30 秒自動刷新
    }
}
