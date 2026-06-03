<?php

namespace App\Filament\Resources\Inquiries\Schemas;

use App\Support\PublicStorage;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
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

                Section::make(__('Inquiry Attachment'))
                    ->description(__('Files attached by the customer'))
                    ->components([
                        TextInput::make('attachment_url')
                            ->label(__('Attachment File'))
                            ->disabled()
                            ->placeholder(__('No attachment provided'))
                            ->suffixAction(
                                Action::make('openAttachment')
                                    ->icon('heroicon-m-arrow-top-right-on-square')
                                    ->tooltip(__('Open Attachment'))
                                    ->url(fn(?string $state): ?string => $state ? PublicStorage::url($state) : null)
                                    ->openUrlInNewTab()
                                    ->visible(fn(?string $state): bool => (bool) $state)
                            ),
                    ]),
            ]);
    }
}
