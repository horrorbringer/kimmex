<?php

namespace App\Filament\Widgets;

use App\Models\JobApplication;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class LatestJobApplicationsWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'half';

    public static function canView(): bool
    {
        return JobApplication::query()->exists();
    }

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
                    ->url(fn ($record) => $record->resumeUrl ? \App\Support\PublicStorage::url($record->resumeUrl) : null)
                    ->openUrlInNewTab(),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->color(fn ($state): string => match ($state instanceof \App\Enums\ApplicationStatus ? $state->value : (string) $state) {
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
                \Filament\Actions\ViewAction::make()
                    ->schema(fn ($record): array => \App\Filament\Support\FlatRecordDetails::schema($record)),
            ]);
    }
}
