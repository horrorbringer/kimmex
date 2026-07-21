<?php

namespace App\Filament\Resources\SystemSettings\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SystemSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->required(),
                Textarea::make('value')
                    ->required()
                    ->rows(15)
                    ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $state)
                    ->dehydrateStateUsing(fn ($state) => json_decode($state, true))
                    ->rules([
                        fn (): \Closure => function (string $attribute, $value, \Closure $fail) {
                            if (is_string($value) && json_decode($value) === null && json_last_error() !== JSON_ERROR_NONE) {
                                $fail(__('The value must be a valid JSON string.'));
                            }
                        },
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
