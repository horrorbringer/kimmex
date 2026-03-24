<?php

namespace App\Filament\Resources\ActivityLogs\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ActivityLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('log_name')
                    ->label(__('Log Name')),
                Textarea::make('description')
                    ->label(__('Description'))
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('subject_type')
                    ->label(__('Subject Type')),
                TextInput::make('subject_id')
                    ->label(__('Subject ID'))
                    ->numeric(),
                TextInput::make('causer_type')
                    ->label(__('Causer Type')),
                TextInput::make('causer_id')
                    ->label(__('Causer ID'))
                    ->numeric(),
                Textarea::make('properties')
                    ->label(__('Properties'))
                    ->columnSpanFull(),
                TextInput::make('event')
                    ->label(__('Event')),
                TextInput::make('batch_uuid')
                    ->label(__('Batch UUID')),
            ]);
    }
}
