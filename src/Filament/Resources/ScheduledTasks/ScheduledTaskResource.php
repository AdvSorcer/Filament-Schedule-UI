<?php

namespace AdvSorcer\FilamentScheduleUI\Filament\Resources\ScheduledTasks;

use AdvSorcer\FilamentScheduleUI\Filament\Resources\ScheduledTasks\Pages\ListScheduledTasks;
use AdvSorcer\FilamentScheduleUI\Filament\Resources\ScheduledTasks\Schemas\ScheduledTaskForm;
use AdvSorcer\FilamentScheduleUI\Filament\Resources\ScheduledTasks\Tables\ScheduledTasksTable;
use AdvSorcer\FilamentScheduleUI\Models\ScheduledTask;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ScheduledTaskResource extends Resource
{
    protected static ?string $model = ScheduledTask::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ScheduledTaskForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ScheduledTasksTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListScheduledTasks::route('/'),
            'view' => Pages\ViewScheduledTask::route('/{record}'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\LogsRelationManager::class,
        ];
    }
}

