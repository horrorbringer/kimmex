<?php

namespace App\Filament\Widgets;

use App\Enums\ApplicationStatus;
use App\Filament\Support\FlatRecordDetails;
use App\Models\JobApplication;
use App\Support\PublicStorage;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestJobApplicationsWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        return false; // Replaced by RecentActivityFeedWidget
    }

    protected int|string|array $columnSpan = 'half';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                JobApplication::query()->latest()->limit(5)
            )
            ->columns([
                TextColumn::make('applicantName')
                    ->label(__('Applicant'))
                    ->searchable(),
                TextColumn::make('job.title')
                    ->label(__('Position'))
                    ->formatStateUsing(fn (?string $state): string => $state ?: __('General Application'))
                    ->searchable(),
                TextColumn::make('resumeUrl')
                    ->label(__('Resume'))
                    ->formatStateUsing(fn ($state) => $state ? __('View Resume') : __('No Resume'))
                    ->icon(fn ($state) => $state ? 'heroicon-o-document-text' : null)
                    ->color(fn ($state) => $state ? 'success' : 'gray')
                    ->url(fn ($record) => $record->resumeUrl ? PublicStorage::url($record->resumeUrl) : null)
                    ->openUrlInNewTab(),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->color(fn ($state): string => match ($state instanceof ApplicationStatus ? $state->value : (string) $state) {
                        'PENDING' => 'warning',
                        'REVIEWING' => 'info',
                        'ACCEPTED' => 'success',
                        'REJECTED' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('submittedAt')
                    ->label(__('Date'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                ViewAction::make()
                    ->schema(fn ($record): array => FlatRecordDetails::schema($record)),
            ]);
    }
}
