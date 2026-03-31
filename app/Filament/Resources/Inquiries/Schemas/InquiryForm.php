<?php

namespace App\Filament\Resources\Inquiries\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class InquiryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Inquiry Overview'))
                    ->description(__('Subject and status management'))
                    ->components([
                        Grid::make(2)->components([
                            TextInput::make('subject')
                                ->label(__('Inquiry Subject'))
                                ->disabled(),
                            Select::make('status')
                                ->label(__('Management Status'))
                                ->options([
                                    'NEW' => __('New'),
                                    'READ' => __('Read'),
                                    'REPLIED' => __('Replied'),
                                    'CLOSED' => __('Closed'),
                                ])
                                ->required()
                                ->default('NEW')
                                ->selectablePlaceholder(false),
                        ]),
                    ]),

                Section::make(__('Contact Information'))
                    ->description(__('Customer identity and communication channels'))
                    ->components([
                        Grid::make(3)->components([
                            TextInput::make('name')
                                ->label(__('Full Name'))
                                ->disabled()
                                ->required(),
                            TextInput::make('email')
                                ->label(__('Email Address'))
                                ->email()
                                ->disabled()
                                ->required(),
                            TextInput::make('phone')
                                ->label(__('Contact Phone'))
                                ->tel()
                                ->disabled(),
                        ]),
                    ]),

                Section::make(__('Customer Message'))
                    ->components([
                        Textarea::make('message')
                            ->label(__('Detailed Message'))
                            ->disabled()
                            ->required()
                            ->rows(8)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
