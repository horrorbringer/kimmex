<?php

namespace App\Filament\Resources\Departments\Schemas;

use App\Filament\Support\TranslationHelper;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class DepartmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Department Identity'))
                    ->icon('heroicon-o-building-office')
                    ->description(__('Define the core identification for this corporate department.'))
                    ->components([
                        Grid::make(2)->components([
                            TextInput::make('name')
                                ->label(__('Department Name'))
                                ->placeholder(__('E.g., Civil Engineering'))
                                ->required()
                                ->live(onBlur: true)
                                ->suffixAction(TranslationHelper::getAutoTranslateAction('name'))
                                ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                            TextInput::make('slug')
                                ->label(__('URL Slug'))
                                ->placeholder(__('civil-engineering'))
                                ->helperText(__('Auto-generated. Click ✏️ to edit manually.'))
                                ->prefix('kimmex.com/dept/')
                                ->unique(ignoreRecord: true)
                                ->required()
                                ->disabled(fn ($get) => ! $get('_slug_manual'))
                                ->dehydrated()
                                ->suffixAction(
                                    Action::make('toggleSlugManual')
                                        ->icon(fn ($get) => $get('_slug_manual') ? 'heroicon-o-lock-open' : 'heroicon-o-pencil-square')
                                        ->tooltip(fn ($get) => $get('_slug_manual') ? __('Lock (auto-generate)') : __('Edit manually'))
                                        ->action(function (Set $set, $get) {
                                            $set('_slug_manual', ! $get('_slug_manual'));
                                        })
                                ),
                            Hidden::make('_slug_manual')->default(false)->dehydrated(false),
                            Toggle::make('isActive')
                                ->label(__('Is Active'))
                                ->default(true)
                                ->required(),
                        ]),
                    ]),

                Section::make(__('Information'))
                    ->icon('heroicon-o-information-circle')
                    ->description(__('Provide a detailed overview of the department\'s scope and responsibilities.'))
                    ->components([
                        RichEditor::make('description')->resizableImages()->resizableImages()
                            ->label(__('Detailed Description'))
                            ->hintAction(TranslationHelper::getAutoTranslateAction('description'))
                            ->fileAttachmentsDisk(config('filesystems.public_uploads_disk'))
                            ->fileAttachmentsVisibility('public')
                            ->placeholder(__('Describe the department goals...'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
