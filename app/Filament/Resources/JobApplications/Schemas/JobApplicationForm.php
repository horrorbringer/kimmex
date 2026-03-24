<?php

namespace App\Filament\Resources\JobApplications\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class JobApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('jobId')
                    ->label(__('Job Title'))
                    ->relationship('job', 'title')
                    ->disabled()
                    ->required(),
                TextInput::make('applicantName')
                    ->label(__('Applicant Name'))
                    ->disabled()
                    ->required(),
                TextInput::make('email')
                    ->label(__('Email'))
                    ->email()
                    ->disabled()
                    ->required(),
                TextInput::make('phone')
                    ->label(__('Phone'))
                    ->tel()
                    ->disabled()
                    ->required(),
                TextInput::make('resumeUrl')
                    ->label(__('Resume Link'))
                    ->disabled()
                    ->required(),
                Textarea::make('coverLetter')
                    ->label(__('Cover Letter'))
                    ->disabled()
                    ->columnSpanFull(),
                Select::make('status')
                    ->label(__('Status'))
                    ->options([
                        'PENDING' => __('Pending'),
                        'REVIEWED' => __('Reviewed'),
                        'INTERVIEWING' => __('Interviewing'),
                        'HIRED' => __('Hired'),
                        'REJECTED' => __('Rejected'),
                    ])
                    ->required()
                    ->default('PENDING'),
                DateTimePicker::make('submittedAt')
                    ->label(__('Submitted At'))
                    ->disabled()
                    ->required(),
            ]);
    }
}
