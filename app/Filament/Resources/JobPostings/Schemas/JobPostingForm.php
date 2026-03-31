<?php

namespace App\Filament\Resources\JobPostings\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;
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
                    ->afterStateUpdated(fn($set, ?string $state) => $set('slug', Str::slug($state))),
                TextInput::make('slug')
                    ->label(__('Slug'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->prefix('kimmex.com/careers/'),
                Select::make('departmentId')
                    ->label(__('Department'))
                    ->relationship('department', 'name', fn($query) => $query->orderBy('name->en'))
                    ->searchable()
                    ->preload()
                    ->required(),
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
                RichEditor::make('summary')
                    ->label(__('Summary'))
                    ->columnSpanFull(),
                RichEditor::make('requirements')
                    ->label(__('Requirements'))
                    ->columnSpanFull(),
                RichEditor::make('benefits')
                    ->label(__('Benefits'))
                    ->columnSpanFull(),
                Toggle::make('isActive')
                    ->label(__('Is Active'))
                    ->required()
                    ->default(true),
                DateTimePicker::make('closingDate')
                    ->label(__('Closing Date')),
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
                RichEditor::make('responsibilities')
                    ->label(__('Responsibilities'))
                    ->columnSpanFull(),
            ]);
    }
}
