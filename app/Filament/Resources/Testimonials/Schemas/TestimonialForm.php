<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Client Identity'))
                    ->description(__('Verification of the client providing the testimonial'))
                    ->components([
                        Grid::make(2)->components([
                            TextInput::make('clientName')
                                ->label(__('Client Name'))
                                ->required(),
                            FileUpload::make('image')
                                ->image()
                                ->directory('testimonials')
                                ->label(__('Client Avatar')),
                        ]),
                        Grid::make(2)->components([
                            TextInput::make('clientRole')
                                ->label(__('Role'))
                                ->placeholder('e.g. CEO'),
                            TextInput::make('company')
                                ->label(__('Company'))
                                ->placeholder('e.g. Acme Corp'),
                        ]),
                    ]),

                Section::make(__('Testimonial Content'))
                    ->components([
                        Select::make('rating')
                            ->label(__('Rating'))
                            ->options([
                                5 => '⭐⭐⭐⭐⭐ (' . __('Excellent') . ')',
                                4 => '⭐⭐⭐⭐ (' . __('Good') . ')',
                                3 => '⭐⭐⭐ (' . __('Average') . ')',
                                2 => '⭐⭐ (' . __('Poor') . ')',
                                1 => '⭐ (' . __('Terrible') . ')',
                            ])
                            ->required()
                            ->default(5),
                        RichEditor::make('content')
                            ->label(__('Content'))
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make(__('Internal Settings'))
                    ->collapsed()
                    ->components([
                        Grid::make(2)->components([
                            Toggle::make('isFeatured')
                                ->label(__('Is Featured'))
                                ->required(),
                            TextInput::make('orderIndex')
                                ->label(__('Order'))
                                ->numeric()
                                ->default(0),
                        ]),
                    ]),
            ]);
    }
}
