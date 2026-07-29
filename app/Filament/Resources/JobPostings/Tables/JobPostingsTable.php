<?php

namespace App\Filament\Resources\JobPostings\Tables;

use App\Filament\Support\FlatRecordDetails;
use App\Models\JobPosting;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
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
                TextColumn::make('department.name')
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
                ActionGroup::make([
                    ViewAction::make()->schema(fn ($record): array => FlatRecordDetails::schema($record)),
                    EditAction::make(),
                    Action::make('viewOnWebsite')
                        ->label(__('View on Website'))
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->color('info')
                        ->url(fn (JobPosting $record): string => route('careers.show', ['slug' => $record->slug]))
                        ->openUrlInNewTab(),
                    Action::make('copy_link')
                        ->label(__('Copy Link'))
                        ->icon('heroicon-o-link')
                        ->color('gray')
                        ->extraAttributes(fn ($record) => [
                            'x-on:click' => "window.navigator.clipboard.writeText('".route('careers.show', ['slug' => $record->slug])."')",
                        ])
                        ->action(fn () => Notification::make()->title(__('Link Copied'))->success()->send()),
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
