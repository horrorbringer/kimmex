<?php

namespace App\Filament\Resources\Partners\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PartnerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Partner Identity'))
                    ->components([
                        Grid::make(1)->components([
                            TextInput::make('name')
                                ->label(__('Name'))
                                ->required(),
                            FileUpload::make('logoUrl')
                                ->image()
                                ->directory('partners')
                                ->label(__('Partner Logo'))
                                ->required(),
                        ]),
                        Grid::make(3)->components([
                            TextInput::make('website')
                                ->label(__('Website'))
                                ->url(),
                            Select::make('type')
                                ->label(__('Type'))
                                ->options([
                                    'CLIENT' => __('Client'),
                                    'PARTNER' => __('Partner'),
                                    'SPONSOR' => __('Sponsor'),
                                    'VENDOR' => __('Vendor'),
                                ])
                                ->required()
                                ->default('CLIENT'),
                            TextInput::make('orderIndex')
                                ->label(__('Order'))
                                ->required()
                                ->numeric()
                                ->default(0),
                        ]),
                    ]),
            ]);
    }
}
