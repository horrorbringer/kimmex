<?php

namespace App\Filament\Resources\ProjectImages\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProjectImageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\FileUpload::make('url')
                    ->label(__('Image'))
                    ->image()
                    ->disk('public')
                    ->directory('projects/gallery')
                    ->required(),
                TextInput::make('caption')
                    ->label(__('Caption')),
                \Filament\Forms\Components\Select::make('projectId')
                    ->label(__('Project'))
                    ->relationship('project', 'title', fn($query) => $query->orderBy('title->en'))
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }
}
