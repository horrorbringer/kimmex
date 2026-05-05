<?php

namespace App\Filament\Resources\Policies\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Filament\Schemas\Components\Utilities\Set;

class PolicyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Policy Identity'))
                    ->components([
                        Grid::make(2)->components([
                            TextInput::make('title')
                                ->label(__('Title'))
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                            TextInput::make('slug')
                                ->label(__('Slug'))
                                ->unique(ignoreRecord: true)
                                ->required(),
                            \Filament\Forms\Components\Select::make('icon')
                                ->label(__('Icon'))
                                ->options([
                                    'heroicon-o-shield-check' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-shield-check style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle;" />') . ' Safety & Security',
                                    'heroicon-o-check-badge' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-check-badge style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle;" />') . ' Quality & Standards',
                                    'heroicon-o-globe-alt' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-globe-alt style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle;" />') . ' Environmental',
                                    'heroicon-o-finger-print' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-finger-print style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle;" />') . ' Ethics & Integrity',
                                    'heroicon-o-lock-closed' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-lock-closed style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle;" />') . ' Privacy & Data',
                                    'heroicon-o-document-text' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-document-text style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle;" />') . ' General Policy',
                                    'heroicon-o-user-group' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-user-group style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle;" />') . ' Governance & HR',
                                    'heroicon-o-briefcase' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-briefcase style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle;" />') . ' Corporate/Business',
                                    'heroicon-o-exclamation-triangle' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-exclamation-triangle style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle;" />') . ' Risk Management',
                                    'heroicon-o-clipboard-document-check' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-clipboard-document-check style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle;" />') . ' Compliance',
                                ])
                                ->allowHtml()
                                ->searchable()
                                ->prefixIcon(fn ($state) => $state)
                                ->placeholder(__('Select an icon')),
                            TextInput::make('sort_order')
                                ->label(__('Order'))
                                ->numeric()
                                ->default(0),
                        ]),
                    ]),

                Section::make(__('Policy Body'))
                    ->components([
                        RichEditor::make('content')
                            ->label(__('Content'))
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsVisibility('public')
                            ->columnSpanFull()
                            ->required(),
                    ]),

                Section::make(__('Visibility'))
                    ->components([
                        Toggle::make('is_public')
                            ->label(__('Visible on Website'))
                            ->default(true),
                    ]),
            ]);
    }
}
