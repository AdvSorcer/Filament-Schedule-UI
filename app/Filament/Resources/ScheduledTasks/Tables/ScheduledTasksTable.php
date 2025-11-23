<?php

namespace App\Filament\Resources\ScheduledTasks\Tables;

use App\Filament\Resources\ScheduledTasks\Actions\RunNowAction;
use App\Filament\Resources\ScheduledTasks\Actions\SyncSchedulesAction;
use App\Filament\Resources\ScheduledTasks\Actions\ToggleActiveAction;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ScheduledTasksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('名稱')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('command')
                    ->label('命令')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('command_type')
                    ->label('類型')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'command' => 'success',
                        'call' => 'info',
                        'exec' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('expression')
                    ->label('Cron 表達式')
                    ->searchable(),
                ToggleColumn::make('is_active')
                    ->label('啟用'),
                TextColumn::make('next_run_at')
                    ->label('下次執行')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('last_run_at')
                    ->label('最後執行')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('is_active')
                    ->label('狀態')
                    ->options([
                        true => '啟用',
                        false => '停用',
                    ]),
                SelectFilter::make('command_type')
                    ->label('類型')
                    ->options([
                        'command' => 'Command',
                        'call' => 'Call',
                        'exec' => 'Exec',
                    ]),
            ])
            ->recordActions([
                RunNowAction::make(),
                ToggleActiveAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    SyncSchedulesAction::make(),
                ]),
            ])
            ->defaultSort('next_run_at');
    }
}
