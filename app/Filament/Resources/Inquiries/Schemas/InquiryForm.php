<?php

namespace App\Filament\Resources\Inquiries\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class InquiryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('Name'))
                    ->disabled()
                    ->required(),
                TextInput::make('email')
                    ->label(__('Email'))
                    ->email()
                    ->disabled()
                    ->required(),
                TextInput::make('phone')
                    ->label(__('Phone'))
                    ->tel()
                    ->disabled(),
                TextInput::make('subject')
                    ->label(__('Subject'))
                    ->disabled(),
                Textarea::make('message')
                    ->label(__('Message'))
                    ->disabled()
                    ->required()
                    ->columnSpanFull(),
                Select::make('status')
                    ->label(__('Status'))
                    ->options([
                        'NEW' => __('New'),
                        'READ' => __('Read'),
                        'REPLIED' => __('Replied'),
                        'CLOSED' => __('Closed'),
                    ])
                    ->required()
                    ->default('NEW'),
            ]);
    }
}
