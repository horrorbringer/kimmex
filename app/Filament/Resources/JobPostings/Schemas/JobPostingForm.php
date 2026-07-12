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
                    ->helperText(__('Auto-generated from title. Used for the job posting URL.'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->prefix('kimmex.com/careers/'),

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
                        RichEditor::make('summary')
                            ->label(__('Summary'))
                            ->fileAttachmentsDisk(config('filesystems.public_uploads_disk'))
                            ->fileAttachmentsVisibility('public')
                            ->columnSpanFull(),
                        RichEditor::make('responsibilities')
                            ->label(__('Responsibilities'))
                            ->fileAttachmentsDisk(config('filesystems.public_uploads_disk'))
                            ->fileAttachmentsVisibility('public')
                            ->columnSpanFull(),
                        RichEditor::make('requirements')
                            ->label(__('Requirements'))
                            ->fileAttachmentsDisk(config('filesystems.public_uploads_disk'))
                            ->fileAttachmentsVisibility('public')
                            ->columnSpanFull(),
                        RichEditor::make('benefits')
                            ->label(__('Benefits'))
                            ->fileAttachmentsDisk(config('filesystems.public_uploads_disk'))
                            ->fileAttachmentsVisibility('public')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
