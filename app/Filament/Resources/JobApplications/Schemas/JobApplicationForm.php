<?php

namespace App\Filament\Resources\JobApplications\Schemas;

use App\Enums\ApplicationStatus;
use App\Support\PublicStorage;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JobApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Application Overview'))
                    ->description(__('Candidate status and job information'))
                    ->components([
                        Grid::make(3)->components([
                            TextInput::make('jobTitle')
                                ->label(__('Job Portfolio'))
                                ->disabled()
                                ->dehydrated(false)
                                ->afterStateHydrated(function (TextInput $component, $record): void {
                                    $component->state($record?->job?->title ?? __('General Application'));
                                }),
                            Select::make('status')
                                ->label(__('Management Status'))
                                ->options(ApplicationStatus::class)
                                ->required()
                                ->default('PENDING')
                                ->selectablePlaceholder(false),
                            DateTimePicker::make('submittedAt')
                                ->label(__('Submission Date'))
                                ->disabled()
                                ->required(),
                        ]),
                    ]),

                Section::make(__('Candidate Profile'))
                    ->description(__('Contact details and professional documents'))
                    ->components([
                        Grid::make(2)->components([
                            TextInput::make('applicantName')
                                ->label(__('Full Name'))
                                ->disabled()
                                ->required(),
                            TextInput::make('email')
                                ->label(__('Email Address'))
                                ->email()
                                ->disabled()
                                ->required(),
                            TextInput::make('phone')
                                ->label(__('Contact Phone'))
                                ->tel()
                                ->disabled()
                                ->required(),
                            TextInput::make('resumeUrl')
                                ->label(__('Professional CV / Resume'))
                                ->disabled()
                                ->required()
                                ->suffixAction(
                                    Action::make('openResume')
                                        ->icon('heroicon-m-arrow-top-right-on-square')
                                        ->tooltip(__('Open Resume'))
                                        ->url(fn (?string $state): ?string => $state ? PublicStorage::url($state) : null)
                                        ->openUrlInNewTab()
                                ),
                        ]),
                    ]),

                Section::make(__('Motivation & Message'))
                    ->components([
                        Textarea::make('coverLetter')
                            ->label(__('Statement / Cover Letter'))
                            ->disabled()
                            ->rows(6)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
