<?php

namespace App\Filament\Resources\JobApplications\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Actions\Action;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

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
                            Select::make('jobId')
                                ->label(__('Job Portfolio'))
                                ->relationship('job', 'title', fn($query) => $query->orderBy('title->en'))
                                ->disabled()
                                ->required(),
                            Select::make('status')
                                ->label(__('Management Status'))
                                ->options([
                                    'PENDING' => __('Pending'),
                                    'REVIEWED' => __('Reviewed'),
                                    'INTERVIEWING' => __('Interviewing'),
                                    'HIRED' => __('Hired'),
                                    'REJECTED' => __('Rejected'),
                                ])
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
                                        ->url(fn(?string $state): ?string => $state ? asset('storage/' . $state) : null)
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
