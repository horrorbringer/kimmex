<?php

namespace App\Filament\Resources\Documents\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class DocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Document Identity'))
                    ->components([
                        Grid::make(2)->components([
                            TextInput::make('title')
                                ->label(__('Title'))
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', \Illuminate\Support\Str::slug($state))),
                            TextInput::make('slug')
                                ->label(__('Slug'))
                                ->unique(ignoreRecord: true)
                                ->required(),
                        ]),
                    ]),

                Section::make(__('Document Content'))
                    ->components([
                        RichEditor::make('description')
                            ->label(__('Description'))
                            ->columnSpanFull(),
                    ]),

                Section::make(__('Files & Media'))
                    ->description(__('Upload the main document and its thumbnail preview'))
                    ->components([
                        Grid::make(2)->components([
                            FileUpload::make('fileUrl')
                                ->label(__('Main Document File'))
                                ->directory('documents/files')
                                ->preserveFilenames()
                                ->required(),
                            FileUpload::make('thumbnailUrl')
                                ->label(__('Thumbnail Preview'))
                                ->image()
                                ->directory('documents/thumbnails'),
                        ]),
                    ]),

                Section::make(__('Organization & Access'))
                    ->components([
                        Grid::make(3)->components([
                            Select::make('document_category_id')
                                ->label(__('Category'))
                                ->relationship('documentCategory', 'name', fn($query) => $query->orderBy('name->en'))
                                ->searchable()
                                ->preload()
                                ->createOptionForm([
                                    TextInput::make('name')
                                        ->label(__('Name'))
                                        ->required(),
                                    TextInput::make('slug')
                                        ->label(__('Slug'))
                                        ->required(),
                                ])
                                ->required(),
                            Select::make('departmentId')
                                ->label(__('Department'))
                                ->relationship('department', 'name', fn($query) => $query->orderBy('name->en'))
                                ->searchable()
                                ->preload(),
                            Toggle::make('isPublic')
                                ->label(__('Is Publicly Accessible'))
                                ->default(true),
                            Toggle::make('is_featured')
                                ->label(__('Is Featured'))
                                ->default(false),
                        ]),
                    ]),

                Section::make(__('Statistics'))
                    ->collapsed()
                    ->components([
                        Grid::make(3)->components([
                            TextInput::make('fileSize')
                                ->label(__('File Size'))
                                ->disabled(),
                            TextInput::make('fileType')
                                ->label(__('File Type'))
                                ->disabled(),
                            TextInput::make('downloadCount')
                                ->label(__('Total Downloads'))
                                ->numeric()
                                ->disabled()
                                ->default(0),
                        ]),
                    ]),
            ]);
    }
}
