<?php

namespace App\Filament\Resources\ScheduledTasks\Tables;

use App\Filament\Resources\ScheduledTasks\Actions\RunNowAction;
use App\Filament\Resources\ScheduledTasks\Actions\SyncSchedulesAction;
use App\Models\ScheduledTask;
use Filament\Actions\BulkActionGroup;
use Filament\Notifications\Notification;
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
                TextColumn::make('description')
                    ->label(__('schedule.name'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state, $record) => $state ?: $record->command)
                    ->placeholder('-'),
                TextColumn::make('command')
                    ->label(__('schedule.command'))
                    ->searchable()
                    ->limit(50),
                TextColumn::make('command_type')
                    ->label(__('schedule.type'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'command' => 'success',
                        'call' => 'info',
                        'exec' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('expression')
                    ->label(__('schedule.cron_expression'))
                    ->searchable(),
                ToggleColumn::make('is_active')
                    ->label(__('schedule.enabled'))
                    ->afterStateUpdated(function (ScheduledTask $record, $state) {
                        $taskName = $record->description ?: $record->command;
                        $statusKey = $state ? 'task_enabled' : 'task_disabled';

                        try {
                            Notification::make()
                                ->title(__('schedule.operation_success'))
                                ->body(__("schedule.{$statusKey}", ['name' => $taskName]))
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title(__('schedule.operation_failed'))
                                ->body(__('schedule.update_status_error', ['error' => $e->getMessage()]))
                                ->danger()
                                ->send();
                        }
                    }),
                TextColumn::make('next_run_at')
                    ->label(__('schedule.next_run'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('last_run_at')
                    ->label(__('schedule.last_run'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('is_active')
                    ->label(__('schedule.status'))
                    ->options([
                        true => __('schedule.active'),
                        false => __('schedule.inactive'),
                    ]),
                SelectFilter::make('command_type')
                    ->label(__('schedule.type'))
                    ->options([
                        'command' => __('schedule.command_type'),
                        'call' => __('schedule.call_type'),
                        'exec' => __('schedule.exec_type'),
                    ]),
            ])
            ->recordActions([
                RunNowAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    SyncSchedulesAction::make(),
                ]),
            ])
            ->defaultSort('next_run_at');
    }
}
