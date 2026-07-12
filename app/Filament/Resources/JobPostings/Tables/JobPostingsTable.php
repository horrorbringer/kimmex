<?php

namespace App\Filament\Resources\JobPostings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class JobPostingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('department'))
            ->columns([
                TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable(),
                TextColumn::make('departmentId')
                    ->label(__('Department'))
                    ->searchable(),
                TextColumn::make('location')
                    ->label(__('Location'))
                    ->searchable(),
                TextColumn::make('type')
                    ->label(__('Type'))
                    ->searchable(),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('closingDate')
                    ->label(__('Closing Date'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\ViewAction::make()->schema(fn ($record): array => \App\Filament\Support\FlatRecordDetails::schema($record)),
                \Filament\Actions\Action::make('viewOnWebsite')
                    ->label(__('View on Website'))
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('info')
                    ->url(fn(\App\Models\JobPosting $record): string => route('careers.show', ['slug' => $record->slug]))
                    ->openUrlInNewTab(),
                \Filament\Actions\Action::make('copy_link')
                    ->label(__('Copy Link'))
                    ->icon('heroicon-o-link')
                    ->color('gray')
                    ->extraAttributes(fn($record) => [
                        'x-on:click' => "window.navigator.clipboard.writeText('" . route('careers.show', ['slug' => $record->slug]) . "')",
                    ])
                    ->action(fn() => \Filament\Notifications\Notification::make()->title(__('Link Copied'))->success()->send()),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->visible(fn() => auth()->user()?->isAdmin()),
                ]),
            ]);
    }
}
