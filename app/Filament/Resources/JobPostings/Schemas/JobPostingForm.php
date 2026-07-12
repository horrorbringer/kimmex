<?php

namespace App\Filament\Resources\JobPostings\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use App\Enums\JobPostingStatus;
use App\Filament\Support\TranslationHelper;

class JobPostingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label(__('Title'))
                    ->suffixAction(TranslationHelper::getAutoTranslateAction('title'))
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state))),

                TextInput::make('slug')
                    ->label(__('Slug'))
                    ->helperText(__('Auto-generated from title. Click ✏️ to edit manually.'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->prefix('kimmex.com/careers/')
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

                Section::make(__('Job Details'))
                    ->icon('heroicon-o-briefcase')
                    ->description(__('Essential information about the position'))
                    ->components([
                        Select::make('departmentId')
                            ->label(__('Department'))
                            ->relationship('department', 'name', fn($query) => $query->orderBy('name->en'))
                            ->searchable()
                            ->preload()
                            ->required(),

                        Grid::make(2)->components([
                            TextInput::make('location')
                                ->label(__('Location'))
                                ->suffixAction(TranslationHelper::getAutoTranslateAction('location'))
                                ->required()
                                ->default('Phnom Penh'),

                            Select::make('type')
                                ->label(__('Type'))
                                ->options([
                                    'FULL_TIME' => __('Full Time'),
                                    'PART_TIME' => __('Part Time'),
                                    'CONTRACT' => __('Contract'),
                                    'INTERNSHIP' => __('Internship'),
                                ])
                                ->required()
                                ->default('FULL_TIME'),
                        ]),

                        Grid::make(2)->components([
                            TextInput::make('experience')
                                ->label(__('Experience'))
                                ->suffixAction(TranslationHelper::getAutoTranslateAction('experience'))
                                ->required()
                                ->default('2-3 Years'),

                            TextInput::make('salary')
                                ->label(__('Salary'))
                                ->suffixAction(TranslationHelper::getAutoTranslateAction('salary'))
                                ->required()
                                ->default('Negotiable'),
                        ]),

                        DateTimePicker::make('closingDate')
                            ->label(__('Closing Date'))
                            ->helperText(__('Leave blank for no closing date'))
                            ->native(false),

                        Select::make('status')
                            ->label(__('Status'))
                            ->options(JobPostingStatus::class)
                            ->required()
                            ->default(JobPostingStatus::DRAFT),
                    ]),

                Section::make(__('Job Description'))
                    ->icon('heroicon-o-document-text')
                    ->description(__('Detailed description shown on the careers page'))
                    ->components([
                        RichEditor::make('summary')->resizableImages()
                            ->label(__('Summary'))
                            ->toolbarButtons([
                                ['bold', 'italic', 'underline', 'link'],
                                [\Filament\Forms\Components\RichEditor\ToolbarButtonGroup::make('Heading', ['h3', 'h4'])->textualButtons()],
                                ['bulletList', 'orderedList'],
                                ['undo', 'redo'],
                            ])
                            ->fileAttachmentsDisk(config('filesystems.public_uploads_disk'))
                            ->fileAttachmentsVisibility('public')
                            ->columnSpanFull(),
                        RichEditor::make('responsibilities')->resizableImages()
                            ->label(__('Responsibilities'))
                            ->toolbarButtons([
                                ['bold', 'italic', 'underline', 'link'],
                                ['bulletList', 'orderedList'],
                                ['undo', 'redo'],
                            ])
                            ->fileAttachmentsDisk(config('filesystems.public_uploads_disk'))
                            ->fileAttachmentsVisibility('public')
                            ->columnSpanFull(),
                        RichEditor::make('requirements')->resizableImages()
                            ->label(__('Requirements'))
                            ->toolbarButtons([
                                ['bold', 'italic', 'underline', 'link'],
                                ['bulletList', 'orderedList'],
                                ['undo', 'redo'],
                            ])
                            ->fileAttachmentsDisk(config('filesystems.public_uploads_disk'))
                            ->fileAttachmentsVisibility('public')
                            ->columnSpanFull(),
                        RichEditor::make('benefits')->resizableImages()
                            ->label(__('Benefits'))
                            ->toolbarButtons([
                                ['bold', 'italic', 'underline', 'link'],
                                ['bulletList', 'orderedList'],
                                ['undo', 'redo'],
                            ])
                            ->fileAttachmentsDisk(config('filesystems.public_uploads_disk'))
                            ->fileAttachmentsVisibility('public')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
