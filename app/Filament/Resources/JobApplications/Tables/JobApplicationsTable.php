<?php

namespace App\Filament\Resources\JobApplications\Tables;

use App\Enums\ApplicationStatus;
use App\Filament\Support\FlatRecordDetails;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SelectColumn;
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
                SelectColumn::make('status')
                    ->label(__('Status'))
                    ->options(ApplicationStatus::class)
                    ->rules(['required'])
                    ->selectablePlaceholder(false)
                    ->native(false)
                    ->disabled(fn (): bool => ! auth()->user()?->isAdmin())
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
                ActionGroup::make([
                    ViewAction::make()->schema(fn ($record): array => FlatRecordDetails::schema($record)),
                    EditAction::make()->visible(fn () => auth()->user()?->isAdmin()),
                ])
                    ->icon(Heroicon::EllipsisVertical)
                    ->iconButton()
                    ->tooltip(__('Actions')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->visible(fn () => auth()->user()?->isAdmin()),
                ]),
            ]);
    }
}
