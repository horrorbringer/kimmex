<?php

namespace App\Filament\Resources\JobPostings\Schemas;

use App\Enums\JobPostingStatus;
use App\Filament\Support\TranslationHelper;
use App\Models\SystemSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\ToolbarButtonGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

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
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),

                TextInput::make('slug')
                    ->label(__('Slug'))
                    ->helperText(__('Auto-generated from title. Click ✏️ to edit manually.'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->prefix('kimmex.com/careers/')
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

                Section::make(__('Job Details'))
                    ->icon('heroicon-o-briefcase')
                    ->description(__('Essential information about the position'))
                    ->components([
                        Select::make('departmentId')
                            ->label(__('Department'))
                            ->relationship('department', 'name', fn ($query) => $query->orderBy('name->en'))
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

                        Select::make('telegramChannelId')
                            ->label(__('Shared Telegram Career Channel'))
                            ->options(fn (): array => collect(SystemSetting::get('career_telegram_channels', []))
                                ->pluck('name', 'id')
                                ->filter()
                                ->all())
                            ->searchable()
                            ->placeholder(__('No shared channel selected'))
                            ->helperText(__('Select a channel managed in System Settings. Clear this field to use a manual channel.'))
                            ->live(),

                        FileUpload::make('telegramQr')
                            ->label(__('Manual Telegram QR Image'))
                            ->helperText(__('Optional override for a different channel on this job.'))
                            ->image()
                            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp'])
                            ->maxSize(2048)
                            ->disk(config('filesystems.public_uploads_disk'))
                            ->directory('jobs/telegram-qr')
                            ->visibility('public')
                            ->visible(fn (Get $get): bool => blank($get('telegramChannelId'))),

                        TextInput::make('telegramUrl')
                            ->label(__('Manual Telegram Careers Link'))
                            ->helperText(__('Optional override for a different channel on this job.'))
                            ->url()
                            ->placeholder('https://t.me/kimmexcareers')
                            ->visible(fn (Get $get): bool => blank($get('telegramChannelId'))),
                    ]),

                Section::make(__('Job Description'))
                    ->icon('heroicon-o-document-text')
                    ->description(__('Detailed description shown on the careers page'))
                    ->components([
                        RichEditor::make('summary')->resizableImages()
                            ->label(__('Summary'))
                            ->hintAction(TranslationHelper::getAutoTranslateAction('summary'))
                            ->toolbarButtons([
                                ['bold', 'italic', 'underline', 'link'],
                                [ToolbarButtonGroup::make('Heading', ['h3', 'h4'])->textualButtons()],
                                ['bulletList', 'orderedList'],
                                ['undo', 'redo'],
                            ])
                            ->fileAttachmentsDisk(config('filesystems.public_uploads_disk'))
                            ->fileAttachmentsVisibility('public')
                            ->columnSpanFull(),
                        RichEditor::make('responsibilities')->resizableImages()
                            ->label(__('Responsibilities'))
                            ->hintAction(TranslationHelper::getAutoTranslateAction('responsibilities'))
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
                            ->hintAction(TranslationHelper::getAutoTranslateAction('requirements'))
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
                            ->hintAction(TranslationHelper::getAutoTranslateAction('benefits'))
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
