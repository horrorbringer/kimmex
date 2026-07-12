<?php

namespace App\Filament\Resources\Documents\Schemas;

use Filament\Forms\Components\TextInput;
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
                Section::make(__('Document Details'))
                    ->description(__('Title, category, and main settings'))
                    ->columns(2)
                    ->components([
                        TextInput::make('title')
                            ->label(__('Title'))
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', \Illuminate\Support\Str::slug($state)))
                            ->columnSpanFull(),
                        TextInput::make('slug')
                            ->label(__('Slug'))
                            ->helperText(__('Auto-generated. Click ✏️ to edit manually.'))
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->disabled(fn ($get) => !$get('_slug_manual'))
                            ->dehydrated()
                            ->suffixAction(
                                \Filament\Actions\Action::make('toggleSlugManual')
                                    ->icon(fn ($get) => $get('_slug_manual') ? 'heroicon-o-lock-open' : 'heroicon-o-pencil-square')
                                    ->tooltip(fn ($get) => $get('_slug_manual') ? __('Lock (auto-generate)') : __('Edit manually'))
                                    ->action(function (Set $set, $get) {
                                        $set('_slug_manual', !$get('_slug_manual'));
                                    })
                            ),
                        \Filament\Forms\Components\Hidden::make('_slug_manual')->default(false)->dehydrated(false),
                        Select::make('document_category_id')
                            ->label(__('Category'))
                            ->relationship('documentCategory', 'name', fn($query) => $query->where('isActive', true)->orderBy('name->en'))
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')->label(__('Name'))->required(),
                                TextInput::make('slug')->label(__('Slug'))->required(),
                            ])
                            ->required(),
                    ]),

                Section::make(__('Content'))
                    ->components([
                        RichEditor::make('description')
                            ->label(__('Description'))
                            ->fileAttachmentsDisk(config('filesystems.public_uploads_disk'))
                            ->fileAttachmentsVisibility('public')
                            ->columnSpanFull(),
                    ]),

                Section::make(__('Files'))
                    ->columns(2)
                    ->components([
                        FileUpload::make('fileUrl')
                            ->label(__('Document File'))
                            ->disk(config('filesystems.public_uploads_disk'))
                            ->visibility('public')
                            ->directory('documents/files')
                            ->preserveFilenames()
                            ->required(),
                        FileUpload::make('thumbnailUrl')
                            ->label(__('Thumbnail'))
                            ->image()
                            ->disk(config('filesystems.public_uploads_disk'))
                            ->visibility('public')
                            ->directory('documents/thumbnails'),
                    ]),

                Section::make(__('Settings'))
                    ->columns(4)
                    ->components([
                        Select::make('departmentId')
                            ->label(__('Department'))
                            ->relationship('department', 'name', fn($query) => $query->orderBy('name->en'))
                            ->searchable()
                            ->preload(),
                        Toggle::make('isPublic')
                            ->label(__('Public'))
                            ->default(true),
                        Toggle::make('is_featured')
                            ->label(__('Featured'))
                            ->default(false),
                        Toggle::make('isActive')
                            ->label(__('Active'))
                            ->default(true),
                    ]),

                Section::make(__('Statistics'))
                    ->collapsed()
                    ->hiddenOn('create')
                    ->columns(3)
                    ->components([
                        TextInput::make('fileSize')
                            ->label(__('File Size'))
                            ->disabled(),
                        TextInput::make('fileType')
                            ->label(__('File Type'))
                            ->disabled(),
                        TextInput::make('downloadCount')
                            ->label(__('Downloads'))
                            ->numeric()
                            ->disabled()
                            ->default(0),
                    ]),
            ]);
    }
}
