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
                TextInput::make('url')
                    ->url()
                    ->required(),
                TextInput::make('caption'),
                TextInput::make('projectId')
                    ->required(),
            ]);
    }
}
