<?php

namespace App\Filament\Resources\ScheduledTasks\RelationManagers;

use App\ScheduledTaskLogStatus;
use Filament\Actions\ViewAction;
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
                    ->label(__('schedule.status'))
                    ->badge()
                    ->color(fn (ScheduledTaskLogStatus $state): string => match ($state) {
                        ScheduledTaskLogStatus::Running => 'info',
                        ScheduledTaskLogStatus::Success => 'success',
                        ScheduledTaskLogStatus::Failed => 'danger',
                        ScheduledTaskLogStatus::Skipped => 'warning',
                    }),
                TextColumn::make('started_at')
                    ->label(__('schedule.started_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('finished_at')
                    ->label(__('schedule.finished_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('duration')
                    ->label(__('schedule.duration'))
                    ->formatStateUsing(fn (?int $state): string => $state ? number_format($state / 1000, 2).' '.__('schedule.seconds') : '-'),
                TextColumn::make('exit_code')
                    ->label(__('schedule.exit_code'))
                    ->badge()
                    ->color(fn (?int $state): string => $state === 0 ? 'success' : 'danger'),
                TextColumn::make('error_message')
                    ->label(__('schedule.error_message'))
                    ->limit(50)
                    ->wrap(),
                TextColumn::make('output')
                    ->label(__('schedule.output'))
                    ->limit(100)
                    ->wrap()
                    ->toggleable()
                    ->copyable(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label(__('schedule.view_full_output'))
                    ->modalHeading(fn ($record) => __('schedule.log_detail', ['id' => $record->id]))
                    ->modalContent(function ($record) {
                        return view('filament.scheduled-tasks.log-detail', [
                            'log' => $record,
                        ]);
                    })
                    ->modalWidth('4xl'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('schedule.status'))
                    ->options([
                        ScheduledTaskLogStatus::Running->value => __('schedule.running'),
                        ScheduledTaskLogStatus::Success->value => __('schedule.success'),
                        ScheduledTaskLogStatus::Failed->value => __('schedule.failed'),
                        ScheduledTaskLogStatus::Skipped->value => __('schedule.skipped'),
                    ]),
            ])
            ->defaultSort('started_at', 'desc')
            ->poll('30s');
    }
}
