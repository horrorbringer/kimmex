<?php

namespace App\Filament\Resources\JobApplications\Tables;

use App\Enums\ApplicationStatus;
use App\Filament\Support\FlatRecordDetails;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class JobApplicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('job'))
            ->columns([
                TextColumn::make('job.title')
                    ->label(__('Job Title'))
                    ->getStateUsing(fn ($record): string => $record->job?->title ?? __('General Application'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->color(fn ($state): string => match (is_string($state) ? $state : $state->value) {
                        'PENDING' => 'warning',
                        'REVIEWING' => 'info',
                        'INTERVIEW' => 'warning',
                        'ACCEPTED' => 'success',
                        'REJECTED' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => $state instanceof ApplicationStatus ? $state->getLabel() : $state)
                    ->searchable(),
                TextColumn::make('submittedAt')
                    ->label(__('Submitted At'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->schema(fn ($record): array => FlatRecordDetails::schema($record)),
                EditAction::make()->visible(fn () => auth()->user()?->isAdmin()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->visible(fn () => auth()->user()?->isAdmin()),
                ]),
            ]);
    }
}
